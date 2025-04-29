<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Media;
use App\Entity\Dimension;
use App\Entity\Cadre;
use App\Entity\CadreType;
use App\Entity\MediaCadreType;
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
 * CadreController pour la gestion des cadres
 * 
 * Cette methode permet de verifier le droit de l'utilisateur avant d'effectuer une action dans une methode
 * 
 * $em = $this->entityManager;
 * $AccessControl = $this->utb_admin.AccessControl;
 * $checkAcces = $AccessControl->verifAcces($em, 'ajoutprofilAction', $this->container->get);
 * 
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class CadreController extends AbstractController
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
     *  Methode qui s'occupe de l'ajout d'un cadre
     * 
     * $listestat : Pour afficher les statistiques generales sur le site 
     * 
     * 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un cadre(ajoutCadre.html.twig)
     * 
     */
    public function ajoutCadreAction(): Response(string $locale): Response {

        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutCadreAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $uncadre = new Cadre();

        $uncadre->setTranslatableLocale($locale);

        //recuperation de l'id 
        $user = $this->security->getToken()->getUser()->getId();

        //Donnée de base et non nulles à renseigner
        $uncadre->setCadreAjoutPar($user);
        //$uncadre->setNatureCadre(1);// 1=cadre de base, 2=cadre utilisateur
        // Gestion de l'image
        $uneimage = new Media();
        $extensions = array('jpg', 'png', 'jpeg', 'gif');
        $uneimage->extensions = $extensions;

        if ($uneimage->getUrlMedia() === null) {
            $uneimage->setTypeMedia(3);
            $uneimage->setIllustreImgMedia(1); //
            $uneimage->setNomMedia("---"); //
            $uneimage->setUrlMedia("default_.png"); //
            $uneimage->setUrlFistMedia("default_.png"); //
            $uneimage->setMediaAjoutPar($user);
            $uncadre->addMedia($uneimage);
        } else {
            $uneimage->setTypeMedia(3);
            $uneimage->setIllustreImgMedia(1); // 
            $uneimage->setMediaAjoutPar($user);
            $uncadre->addMedia($uneimage);
        }

        $uncadre->setCadreDateAjout(new \DateTime);
        $form = $this->createForm($this->createForm(CadreType::class), $uncadre);


        $request = $request;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $uncadre = $form->getData();
            if ($uncadre->getContenuCadre() === null) {
                $uncadre->setContenuCadre("---");
            }
            $uneimage->setCadre($uncadre);
            $em->persist($uneimage);
            $em->persist($uncadre);

            $em->flush();

            return $this->redirect($this->generateUrl('utb_admin_listecadre', ['locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Cadre/ajoutCadre.html.twig', array(
                    'form' => $form->createView(), 'listestat' => $listestat, 'locale' => $locale,
        ));
    }

    /**
     *  Methode qui liste les cadres
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listeCadreAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeCadreAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        //$repertoire = $this-> getDoctrine()->getManager()->getRepository();       
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $listecadre = $this->entityManager
                ->getRepository('utbAdminBundle/Cadre')
                ->findAllCadreByLocale($locale);

        //$listecadre = $em->getRepository("utbAdminBundle:Cadre")->findAll();


        return $this->render('utbAdminBundle/Cadre/listeCadre.html.twig', array('listecadre' => $listecadre, 'listestat' => $listestat, 'locale' => $locale,));
    }

    /**
     *  Methode qui s'occupe de la modification d'un cadre
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du cadre
     * 
     * @return <string> return le twig (modifCadre.html.twig)
     * 
     */
    public function modifierCadreAction(): Response(int $id, string $locale): Response {
        $unmedia = null;
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierCadreAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);
        // Récupération du cadre 
        $uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($id);

        $uncadre->setTranslatableLocale($locale);

        if ($uncadre != null) {
            $unmedia = $uncadre->getMedias();
        }
        $type = $uncadre->getTypeCadre(); //type du cadre pr organiser les champs à afficher sur le twig de modif       
        $untypecadre = $em->getRepository("utbAdminBundle:TypeCadre")->find($type);
        //$uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($id);
        //$uncadre->addMedia($unmedia->getId());
        //recuperation de l'id 
        $user = $this->security->getToken()->getUser()->getId();

        //Donnée de base et non nulles à renseigner
        $uncadre->setCadreModifPar($user);
        //$uncadre->setNatureCadre(1);// 1=cadre de base, 2=cadre utilisateur
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité cadre 
        $form = $this->createForm($this->createForm(CadreType::class), $uncadre);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
            /* Si le formulaire est valide, on valide et on redirige vers la liste des cadres */
            $uncadre->setTypeCadre($type);
            $em->persist($uncadre);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Cadre modifié avec succès');

            return $this->redirect($this->generateUrl("utb_admin_listecadre"));
        }
        return $this->render('utbAdminBundle/Cadre/modifCadre.html.twig', array(
                    'form' => $form->createView(), 'listestat' => $listestat, 'unmedia' => $unmedia, 'untypecadre' => $untypecadre, 'id' => $id, 'type' => $type, 'locale' => $locale,));
    }

    /**
     *  Methode qui s'occupe de la modification d'un cadre
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du cadre
     * 
     * @return <string> return le twig (modifCadre.html.twig)
     * 
     */
    public function modifierCadreRubriqueBanniereAction(): Response(int $id, string $locale): Response {
        // $unmedia = null;
        //code qui verifie si l'utilisateur courant a acces a cette action

        $em = $this->entityManager;

        /* $AccessControl =  $this->utb_admin.AccessControl;
          $checkAcces =  $AccessControl->verifAcces($em,'modifierCadreAction', $this->container->get );
          if(!$checkAcces){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect ( $this->generateUrl('utb_admin_accueil', ['locale' =>$locale]));
          } */
        //  

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $request = $this->requestStack->getCurrentRequest();

        $uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($id);
        $idrubrique = $request->request->get('idrubrique');
        //$unerubrique= $em->getRepository("utbAdminBundle:Rubrique")->find($idrubrique);       
        //$uncadre->setTranslatableLocale($locale);

        $user = $this->security->getToken()->getUser()->getId();
        $em->persist($uncadre);


        //Donnée de base et non nulles à renseigner
        $uncadre->setCadreModifPar($user);
        $uncadre->setRubPointer($idrubrique);
        //$uncadre->setNatureCadre(1);// 1=cadre de base, 2=cadre utilisateur
        //$request = $this->requestStack->getCurrentRequest();      
        // On traite les données passées en méthode POST 
        //$uncadre->addRubrique($unerubrique);


        $em->flush();
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Cadre modifié avec succès');

        return $this->redirect($this->generateUrl("utb_admin_accueilbanniere"));
    }

    /**
     *  Methode qui s'occupe de la modification d'un cadre
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du cadre
     * 
     * @return <string> return le twig (modifCadre.html.twig)
     * 
     */
    public function modifierCadreArticleAction(): Response(int $id, string $locale): Response {
        // $unmedia = null;
        //code qui verifie si l'utilisateur courant a acces a cette action        
        $em = $this->entityManager;
        /* $AccessControl =  $this->utb_admin.AccessControl;
          $checkAcces =  $AccessControl->verifAcces($em,'modifierCadreAction', $this->container->get );
          if(!$checkAcces){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect ( $this->generateUrl('utb_admin_accueil', ['locale' =>$locale]));
          } */
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $request = $this->requestStack->getCurrentRequest();

        $uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($id);
        $idarticle = $request->request->get('larticle');
        //$unerubrique= $em->getRepository("utbAdminBundle:Rubrique")->find($idrubrique);       
        //$uncadre->setTranslatableLocale($locale);
        $user = $this->security->getToken()->getUser()->getId();

        //Donnée de base et non nulles à renseigner
        $uncadre->setCadreModifPar($user);
        $uncadre->setArticlePointer($idarticle);
        //$uncadre->setNatureCadre(1);// 1=cadre de base, 2=cadre utilisateur
        $em->persist($uncadre);
        //$request = $this->requestStack->getCurrentRequest();      
        // On traite les données passées en méthode POST        
        //$uncadre->addRubrique($unerubrique);
        $em->flush();
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Cadre modifié avec succès');

        return $this->redirect($this->generateUrl("utb_admin_accueilbanniere"));
    }

    /**
     * Methode permettant de supprimer definitivement des cadres selectionnes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $usersIds: Tableau regoupants les Ids des instances de la classe Cadre selectionnes
     * 
     * $unuser: Instance de la classe Cadre a supprimer definitivement
     * 
     * @return <json> return etat du traitement effectue
     *  
     */
    function supprAllCadresAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprAllCadresAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $cadresIds = $request->request->get('ds');
        $cadresIds = explode("|", $cadresIds);

        foreach ($cadresIds as $key => $value) {

            if (!empty($value)) {
                $uncadre = $em->getRepository("admin/Cadre")->find($value);

                //suppression des medias liés
                $medias = $uncadre->getMedias();

                foreach ($medias as $key => $unmedia) {
                    if ($unmedia->getUrlMedia() != "default_.png") {
                        $unmedia->removeUpload(__DIR__ . '/../../../../web/' . "upload/articles/" . $unmedia->getUrlMedia());
                        $em->remove($unmedia);
                    } else {
                        $em->remove($unmedia);
                    }
                }
                // Enfin on supprime l'article...
                $em->remove($uncadre);
                // return new Response( json_encode(array("result"=>"erreurstatut")));  
            }
        }

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    function gererAllCadresAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'gererAllCadresAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $cadreIds = $request->request->get('cadreIds');

        $etat = $request->request->get('etat');

        $cadreIds = explode("|", $cadreIds);

        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($cadreIds as $key => $value) {
            if (!empty($value)) {
                $uncadre = $em->getRepository("utbAdminBundle/Cadre")->find($value);
                if ($uncadre != null) {
                    //Activation|Désactivation
                    $uncadre->setEtatCadre($etat);
                    $em->persist($uncadre);
                    $em->flush();
                }
            }
        }

        //$em->flush();   
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Methode gerant la modification  de l'image à une cadre 
     *  
     * Les variables
     *
     * $unecadre :   Array d'objet pour avoir la cadre
     * 
     * $unmedia :       Objet de la classe Media
     * 
     * $extensions :     Tableau des extensions acceptées
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la cadre
     * 
     * 
     * @return <string> return le twig (modifMediaCadre.html.twig , oneCadre.html.twig)
     * 
     */
    public function modifMediaCadreAction(): Response(int $id, $idmedia, string $locale): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifMediaCadreAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //$idmedia = intval($idmedia);
        //var_dump($idmedia);
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unecadre = $em->getRepository("utbAdminBundle:Cadre")->find($id);
        //$unedimension = $em->getRepository("utbAdminBundle:Dimension")->find(2);

        $user = $this->security->getToken()->getUser()->getId();
        $unmedia = $em->getRepository("utbAdminBundle:Media")->find($idmedia);
        $unmedia->setMediaModifPar($user);
        //$unmedia->setNomMedia("");
        $unmedia->setMediaDateModif(new \DateTime());
        //$unmedia ->setTypeMedia(1);
        //var_dump($unmedia);      
        $extensions = array('jpg', 'png', 'jpeg', 'gif');
        $unmedia->extensions = $extensions;
        //$unmedia ->setDimension($unedimension->getId());
        $form = $this->createForm($this->createForm(MediaCadreType::class), $unmedia);
        $request = $request;


        /* $listestat = $this->entityManager
          ->getRepository('App\Entity\Statistique')
          ->getInfoOrStat($typeStat=3 ,$locale ,$valeur=0, $article=$id) ; */


        if ($request->isMethod('POST')) {

            //Modification de l'image
            $form->handleRequest($request);

            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');


                return $this->redirect($this->generateUrl('utb_admin_modifcadre', ['id' => $id, 'locale' => $locale,]));
            }
            //supprimer l'ancienne image

            $em->persist($unmedia);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Media modifié avec succès');
            //fonctions du Bundle Gregwar pour redimensionner les images
            //$this->image.handling->open($unmedia->getWebPath())
            //->resize($unedimension->getLargeur(),$unedimension->getHauteur())
            // ->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unecadre->getNomCadre().".".'jpg');  
            //$this->image.handling->open($unmedia->getWebPath())
            //->forceResize($unedimension->getLargeur(),$unedimension->getHauteur())
            //->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unmedia->getUrlMedia());              

            return $this->redirect($this->generateUrl('utb_admin_modifcadre', ['id' => $id]));
        }

        return $this->render('utbAdminBundle/Cadre/modifImageCadre.html.twig', array('id' => $id, 'idmedia' => $idmedia,
                    'form' => $form->createView(), 'locale' => $locale,
        ));
    }

}