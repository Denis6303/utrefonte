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
use Symfony\Component\HttpFoundation\Response;
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
        $this->requestStack->getCurrentRequest()Stack = $requestStack;
        $this->translator = $translator;
    }

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
        $this->requestStack->getCurrentRequest()Stack = $requestStack;
        $this->translator = $translator;
    } {

    public function __construct() {
        //$this->Utils =  $this->utb_admin.utils;
    }

    /**
     *  Methode qui s'occupe de l'ajout d'un  Sondage
     * 
     * $unsondage : Un objet de la classe Sondage
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un Sondage (ajoutSondage.html.twig)
     * 
     */
    public function ajoutSondageAction(): Response(string $locale, string $type): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutSondageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //        
        $this->requestStack->getCurrentRequest()->setLocale($locale);


        $unsondage = new Sondage();
        $unsondage->setTranslatableLocale($locale);
        //$em->refresh($unsondage);
        //$unsondage->setTranslatableLocale($locale);
        $user = $this->security->getToken()->getUser()->getId();

        //Donnée de base et non nulles à renseigner
        //$unsondage->setTypeSondage(0);

        $form = $this->createForm($this->createForm(SondageType::class), $unsondage);

        $request = $request;
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat( 4, $locale, 0, null);

        $opinion = array();
        if ($request->isMethod('POST')) {


            $form->handleRequest($request);
            $unsondage = $form->getData();
            $unsondage->setQuestionAjoutPar($user);
            //$unsondage->setEtatSondage(0);
            //$unsondage->setNatureSondage(1);
            $unsondage->setQuestionDateAjout(new \DateTime);
            $unsondage->setActif(0);

            $opinion = $unsondage->getSondageOpinions();

            foreach ($opinion as $uneopinion) {
                $uneopinion->setNbReponse(0);
                $uneopinion->setSondage($unsondage);

                $em->persist($uneopinion);
            }
            // $unsondage->addSondageOpinion($opinion);

            $em->persist($unsondage);


            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');
            return $this->redirect($this->generateUrl('utb_admin_listesondage', ['locale' => $locale,]));
        }

        if ($type == 1) {
            return $this->render('utbAdminBundle/Sondage/ajoutDossier.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
            ));
        } else {
            return $this->render('utbAdminBundle/Sondage/ajoutSondage.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
            ));
        }
    }

    /**
     *  Methode qui s'occupe de l'ajout d'un  Sondage
     * 
     * $unsondage : Un objet de la classe Sondage
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un Sondage (ajoutSondage.html.twig)
     * 
     */
    public function ajoutOpinionAction(): Response(string $locale, $idsondage): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutOpinionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //        
        $this->requestStack->getCurrentRequest()->setLocale($locale);


        $unsondageop = new SondageOpinion();
        $unsondageop->setTranslatableLocale($locale);
        //$em->refresh($unsondageop);
        $user = $this->security->getToken()->getUser()->getId();

        //Donnée de base et non nulles à renseigner
        //$unsondage->setTypeSondage(0);
        $unsondage = $em->getRepository("admin/Sondage")->find($idsondage);

        $form = $this->createForm($this->createForm(SondageOpinionType::class), $unsondageop);

        $request = $request;
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale,0,null);


        if ($request->isMethod('POST')) {


            $form->handleRequest($request);
            $unsondageop = $form->getData();
            //$unsondage->setEtatSondage(0);
            //$unsondage->setNatureSondage(1);
            $unsondageop->setNbReponse(0);
            $unsondageop->setSondage($unsondage);


            // $unsondage->addSondageOpinion($opinion);

            $em->persist($unsondageop);
            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');
            return $this->redirect($this->generateUrl('utb_admin_listeopinion', ['locale' => $locale, 'id' => $idsondage,]));
        }

        return $this->render('utbAdminBundle/Sondage/ajoutOpinion.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat, 'idsondage' => $idsondage,
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
     * @param <integer> $id     Identifiant  du sondage
     * @param <string>  $locale, Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutLangueSondage.html.twig
     *  
     */
    public function ajoutLangueSondageAction(): Response(string $locale, int $id): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutLangueSondageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unsondage = $em->getRepository("admin/Sondage")->find($id);
        $unsondage->setTranslatableLocale($locale);
        $em->refresh($unsondage);
        // Change la locale  
        $form = $this->createForm($this->createForm(SondageType::class), $unsondage);
        $unsondage->setQuestionDateAjout(new \DateTime);
        $request = $request;
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale,0,null);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $em->persist($unsondage);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Sondage ajouté avec succès');

            return $this->redirect($this->generateUrl('utb_admin_listesondage', ['locale' => $locale,
            ]));
        }

        return $this->render('utbAdminBundle/Sondage/ajoutLangueSondage.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'listestat' => $listestat,
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
     * @param <integer> $id     Identifiant  du sondageOpinion
     * @param <string>  $locale, Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutLangueSondageOpinion.html.twig
     *  
     */
    public function ajoutLangueOpinionAction(): Response(string $locale, int $id, $idsondage): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutLangueOpinionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unsondageOpinion = $em->getRepository("admin/SondageOpinion")->find($id);
        $unsondageOpinion->setTranslatableLocale($locale);
        $em->refresh($unsondageOpinion);
        // Change la locale  
        $form = $this->createForm($this->createForm(SondageOpinionType::class), $unsondageOpinion);

        $request = $request;
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat( 4, $locale,0,null);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $em->persist($unsondageOpinion);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'SondageOpinion ajouté avec succès');

            return $this->redirect($this->generateUrl('utb_admin_listeopinion', ['locale' => $locale, 'id' => $idsondage,
            ]));
        }

        return $this->render('utbAdminBundle/Sondage/ajoutLangueOpinion.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'listestat' => $listestat, 'idsondage' => $idsondage,
        ));
    }

    /**
     *  Methode qui liste les Sondages
     * 
     * $listesondage / Un objet de la classe Sondage
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listesondageAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        //code qui verifie si l'utilisateur courant a acces a cette action

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listesondageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        //$repertoire = $this-> getDoctrine()->getManager()->getRepository();       
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listesondage = $em->getRepository("admin/Sondage")->findAll();
        foreach ($listesondage as $unsondage) {

            $unsondage->setTranslatableLocale($locale);
            $em->refresh($unsondage);
        }
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat( 4, $locale, 0,null);

        return $this->render('utbAdminBundle/Sondage/listeSondage.html.twig', array('listesondage' => $listesondage, 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     *  Methode qui liste les Sondages
     * 
     * $listesondage / Un objet de la classe Sondage
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listeOpinionAction(): Response(string $locale, int $id): Response {
        $em = $this->entityManager;

        //code qui verifie si l'utilisateur courant a acces a cette action

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeOpinionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        //$repertoire = $this-> getDoctrine()->getManager()->getRepository(); 
        $unsondage = $em->getRepository("utbAdminBundle/Sondage")->find($id);
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listeopinion = $em->getRepository("utbAdminBundle/SondageOpinion")->findby(array("sondage" => $unsondage));
        foreach ($listeopinion as $opinion) {

            $opinion->setTranslatableLocale($locale);
            $em->refresh($opinion);
        }
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale,0,null);

        return $this->render('utbAdminBundle/Sondage/listeOpinions.html.twig', array('listeopinion' => $listeopinion, 'locale' => $locale, 'listestat' => $listestat, 'id' => $id));
    }

    /**
     *  Methode qui soccupe de la suppression des sondages
     * 
     * $listesondage : Un objet de la classe Sondage
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *   
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id Identifiant du Sondage
     * 
     * @return <string> return le twig (listeSondage.html.twig)
     * 
     */
    public function supprsondageAction(): Response(int $id, string $locale, string $type): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprsondageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unsondage = $em->getRepository("utbAdminBundle:Sondage")->find($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        /* $unUser = $this->entityManager
          ->getRepository('App\Entity\User')
          ->findSondage($id);

          if($unUser == null){ */

        /// test pour voir si le sondage / dossier à supprimer contient des messages
        $nbremessage = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreMessageSondage($id, $locale);

        if ($nbremessage == 0) {
            $em->remove($unsondage);
            $em->flush($unsondage);
            if ($type == 1) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprdossiersuc');
                return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale]));
            } else {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprsondagesuc');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Sondage supprimé avec succès');
                return $this->redirect($this->generateUrl('utb_admin_listesondage', array(
                                    'locale' => $locale, 'listestat' => $listestat,)));
                /* ... et on redirige vers la page d'administration des categorie */
            }
            /*  }else{
              $listesondage = $this->entityManager
              ->getRepository("utbAdminBundle/Sondage")
              ->findAllByLocale($locale);
              return $this->render('utbAdminBundle/Sondage/listeSondage.html.twig', array('listesondage' => $listesondage,'locale' =>$locale,'listestat'=>$listestat, ));
              } */
        } else {

            if ($type == 1) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprdossierimp');
                return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale]));
            } else {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'suppr.sondage.impSuppression du sondage impossible, le sondage contient a des message');
                return $this->redirect($this->generateUrl('utb_admin_listesondage', array(
                                    'locale' => $locale, 'listestat' => $listestat,)));        /* ... et on redirige vers la page d'administration des categorie */
            }
        }
    }

    /**
     *  Methode qui soccupe de la suppression des sondages
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du sondage
     * @param <integer> $etat  etat =0 (desactive) | etat =1 (active)
     * 
     * @return <string> return le twig (listeSondage.html.twig)
     * 
     */
    public function gererEtatSondageAction(): Response(int $id, int $etat, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'gererEtatSondageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale,  0,null);

        // Récupération du sondage 
        $unsondage = $em->getRepository("utbAdminBundle:Sondage")->find($id);
        $unsondage->setEtatSondage($etat);

        $em->persist($unsondage);

        $em->flush();

        if ($etat == 0) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Sondage désactivé avec succès');
        } else {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Sondage activé avec succès');
        }

        return $this->redirect($this->generateUrl('utb_admin_listesondage', [
                            'locale' => $locale, 'listestat' => $listestat,]));
    }

    function gererAllSondageAction(): Response {

        $request = $this->requestStack->getCurrentRequest();
        $etat = $request->request->get('etat');

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;


        $checkAcces = $AccessControl->verifAcces($em, 'gererSondageAction', $this->container->get);
        if (!$checkAcces) {
            //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            //return $this->redirect ( $this->generateUrl('utb_admin_accueil', ['locale' =>$locale]));
            return new Response(json_encode(array("result" => "accesdenied")));
        }

        $sondageIds = $request->request->get('sondageIds');

        $lessondage = $em->getRepository("admin/Sondage")->findAll();

        foreach ($lessondage as $lesondage) {

            $lesondage->setActif(0);
            $em->persist($lesondage);
        }
        $em->flush();

        $sondageIds = explode("|", $sondageIds);
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($sondageIds as $key => $value) {
            if (!empty($value)) {
                $unsondage = $em->getRepository("utbAdminBundle/Sondage")->find($value);
                //Désactivation
                $unsondage->setActif($etat);
                $em->persist($unsondage);
            }
        }

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui s'occupe de la modification d'un sondage
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du sondage
     * 
     * @return <string> return le twig (modifSondage.html.twig)
     * 
     */
    public function modifierSondageAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierSondageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        // Récupération du sondage 
        $unsondage = $em->getRepository("utbAdminBundle:Sondage")->find($id);
        $unsondage->setTranslatableLocale($locale);
        $em->refresh($unsondage);

        $user = $this->security->getToken()->getUser()->getId();
        $lesopignions = array();
        foreach ($unsondage->getSondageOpinions() as $uneopinion) {
            $lesopignions[] = $uneopinion;
            $uneopinion->setTranslatableLocale($locale);
            $em->refresh($uneopinion);
        }
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité sondage 
        $form = $this->createForm($this->createForm(SondageType::class), $unsondage);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale,0, null);


        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
            /* Si le formulaire est valide, on valide et on redirige vers la liste des sondages */
            $unsondage->setQuestionDateAjout(new \DateTime);
            foreach ($unsondage->getSondageOpinions() as $uneopinion) {
                foreach ($lesopignions as $key => $toDel) {
                    if ($toDel->getId() === $uneopinion->getId()) {
                        unset($lesopignions[$key]);
                    }
                }
            }

            foreach ($lesopignions as $uneopinion) {
                // supprime la « Task » du Tag
                $uneopinion->getSondage()->removeElement($uneopinion);

                // si c'était une relation ManyToOne, vous pourriez supprimer la
                // relation comme ceci
                // $tag->setTask(null);

                $em->persist($uneopinion);

                // si vous souhaitiez supprimer totalement le Tag, vous pourriez
                // aussi faire comme cela
                // $em->remove($tag);
            }

            $em->persist($unsondage);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Sondage modifié avec succès');

            return $this->redirect($this->generateUrl("utb_admin_listesondage"));
        }
        return $this->render('utbAdminBundle/Sondage/modifSondage.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     *  Methode qui s'occupe de la modification d'une opinion
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du sondage
     * 
     * @return <string> return le twig (modifSondage.html.twig)
     * 
     */
    public function modifierOpinionAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierOpinionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        // Récupération du sondage 
        $uneopinion = $em->getRepository("admin/SondageOpinion")->find($id);
        $user = $this->security->getToken()->getUser()->getId();

        $uneopinion->setTranslatableLocale($locale);
        $em->refresh($uneopinion);
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité sondage 
        $form = $this->createForm($this->createForm(SondageOpinionType::class), $uneopinion);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);


        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
            /* Si le formulaire est valide, on valide et on redirige vers la liste des sondages */
            //$unsondage->setQuestionDateAjout(new \DateTime);  



            $em->persist($uneopinion);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Sondage modifié avec succès');

            return $this->redirect($this->generateUrl("utb_admin_listeopinion"));
        }
        return $this->render('utbAdminBundle/Sondage/modifOpinion.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     *  Methode qui s'occupe de la suppression d'un sondage
     * 
     *  Methode utilisant Ajax
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du sondage
     * 
     * @return <string> return le twig (listeObjet.html.twig)
     * 
     */
    function corbeilleSondageAction(): Response {
        $locale="fr";
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'corbeilleSondageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $sondageIds = $request->request->get('sondageIds');
        $sondageIds = explode("|", $sondageIds);
        foreach ($sondageIds as $key => $value) {
            if (!empty($value)) {
                $unsondage = $em->getRepository("utbAdminBundle/Sondage")->find($value);
                foreach ($unsondage->getSondageOpinions() as $lopinion) {
                    $em->remove($lopinion);
                }
                $em->remove($unsondage);
            }
            $em->flush();
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui s'occupe de la suppression d'une opinion
     * 
     *  Methode utilisant Ajax
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du sondage
     * 
     * @return <string> return le twig (listeObjet.html.twig)
     * 
     */
    function corbeilleOpinionAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'corbeilleSondageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $opinionIds = $request->request->get('opinionIds');
        $opinionIds = explode("|", $opinionIds);
        foreach ($opinionIds as $key => $value) {
            if (!empty($value)) {
                $uneopinion = $em->getRepository("utbAdminBundle/SondageOpinion")->find($value);
                /* foreach ($unsondage->getSondageOpinions() as $lopinion){                    
                  $em->remove($lopinion);
                  } */
                $em->remove($uneopinion);
            }
            $em->flush();
        }
        return new Response(json_encode(array("result" => "success")));
    }

    function resultatAction(): Response {
        $em = $this->entityManager;

        $unsondage = $em->getRepository("utbAdminBundle/Sondage")->findby(array("actif" => 1));
        //var_dump($unsondage);
        $listeopinion = $em->getRepository("utbAdminBundle:SondageOpinion")->findby(array("sondage" => $unsondage));

        return $this->render('utbSiteBundle/Site/resultat.html.twig', array('sondage' => $unsondage, 'listeopinion' => $listeopinion));
    }

}