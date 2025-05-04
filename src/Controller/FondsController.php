<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Fonds;
use App\Entity\FondsType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /**
     * @Route("/fonds", name="fonds_index")
     */
    public function index(): Response
    {
        $fonds = $this->entityManager->getRepository(Fonds::class)->findAll();
        return $this->render('fonds/index.html.twig', [
            'fonds' => $fonds,
        ]);
    }

    /**
     * @Route("/fonds/new", name="fonds_new")
     */
    public function new(Request $request): Response
    {
        $fond = new Fonds();
        $form = $this->createForm(FondsType::class, $fond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($fond);
            $this->entityManager->flush();

            return $this->redirectToRoute('fonds_index');
        }

        return $this->render('fonds/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fonds/{id}", name="fonds_show")
     */
    public function show(Fonds $fond): Response
    {
        return $this->render('fonds/show.html.twig', [
            'fond' => $fond,
        ]);
    }

    /**
     * @Route("/fonds/{id}/edit", name="fonds_edit")
     */
    public function edit(Request $request, Fonds $fond): Response
    {
        $form = $this->createForm(FondsType::class, $fond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('fonds_index');
        }

        return $this->render('fonds/edit.html.twig', [
            'fond' => $fond,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fonds/{id}/delete", name="fonds_delete")
     */
    public function delete(Request $request, Fonds $fond): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fond->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($fond);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('fonds_index');
    }
} 