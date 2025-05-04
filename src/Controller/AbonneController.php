<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Abonne;
use App\Entity\Compte;
use App\Entity\SousAbonneType;
use App\Entity\AbonneType;
use App\Entity\ModifPwdType;
use App\Entity\MessageClient;
use App\Entity\ModifFicheAbonneType;
use App\Entity\ModAbonneType;
use App\Entity\Parametrage;
use App\Entity\Type\RegistrationFormType;
use App\Entity\Fonds;
use App\Entity\HistoriqueConnexion;
use App\Entity\ProfilClient;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * AbonneController 
 * 
 * Le controleur gerant la pluspart des fonctionnalités du module "Gestion des Abonné"
 */
#[Route('/{_locale}')]
class AbonneController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {}

    #[Route('/abonne/edit', name: 'abonne_edit')]
    #[IsGranted('ROLE_EDIT_ABONNE', message: 'Accès refusé à cette section.')]
    public function edit(Request $request): Response
    {
        $abonne = new Abonne();
        $form = $this->createForm(AbonneType::class, $abonne);
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($abonne);
            $this->entityManager->flush();
            
            $this->addFlash('success', $this->translator->trans('abonne.created.success'));
            return $this->redirectToRoute('abonne_list');
        }

        return $this->render('abonne/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
} 