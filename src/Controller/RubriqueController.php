<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Rubrique;
use App\Entity\RubriqueType;
use App\Entity\RubriqueSeulType;
use App\Entity\RubriqueModifiableType;
use App\Entity\Media;
use App\Entity\MediaRubriqueType;
use App\Entity\MediaRubriqueAjoutType;
use App\Entity\MediaBanniereType;
use App\Entity\Dimension;
use App\Entity\DimensionType;
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
 * RubriqueController pour la gestion des rubriques
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
class RubriqueController extends AbstractController
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
     *  Methode qui s'occupe de l'ajout d'une rubrique
     * 
     *  Les Formulaires varient suivant  la rubrique 
     * 
     * @var 
     *     
     * Les variables
     *
     * $unerubrique :  Objet de la classe Rubrique
     * 
     * $user :  pour avoir l'identifiant de l'utilisateur connecté
     * 
     * $larubrique :  Permet d'avoir le nom de la rubrique à laquelle appartient l'article
     * 
     * $listestat :   Pour afficher les statistiques sur le site
     * 
     * $uneimage :    Objet de la classe Media
     * 
     * $idarticle :   Variable pour recevoir l'id à laquelle appartient l'article enrégistré
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id   id de la rubrique pour qu'apres ajout l'on soit redirige vers le detail de la rubrique ajoutee
     * 
     * @return <string> return le formulaire d'ajout d'une rubrique(ajoutRubrique.html.twig)
     * 
     */
    public function ajoutrubriqueAction(): Response(string $locale, int $id): Response {

        $em = $this->entityManager;


        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutrubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        //on declare le manager et on fixe la langue pour traduction template
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        /*
         * Creation d'une rubrique
         * fixe langue pour traduction contenu
         */
        $user = $this->security->getToken()->getUser()->getId();
        $isfaq = 0;

        /*
         * Création d'image
         * typemedia=1 =>IMAGE
         * IllustreImgMedia = 1 => cette image est une image illustrative de la rubrique
         */
        //$uneimage = new Media(); 
        //$uneimage ->setTypeMedia(1);  
        //$uneimage ->setIllustreImgMedia(1);       
        //$uneimage ->setMediaAjoutPar($user);

        $unesousrubrique = $em->getRepository('App\Entity\Rubrique')->find($id);

        if (($unesousrubrique != null) && ($unesousrubrique->getIdparent() != null)) {
            if (($unesousrubrique->getIdparent()->getId() == 0) ||
                    (
                    ($unesousrubrique->getIdparent()->getId() != 0) &&
                    ($unesousrubrique->getIdgrandparent() == $unesousrubrique->getIdparent()->getId())
                    )
            ) {
                $isfaq = 1;
            }
            else
                $isfaq = 0;
        }



        // $this->requestStack->getCurrentRequest()->attributes->set('isfaq', $isfaq); 

        $unerubrique = new Rubrique();
        $unerubrique->setTranslatableLocale($locale);
        $unerubrique->setRubriqueAjoutPar($user);
        $unerubrique->setTypePresentation(0);
        $unerubrique->setTypeRubrique(2);
        $unerubrique->setIdparent($unesousrubrique);

        //$unesousrubrique->setTranslatableLocale($locale);
        //$em->refresh($unesousrubrique);


        $infosparent = array();
        /* if ($id != 0){
          $infosparent[] = $unesousrubrique ->getNomRubrique();
          $infosparent[] = $unesousrubrique ->getIdparent()->getId();
          $infosparent[] = $unesousrubrique ->getIdparent()->getNomRubrique();
          $infosparent[] = $unesousrubrique ->getIdgrandparent();
          } */


        $boxinfos = $this->entityManager
                ->getRepository("utbAdminBundle:Parametrage")
                ->getTexteBoxInfos($locale, 11);

        //$unerubrique ->addMedia($uneimage);       
        //$uneimage->setRubrique($unerubrique);

        /* Définition de la dimension avec N°2 
         * défini comme dimension pour les autres images 
         */

        $unedimension = $em->getRepository("utbAdminBundle:Dimension")->find(2);

        // Type Stat =2  -----> infos sur un article donné
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);
        $unerubrique->setTranslatableLocale($locale);
        $form = $this->createForm($this->createForm(RubriqueType::class), $unerubrique);

        $request = $request;

        $extensions = array('jpg', 'png', 'jpeg', 'gif');

        $listeRubrique = //$em->getRepository("admin/Rubrique")->findBy( array("idparent"=>0) );
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);


        if ($request->isMethod('POST')) {


            $form->handleRequest($request);
            $unerubrique = $form->getData();



            $verificateur = $this->utb_admin.ArticleService;
            $verifSaisie = $verificateur->verifSaisie($form->get('nomRubrique')->getData(), array('/', '%'));

            //verifier le nombre de carater

            if (strlen($unerubrique->getNomRubrique()) < 3) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errornombrecaratere');
                return $this->render('utbAdminBundle/Rubrique/ajoutRubrique.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale, 'infosparent' => $infosparent,
                            'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'id' => $id,
                            'isfaq' => $isfaq,
                ));
            }


            if (!$verifSaisie) {
                return $this->render('utbAdminBundle/Rubrique/ajoutRubrique.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale, 'infosparent' => $infosparent,
                            'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'id' => $id,
                            'isfaq' => $isfaq,
                ));
            }

            if ($unerubrique->icone !== null && !in_array($unerubrique->icone->guessExtension(), $extensions)) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');

                return $this->render('utbAdminBundle/Rubrique/ajoutRubrique.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale, 'infosparent' => $infosparent,
                            'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'id' => $id,
                            'isfaq' => $isfaq,
                ));
            }
            $nomexiste = $this->entityManager
                    ->getRepository('utbAdminBundle/Rubrique')
                    ->getTestNomRubrique($unerubrique->getNomRubrique());

            if ($nomexiste != 0) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtrubexist');
                return $this->render('utbAdminBundle/Rubrique/ajoutRubrique.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale, 'infosparent' => $infosparent,
                            'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'id' => $id,
                            'isfaq' => $isfaq,
                ));
            }

            if ($unerubrique->getIsFaq() == 1) {

                $faqxiste = $this->entityManager
                        ->getRepository('utbAdminBundle/Rubrique')
                        ->getNbre($unerubrique->getIdparent()->getId(), $unerubrique->getId());

                if ($faqxiste != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorFaqexist');

                    //return $this->redirect($this->generateUrl("utb_admin_detailrubrique",array('id'=>$unerubrique->getIdparent()->getId(), 'locale'=>$locale,)));

                    return $this->render('utbAdminBundle/Rubrique/ajoutRubrique.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'infosparent' => $infosparent,
                                'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'id' => $id,
                                'isfaq' => $isfaq,
                    ));
                }
            }

            if ($unerubrique->getUrlIcone() == "") {
                $unerubrique->setUrlIcone('default_icone.png');
            }

            if (( $unerubrique->getIsFaq() == null ) ||
                    ( $unerubrique->getIdparent()->getId() == 0 )) {
                $unerubrique->setIsFaq(0);
            }


            $em->persist($unerubrique);
            $sousrubrique = 0;
            if ($unerubrique->getIdparent()->getId() != 0) {
                $sousrubrique = 1;
            }

            try {
                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successajtrub');
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtrub');
            }



            $this->image.handling->open($unerubrique->getWebPath())
                    ->forceResize($unedimension->getLargeur(), $unedimension->getHauteur())
                    ->save($unerubrique->getUrlIcone());

            /* if($sousrubrique==1){ */
            return $this->redirect($this->generateUrl("utb_admin_detailrubrique", array('id' => $unerubrique->getId(), 'locale' => $locale,)));
            /* }else{ 
              return $this->redirect($this->generateUrl("utb_admin_listerubrique",array(
              'locale'=>$locale,
              )));
              } */
        }



        return $this->render('utbAdminBundle/Rubrique/ajoutRubrique.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'infosparent' => $infosparent,
                    'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'id' => $id,
                    'isfaq' => $isfaq,
        ));
    }

    /**
     *  Methode qui s'occupe de l'ajout d'une rubrique dans une autre langue 
     * 
     * @var 
     *     
     * Les variables
     *
     * $unerubrique :  Objet de la classe Rubrique
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id   id de la rubrique pour qu'apres ajout l'on soit redirige vers le detail de la rubrique ajoutee
     * 
     * @return <string> return le formulaire d'ajout d'une rubrique(ajoutLangueRubrique.html.twig)
     * 
     */
    public function ajoutLangueRubriqueAction(): Response(string $locale, int $id, $typeaction): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutLangueRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unerubrique = $em->getRepository("utbAdminBundle/Rubrique")->find($id);
        $unerubrique->setTranslatableLocale($locale);
        $em->refresh($unerubrique);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, 0, $id);

        if ($typeaction == 2) {
            $unerubrique->setDescriptionRubrique("- default text -");
            $em->persist($unerubrique);
            try {
                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'reussitesupprdesc');
                //var_dump($unerubrique);exit;
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'echecsupprdesc');
            }

            return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
        }

        // Change la locale  
        $form = $this->createForm($this->createForm(RubriqueSeulType::class), $unerubrique);

        $request = $request;

        $listeRubrique = //$em->getRepository("admin/Rubrique")->findBy( array("idparent"=>0) );
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);

        $languerubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);

        /*  if($locale=="fr"){ 

          $languerubrique->setTranslatableLocale("en");
          $em->refresh($languerubrique);

          }else{
          $languerubrique->setTranslatableLocale("fr");
          $em->refresh($languerubrique);
          } */



        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if (strlen($form->get('nomRubrique')->getData()) < 3) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errornombrecaratere');
                return $this->render('utbAdminBundle/Rubrique/ajoutLangueRubrique.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'listeRubrique' => $listeRubrique, 'listestat' => $listestat,
                            'languerubrique' => $languerubrique,
                ));
            }


            $em->persist($unerubrique);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Rubrique ajouté avec succès');

            return $this->redirect($this->generateUrl("utb_admin_detailrubrique", array('id' => $unerubrique->getId(), 'locale' => $locale,)));
        }

        return $this->render('utbAdminBundle/Rubrique/ajoutLangueRubrique.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'listeRubrique' => $listeRubrique, 'listestat' => $listestat, 'languerubrique' => $languerubrique,
        ));
    }

    /**
     *  Methode qui s'occupe de la suppression d'une rubrique 
     * 
     * @var 
     *     
     * Les variables
     *
     * $unerubrique :  Objet de la classe Rubrique
     * 
     * $user :  pour avoir l'identifiant de l'utilisateur connecté
     * 
     * $avoirIdmedia :  variable pour avoir l'id de l'image illustrative de la rubrique
     * 
     * $media :   Array d'objet pour avoir l'image de la rubrique
     * 
     * $cas1 :    Variable pour vérifier si la rubrique a des sous-rubriques
     * 
     * $cas2 :   Variable pour vérifier si la rubrique à des articles 
     * 
     * $idduparent : Pour voir si la rubrique est une catégorie id  
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id   id de la rubrique
     * 
     * @return <string> return le formulaire d'ajout d'une rubrique(listeRubrique.html.twig)
     * 
     */
    public function supprrubriqueAction(): Response(int $id, string $locale): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprrubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unerubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);

        $unerubrique->setTranslatableLocale($locale);

        $avoirIdmedia = $this->entityManager
                ->getRepository('App\Entity\Media')
                ->getImageRubrique($id, $locale);

        if ($avoirIdmedia != null) {
            $media = $em->getRepository("utbAdminBundle:Media")->find($avoirIdmedia[0]['idmedia']);
        }

        $em->refresh($unerubrique);
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Rubrique supprimé avec succès');

        $cas1 = $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getNombreRubSousRub($id, $locale);

        $cas2 = $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getNombreArticleRub($id, $locale);

        $idduparent = 0;
        if ($unerubrique->getIdParent() != null) {
            $idduparent = $unerubrique->getIdParent()->getId();
        }
        if ($unerubrique->getTypeRubrique() == 1) {
            // if ( $unerubrique->getIdgrandparent() == 0 ) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorsuprubbase');
            //}elseif ( ($unerubrique->getIdgrandparent() != 0) && ( $idduparent !=0 && $unerubrique->getIdgrandparent() == $idduparent ) ){
            //   $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorsuprubbase');  
            // }elseif ( ($unerubrique->getIdgrandparent() != 0) && ( $idduparent !=0 && $unerubrique->getIdgrandparent() != $idduparent ) ){
            //   $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorsuprubbase');  
            // }             
            return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
        } elseif (($cas1 == 0) && ($cas2 == 0)) {
            /* Enfin on supprime la rubrique... */

            if ($avoirIdmedia != null) {
                $em->remove($media);
                //$em->flush();
            }
            $em->remove($unerubrique);

            try {
                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successsuprub');
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorsuprub');
                return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
            }
            // $em->flush();
        } else {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorsuprubcontenu');
            // return $this->redirect($this->generateUrl('utb_admin_listerubrique',array('locale'=>$locale,))); 
            return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
        }
        /* ... et on redirige vers la page d'administration des rubriques */
        return $this->redirect($this->generateUrl('utb_admin_listerubrique', array('locale' => $locale,)));
        //}
    }

    /**
     *  Methode qui s'occupe de la suppression d'une rubrique utilisant Ajax
     * 
     * @var 
     *     
     * Les variables
     *
     * $rubriqueId /  Identifiant de la rubrique
     * 
     * $unerubrique :   Array d'objet pour avoir la rubrique
     *  
     * @param <integer> $rubriqueId   id de la rubrique
     * 
     * @return <string> return le formulaire d'ajout d'une rubrique(listeRubrique.html.twig)
     * 
     */
    function supprOneRubriqueAction(): Response {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $rubriqueId = $request->request->get('articlesId');
        //$articlesId = explode("|",$articlesId);

        if ($rubriqueId != "") {
            $unerubrique = $em->getRepository("admin/Rubrique")->find($rubriqueId);
            $em->remove($unerubrique);
            $em->flush();
        }


        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui s'occupe de la suppression d'une rubrique utilisant Ajax
     * 
     * @var 
     *     
     * Les variables
     *
     * $rubriqueId /  Identifiant de la rubrique
     * 
     * $unerubrique :   Array d'objet pour avoir la rubrique
     *  
     * @param <integer> $rubriqueId   id de la rubrique
     * 
     * @return <string> return le formulaire d'ajout d'une rubrique(listeRubrique.html.twig)
     * 
     */
    function supprBanniereAction(): Response {

        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $banniereIds = $request->request->get('banniereIds');

        //$articlesId = explode("|",$articlesId);

        if ($banniereIds != "") {

            $unmedia = $em->getRepository("utbAdminBundle:Media")->find($banniereIds);
            
            $nomimage = $unmedia->getUrlMedia();
            $em->remove($unmedia);
            if (file_exists(__DIR__ . '/../../../../web/' . "upload/rubriques/" . $unmedia->getUrlMedia()) && $unmedia->getUrlMedia()!="") {
                $unmedia->removeUpload(__DIR__ . '/../../../../web/' . "upload/rubriques/" . $unmedia->getUrlMedia());
            }
            $em->flush();
        }


        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui permet d'avoir la liste des rubriques parents
     * 
     * Les variables
     *
     * $rubriqueId :  Identifiant de la rubrique
     * 
     * $unerubrique :   Array d'objet pour avoir la rubrique
     * 
     * $listerubrique :   Pour avoir la liste des rubriques (de 1 ere niveau )
     * 
     * $listearticlerecent :   Liste  5 articles recement publies.
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     * 
     * $boxinfos :
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeRubrique.html.twig)
     * 
     */
    public function listerubriqueAction(): Response(string $locale): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listerubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $listerubrique = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getAllRubrique($locale);
        //->findAllByLocale($locale);
        //articles recents
        $listearticlerecent = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('utbAdminBundle/Article')
                ->findAllByLocaleRecent($locale);

        $listearticlesoumis = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('admin/Article')
                ->findAllByLocaleType($locale, 2, 5, 0);

        $listearticleattente = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('utbAdminBundle/Article')
                ->findAllByLocaleType($locale, 3, 5, 0);


        // Type Stat =2  -----> infos sur un article donné
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 6, $locale, $valeur = 0, $article = null);

        //liste des utielisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        //var_dump($listeRubrique);

        $boxinfos = $this->entityManager
                ->getRepository("utbAdminBundle:Parametrage")
                ->getTexteBoxInfos($locale, $type = 7);


        return $this->render('utbAdminBundle/Rubrique/allRubriques.html.twig', array('listerubrique' => $listerubrique,
                    'locale' => $locale,
                    'listearticlerecent' => $listearticlerecent,
                    'listestat' => $listestat,
                    'listeuser' => $listeuser,
                    'infos' => $boxinfos,
                    'listeRubrique' => $listeRubrique,
                    'listearticleattente' => $listearticleattente,
                    'listearticlesoumis' => $listearticlesoumis,
        ));
    }

    /**
     *  Methode utilise pour modifier une rubrique 
     * 
     * Les variables
     *
     * $unerubrique :   Array d'objet pour avoir la rubrique
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $nomexiste :     Test l'existence du nom de la rubrique
     * 
     * $boxinfos : 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la rubrique
     * @param <integer> $typeaction si typeaction = 2(action supprimer une description) 
     * @param <string> $nomrubrique utilise pour faciliter le controle d'unicite de nom de la rubrique 
     * @return <string> return le twig (modifRubrique.html.twig , oneRubrique.html.twig)
     * 
     */
    public function modifierrubriqueAction(): Response(int $id, string $locale, $typeaction, $nomrubrique): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierrubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $isfaq = 0;
 
        $unerubrique = $em->getRepository("admin/Rubrique")->find($id);

        $faqxiste = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getNbre($unerubrique->getIdparent()->getId(), $unerubrique->getId());

     /*   if ($faqxiste != 0) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorFaqexist');

            return $this->redirect($this->generateUrl("utb_admin_detailrubrique", array('id' => $unerubrique->getIdparent()->getId(), 'locale' => $locale,)));
        }*/

        if ($unerubrique->getIdparent() != null) {
            if ($unerubrique->getIdparent()->getId() != 0) {
                $isfaq = 1;
            }
        }

        $user = $this->security->getToken()->getUser()->getId();
        $unerubrique->setRubriqueModifPar($user);
        $unerubrique->setRubriqueDateModif(new \Datetime());

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle/Rubrique")
                ->findOneByLocale($id, $locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale,  0, $id);

        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findBy( array("idparent"=>0) );
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        if ($typeaction == 2) {
            $unerubrique->setDescriptionRubrique("- default text -");
            $em->persist($unerubrique);
            try {
                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'reussitesupprdesc');
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'echecsupprdesc');
            }

            return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
        } else {
            if (trim($unerubrique->getDescriptionRubrique()) == "- default text -") {
                $unerubrique->setDescriptionRubrique(null);
            }
        }


        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité rubrique 



        $form = $this->createForm($this->createForm(RubriqueModifiableType::class), $unerubrique);

        $request = $request;

        if ($request->isMethod('POST') || $typeaction == 2) {

            if ($typeaction != 2) {
                $form->handleRequest($request);
            }

            if (strlen($unerubrique->getNomRubrique()) < 3) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errornombrecaratere');
                return $this->render('utbAdminBundle/Rubrique/modifRubrique.html.twig', array(
                            'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'typeaction' => $typeaction, 'unerubrique' => $larubrique, 'listestat' => $listestat,
                            'nomrubrique' => $nomrubrique, 'listeRubrique' => $listeRubrique, 'isfaq' => $isfaq,
                ));
            }

            if ($nomrubrique != $unerubrique->getNomRubrique()) {

                $nomexiste = $this->entityManager
                        ->getRepository('utbAdminBundle/Rubrique')
                        ->getTestNomRubrique($unerubrique->getNomRubrique());

                if ($nomexiste != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtrubexist');
                    return $this->render('utbAdminBundle/Rubrique/modifRubrique.html.twig', array(
                                'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'typeaction' => $typeaction, 'unerubrique' => $larubrique, 'listestat' => $listestat,
                                'nomrubrique' => $nomrubrique, 'listeRubrique' => $listeRubrique, 'isfaq' => $isfaq,
                    ));
                }
            }

            if ($unerubrique->getDescriptionRubrique() == "") {
                $unerubrique->setDescriptionRubrique("- default text -");
            }

            if ($unerubrique->getIsFaq() == 1) {
                $faqxiste = $this->entityManager
                        ->getRepository('utbAdminBundle/Rubrique')
                        ->getNbre($unerubrique->getIdparent()->getId(), $unerubrique->getId());

                if ($faqxiste != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorFaqexist');

                    return $this->render('utbAdminBundle/Rubrique/modifRubrique.html.twig', array(
                                'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'typeaction' => $typeaction, 'unerubrique' => $larubrique,
                                'listestat' => $listestat, 'nomrubrique' => $nomrubrique, 'listeRubrique' => $listeRubrique, 'isfaq' => $isfaq,
                    ));
                }
            }


            if ($unerubrique->getIsFaq() == null) {
                $unerubrique->setIsFaq(0);
            }

            $em->persist($unerubrique);
            try {
                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successmodrub');
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errormodrub');
            }

            return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
        }
        return $this->render('utbAdminBundle/Rubrique/modifRubrique.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'typeaction' => $typeaction, 'unerubrique' => $larubrique,
                    'listestat' => $listestat, 'nomrubrique' => $nomrubrique, 'listeRubrique' => $listeRubrique, 'isfaq' => $isfaq,
        ));
    }

    /**
     *  Methode gerant l'ajout d'une image à une rubrique  
     * 
     * Les variables
     *
     * $unerubrique :   Array d'objet pour avoir la rubrique
     * 
     * $unmedia :       Objet de la classe Media
     * 
     * $extensions :     Tableau des extensions acceptées
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la rubrique
     * 
     * @return <string> return le twig (ajoutMediaRubrique.html.twig , oneRubrique.html.twig)
     * 
     */
    public function ajoutMediaRubriqueAction(): Response(int $id, string $locale, string $type): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutMediaRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );   
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);


        $user = $this->security->getToken()->getUser()->getId();
        $this->requestStack->getCurrentRequest()->setLocale($locale);


        $unerubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);
        $unedimension = $em->getRepository("admin/Dimension")->find(2);
        $unmedia = new Media();
        $extensions = array('jpg', 'png', 'jpeg', 'gif');
        $unmedia->setRubrique($unerubrique);
        
       

        $unmedia->setUrlFistMedia("-------");
        $unmedia->setTranslatableLocale($locale); 

        if ($type == 1) {
            $unmedia->setTypeMedia(3);
            $unmedia->setIllustreImgMedia(0); //
        } else {
            $unmedia->setTypeMedia(1);
            $unmedia->setIllustreImgMedia(1); //
        }
        $unmedia->setMediaAjoutPar($user);

        $unmedia->extensions = $extensions;
        //$unmedia ->setDimension($unedimension->getId());
        if ($type == 1) {

            $form = $this->createForm($this->createForm(MediaBanniereType::class), $unmedia);
        } else {

            $form = $this->createForm($this->createForm(MediaRubriqueAjoutType::class), $unmedia);
        }
        $request = $request;

        /* $listestat = $this->entityManager
          ->getRepository('App\Entity\Statistique')
          ->getInfoOrStat($typeStat=3 ,$locale ,$valeur=0, $article=$id) ; */

       
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');

                return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
            }
            $unmedia = $form->getData();
            $unmedia->setAjoutmodifMedia(0);
            $em->persist($unmedia);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Média ajouté avec succès');
            //$this->image.handling->open($unmedia->getWebPath())
            //->forceResize($unedimension->getLargeur(),$unedimension->getHauteur())
            //->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unerubrique->getNomRubrique().".".'jpg');              
            if ($id == 0) {
                return $this->redirect($this->generateUrl('utb_admin_accueilbanniere', ['locale' => $locale,]));
            } else {
                return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
            }
        }

        return $this->render('utbAdminBundle/Rubrique/ajoutImageRubrique.html.twig', array('id' => $id,
                    'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     * Methode gerant la modification  de l'image à une rubrique 
     *  
     * Les variables
     *
     * $unerubrique /   Array d'objet pour avoir la rubrique
     * 
     * $unmedia :       Objet de la classe Media
     * 
     * $extensions :     Tableau des extensions acceptées
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository 
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param integer $id identifiant de la rubrique
     * 
     * 
     * @return string return le twig (modifMediaRubrique.html.twig , oneRubrique.html.twig)
     * 
     */
    public function modifMediaRubriqueAction(): Response(int $id, $idmedia, string $locale): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifMediaRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        $idmedia = intval($idmedia);
        //var_dump($idmedia);
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        //$em = $this-> getDoctrine()->getEntityManager();        
        $unerubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);
        $unedimension = $em->getRepository("utbAdminBundle:Dimension")->find(2);

        $user = $this->security->getToken()->getUser()->getId();
        $unmedia = $em->getRepository("utbAdminBundle:Media")->find($idmedia);
        
        $lecount = $this->entityManager
                ->getRepository("utbAdminBundle:Media")
                ->getSiMediaExist($idmedia, $locale);           
      
        $unmedia->setMediaModifPar($user);
        $unmedia->setNomMedia("");

        $unmedia->setMediaDateModif(new \DateTime());
        
        $extensions = array('jpg', 'png', 'jpeg', 'gif');
        $unmedia->extensions = $extensions;
        
        $form = $this->createForm($this->createForm(MediaRubriqueAjoutType::class), $unmedia);
        $request = $request;

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, 0,$id);

        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) ); 
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);


        if ($request->isMethod('POST')) {

            //Modification de l'image
            
            $form->handleRequest($request);
            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');

                return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
            }
            //supprimer l'ancienne image
            //$unmedia->setTranslatableLocale($locale);          
            if ( $lecount == 1 ) { 
                
                $unmedia->setTranslatableLocale($locale);                
                $em->refresh($unmedia);
                $unmedia->setPositionMedia(1);
                $unmedia->setAjoutmodifMedia(1);
                
            }elseif ($lecount == 0) {  
                
                $unmedia->setTranslatableLocale($locale);                
                $unmedia->setAjoutmodifMedia(2);
                //$unmedia->setPositionMedia(3);
            }                         
            
            $em->persist($unmedia);
            $em->flush();

            //enregistrement de l'image enanglais
            $nouveauUrl=$unmedia->getUrlMedia();
            $newmedia = $em->getRepository("utbAdminBundle:Media")->find($idmedia);
            //var_dump($nouveauUrl);exit;
            
            if($newmedia->getAjoutmodifMedia()==2 && $lecount != 1 ){
                
                $newmedia->setTranslatableLocale($locale);
                $em->refresh($newmedia);
                $newmedia->setUrlMedia($nouveauUrl);
                $newmedia->setPositionMedia(3);
                $newmedia->setAjoutmodifMedia(2);
                $em->persist($newmedia);
                $em->flush();
            }
            
            $em->clear();
            $_media = $em->getRepository("admin/Media")->find($idmedia);
            $_media->setPositionMedia(2);
            $em->persist($_media);
            $em->flush();
            
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Media modifié avec succès');
            //fonctions du Bundle Gregwar pour redimensionner les images
            //$this->image.handling->open($unmedia->getWebPath())
            //->resize($unedimension->getLargeur(),$unedimension->getHauteur())
            // ->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unerubrique->getNomRubrique().".".'jpg');  
            //$this->image.handling->open($unmedia->getWebPath())
            //->forceResize($unedimension->getLargeur(),$unedimension->getHauteur())
            //->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unmedia->getUrlMedia());              

            return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Rubrique/modifImageRubrique.html.twig', array('id' => $id, 'idmedia' => $idmedia,
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat, 'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     * Methode utilise pour avoir le detail de la rubrique 
     *  
     * Les variables
     *
     * $larubrique /   Array d'objet pour avoir la rubrique 
     * 
     * $larubrique2 :   Array (id et nomRubrique) pour avoir la rubrique 
     * 
     * $larubrique3 :   Array (id et nomRubrique) pour avoir la sous-rubrique
     * 
     * $larubrique4 :   Array (id et nomRubrique) pour avoir la catégorie
     *  
     * $nom  : Tableau à 2 dimension pour avoir le nom de la rubrique | sous-rubrique et catégorie
     * 
     * $vnom : Variable utilisee pour reordonner les informations de la variable $nom
     * 
     * $image :       Objet de la classe Media
     * 
     * $listestat1 : Pour avoir accès aux informations des articles soumis et En attente de publication
     * 
     * $listesousrub :     Avoir la liste des sous ribriques d'une rubrique 
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la rubrique
     * @param <integer> $page Variable utilisée dans la pagination
     * 
     * 
     * @return <string> return le twig (oneRubrique.html.twig , oneRubrique.html.twig)
     * 
     */
    public function detailsRubriqueAction(): Response(int $id, string $locale, $page): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'detailsRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        //$em = $this->entityManager;
        //$larubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($id, $locale);

        $nom[0][0] = $larubrique[0]['id'];
        $nom[0][1] = $larubrique[0]['nomRubrique'];

        if ($larubrique[0]['idParent'] === 0 && $larubrique[0]['idGrandParent'] === 0) {
            
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {

            $larubrique2 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique2[0]['id'];
            $nom[1][1] = $larubrique2[0]['nomRubrique'];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {

            $larubrique3 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique3[0]['id'];
            $nom[1][1] = $larubrique3[0]['nomRubrique'];

            $larubrique4 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idGrandParent'], $locale);
            $nom[2][0] = $larubrique4[0]['id'];
            $nom[2][1] = $larubrique4[0]['nomRubrique'];
        }



        if ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {



            $vnom[2][0] = $nom[2][0];
            $vnom[2][1] = $nom[2][1];
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        }
        $vnom[0][0] = $larubrique[0]['id'];
        $vnom[0][1] = $larubrique[0]['nomRubrique'];

        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);


        //var_dump($nom);
        //$var1 =$larubrique[0];
        //$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$id));
        // 
        // 
        /*
          $nomrubrique[] =array
          (
          $id,
          $var1->getNomRubrique()
          );
          $var2 =$var1->getIdParent();
          /*   $nomrubrique[] =array
          (
          $var2->getId(),
          $var2->getnomRubrique()
          )  ;
          $var3 =$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$var2->getIdParent()));
          $nomrubrique[] =array
          (
          $var3->getId(),
          $var3->getnomRubrique()
          )  ; */////////////////                
        //var_dump($var2);
        //$nomrubrique = $larubrique->getNomRubrique();
        //$numerorubrique = $larubrique->getId();
        //$descriptrubrique = $larubrique->getDescriptionRubrique();
        $listesousrub = $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getRubSousRub($id, $locale);

        /* $listearticle = $this->entityManager
          ->getRepository('App\Entity\Rubrique')
          ->getArticleRub($larubrique->getrubrique->getId()); */

        $image = $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getListeImageOrIcone($id, 1, 1, $locale);

        $banniere = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeImageOrIcone($id, 0, 3, $locale);
       // var_dump($banniere);exit;

        $articleservice = $this->utb_admin.ArticleService;

        // infos alertes
        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 7, $locale, $valeur = 0, $article = $id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, $valeur = 0, $article = $id);

        /* total des résultats */
        $listeart = $em->getRepository("utbAdminBundle/Article")->getListeByRubriqueLocale($id, $locale);
        $total = count($listeart);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_rub');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;


        $listearticle = $articleservice->allArticle($em, $id, $locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        $listecadre = $this->entityManager
                ->getRepository('App\Entity\Cadre')
                ->findAllCadreByLocale($locale);


        $tauxTraduction = $this->entityManager
                ->getRepository('App\Entity\Article')
                ->getTauxTraduction($id, $locale, 1);

        //var_dump($tauxTraduction);

        return $this->render('utbAdminBundle/Rubrique/oneRubrique.html.twig', array(
                    'listesousrub' => $listesousrub, 'larubrique' => $larubrique,
                    'image' => $image, 'banniere' => $banniere, 'id' => $id,
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat,
                    'listestat1' => $listestat1,
                    'nom' => $vnom,
                    'listeRubrique' => $listeRubrique,
                    'listecadre' => $listecadre,
                    'tauxTraduction' => $tauxTraduction,
        ));
    }

    /**
     * Methode utilise pour avoir le detail de la rubrique 
     *  
     * Les variables
     *
     * $larubrique :   Array d'objet pour avoir la rubrique 
     * 
     * $larubrique2 :   Array (id et nomRubrique) pour avoir la rubrique 
     * 
     * $larubrique3 :   Array (id et nomRubrique) pour avoir la sous-rubrique
     * 
     * $larubrique4 :   Array (id et nomRubrique) pour avoir la catégorie
     *  
     * $nom  : Tableau à 2 dimension pour avoir le nom de la rubrique | sous-rubrique et catégorie
     * 
     * $vnom : Variable utilisee pour reordonner les informations de la variable $nom
     * 
     * $image :       Objet de la classe Media
     * 
     * $listestat1 : Pour avoir accès aux informations des articles soumis et En attente de publication
     * 
     * $listesousrub :     Avoir la liste des sous ribriques d'une rubrique 
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la rubrique
     * @param <integer> $page Variable utilisée dans la pagination
     * 
     * 
     * @return <string> return le twig (oneRubrique.html.twig , oneRubrique.html.twig)
     * 
     */
    public function banniereAction(): Response(string $locale): Response {

        /*
         * $em =$this->entityManager;	
          $AccessControl =  $this->utb_admin.AccessControl;
          $checkAcces =  $AccessControl->verifAcces($em,'detailsRubriqueAction', $this->container->get );

          if(!$checkAcces){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect ( $this->generateUrl('utb_admin_accueil', ['locale' =>$locale]));
          }
         *  
         */

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        //$larubrique = $em->getRepository("utbAdminBundle/Rubrique")->find($id);              

        $banniere = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeImageOrIcone($id = 0, 0, 3, $locale);
        $listecadre = $this->entityManager
                ->getRepository('admin/Cadre')
                ->findAllCadreAccueil($locale, 7);

        $listecadreArticle = $this->entityManager
                ->getRepository('utbAdminBundle/Cadre')
                ->findAllCadreArticleAccueil($locale, 8);

        $unerubrique = array();
        $i = 0;
        foreach ($listecadre as $uncadre) {
            $unerubrique[$i] = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($uncadre['rubPointer'], $locale);
            $i++;
        }

        $unarticle = array();
        $i = 0;
        foreach ($listecadreArticle as $uncadre) {
            $unarticle[$i] = $this->entityManager
                    ->getRepository("utbAdminBundle:Article")
                    ->findOneByLocale($uncadre['articlePointer'], $locale);
            $i++;
        }
        // var_dump($unarticle);exit;
        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findAll();
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        return $this->render('utbAdminBundle/Rubrique/banniere.html.twig', array(
                    'locale' => $locale, 'banniere' => $banniere, 'listecadre' => $listecadre, "rubriques" => $listeRubrique, 'unerubrique' => $unerubrique, 'listecadreArticle' => $listecadreArticle, 'unarticle' => $unarticle
        ));
    }

    /**
     *  La liste des articles mis en corbeille d'une rubrique 
     * 
     * 
     * Les variables
     *
     * $larubrique :   Array d'objet pour avoir la rubrique 
     * 
     * $larubrique2 :   Array (id et nomRubrique) pour avoir la rubrique 
     * 
     * $larubrique3 :   Array (id et nomRubrique) pour avoir la sous-rubrique
     * 
     * $larubrique4 :   Array (id et nomRubrique) pour avoir la catégorie
     *  
     * $nom  : Tableau à 2 dimension pour avoir le nom de la rubrique | sous-rubrique et catégorie
     * 
     * $vnom : Variable utilisee pour reordonner les informations de la variable $nom
     * 
     * $image :       Objet de la classe Media
     * 
     * $listestat1 : Pour avoir accès aux informations des articles soumis et En attente de publication
     * 
     * $listesousrub :     Avoir la liste des sous ribriques d'une rubrique 
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la rubrique
     * @param <integer> $page Variable utilisée dans la pagination
     * 
     * @return <string> return le twig (oneRubriqueCorbeille.html.twig , oneRubrique.html.twig)
     * 
     */
    public function detailsCorbeilleRubriqueAction(): Response(int $id, string $locale, $page): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'detailsCorbeilleRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        //$larubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($id, $locale);

        $nom[0][0] = $larubrique[0]['id'];
        $nom[0][1] = $larubrique[0]['nomRubrique'];

        if ($larubrique[0]['idParent'] === 0 && $larubrique[0]['idGrandParent'] === 0) {
            
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {

            $larubrique2 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique2[0]['id'];
            $nom[1][1] = $larubrique2[0]['nomRubrique'];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {

            $larubrique3 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique3[0]['id'];
            $nom[1][1] = $larubrique3[0]['nomRubrique'];

            $larubrique4 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idGrandParent'], $locale);
            $nom[2][0] = $larubrique4[0]['id'];
            $nom[2][1] = $larubrique4[0]['nomRubrique'];
        }

        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) ); 
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);


        if ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {



            $vnom[2][0] = $nom[2][0];
            $vnom[2][1] = $nom[2][1];
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        }
        $vnom[0][0] = $larubrique[0]['id'];
        $vnom[0][1] = $larubrique[0]['nomRubrique'];

        //var_dump($nom);
        //$var1 =$larubrique[0];
        //$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$id));
        // 
        // 
        /*
          $nomrubrique[] =array
          (
          $id,
          $var1->getNomRubrique()
          );
          $var2 =$var1->getIdParent();
          /*   $nomrubrique[] =array
          (
          $var2->getId(),
          $var2->getnomRubrique()
          )  ;
          $var3 =$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$var2->getIdParent()));
          $nomrubrique[] =array
          (
          $var3->getId(),
          $var3->getnomRubrique()
          )  ; */

        //var_dump($var2);
        //$nomrubrique = $larubrique->getNomRubrique();
        //$numerorubrique = $larubrique->getId();
        //$descriptrubrique = $larubrique->getDescriptionRubrique();
        $listesousrub = $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getRubSousRub($id, $locale);

        /* $listearticle = $this->entityManager
          ->getRepository('App\Entity\Rubrique')
          ->getArticleRub($larubrique->getrubrique->getId()); */

        $image = $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getListeImageOrIcone($id, 1, 1, $locale);

        /* $icone = $this->entityManager
          ->getRepository('utbAdminBundle/Rubrique')
          ->getListeImageOrIcone($id,1,$locale); */

        $articleservice = $this->utb_admin.ArticleService;


        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 7, $locale, $valeur = 0, $article = $id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, $valeur = 0, $article = $id);

        /* total des résultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalListeCorbeilleRubriqueLocale($id, $locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $boxinfos = $this->entityManager
                ->getRepository("utbAdminBundle:Parametrage")
                ->getTexteBoxInfos($locale, $type = 3);


        $listearticle = $articleservice->allArticleCorbeille($em, $id, $locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        // var_dump($listeuser);

        return $this->render('utbAdminBundle/Rubrique/oneRubriqueCorbeille.html.twig', array(
                    'listesousrub' => $listesousrub, 'larubrique' => $larubrique,
                    'image' => $image, //'icone' => $icone, 
                    'id' => $id,
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat,
                    'listestat1' => $listestat1,
                    'nom' => $vnom,
                    'listeRubrique' => $listeRubrique,
                    'infos' => $boxinfos,
        ));
    }

    /**
     *  La liste des articles archivés  d'un rubrique 
     *  
     * Les variables
     *
     * $larubrique :   Array d'objet pour avoir la rubrique 
     * 
     * $larubrique2 :   Array (id et nomRubrique) pour avoir la rubrique 
     * 
     * $larubrique3 :   Array (id et nomRubrique) pour avoir la sous-rubrique
     * 
     * $larubrique4 :   Array (id et nomRubrique) pour avoir la catégorie
     *  
     * $nom  : Tableau à 2 dimension pour avoir le nom de la rubrique | sous-rubrique et catégorie
     * 
     * $vnom : Variable utilisee pour reordonner les informations de la variable $nom
     * 
     * $image :       Objet de la classe Media
     * 
     * $listestat1 : Pour avoir accès aux informations des articles soumis et En attente de publication
     * 
     * $listesousrub :     Avoir la liste des sous ribriques d'une rubrique 
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la rubrique
     * @param <integer> $page Variable utilisée dans la pagination
     * 
     * @return <string> return le twig (oneRubriqueArchive.html.twig , oneRubrique.html.twig)
     * 
     */
    public function detailsArchiveRubriqueAction(): Response(int $id, string $locale, $page): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'detailsArchiveRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        //$larubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($id, $locale);

        $nom[0][0] = $larubrique[0]['id'];
        $nom[0][1] = $larubrique[0]['nomRubrique'];

        if ($larubrique[0]['idParent'] === 0 && $larubrique[0]['idGrandParent'] === 0) {
            
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {

            $larubrique2 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique2[0]['id'];
            $nom[1][1] = $larubrique2[0]['nomRubrique'];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {

            $larubrique3 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique3[0]['id'];
            $nom[1][1] = $larubrique3[0]['nomRubrique'];

            $larubrique4 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idGrandParent'], $locale);
            $nom[2][0] = $larubrique4[0]['id'];
            $nom[2][1] = $larubrique4[0]['nomRubrique'];
        }



        if ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {



            $vnom[2][0] = $nom[2][0];
            $vnom[2][1] = $nom[2][1];
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        }
        $vnom[0][0] = $larubrique[0]['id'];
        $vnom[0][1] = $larubrique[0]['nomRubrique'];

        //var_dump($nom);
        //$var1 =$larubrique[0];
        //$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$id));
        // 
        // 
        /*
          $nomrubrique[] =array
          (
          $id,
          $var1->getNomRubrique()
          );
          $var2 =$var1->getIdParent();
          /*   $nomrubrique[] =array
          (
          $var2->getId(),
          $var2->getnomRubrique()
          )  ;
          $var3 =$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$var2->getIdParent()));
          $nomrubrique[] =array
          (
          $var3->getId(),
          $var3->getnomRubrique()
          )  ; */

        //var_dump($var2);
        //$nomrubrique = $larubrique->getNomRubrique();
        //$numerorubrique = $larubrique->getId();
        //$descriptrubrique = $larubrique->getDescriptionRubrique();
        $listesousrub = $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getRubSousRub($id, $locale);

        /* $listearticle = $this->entityManager
          ->getRepository('App\Entity\Rubrique')
          ->getArticleRub($larubrique->getrubrique->getId()); */

        $image = $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getListeImageOrIcone($id, 1, 1, $locale);

        /* $icone = $this->entityManager
          ->getRepository('utbAdminBundle/Rubrique')
          ->getListeImageOrIcone($id,1,$locale); */

        $articleservice = $this->utb_admin.ArticleService;

        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findBy( array("idparent"=>0) );        
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);


        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 7, $locale, $valeur = 0, $article = $id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, $valeur = 0, $article = $id);

        /* total des résultats */
        $total = $em->getRepository("utbAdminBundle:Article")->getTotalListeArchiveRubriqueLocale($id, $locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;


        $listearticle = $articleservice->allArticleArchive($em, $id, $locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        // var_dump($listeuser);
        $boxinfos = $this->entityManager
                ->getRepository("utbAdminBundle:Parametrage")
                ->getTexteBoxInfos($locale, $type = 5);


        return $this->render('utbAdminBundle/Rubrique/oneRubriqueArchive.html.twig', array(
                    'listesousrub' => $listesousrub, 'larubrique' => $larubrique,
                    'image' => $image, //'icone' => $icone, 
                    'id' => $id,
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat,
                    'listestat1' => $listestat1,
                    'nom' => $vnom,
                    'listeRubrique' => $listeRubrique,
                    'infos' => $boxinfos,
        ));
    }

    /**
     *  La liste des articles soumis pour validation  d'une rubrique 
     *
     * Les variables
     *
     * $larubrique :   Array d'objet pour avoir la rubrique 
     * 
     * $larubrique2 :   Array (id et nomRubrique) pour avoir la rubrique 
     * 
     * $larubrique3 :   Array (id et nomRubrique) pour avoir la sous-rubrique
     * 
     * $larubrique4 :   Array (id et nomRubrique) pour avoir la catégorie
     *  
     * $nom  : Tableau à 2 dimension pour avoir le nom de la rubrique | sous-rubrique et catégorie
     * 
     * $vnom : Variable utilisee pour reordonner les informations de la variable $nom
     * 
     * $image :       Objet de la classe Media
     * 
     * $listestat1 : Pour avoir accès aux informations des articles soumis et En attente de publication
     * 
     * $listesousrub :     Avoir la liste des sous ribriques d'une rubrique 
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository 
     *   
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la rubrique
     * @param <integer> $page Variable utilisée dans la pagination
     * 
     * @return <string> return le twig (oneRubriqueSoumis.html.twig , oneRubrique.html.twig)
     * 
     */
    public function detailsSoumisRubriqueAction(): Response(int $id, string $locale, $page): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'detailsSoumisRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        //$larubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($id, $locale);

        $nom[0][0] = $larubrique[0]['id'];
        $nom[0][1] = $larubrique[0]['nomRubrique'];

        if ($larubrique[0]['idParent'] === 0 && $larubrique[0]['idGrandParent'] === 0) {
            
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {

            $larubrique2 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique2[0]['id'];
            $nom[1][1] = $larubrique2[0]['nomRubrique'];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {

            $larubrique3 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique3[0]['id'];
            $nom[1][1] = $larubrique3[0]['nomRubrique'];

            $larubrique4 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idGrandParent'], $locale);
            $nom[2][0] = $larubrique4[0]['id'];
            $nom[2][1] = $larubrique4[0]['nomRubrique'];
        }



        if ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {



            $vnom[2][0] = $nom[2][0];
            $vnom[2][1] = $nom[2][1];
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        }
        $vnom[0][0] = $larubrique[0]['id'];
        $vnom[0][1] = $larubrique[0]['nomRubrique'];

        //var_dump($nom);
        //$var1 =$larubrique[0];
        //$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$id));
        // 
        // 
        /*
          $nomrubrique[] =array
          (
          $id,
          $var1->getNomRubrique()
          );
          $var2 =$var1->getIdParent();
          /*   $nomrubrique[] =array
          (
          $var2->getId(),
          $var2->getnomRubrique()
          )  ;
          $var3 =$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$var2->getIdParent()));
          $nomrubrique[] =array
          (
          $var3->getId(),
          $var3->getnomRubrique()
          )  ; */

        //var_dump($var2);
        //$nomrubrique = $larubrique->getNomRubrique();
        //$numerorubrique = $larubrique->getId();
        //$descriptrubrique = $larubrique->getDescriptionRubrique();
        $listesousrub = $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getRubSousRub($id, $locale);

        /* $listearticle = $this->entityManager
          ->getRepository('App\Entity\Rubrique')
          ->getArticleRub($larubrique->getrubrique->getId()); */

        $image = $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getListeImageOrIcone($id, 1, $locale);

        $icone = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeImageOrIcone($id, 1, $locale);
        $articleservice = $this->utb_admin.ArticleService;


        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 7, $locale, $valeur = 0, $article = $id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, $valeur = 0, $article = $id);

        /* total des résultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalListeSoumisRubriqueLocale($id, $locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;


        $listearticle = $articleservice->allArticleSoumis($em, $id, $locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);


        // var_dump($listeuser);

        return $this->render('utbAdminBundle/Rubrique/oneRubriqueSoumis.html.twig', array(
                    'listesousrub' => $listesousrub, 'larubrique' => $larubrique,
                    'image' => $image, 'icone' => $icone, 'id' => $id,
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat,
                    'listestat1' => $listestat1,
                    'nom' => $vnom,
                    'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     *  La liste des articles en Antente de publication 
     *  
     * Les variables
     *
     * $larubrique :   Array d'objet pour avoir la rubrique 
     * 
     * $larubrique2 :   Array (id et nomRubrique) pour avoir la rubrique 
     * 
     * $larubrique3 :   Array (id et nomRubrique) pour avoir la sous-rubrique
     * 
     * $larubrique4 :   Array (id et nomRubrique) pour avoir la catégorie
     *  
     * $nom  : Tableau à 2 dimension pour avoir le nom de la rubrique | sous-rubrique et catégorie
     * 
     * $vnom : Variable utilisee pour reordonner les informations de la variable $nom
     * 
     * $image :       Objet de la classe Media
     * 
     * $listestat1 : Pour avoir accès aux informations des articles soumis et En attente de publication
     * 
     * $listesousrub :     Avoir la liste des sous ribriques d'une rubrique 
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la rubrique
     * @param <integer> $page Variable utilisée dans la pagination
     * 
     * @return <string> return le twig (oneRubriqueAttente.html.twig )
     * 
     */
    public function detailsAttenteRubriqueAction(): Response(int $id, string $locale, $page): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'detailsAttenteRubriqueAction', $this->container->get);
        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        //$larubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($id);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($id, $locale);

        $nom[0][0] = $larubrique[0]['id'];
        $nom[0][1] = $larubrique[0]['nomRubrique'];

        if ($larubrique[0]['idParent'] === 0 && $larubrique[0]['idGrandParent'] === 0) {
            
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {

            $larubrique2 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique2[0]['id'];
            $nom[1][1] = $larubrique2[0]['nomRubrique'];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {

            $larubrique3 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idParent'], $locale);
            $nom[1][0] = $larubrique3[0]['id'];
            $nom[1][1] = $larubrique3[0]['nomRubrique'];

            $larubrique4 = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->findOneByLocale($larubrique[0]['idGrandParent'], $locale);
            $nom[2][0] = $larubrique4[0]['id'];
            $nom[2][1] = $larubrique4[0]['nomRubrique'];
        }



        if ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] == $larubrique[0]['idGrandParent']) {
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        } elseif ($larubrique[0]['idParent'] != 0 && $larubrique[0]['idGrandParent'] != 0 && $larubrique[0]['idParent'] != $larubrique[0]['idGrandParent']) {



            $vnom[2][0] = $nom[2][0];
            $vnom[2][1] = $nom[2][1];
            $vnom[1][0] = $nom[1][0];
            $vnom[1][1] = $nom[1][1];
        }
        $vnom[0][0] = $larubrique[0]['id'];
        $vnom[0][1] = $larubrique[0]['nomRubrique'];

        //var_dump($nom);
        //$var1 =$larubrique[0];
        //$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$id));
        // 
        // 
        /*
          $nomrubrique[] =array
          (
          $id,
          $var1->getNomRubrique()
          );
          $var2 =$var1->getIdParent();
          /*   $nomrubrique[] =array
          (
          $var2->getId(),
          $var2->getnomRubrique()
          )  ;
          $var3 =$this->entityManager->getRepository("utbAdminBundle:Rubrique")->findOneBy(array('id'=>$var2->getIdParent()));
          $nomrubrique[] =array
          (
          $var3->getId(),
          $var3->getnomRubrique()
          )  ; */

        //var_dump($var2);
        //$nomrubrique = $larubrique->getNomRubrique();
        //$numerorubrique = $larubrique->getId();
        //$descriptrubrique = $larubrique->getDescriptionRubrique();
        $listesousrub = $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getRubSousRub($id, $locale);

        /* $listearticle = $this->entityManager
          ->getRepository('App\Entity\Rubrique')
          ->getArticleRub($larubrique->getrubrique->getId()); */

        $image = $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getListeImageOrIcone($id, 1, $locale);

        $icone = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeImageOrIcone($id, 1, $locale);
        $articleservice = $this->utb_admin.ArticleService;


        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 7, $locale, $valeur = 0, $article = $id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, $valeur = 0, $article = $id);

        /* total des résultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalListeAttenteRubriqueLocale($id, $locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;


        $listearticle = $articleservice->allArticleAttente($em, $id, $locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();
        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        return $this->render('utbAdminBundle/Rubrique/oneRubriqueAttente.html.twig', array(
                    'listesousrub' => $listesousrub, 'larubrique' => $larubrique,
                    'image' => $image, 'icone' => $icone, 'id' => $id,
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat,
                    'listestat1' => $listestat1,
                    'nom' => $vnom,
                    'listeRubrique' => $listeRubrique,
        ));
    }

    function presentationRubriqueAction(): Response {

        /* $em =$this->entityManager;	
          $AccessControl =  $this->utb_admin.AccessControl;
          $checkAcces =  $AccessControl->verifAcces($em,'corbeilleSondageAction', $this->container->get );

          if(!$checkAcces){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect ( $this->generateUrl('utb_admin_accueil', ['locale' =>$locale]));
          } */
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $rubriqueIds = $request->request->get('rubriqueIds');
        $type = $request->request->get('typeId');


        //$unip=new AdresseIp(); 

        $unerubrique = $em->getRepository("admin/Rubrique")->find($rubriqueIds);
        $unerubrique->setTypePresentation($type);

        $em->persist($unerubrique);
        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui liste les cadres d'un article
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadreArticle.html.twig)
     * 
     */
    public function listeCadreRubriqueAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeCadreRubriqueAction', $this->container->get);

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

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle/Rubrique")
                ->findOneByLocale2($id, $locale);

        $listecadre = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeCadre($id, $locale);



        //$listecadre = $em->getRepository("utbAdminBundle:Cadre")->findAll();


        return $this->render('utbAdminBundle/Rubrique/listeCadreRubrique.html.twig', array('listecadre' => $listecadre, 'listestat' => $listestat,
                    'id' => $id, 'larubrique' => $larubrique, 'locale' => $locale,));
    }

    /**
     *  Methode qui s'occupe de l'ajout d'un cadre à une rubrique
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un cadre(ajoutCadre.html.twig)
     * 
     */
    public function ajoutCadreRubriqueAction(): Response(int $id, string $locale): Response {

        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutCadreRubriqueAction', $this->container->get);

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

        $larubrique = $this->entityManager
                ->getRepository("admin/Rubrique")
                ->findOneByLocale2($id, $locale);

        $listecadre = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeCadre($id, $locale);
        $ids = '';
        foreach ($listecadre as $key => $cadre) {
            $ids .= $cadre['id'];
            if ($key != count($listecadre) - 1)
                $ids .= ',';
        }
        //$ids .= ')';
        // var_dump($ids);
        $listecadreabsent = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeCadreAbsent($ids, $locale);
        //exit;
        //$listecadre = $em->getRepository("utbAdminBundle/Cadre")->findAll();


        return $this->render('utbAdminBundle/Rubrique/ajoutCadre.html.twig', array('listecadre' => $listecadre, 'listestat' => $listestat,
                    'larubrique' => $larubrique, 'listecadreabsent' => $listecadreabsent, 'locale' => $locale,));
    }

    /**
     * Methode permettant de valider des cadres selectionnes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * @return <json> return etat du traitement effectue
     *  
     */
    function validerAllCadresRubriqueAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'validerAllCadresRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $cadresIds = $request->request->get('ds');
        $id = $request->request->get('id');

        $cadresIds = explode("|", $cadresIds);
        $unerubrique = $em->getRepository("admin/Rubrique")->find($id);

        foreach ($cadresIds as $key => $value) {

            if (!empty($value)) {
                $uncadre = $em->getRepository("utbAdminBundle/Cadre")->find($value);
                $unerubrique->addCadre($uncadre);
            }
        }

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
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
    public function modifierCadreRubriqueAction(): Response(int $id, $idrubrique, string $locale): Response {

        $unmedia = null;
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierCadreRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale2($idrubrique, $locale);


        // Récupération du cadre 
        $uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($id);

        $uncadre->setTranslatableLocale($locale);

        if ($uncadre != null) {
            $unmedia = $uncadre->getMedias();
        }
        $typecadre = $uncadre->getTypeCadre(); //type du cadre pr organiser les champs à afficher sur le twig de modif       
        $untypecadre = $em->getRepository("utbAdminBundle:TypeCadre")->find($typecadre);
        //$uncadre = $em->getRepository("admin/Cadre")->find($id);
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
            $uncadre->setTypeCadre($typecadre);
            $em->persist($uncadre);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Cadre modifié avec succès');

            return $this->redirect($this->generateUrl('utb_admin_listecadrerubrique', array('id' => $idrubrique, 'locale' => $locale)));
        }
        return $this->render('utbAdminBundle/Rubrique/modifCadre.html.twig', array(
                    'form' => $form->createView(), 'listestat' => $listestat, 'unmedia' => $unmedia, 'untypecadre' => $untypecadre, 'id' => $id,
                    'typecadre' => $typecadre, 'larubrique' => $larubrique, 'locale' => $locale,));
    }

    /**
     * Methode gerant la modification  de l'image à une cadre 
     *  
     * Les variables
     *
     * $unecadre /   Array d'objet pour avoir la cadre
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
    public function modifMediaCadreRubriqueAction(): Response(int $id, $idrubrique, $idmedia, string $locale): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifMediaCadreRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //$idmedia = intval($idmedia);
        //var_dump($idmedia);
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale2($idrubrique, $locale);


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


        if ($request->isMethod('POST')) {

            //Modification de l'image
            $form->handleRequest($request);

            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');


                return $this->redirect($this->generateUrl('utb_admin_modifcadrerubrique', ['id' => $id, 'idrubrique' => $idrubrique, 'locale' => $locale]));
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

            return $this->redirect($this->generateUrl('utb_admin_modifcadrerubrique', ['id' => $id, 'idrubrique' => $idrubrique, 'locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Rubrique/modifImageCadre.html.twig', array('id' => $id, 'idmedia' => $idmedia,
                    'form' => $form->createView(), 'larubrique' => $larubrique, 'locale' => $locale,
        ));
    }

    /**
     * Methode permettant de supprimer des cadres selectionnes pour une rubrique - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $usersIds: Tableau regoupants les Ids des instances de la classe Cadre selectionnes
     * 
     * @return <json> return etat du traitement effectue
     *  
     */
    function supprAllCadresRubriqueAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprAllCadresRubriqueAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $cadresIds = $request->request->get('ds');
        $cadresIds = explode("|", $cadresIds);
        $idrubrique = $request->request->get('idrubrique');

        foreach ($cadresIds as $key => $value) {

            if (!empty($value)) {
                $uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($value);
                $unerubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($idrubrique);
                $unerubrique->removeCadre($uncadre);
                // return new Response( json_encode(array("result"=>"erreurstatut")));  
            }
        }

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    public function remplirArticleAction(): Response(): Response {
        $em = $this->entityManager;
        $request = $request;

        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax
            $id = $request->request->get('id');

            if ($id != null) {
                $rubrique = $em->getRepository('App\Entity\Rubrique')
                        ->find($id);

                $articles = $em->getRepository("utbAdminBundle:Article")->findBy(array('rubrique' => $rubrique, 'statutArticle' => '4'));
                $article = null;
                $article = array();
                $i = 0;
                foreach ($articles as $unarticle) {

                    $article[$i]['id'] = $unarticle->getId();
                    $article[$i]['titreArticle'] = $unarticle->getTitreArticle();
                    $i++;
                }

                $response = new Response();

                $article = json_encode(array('article' => $article));
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent($article);
                return $response;
            }
        }

        return new Response("Nonnn ....");
    }

}