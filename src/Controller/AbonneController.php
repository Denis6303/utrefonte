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
use PHPExcel;
use PHPExcel_IOFactory;

/**
 * AbonneController 
 * 
 * Le controleur gerant la pluspart des fonctionnalités du module "Gestion des Abonné"
 */
class AbonneController extends AbstractController
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

    #[Route("/{locale}/abonne/edit", name: "abonne_edit")]
    public function edit(string $locale, Request $request): Response
    {
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'EditAction');

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_dashboard', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $abonne = new Abonne();
        $form = $this->createForm(AbonneType::class, $abonne);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($abonne);
                $em->flush();
                $this->addFlash('success', 'Abonné créé avec succès');
                return $this->redirectToRoute('abonne_list', ['locale' => $locale]);
            }
        }

        return $this->render('abonne/edit.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }
} 