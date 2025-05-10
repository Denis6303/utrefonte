<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\DeviseSimulation;
use App\Entity\DeviseSimulationType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /**
     * @Route("/devise-simulation", name="devise_simulation_index")
     */
    public function index(): Response
    {
        $simulations = $this->entityManager->getRepository(DeviseSimulation::class)->findAll();
        return $this->render('devise_simulation/index.html.twig', [
            'simulations' => $simulations,
        ]);
    }

    /**
     * @Route("/devise-simulation/new", name="devise_simulation_new")
     */
    public function new(Request $request): Response
    {
        $simulation = new DeviseSimulation();
        $form = $this->createForm(DeviseSimulationType::class, $simulation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($simulation);
            $this->entityManager->flush();

            return $this->redirectToRoute('devise_simulation_index');
        }

        return $this->render('devise_simulation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/devise-simulation/{id}", name="devise_simulation_show")
     */
    public function show(DeviseSimulation $simulation): Response
    {
        return $this->render('devise_simulation/show.html.twig', [
            'simulation' => $simulation,
        ]);
    }

    /**
     * @Route("/devise-simulation/{id}/edit", name="devise_simulation_edit")
     */
    public function edit(Request $request, DeviseSimulation $simulation): Response
    {
        $form = $this->createForm(DeviseSimulationType::class, $simulation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('devise_simulation_index');
        }

        return $this->render('devise_simulation/edit.html.twig', [
            'simulation' => $simulation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/devise-simulation/{id}/delete", name="devise_simulation_delete")
     */
    public function delete(Request $request, DeviseSimulation $simulation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $simulation->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($simulation);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('devise_simulation_index');
    }
}
