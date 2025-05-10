<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Agence;
use App\Entity\AgenceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

class AgenceController extends AbstractController
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

    #[Route('/agence/ajouter/{locale}', name: 'app_agence_ajouter')]
    public function ajouter(Request $request, string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($agence);
            $this->entityManager->flush();

            $this->addFlash('success', 'agence.ajout_success');
            return $this->redirectToRoute('app_agence_liste', ['locale' => $locale]);
        }

        return $this->render('agence/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route('/agence/liste/{locale}', name: 'app_agence_liste')]
    public function liste(string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $agences = $this->entityManager->getRepository(Agence::class)->findAll();

        return $this->render('agence/liste.html.twig', [
            'agences' => $agences,
            'locale' => $locale
        ]);
    }

    #[Route('/agence/modifier/{id}/{locale}', name: 'app_agence_modifier')]
    public function modifier(Request $request, int $id, string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $agence = $this->entityManager->getRepository(Agence::class)->find($id);

        if (!$agence) {
            throw $this->createNotFoundException('agence.not_found');
        }

        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'agence.modif_success');
            return $this->redirectToRoute('app_agence_liste', ['locale' => $locale]);
        }

        return $this->render('agence/modifier.html.twig', [
            'form' => $form->createView(),
            'agence' => $agence,
            'locale' => $locale
        ]);
    }

    #[Route('/agence/supprimer/{id}/{locale}', name: 'app_agence_supprimer')]
    public function supprimer(int $id, string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $agence = $this->entityManager->getRepository(Agence::class)->find($id);

        if (!$agence) {
            throw $this->createNotFoundException('agence.not_found');
        }

        $agence->setSuppr(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'agence.suppr_success');
        return $this->redirectToRoute('app_agence_liste', ['locale' => $locale]);
    }
}
