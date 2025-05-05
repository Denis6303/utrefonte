<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Devise;
use App\Form\DeviseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

class DeviseSimulationController extends AbstractController
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

    #[Route('/devise/liste/{locale}', name: 'app_devise_liste')]
    public function liste(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $devises = $this->entityManager->getRepository(Devise::class)->findAll();
        $deviseLocale = $this->entityManager->getRepository(Devise::class)->findOneBy(['estLocale' => true]);

        return $this->render('devise/liste.html.twig', [
            'devises' => $devises,
            'deviseLocale' => $deviseLocale,
            'locale' => $locale
        ]);
    }

    #[Route('/devise/ajouter/{locale}', name: 'app_devise_ajouter')]
    public function ajouter(Request $request, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $devise = new Devise();
        $form = $this->createForm(DeviseType::class, $devise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingDevise = $this->entityManager->getRepository(Devise::class)
                ->findOneBy(['code' => $devise->getCode()]);

            if ($existingDevise) {
                $this->addFlash('error', 'devise.code_exists');
                return $this->redirectToRoute('app_devise_liste', ['locale' => $locale]);
            }

            if (!$devise->getIcone()) {
                $devise->setIcone('default_icone.png');
            }

            $this->entityManager->persist($devise);
            $this->entityManager->flush();

            $this->addFlash('success', 'devise.ajout_success');
            return $this->redirectToRoute('app_devise_liste', ['locale' => $locale]);
        }

        return $this->render('devise/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route('/devise/modifier/{id}/{locale}', name: 'app_devise_modifier')]
    public function modifier(Request $request, int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $devise = $this->entityManager->getRepository(Devise::class)->find($id);

        if (!$devise) {
            throw $this->createNotFoundException('devise.not_found');
        }

        $form = $this->createForm(DeviseType::class, $devise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'devise.modif_success');
            return $this->redirectToRoute('app_devise_liste', ['locale' => $locale]);
        }

        return $this->render('devise/modifier.html.twig', [
            'form' => $form->createView(),
            'devise' => $devise,
            'locale' => $locale
        ]);
    }

    #[Route('/devise/supprimer/{id}/{locale}', name: 'app_devise_supprimer')]
    public function supprimer(int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $devise = $this->entityManager->getRepository(Devise::class)->find($id);

        if (!$devise) {
            throw $this->createNotFoundException('devise.not_found');
        }

        if ($devise->getEstLocale()) {
            $this->addFlash('error', 'devise.cannot_delete_locale');
            return $this->redirectToRoute('app_devise_liste', ['locale' => $locale]);
        }

        $this->entityManager->remove($devise);
        $this->entityManager->flush();

        $this->addFlash('success', 'devise.suppr_success');
        return $this->redirectToRoute('app_devise_liste', ['locale' => $locale]);
    }

    #[Route('/devise/definir-locale/{id}/{locale}', name: 'app_devise_definir_locale')]
    public function definirLocale(int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $devise = $this->entityManager->getRepository(Devise::class)->find($id);

        if (!$devise) {
            throw $this->createNotFoundException('devise.not_found');
        }

        // Réinitialiser toutes les devises
        $allDevises = $this->entityManager->getRepository(Devise::class)->findAll();
        foreach ($allDevises as $d) {
            $d->setEstLocale(false);
        }

        // Définir la nouvelle devise locale
        $devise->setEstLocale(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'devise.locale_definie');
        return $this->redirectToRoute('app_devise_liste', ['locale' => $locale]);
    }
} 