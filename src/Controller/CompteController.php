<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Compte;
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

class CompteController extends AbstractController
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

    #[Route('/compte/ajouter/{locale}/{idabonne}', name: 'app_compte_ajouter')]
    public function ajouter(Request $request, string $locale, int $idabonne): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $abonne = $this->entityManager->getRepository(Abonne::class)->find($idabonne);
        if (!$abonne) {
            throw $this->createNotFoundException('abonne.not_found');
        }

        $form = $this->createForm(AbonneCompteType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte = new Compte();
            $compte->setNumero($form->get('numero')->getData());
            $compte->setType($form->get('type')->getData());
            $compte->setAbonne($abonne);

            $this->entityManager->persist($compte);
            $this->entityManager->flush();

            $this->addFlash('success', 'compte.ajout_success');
            return $this->redirectToRoute('app_compte_liste', ['locale' => $locale]);
        }

        return $this->render('compte/ajouter.html.twig', [
            'form' => $form->createView(),
            'abonne' => $abonne,
            'locale' => $locale
        ]);
    }

    #[Route('/compte/liste/{locale}', name: 'app_compte_liste')]
    public function liste(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $comptes = $this->entityManager->getRepository(Compte::class)->findAll();

        return $this->render('compte/liste.html.twig', [
            'comptes' => $comptes,
            'locale' => $locale
        ]);
    }

    #[Route('/compte/modifier/{id}/{locale}', name: 'app_compte_modifier')]
    public function modifier(Request $request, int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $compte = $this->entityManager->getRepository(Compte::class)->find($id);

        if (!$compte) {
            throw $this->createNotFoundException('compte.not_found');
        }

        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'compte.modif_success');
            return $this->redirectToRoute('app_compte_liste', ['locale' => $locale]);
        }

        return $this->render('compte/modifier.html.twig', [
            'form' => $form->createView(),
            'compte' => $compte,
            'locale' => $locale
        ]);
    }

    #[Route('/compte/supprimer/{id}/{locale}', name: 'app_compte_supprimer')]
    public function supprimer(int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $compte = $this->entityManager->getRepository(Compte::class)->find($id);

        if (!$compte) {
            throw $this->createNotFoundException('compte.not_found');
        }

        $this->entityManager->remove($compte);
        $this->entityManager->flush();

        $this->addFlash('success', 'compte.suppr_success');
        return $this->redirectToRoute('app_compte_liste', ['locale' => $locale]);
    }

    #[Route('/compte/operations/{id}/{locale}', name: 'app_compte_operations')]
    public function operations(int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $compte = $this->entityManager->getRepository(Compte::class)->find($id);

        if (!$compte) {
            throw $this->createNotFoundException('compte.not_found');
        }

        $operations = $this->entityManager->getRepository(Operation::class)
            ->findBy(['compte' => $compte], ['dateOperation' => 'DESC']);

        return $this->render('compte/operations.html.twig', [
            'compte' => $compte,
            'operations' => $operations,
            'locale' => $locale
        ]);
    }
}
