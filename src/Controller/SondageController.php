<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Sondage;
use App\Entity\SondageOpinion;
use App\Entity\SondageType;
use App\Entity\SondageOpinionType;
use App\Entity\DossierType;
use App\Entity\AdresseIp;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;


/**
 * 
 * SondageController pour la gestion des sondages
 * 
 * Cette methode permet de verifier le droit de l'utilisateur avant d'effectuer une action dans une methode
 * 
 * 
 * $em = $this->entityManager;
 * $AccessControl = $this->utb_admin.AccessControl;
 * $checkAcces = $AccessControl->verifAcces($em, 'ajoutsondageAction', $this->container->get);
 * 
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class SondageController extends AbstractController
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
     *  Methode qui s'occupe de l'ajout d'un  Sondage
     * 
     * $unsondage : Un objet de la classe Sondage
     *  
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param string $type Type de sondage
     * 
     * @return Response Le formulaire d'ajout d'un Sondage (ajoutSondage.html.twig)
     */
    #[Route(
        path: '/admin/sondage/ajout/{locale}/{type}',
        name: 'app_sondage_ajout',
        requirements: [
            'locale' => '[a-z]{2}',
            'type' => '\d+'
        ]
    )]
    public function ajoutSondage(Request $request, string $locale, string $type): Response
    {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutSondage', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unsondage = new Sondage();
        $unsondage->setTranslatableLocale($locale);
        $user = $this->getUser()->getId();

        $form = $this->createForm(SondageType::class, $unsondage);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        $opinion = array();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unsondage = $form->getData();
            $unsondage->setQuestionAjoutPar($user);
            $unsondage->setQuestionDateAjout(new \DateTime);
            $unsondage->setActif(0);

            $opinion = $unsondage->getSondageOpinions();

            foreach ($opinion as $uneopinion) {
                $uneopinion->setNbReponse(0);
                $uneopinion->setSondage($unsondage);
                $em->persist($uneopinion);
            }

            $em->persist($unsondage);
            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');
            return $this->redirect($this->generateUrl('app_admin_listesondage', ['locale' => $locale]));
        }

        if ($type == 1) {
            return $this->render('utbAdminBundle/Sondage/ajoutDossier.html.twig', array(
                'form' => $form->createView(), 
                'locale' => $locale, 
                'listestat' => $listestat,
            ));
        } else {
            return $this->render('utbAdminBundle/Sondage/ajoutSondage.html.twig', array(
                'form' => $form->createView(), 
                'locale' => $locale, 
                'listestat' => $listestat,
            ));
        }
    }

    /**
     *  Methode qui s'occupe de l'ajout d'un  Sondage
     * 
     * $unsondage : Un objet de la classe Sondage
     *  
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $idsondage Identifiant du sondage
     * 
     * @return Response Le formulaire d'ajout d'un Sondage (ajoutSondage.html.twig)
     */
    #[Route(
        path: '/admin/sondage/ajout-opinion/{locale}/{idsondage}',
        name: 'app_sondage_ajout_opinion',
        requirements: [
            'locale' => '[a-z]{2}',
            'idsondage' => '\d+'
        ]
    )]
    public function ajoutOpinion(Request $request, string $locale, int $idsondage): Response
    {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutOpinion', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unsondageop = new SondageOpinion();
        $unsondageop->setTranslatableLocale($locale);
        $user = $this->getUser()->getId();

        $unsondage = $em->getRepository("App\Entity\Sondage")->find($idsondage);

        $form = $this->createForm(SondageOpinionType::class, $unsondageop);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unsondageop = $form->getData();
            $unsondageop->setNbReponse(0);
            $unsondageop->setSondage($unsondage);

            $em->persist($unsondageop);
            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');
            return $this->redirect($this->generateUrl('app_admin_listeopinion', ['locale' => $locale, 'id' => $idsondage]));
        }

        return $this->render('utbAdminBundle/Sondage/ajoutOpinion.html.twig', array(
            'form' => $form->createView(), 
            'locale' => $locale, 
            'listestat' => $listestat, 
        ));
    }

    /**
     * Methode permettant d'ajouter un sondage dans une autre langue(une traduction) - Backoffice
     * 
     * Abandonnée par la suite
     * 
     * @deprecated since version 1.0
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat/ Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $unsondage: Instance de la classe Sondage a modifier
     * 
     * @param int $id Identifiant du sondage
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return Response Le template ajoutLangueSondage.html.twig
     */
    #[Route(
        path: '/admin/sondage/ajout-langue/{id}/{locale}',
        name: 'app_sondage_ajout_langue',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function ajoutLangueSondage(Request $request, int $id, string $locale): Response
    {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutLangueSondage', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unsondage = $em->getRepository("App\Entity\Sondage")->find($id);
        $unsondage->setTranslatableLocale($locale);
        $em->refresh($unsondage);
        
        $form = $this->createForm(SondageType::class, $unsondage);
        $unsondage->setQuestionDateAjout(new \DateTime);
        
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $em->persist($unsondage);
            $em->flush();
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Sondage ajouté avec succès');

            return $this->redirect($this->generateUrl('app_admin_listesondage', ['locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Sondage/ajoutLangueSondage.html.twig', array(
            'form' => $form->createView(), 
            'locale' => $locale, 
            'id' => $id, 
            'listestat' => $listestat,
        ));
    }

    /**
     * Methode permettant d'ajouter un sondageOpinion dans une autre langue(une traduction) - Backoffice
     * 
     * Abandonnée par la suite
     * 
     * @deprecated since version 1.0
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat/ Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $unsondageOpinion: Instance de la classe SondageOpinion a modifier
     * 
     * @param int $id Identifiant du sondageOpinion
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $idsondage Identifiant du sondage parent
     * 
     * @return Response Le template ajoutLangueSondageOpinion.html.twig
     */
    #[Route(
        path: '/admin/sondage/ajout-langue-opinion/{id}/{locale}/{idsondage}',
        name: 'app_sondage_ajout_langue_opinion',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}',
            'idsondage' => '\d+'
        ]
    )]
    public function ajoutLangueOpinion(Request $request, int $id, string $locale, int $idsondage): Response
    {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutLangueOpinion', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unsondageOpinion = $em->getRepository("App\Entity\SondageOpinion")->find($id);
        $unsondageOpinion->setTranslatableLocale($locale);
        $em->refresh($unsondageOpinion);
        
        $form = $this->createForm(SondageOpinionType::class, $unsondageOpinion);
        
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $em->persist($unsondageOpinion);
            $em->flush();
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'SondageOpinion ajouté avec succès');

            return $this->redirect($this->generateUrl('app_admin_listeopinion', ['locale' => $locale, 'id' => $idsondage]));
        }

        return $this->render('utbAdminBundle/Sondage/ajoutLangueOpinion.html.twig', array(
            'form' => $form->createView(), 
            'locale' => $locale, 
            'id' => $id, 
            'listestat' => $listestat, 
            'idsondage' => $idsondage,
        ));
    }

    /**
     *  Methode qui liste les Sondages
     * 
     * $listesondage / Un objet de la classe Sondage
     * 
     * $listestat : Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return Response Le template listeSondage.html.twig
     */
    #[Route(
        path: '/admin/sondage/liste/{locale}',
        name: 'app_sondage_liste',
        requirements: [
            'locale' => '[a-z]{2}'
        ]
    )]
    public function listeSondage(Request $request, string $locale): Response
    {
        $em = $this->entityManager;
        //code qui verifie si l'utilisateur courant a acces a cette action
        $checkAcces = $this->accessControl->verifAcces($em, 'listeSondage', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listesondage = $em->getRepository("App\Entity\Sondage")->findAll();
        foreach ($listesondage as $unsondage) {
            $unsondage->setTranslatableLocale($locale);
            $em->refresh($unsondage);
        }
        
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        return $this->render('utbAdminBundle/Sondage/listeSondage.html.twig', array(
            'listesondage' => $listesondage, 
            'locale' => $locale, 
            'listestat' => $listestat,
        ));
    }

    /**
     *  Methode qui liste les Opinions d'un Sondage
     * 
     * $listeopinion / Un objet de la classe SondageOpinion
     * 
     * $listestat : Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $id Identifiant du sondage
     * 
     * @return Response Le template listeOpinions.html.twig
     */
    #[Route(
        path: '/admin/sondage/liste-opinion/{locale}/{id}',
        name: 'app_sondage_liste_opinion',
        requirements: [
            'locale' => '[a-z]{2}',
            'id' => '\d+'
        ]
    )]
    public function listeOpinion(Request $request, string $locale, int $id): Response
    {
        $em = $this->entityManager;
        //code qui verifie si l'utilisateur courant a acces a cette action
        $checkAcces = $this->accessControl->verifAcces($em, 'listeOpinion', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $unsondage = $em->getRepository("App\Entity\Sondage")->find($id);
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listeopinion = $em->getRepository("App\Entity\SondageOpinion")->findby(array("sondage" => $unsondage));
        foreach ($listeopinion as $opinion) {
            $opinion->setTranslatableLocale($locale);
            $em->refresh($opinion);
        }
        
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        return $this->render('utbAdminBundle/Sondage/listeOpinions.html.twig', array(
            'listeopinion' => $listeopinion, 
            'locale' => $locale, 
            'listestat' => $listestat, 
            'id' => $id
        ));
    }

    /**
     * Methode qui s'occupe de la suppression d'un sondage
     * 
     * @param int $id L'identifiant du sondage à supprimer
     * @param string $locale La locale
     * @return Response Une réponse JSON
     */
    #[Route(
        path: '/admin/sondage/supprimer/{id}/{locale}',
        name: 'app_sondage_supprimer',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function supprimerSondage(Request $request, int $id, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'supprimerSondage', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accesdenied")));
        }

        $unsondage = $em->getRepository("App\Entity\Sondage")->find($id);
        $em->remove($unsondage);
        $em->flush();

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui s'occupe de la gestion de l'état des sondages
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $id Identifiant du sondage
     * @param int $etat Etat =0 (desactive) | etat =1 (active)
     * 
     * @return Response Le template listeSondage.html.twig
     */
    #[Route(
        path: '/admin/sondage/gerer-etat/{id}/{etat}/{locale}',
        name: 'app_sondage_gerer_etat',
        requirements: [
            'id' => '\d+',
            'etat' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function gererEtatSondage(Request $request, int $id, int $etat, string $locale): Response
    {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'gererEtatSondage', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        // Récupération du sondage 
        $unsondage = $em->getRepository("App\Entity\Sondage")->find($id);
        $unsondage->setEtatSondage($etat);

        $em->persist($unsondage);
        $em->flush();

        if ($etat == 0) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Sondage désactivé avec succès');
        } else {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Sondage activé avec succès');
        }

        return $this->redirect($this->generateUrl('app_admin_listesondage', [
            'locale' => $locale, 
            'listestat' => $listestat,
        ]));
    }

    /**
     *  Methode qui s'occupe de la gestion de l'état de tous les sondages
     * 
     * @return Response Une réponse JSON
     */
    #[Route(
        path: '/admin/sondage/gerer-tous',
        name: 'app_sondage_gerer_tous',
        methods: ['POST']
    )]
    public function gererAllSondage(Request $request): Response
    {
        $etat = $request->request->get('etat');

        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'gererAllSondage', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accesdenied")));
        }

        $sondageIds = $request->request->get('sondageIds');

        $lessondage = $em->getRepository("App\Entity\Sondage")->findAll();

        foreach ($lessondage as $lesondage) {
            $lesondage->setActif(0);
            $em->persist($lesondage);
        }
        $em->flush();

        $sondageIds = explode("|", $sondageIds);
        $user = $this->getUser()->getId();
        
        //boucle sur les ids articles
        foreach ($sondageIds as $key => $value) {
            if (!empty($value)) {
                $unsondage = $em->getRepository("App\Entity\Sondage")->find($value);
                //Désactivation
                $unsondage->setActif($etat);
                $em->persist($unsondage);
            }
        }

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui s'occupe de la gestion de l'état de toutes les opinions
     * 
     * @return Response Une réponse JSON
     */
    #[Route(
        path: '/admin/sondage/gerer-toutes-opinions',
        name: 'app_sondage_gerer_toutes_opinions',
        methods: ['POST']
    )]
    public function gererAllOpinion(Request $request): Response
    {
        $etat = $request->request->get('etat');

        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'gererAllOpinion', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accesdenied")));
        }

        $opinionIds = $request->request->get('opinionIds');

        $lesopinions = $em->getRepository("App\Entity\SondageOpinion")->findAll();

        foreach ($lesopinions as $lopinion) {
            $lopinion->setActif(0);
            $em->persist($lopinion);
        }
        $em->flush();

        $opinionIds = explode("|", $opinionIds);
        $user = $this->getUser()->getId();
        
        //boucle sur les ids articles
        foreach ($opinionIds as $key => $value) {
            if (!empty($value)) {
                $uneopinion = $em->getRepository("App\Entity\SondageOpinion")->find($value);
                //Désactivation
                $uneopinion->setActif($etat);
                $em->persist($uneopinion);
            }
        }

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Methode qui s'occupe de la modification d'un sondage
     * 
     * @param int $id L'identifiant du sondage à modifier
     * @param string $locale La locale
     * @return Response Le template modifSondage.html.twig
     */
    #[Route(
        path: '/admin/sondage/modifier/{id}/{locale}',
        name: 'app_sondage_modifier',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function modifierSondage(Request $request, int $id, string $locale): Response
    {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'modifierSondage', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        // Récupération du sondage 
        $unsondage = $em->getRepository("App\Entity\Sondage")->find($id);
        $unsondage->setTranslatableLocale($locale);
        $em->refresh($unsondage);

        $user = $this->getUser()->getId();
        $lesopignions = array();
        foreach ($unsondage->getSondageOpinions() as $uneopinion) {
            $lesopignions[] = $uneopinion;
            $uneopinion->setTranslatableLocale($locale);
            $em->refresh($uneopinion);
        }
        
        // Création du formulaire
        $form = $this->createForm(SondageType::class, $unsondage);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        // On traite les données passées en méthode POST 
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            $unsondage->setQuestionDateAjout(new \DateTime);
            foreach ($unsondage->getSondageOpinions() as $uneopinion) {
                foreach ($lesopignions as $key => $toDel) {
                    if ($toDel->getId() === $uneopinion->getId()) {
                        unset($lesopignions[$key]);
                    }
                }
            }

            foreach ($lesopignions as $uneopinion) {
                $uneopinion->getSondage()->removeElement($uneopinion);
                $em->persist($uneopinion);
            }

            $em->persist($unsondage);
            $em->flush();
            
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Sondage modifié avec succès');
            return $this->redirect($this->generateUrl('app_admin_listesondage', ['locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Sondage/modifSondage.html.twig', [
            'form' => $form->createView(),
            'id' => $id,
            'locale' => $locale,
            'listestat' => $listestat
        ]);
    }

    /**
     * Methode qui s'occupe de la modification d'une opinion
     * 
     * @param int $id L'identifiant de l'opinion à modifier
     * @param string $locale La locale
     * @return Response Le template modifOpinion.html.twig
     */
    #[Route(
        path: '/admin/sondage/modifier-opinion/{id}/{locale}',
        name: 'app_sondage_modifier_opinion',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function modifierOpinion(Request $request, int $id, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'modifierOpinion', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        // Récupération de l'opinion
        $uneopinion = $em->getRepository("App\Entity\SondageOpinion")->find($id);
        $uneopinion->setTranslatableLocale($locale);
        $em->refresh($uneopinion);

        // Création du formulaire
        $form = $this->createForm(SondageOpinionType::class, $uneopinion);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            $em->persist($uneopinion);
            $em->flush();
            
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Opinion modifiée avec succès');
            return $this->redirect($this->generateUrl('app_admin_listeopinion', ['locale' => $locale, 'id' => $uneopinion->getSondage()->getId()]));
        }

        return $this->render('utbAdminBundle/Sondage/modifOpinion.html.twig', [
            'form' => $form->createView(),
            'id' => $id,
            'locale' => $locale,
            'listestat' => $listestat
        ]);
    }

    /**
     * Methode qui s'occupe de la suppression en masse des sondages
     * 
     * @return Response Une réponse JSON
     */
    #[Route(
        path: '/admin/sondage/corbeille',
        name: 'app_sondage_corbeille',
        methods: ['POST']
    )]
    public function corbeilleSondage(Request $request): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'corbeilleSondage', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accesdenied")));
        }

        $sondageIds = $request->request->get('sondageIds');
        $sondageIds = explode("|", $sondageIds);
        
        foreach ($sondageIds as $key => $value) {
            if (!empty($value)) {
                $unsondage = $em->getRepository("App\Entity\Sondage")->find($value);
                foreach ($unsondage->getSondageOpinions() as $lopinion) {
                    $em->remove($lopinion);
                }
                $em->remove($unsondage);
            }
        }
        $em->flush();
        
        return new Response(json_encode(array("result" => "success")));
    }

    function resultatAction(): Response {
        $em = $this->entityManager;

        $unsondage = $em->getRepository("utbAdminBundle/Sondage")->findby(array("actif" => 1));
        //var_dump($unsondage);
        $listeopinion = $em->getRepository("utbAdminBundle:SondageOpinion")->findby(array("sondage" => $unsondage));

        return $this->render('utbSiteBundle/Site/resultat.html.twig', array('sondage' => $unsondage, 'listeopinion' => $listeopinion));
    }

    /**
     *  Methode qui s'occupe de la gestion de l'état d'une opinion
     * 
     * @param int $id L'identifiant de l'opinion
     * @param int $etat Le nouvel état de l'opinion
     * @param string $locale La locale
     * @return Response Une réponse JSON
     */
    #[Route(
        path: '/admin/sondage/gerer-etat-opinion/{id}/{etat}/{locale}',
        name: 'app_sondage_gerer_etat_opinion',
        requirements: [
            'id' => '\d+',
            'etat' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function gererEtatOpinion(Request $request, int $id, int $etat, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'gererEtatOpinion', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accesdenied")));
        }

        $uneopinion = $em->getRepository("App\Entity\SondageOpinion")->find($id);
        $uneopinion->setActif($etat);
        $em->persist($uneopinion);
        $em->flush();

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Methode qui s'occupe de la suppression en masse des opinions
     * 
     * @return Response Une réponse JSON
     */
    #[Route(
        path: '/admin/sondage/corbeille-opinions',
        name: 'app_sondage_corbeille_opinions',
        methods: ['POST']
    )]
    public function corbeilleOpinion(Request $request): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'corbeilleOpinion', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accesdenied")));
        }

        $opinionIds = $request->request->get('opinionIds');
        $opinionIds = explode("|", $opinionIds);
        
        foreach ($opinionIds as $key => $value) {
            if (!empty($value)) {
                $uneopinion = $em->getRepository("App\Entity\SondageOpinion")->find($value);
                $em->remove($uneopinion);
            }
        }
        $em->flush();

        return new Response(json_encode(array("result" => "success")));
    }

}