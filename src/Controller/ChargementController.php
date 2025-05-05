<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Chargement;
use App\Form\ChargementType;
use App\Form\PrerequisChargementType;
use App\Entity\Abonne;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ChargementController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private AccessControl $accessControl;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private string $uploadDir;

    public function __construct(
        EntityManagerInterface $entityManager,
        AccessControl $accessControl,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        string $projectDir
    ) {
        $this->entityManager = $entityManager;
        $this->accessControl = $accessControl;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->uploadDir = $projectDir . '/public/upload/chargement';
    }

    #[Route('/chargement/ajouter/{locale}/{type}', name: 'app_chargement_ajouter')]
    public function ajouter(Request $request, string $locale, string $type): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $chargement = new Chargement();
        $form = $this->createForm(ChargementType::class, $chargement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('fichier')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move($this->uploadDir, $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'chargement.upload_error');
                    return $this->redirectToRoute('app_chargement_ajouter', ['locale' => $locale, 'type' => $type]);
                }

                $chargement->setNomFichier($newFilename);
                $chargement->setTypeChargement($type);
                $chargement->setDateAjout(new \DateTime());
                $chargement->setArchive(false);

                $this->entityManager->persist($chargement);
                $this->entityManager->flush();

                $this->addFlash('success', 'chargement.ajout_success');
                return $this->redirectToRoute('app_chargement_liste', ['locale' => $locale, 'type' => $type]);
            }
        }

        return $this->render('chargement/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
            'type' => $type
        ]);
    }

    #[Route('/chargement/liste/{locale}/{type}', name: 'app_chargement_liste')]
    public function liste(string $locale, string $type): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $chargements = $this->entityManager->getRepository(Chargement::class)->findBy([
            'typeChargement' => $type,
            'archive' => false
        ], ['dateAjout' => 'DESC']);

        return $this->render('chargement/liste.html.twig', [
            'chargements' => $chargements,
            'locale' => $locale,
            'type' => $type
        ]);
    }

    #[Route('/chargement/supprimer/{id}/{locale}/{type}', name: 'app_chargement_supprimer')]
    public function supprimer(int $id, string $locale, string $type): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $chargement = $this->entityManager->getRepository(Chargement::class)->find($id);

        if (!$chargement) {
            throw $this->createNotFoundException('chargement.not_found');
        }

        $filePath = $this->uploadDir . '/' . $chargement->getNomFichier();
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->entityManager->remove($chargement);
        $this->entityManager->flush();

        $this->addFlash('success', 'chargement.suppr_success');
        return $this->redirectToRoute('app_chargement_liste', ['locale' => $locale, 'type' => $type]);
    }

    #[Route('/chargement/archiver/{id}/{locale}/{type}', name: 'app_chargement_archiver')]
    public function archiver(int $id, string $locale, string $type): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $chargement = $this->entityManager->getRepository(Chargement::class)->find($id);

        if (!$chargement) {
            throw $this->createNotFoundException('chargement.not_found');
        }

        $chargement->setArchive(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'chargement.archive_success');
        return $this->redirectToRoute('app_chargement_liste', ['locale' => $locale, 'type' => $type]);
    }
} 