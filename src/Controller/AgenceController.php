<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Agence;
use App\Entity\AgenceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/{_locale}/agence/ajout', name: 'agence_ajout')]
    public function ajoutAgence(Request $request): Response
    {
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($agence);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('agence.created_success'));
            return $this->redirectToRoute('agence_list');
        }

        return $this->render('agence/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{_locale}/agence/liste', name: 'agence_list')]
    public function listeAgence(): Response
    {
        $agences = $this->entityManager->getRepository(Agence::class)->findAll();

        return $this->render('agence/liste.html.twig', [
            'agences' => $agences,
        ]);
    }

    #[Route('/{_locale}/agence/{id}/supprimer', name: 'agence_delete')]
    public function supprAgence(Agence $agence): Response
    {
        $this->entityManager->remove($agence);
        $this->entityManager->flush();

        $this->addFlash('success', $this->translator->trans('agence.deleted_success'));
        return $this->redirectToRoute('agence_list');
    }

    #[Route('/{_locale}/agence/{id}/modifier', name: 'agence_edit')]
    public function modifierAgence(Request $request, Agence $agence): Response
    {
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('agence.updated_success'));
            return $this->redirectToRoute('agence_list');
        }

        return $this->render('agence/modifier.html.twig', [
            'form' => $form->createView(),
            'agence' => $agence,
        ]);
    }
}
