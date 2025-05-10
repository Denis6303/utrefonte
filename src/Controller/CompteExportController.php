<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Compte;
use App\Entity\CompteRepository;
use App\Entity\AbonneCompteType;
use App\Entity\Abonne;
use App\Entity\HistoriqueConnexion;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;
use App\Entity\CompteExport;
use App\Form\CompteExportType;

class CompteExportController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private AccessControl $accessControl;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        AccessControl $accessControl,
        RequestStack $requestStack,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->accessControl = $accessControl;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    #[Route('/compte-export/liste/{locale}', name: 'app_compte_export_liste')]
    public function liste(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $compteExports = $this->entityManager->getRepository(CompteExport::class)->findAll();

        return $this->render('compte_export/liste.html.twig', [
            'compteExports' => $compteExports,
            'locale' => $locale
        ]);
    }

    #[Route('/compte-export/ajouter/{locale}', name: 'app_compte_export_ajouter')]
    public function ajouter(Request $request, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $compteExport = new CompteExport();
        $form = $this->createForm(CompteExportType::class, $compteExport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($compteExport);
            $this->entityManager->flush();

            $this->addFlash('success', 'compte_export.ajout_success');
            return $this->redirectToRoute('app_compte_export_liste', ['locale' => $locale]);
        }

        return $this->render('compte_export/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route('/compte-export/modifier/{id}/{locale}', name: 'app_compte_export_modifier')]
    public function modifier(Request $request, int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $compteExport = $this->entityManager->getRepository(CompteExport::class)->find($id);

        if (!$compteExport) {
            throw $this->createNotFoundException('compte_export.not_found');
        }

        $form = $this->createForm(CompteExportType::class, $compteExport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'compte_export.modif_success');
            return $this->redirectToRoute('app_compte_export_liste', ['locale' => $locale]);
        }

        return $this->render('compte_export/modifier.html.twig', [
            'form' => $form->createView(),
            'compteExport' => $compteExport,
            'locale' => $locale
        ]);
    }

    #[Route('/compte-export/supprimer/{id}/{locale}', name: 'app_compte_export_supprimer')]
    public function supprimer(int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $compteExport = $this->entityManager->getRepository(CompteExport::class)->find($id);

        if (!$compteExport) {
            throw $this->createNotFoundException('compte_export.not_found');
        }

        $this->entityManager->remove($compteExport);
        $this->entityManager->flush();

        $this->addFlash('success', 'compte_export.suppr_success');
        return $this->redirectToRoute('app_compte_export_liste', ['locale' => $locale]);
    }
}
