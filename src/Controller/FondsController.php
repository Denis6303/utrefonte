<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Fonds;
use App\Form\FondsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

class FondsController extends AbstractController
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

    #[Route('/fonds/liste/{locale}', name: 'app_fonds_liste')]
    public function liste(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $fonds = $this->entityManager->getRepository(Fonds::class)->findAll();

        return $this->render('fonds/liste.html.twig', [
            'fonds' => $fonds,
            'locale' => $locale
        ]);
    }

    #[Route('/fonds/ajouter/{locale}', name: 'app_fonds_ajouter')]
    public function ajouter(Request $request, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $fonds = new Fonds();
        $fonds->setEtat(0);
        $form = $this->createForm(FondsType::class, $fonds);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($fonds);
            $this->entityManager->flush();

            $this->addFlash('success', 'fonds.ajout_success');
            return $this->redirectToRoute('app_fonds_liste', ['locale' => $locale]);
        }

        return $this->render('fonds/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route('/fonds/modifier/{id}/{locale}', name: 'app_fonds_modifier')]
    public function modifier(Request $request, int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $fonds = $this->entityManager->getRepository(Fonds::class)->find($id);

        if (!$fonds) {
            throw $this->createNotFoundException('fonds.not_found');
        }

        $form = $this->createForm(FondsType::class, $fonds);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'fonds.modif_success');
            return $this->redirectToRoute('app_fonds_liste', ['locale' => $locale]);
        }

        return $this->render('fonds/modifier.html.twig', [
            'form' => $form->createView(),
            'fonds' => $fonds,
            'locale' => $locale
        ]);
    }

    #[Route('/fonds/supprimer/{id}/{locale}', name: 'app_fonds_supprimer')]
    public function supprimer(int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $fonds = $this->entityManager->getRepository(Fonds::class)->find($id);

        if (!$fonds) {
            throw $this->createNotFoundException('fonds.not_found');
        }

        $this->entityManager->remove($fonds);
        $this->entityManager->flush();

        $this->addFlash('success', 'fonds.suppr_success');
        return $this->redirectToRoute('app_fonds_liste', ['locale' => $locale]);
    }

    #[Route('/fonds/gestionnaire/{locale}', name: 'app_fonds_gestionnaire')]
    public function listeGestionnaire(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        $fonds = $this->entityManager->getRepository(Fonds::class)
            ->findBy(['gestionnaire' => $user]);

        return $this->render('fonds/liste_gestionnaire.html.twig', [
            'fonds' => $fonds,
            'locale' => $locale
        ]);
    }

    #[Route('/fonds/corbeille/{locale}', name: 'app_fonds_corbeille')]
    public function corbeille(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $fonds = $this->entityManager->getRepository(Fonds::class)
            ->findBy(['etat' => -1]);

        return $this->render('fonds/corbeille.html.twig', [
            'fonds' => $fonds,
            'locale' => $locale
        ]);
    }
} 