<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use App\Entity\Media;
use App\Entity\Dimension;
use App\Entity\Rubrique;
use App\Entity\RechercheType;
use App\Entity\Recherche;
use App\Entity\ArticleActualiteType;
use App\Entity\MediaRubriqueType;
use App\Entity\DimensionType;
use App\Entity\MediaRubriqueAjoutType;
use App\Entity\MediaRubriqueAjoutTitreType;
use App\Entity\AffichageArticleType;
use App\Entity\MediaPublicationType;
use App\Entity\ArticleRubriqueType;
use App\Entity\ArticlePublicationType;
use App\Entity\ArticlePublicationModifType;
use App\Entity\ArticleSmallType;
use App\Entity\ArticleActualiteLangueType;
use App\Entity\ArticleRubriqueLangueType;
use App\Entity\ArticlePublicationLangueType;
use App\Entity\ArticleSmallLangueType;
use App\Entity\ArticleChangeRubriqueType;
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
 * ArticleController pour la gestion des articles du site.
 * Pour rappel tout contenu sur le site est un article que ce soit une brève,
 * une actualité, une agence etc.
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 */
class ArticleController extends AbstractController
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
        
    }

    /**
     *  Methode qui s'occupe de l'ajout d'un article 
     * 
     *  Les Formulaires varient suivant  la rubrique 
     * 
     *  type = 2 -- Pour le Formulaire d'ajout d'actualite(ajoutArticleArtualite.html.twig)
     * 
     *  type = 3 -- Pour le Formulaire d'ajout Presentation(ajoutArticleRubrique.html.twig)
     * 
     *  type = 4 -- Pour le Formulaire d'ajout Publication(ajoutArticlePublication.html.twig)
     * 
     *  type = 5 -- Pour le Formulaire d'ajout Faq (ajoutArticleFaq.html.twig)
     * 
     *  type = 6 -- Pour le Formulaire d'ajout Breve (ajoutArticleBreve.html.twig)
     *  
     *  type > 6 -- Pour le Formulaire d'ajout des article des nouvelles Rubriques creee (AjoutArticleRubrique.html.twig)
     * 
     * @var 
     *     
     * Les variables
     *
     * $infostype: represente le box d'information a afficher par rapport au type d'article.
     * 
     * $unerubrique :  variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $larubrique :  Permet d'avoir le nom de la rubrique a laquelle appartient l'article
     * 
     * $listestat :   Pour afficher les statistiques sur le site
     * 
     * $uneimage :    Objet de la classe Media
     * 
     * $user : pour avoir l'identifiant de l'utilisateur connecte
     * 
     * $idarticle :   Variable pour recevoir l'id auquel appartient l'article enregistre
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type   Identifiant de la rubrique  dans laquelle se trouve l'article
     * 
     * @return <string> return le twig d'ajout de formulaire d'un article  suivant la rubrique (ajoutArticle.html.twig)
     * 
     */
    public function ajoutArticleAction(): Response(string $locale, string $type): Response {
        // Code pour gerer la gestion des droits
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        $id = $type;

        $unerubrique = $em->getRepository("utbAdminBundle:Rubrique")->find($type);
        //si la rubrique parent est une faq
        $isfaq = $unerubrique->getIsFaq();

        $unarticle = new Article();
        $unarticle->setRubrique($unerubrique);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($type, $locale);
        //$unarticle->setTranslatableLocale($locale)   ;


        $unedimension = $em->getRepository("utbAdminBundle:Dimension")->find(2);
        //rï¿½cupï¿½ration de l'id 
        $user = $this->security->getToken()->getUser()->getId();
        
        //Donnï¿½e de base et non nulles ï¿½ renseigner
        $unarticle->setArticleAjoutPar($user);
        $unarticle->setStatutArticle(1);
        $unarticle->setLastRubriqueArticle(1);
        // $unarticle->setTypeArticle(2);
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, 0, $type);

        $infostype = null;

        if ($type == 4) {

            $infostype = 20;
        } elseif ($type == 5) {

            $infostype = 17;
        } elseif ($type == 6) {

            $infostype = 18;
        } elseif ($type == 2) {

            $infostype = 19;
        } elseif (($type == 3) || ($type > 6)) {

            $infostype = 21;
        }


        $boxinfos = $this->entityManager
                ->getRepository("utbAdminBundle:Parametrage")
                ->getTexteBoxInfos($locale, $infostype);

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        /**
         * Creation d'image
         * typemedia=1 =>IMAGE
         * IllustreImgMedia = 1 => cette image est une image illustrative de larticle
         */
        //$uneimage->setArticle($unarticle);      
        //Choisir le formulaire a afficher suivant le type 

        if ($type == 2 || $type == 3 || $type > 6 || $type == 4) {

            $uneimage = new Media();

            if ($type == 4) {
                $extensions = array('xls', 'xlsx', 'doc', 'docx', 'pdf');
            } else {
                $extensions = array('jpg', 'png', 'jpeg', 'gif');
            }

            $uneimage->extensions = $extensions;
            if ($uneimage->getUrlMedia() === null) {

                $uneimage->setTypeMedia(0);
                $uneimage->setIllustreImgMedia(1); //
                $uneimage->setTranslatableLocale($locale);
                //$uneimage ->setNomMedia("default");//
                $uneimage->setUrlMedia("default_.png"); //
                $uneimage->setUrlFistMedia("default_.png"); //
                $uneimage->setMediaAjoutPar($user);
                $unarticle->addMedia($uneimage);
            } else {
                $uneimage->setTranslatableLocale($locale);
                $uneimage->setTypeMedia(1);
                $uneimage->setIllustreImgMedia(1); // 
                $uneimage->setMediaAjoutPar($user);
                $unarticle->addMedia($uneimage);
            }
        } else {
            $uneimage = new Media();
             $uneimage->setTranslatableLocale($locale);
            $uneimage->setTypeMedia(0);
            $uneimage->setIllustreImgMedia(1); //
            $uneimage->setNomMedia("default"); //
            $uneimage->setUrlMedia("default_.png"); // 
            $uneimage->setUrlFistMedia("default_.png"); //
            $uneimage->setMediaAjoutPar($user);
            $unarticle->addMedia($uneimage);
        }
        $unarticle->setTranslatableLocale($locale);


        //$em->refresh($unarticle);       
        if ($type == 2) {

            $form = $this->createForm($this->createForm(ArticleActualiteType::class), $unarticle);
        } elseif ($type == 3 || $type > 6) {
            $form = $this->createForm($this->createForm(ArticleRubriqueType::class), $unarticle);
        } elseif ($type == 4) {

            $form = $this->createForm($this->createForm(ArticlePublicationType::class), $unarticle);
        } elseif ($type == 5 || $type == 6) {

            $form = $this->createForm($this->createForm(ArticleSmallType::class), $unarticle);
        }
        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);


        $request = $request;



        if ($request->isMethod('POST')) {



            $form->handleRequest($request);

            $verificateur = $this->utb_admin.ArticleService;
            $verifSaisie = $verificateur->verifSaisie($form->get('titreArticle')->getData(), array('/', '%'));


            /*             * ** Ajout ce 02072013 *** */
            if ((trim($form->get('descriptionArticle')->getData()) == '' && $type != 4) || (!$verifSaisie)) {

                if (trim($form->get('descriptionArticle')->getData()) == '' && $type != 4) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');
                }

                if (!$verifSaisie) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartcarfaux');
                }

                if ($type == 2) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleActualite.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 3 || $type > 6) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleRubrique.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'larubrique' => $larubrique,
                                'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 4) {
                    return $this->render('utbAdminBundle/Article/ajoutArticlePublication.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 5) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleFaq.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 6) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleBref.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'isfaq' => $isfaq,
                    ));
                }
            }
            /*             * ** Fin Ajout ce 02072013 *** */



            if ($uneimage->file !== null && !in_array($uneimage->file->guessExtension(), $extensions)) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');
                if ($type == 2) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleActualite.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 3 || $type > 6) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleRubrique.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'larubrique' => $larubrique,
                                'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'isfaq' => $isfaq,
                    ));
                } elseif ($type == 4) {
                    return $this->render('utbAdminBundle/Article/ajoutArticlePublication.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 5) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleFaq.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 6) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleBref.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                }
            }
            $unarticle = $form->getData();

            //$em->persist($uneimage); 

            if ($type == 2 || $type == 3 || $type > 6 || $type == 4) {

                if ($uneimage->getNomMedia() == '')
                    $uneimage->setNomMedia("default");
                $em->persist($uneimage);
                $uneimage->setUrlFistMedia($uneimage->getUrlMedia());
            }

            $em->persist($unarticle);

            $nomexiste = $this->entityManager
                    ->getRepository('utbAdminBundle/Article')
                    ->getTestNomArticle($unarticle->getTitreArticle(), $type);

            if ($unarticle->getTitreArticle() == "" || $nomexiste != 0 || $unarticle->getDescriptionArticle() == "") {
                if ($nomexiste != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                } elseif ($unarticle->getDescriptionArticle() == "") {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');
                }

                if ($type == 2) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleActualite.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 3 || $type > 6) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleRubrique.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'isfaq' => $isfaq,
                                'listestat' => $listestat,
                    ));
                } elseif ($type == 4) {
                    return $this->render('utbAdminBundle/Article/ajoutArticlePublication.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 5) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleFaq.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                } elseif ($type == 6) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleBref.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
                    ));
                }
            } else {

                try {

                    $em->flush();
                    //recuperation du dernier article enregistre
                    $idarticle = $unarticle->getId();
                    //conversion de la valeur de l'id en entier
                    $idarticle = intval($idarticle);

                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successajtart');
                } catch (Exception $e) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'errorajtrub');
                }
                // $this->image.handling->open($uneimage->getWebPath())
                // ->forceResize($unedimension->getLargeur(),$unedimension->getHauteur())
                // ->save($uneimage->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$uneimage->getUrlMedia());              
            }

            return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                'locale' => $locale, 'type' => $type, 'id' => $idarticle,]));
        }

        // var_dump($type);     
        if ($type == 2) {
            return $this->render('utbAdminBundle/Article/ajoutArticleActualite.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
            ));
        } elseif ($type == 3 || $type > 6) {
            return $this->render('utbAdminBundle/Article/ajoutArticleRubrique.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'larubrique' => $larubrique,
                        'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'infos' => $boxinfos, 'isfaq' => $isfaq,
            ));
        } elseif ($type == 4) {
            return $this->render('utbAdminBundle/Article/ajoutArticlePublication.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
            ));
        } elseif ($type == 5) {
            return $this->render('utbAdminBundle/Article/ajoutArticleFaq.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
            ));
        } elseif ($type == 6) {
            return $this->render('utbAdminBundle/Article/ajoutArticleBref.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'infos' => $boxinfos,
            ));
        }
    }

    /**
     *  Methode permettant d'ajouter les articles dans une autre langue 
     *    
     *  Les Formulaires varient suivant  la rubrique 
     * 
     *  type = 2 -- Pour le Formulaire d'ajout d'actualite(AjoutLangueArticleArtualite.html.twig)
     * 
     *  type = 3 -- Pour le Formulaire d'ajout Presentation(AjoutLangueArticleRubrique.html.twig)
     * 
     *  type = 4 -- Pour le Formulaire d'ajout Publication(AjoutLangueArticlePublication.html.twig)
     * 
     *  type = 5 -- Pour le Formulaire d'ajout Faq (AjoutLangueArticleFaq.html.twig)
     * 
     *  type = 6 -- Pour le Formulaire d'ajout Breve (AjoutLangueArticleBreve.html.twig) 
     * 
     *  type > 6 -- Pour le Formulaire d'ajout des article des nouvelles Rubriques creee (AjoutArticleRubrique.html.twig)
     * 
     * @var 
     *     
     * Les variables
     *
     * $unerubrique :  variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $larubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $listestat :   Pour afficher les statistiques sur le site
     * 
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type   Identifiant de la rubrique dans laquelle se trouve l'article
     * @param <integer> $id   Identifiant ï¿½ laquelle appartient l'article ï¿½ traduire
     * 
     * @return <string> return le twig d'ajout de formulaire d'un article  suivant la rubrique
     * 
     */
    public function ajoutLangueArticleAction(): Response(string $locale, int $id, string $type): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutLangueArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle/Rubrique")
                ->findOneByLocale($type, $locale);

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unarticle = $em->getRepository("admin/Article")->find($id);
        $unarticle->setTranslatableLocale($locale);
        $em->refresh($unarticle);
        
        if ($type == 2) {
            $form = $this->createForm($this->createForm(ArticleActualiteLangueType::class), $unarticle);
        } elseif ($type == 3 || $type > 6) {
            $form = $this->createForm($this->createForm(ArticleRubriqueLangueType::class), $unarticle);
        } elseif ($type == 4) {
            $form = $this->createForm($this->createForm(ArticlePublicationLangueType::class), $unarticle);
        } elseif ($type == 5 || $type == 6) {
            $form = $this->createForm($this->createForm(ArticleSmallLangueType::class), $unarticle);
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, 0, $type);
        $medias = $unarticle->getMedias();

        foreach ($medias as $key => $unmedia) {
            $unmedia->setUrlMedia($unmedia->getUrlMedia());
            $unmedia->setNomMedia($unmedia->getNomMedia());
        }

        $listeRubrique = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);

        $larticleAnglais = $em->getRepository("utbAdminBundle:Article")->find($id);

        /* if($locale=="fr"){ 

          $larticleAnglais->setTranslatableLocale("en");
          $em->refresh($larticleAnglais);

          }else{
          $larticleAnglais->setTranslatableLocale("fr");
          $em->refresh($larticleAnglais);
          } */

        $request = $request;

        if ($request->isMethod('POST')) {



            $form->handleRequest($request);
            $em->persist($unarticle);
            $em->flush();

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successmodart');

            return $this->redirect($this->generateUrl('utb_admin_detailarticle', ['locale' => $locale, 'type' => $type, 'id' => $id,
            ]));
        }
        if ($type == 2) {
            return $this->render('utbAdminBundle/Article/ajoutLangueArticleActualite.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'larticleAnglais' => $larticleAnglais,
            ));
        } elseif ($type == 3 || $type > 6) {
            return $this->render('utbAdminBundle/Article/ajoutLangueArticleRubrique.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'larubrique' => $larubrique, 'listestat' => $listestat,
                        'listeRubrique' => $listeRubrique, 'larticleAnglais' => $larticleAnglais,
            ));
        } elseif ($type == 4) {
            return $this->render('utbAdminBundle/Article/ajoutLangueArticlePublication.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'larticleAnglais' => $larticleAnglais,
            ));
        } elseif ($type == 5) {
            return $this->render('utbAdminBundle/Article/ajoutLangueArticleFaq.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'larticleAnglais' => $larticleAnglais,
            ));
        } elseif ($type == 6) {
            return $this->render('utbAdminBundle/Article/ajoutLangueArticleBref.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'larticleAnglais' => $larticleAnglais,
            ));
        }
    }

    /**
     *
     *  Methode permettant de gerer la suppression des articles 
     * 
     * @var 
     *     
     * Les variables
     * 
     * $unarticle : Objet de la classe Article     * 
     * $medias: Ensemble des medias liés a l'article
     *    
     * @param <integer> $id     Identifiant de l'article
     * @param <string>  $locale, Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type   Identifiant de la rubrique  dans laquelle se trouve l'article
     * 
     * @return <string> return sur le twig de la liste des articles
     * 
     */
    public function supprArticleAction(): Response(int $id, string $locale, string $type): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_listetoutarticle', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unarticle = $em->getRepository("utbAdminBundle:Article")->find($id);
        
        //suppression des medias liés
        $medias = $unarticle->getMedias();
        foreach ($medias as $key => $unmedia) {
            if ($unmedia->getUrlMedia() != "default_.png") {
                if ($unmedia->getTypeMedia() == 1 && $unmedia->getIllustreImgMedia() != 1) {
                    if (file_exists(__DIR__ . '/../../../../web/' . "upload/articles/images/" . $unmedia->getUrlMedia())&& $unmedia->getUrlMedia()!="") {
                        $unmedia->removeUpload(__DIR__ . '/../../../../web/' . "upload/articles/images/" . $unmedia->getUrlMedia());
                        $em->remove($unmedia);
                    }
                } elseif ($unmedia->getTypeMedia() == 2) {
                    if (file_exists(__DIR__ . '/../../../../web/' . "upload/articles/docs/" . $unmedia->getUrlMedia())&& $unmedia->getUrlMedia()!="") {
                        $unmedia->removeUpload(__DIR__ . '/../../../../web/' . "upload/articles/docs/" . $unmedia->getUrlMedia());
                        $em->remove($unmedia);
                    }
                } else {
                    if (file_exists(__DIR__ . '/../../../../web/' . "upload/articles/" . $unmedia->getUrlMedia()) && $unmedia->getUrlMedia()!="") {
                        $unmedia->removeUpload(__DIR__ . '/../../../../web/' . "upload/articles/" . $unmedia->getUrlMedia());
                        $em->remove($unmedia);
                    }
                }
            }
        }

        // Enfin on supprime l'article... 
        $em->remove($unarticle);

        $em->flush();
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successsupart');

        /* ... et on redirige vers la page d'administration des articles */
        return $this->redirect($this->generateUrl('utb_admin_detailrubrique', array(
                            'id' => $type, 'locale' => $locale,)));
    }

    /**
     *
     * Methode permettant de gerer la suppression multiple
     * lorsque des cases Ã  cocher sont selectionnees.
     * 
     * @var 
     *     
     * Les variables
     *
     * 
     * $articlesIds : Tableau des identifiants des articles selectionnes dans le checkbox 
     * $medias : Ensemble des medias lies a un article
     * $unarticle :  variable qui recupere un article suivant la valeur de l'id (On a utilise les getRepository )
     * 
     * 
     * @return <string> une reponse json "json_encode(array("result"=>"success") success pour succes et erreur pour une erreur "
     * 
     */
    function supprAllarticlesAction(): Response {
        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprAllarticlesAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $articlesIds = $request->request->get('articlesIds');

        $articlesIds = explode("|", $articlesIds);
        //return new Response( json_encode($articlesIds));

        foreach ($articlesIds as $key => $value) {
            if (!empty($value)) {
                $unarticle = $em->getRepository("utbAdminBundle:Article")->find($value);

                //suppression des medias liés
                
                $medias = $unarticle->getMedias();
                foreach ($medias as $key => $unmedia) {
                    
                    if ($unmedia->getUrlMedia() != "default_.png" && $unmedia->getUrlMedia() != "") {
                        
                       if (file_exists(__DIR__ . '/../../../../web/' . "upload/articles/" . $unmedia->getUrlMedia()) && $unmedia->getUrlMedia()!="") {  
                            $unmedia->removeUpload(__DIR__ . '/../../../../web/' . "upload/articles/" . $unmedia->getUrlMedia());
                            $em->remove($unmedia);
                       } 
                    }
                }

                $em->remove($unarticle);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *
     * Methode permettant d'autoriser ou non
     * l'affichage d'un correspondant sur la page d'accueil.
     * 
     * @var 
     *     
     * Les variables
     * 
     * $articlesIds : Tableau des identifiants des articles selectionnes dans le checkbox 
     * 
     * $unarticle :  variable qui recupere un article suivant la valeur de l'id (On a utilise les getRepository )
     *
     * 
     * @return <string> une reponse json "json_encode(array("result"=>"success") success pour succï¿½s et erreur pour une erreur "
     * 
     */
    function definirAccueilAction(): Response {
        $em = $this->entityManager;

        /*  $AccessControl =  $this->utb_admin.AccessControl;
          $checkAcces =  $AccessControl->verifAcces($em,'supprAllarticlesAction', $this->container->get );

          if(!$checkAcces){
          return new Response( json_encode(array("result"=>"accessdenied")));
          } */

        $request = $this->requestStack->getCurrentRequest();
        $articlesIds = $request->request->get('articlesIds');
        $etat = $request->request->get('etat');

        $articlesIds = explode("|", $articlesIds);
        //return new Response( json_encode($articlesIds));

        foreach ($articlesIds as $key => $value) {
            if (!empty($value)) {
                $unarticle = $em->getRepository("utbAdminBundle:Article")->find($value);
                //Modification du champ afficheAccueil
                $unarticle->setAfficheAccueil($etat);
                $em->persist($unarticle);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /*
      function supprOnearticleAction(): Response{
      $em =$this->entityManager;

      $AccessControl =  $this->utb_admin.AccessControl;
      $checkAcces =  $AccessControl->verifAcces($em,'supprOnearticleAction', $this->container->get );

      if(!$checkAcces){
      return new Response( json_encode(array("result"=>"accessdenied")));
      }

      $request = $this->requestStack->getCurrentRequest();
      $articleId  = $request->request->get('articlesIds');

      if(!empty($articleId)){
      $unarticle = $em->getRepository("utbAdminBundle:Article")->find($articleId);

      $medias = $unarticle -> getMedias();
      foreach($medias as $key=>$unmedia){
      if($unmedia->getUrlMedia()!="default_.png"){
      $unmedia->removeUpload(__DIR__ . '/../../../../web/'."upload/articles/".$unmedia->getUrlMedia());
      $em->remove($unmedia);
      }
      }


      $em->remove($unarticle);
      $em->flush();
      }
      else{
      return new Response( json_encode(array("result"=>"erreur")));
      }

      return new Response( json_encode(array("result"=>"success")));
      }
     */

    /*
      public function listeArticleAction(): Response(string $locale,string $type): Response
      {
      $em =$this->entityManager;
      $AccessControl =  $this->utb_admin.AccessControl;
      $checkAcces =  $AccessControl->verifAcces($em,'listeArticleAction', $this->container->get );

      if(!$checkAcces){
      $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
      return $this->redirect ( $this->generateUrl('utb_admin_accueil', ['locale' =>$locale]));
      }

      $unerecherhe = new Recherche();
      $form = $this->createForm(RechercheType()::class,  $unerecherhe);


      $this->requestStack->getCurrentRequest()->setLocale($locale);

      $listestat = $this->entityManager
      ->getRepository('App\Entity\Statistique')
      ->getInfoOrStat($typeStat=5 ,$locale ,$valeur=null,$article= null) ;

      // $articleservice =  $this->utb_admin.ArticleService;

      // $listearticle =$articleservice->allArticle($em,$type,$locale);

      return $this->redirect($this->generateUrl('utb_admin_detailrubrique', array(
      'locale' =>$locale,'type'=>$type,'id'=>$type)));

      }
     */

    /**
     *
     * Methode permettant d'afficher un formulaire de choix d'une rubrique quand
     * on veut ajouter un article.
     * @var 
     *     
     * Les variables 
     *    
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type  Identifiant de la rubrique  dans laquelle se trouve l'article
     * 
     * @return <string> return le twig d'ajout de formulaire d'un article  suivant la rubrique
     * 
     */
    public function addArticleAction(): Response(): Response {

        $request = $this->requestStack->getCurrentRequest();
        $formData = $request->request->get('formdata');
        $locale = $request->request->get('locale');
        parse_str($formData, $data);

        $url = $this->generateUrl('utb_admin_ajoutarticle', array(
            'type' => $data['rubrique'], 'locale' => $locale));

        return new Response(json_encode(array("result" => "success", "url" => $url)));
    }

    /**
     *
     * Methode permettant d'avoir la liste de tous les articles du site.
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique : variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique a laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listeRubrique :  la liste des rubrique parent
     * 
     * $listearticle :   Pour avoir la liste des rubriques requete a retrouver dans ArticleRepository  findAllByLocale($locale,$total,$page,$articles_per_page)
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticle.html.twig
     * 
     */
    public function listeToutArticleAction(): Response(string $locale, $page): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeToutArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);

        /* total des resultats */
        $total = $em->getRepository("admin/Article")->getTotalArticleLocale($locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $listearticle = $this->entityManager
                ->getRepository('App\Entity\Article')
                ->findAllByLocale($locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 1, $locale, $valeur = null, $article = null);

        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 5, $locale, $valeur = null, $article = null);

        //var_dump($listestat);
        // $qb = $em->createQueryBuilder();
        //$entities = $em->getRepository("utbAdminBundle:Article")->createQueryBuilder('p')->setFirstResult(($page * $articles_per_page) - $articles_per_page)->setMaxResults($this->container->get->getParameter('max_articles_on_listepage'))->getQuery()->getResult();

        return $this->render('utbAdminBundle/Article/listeArticle.html.twig', array(
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
                    "listeRubrique" => $listeRubrique
        ));
    }

    /**
     *
     * Methode permettant d'ordonner les listes d'articles
     * par drag and drop.
     *
     * @var 
     *     
     * Les variables
     *
     * $nbArticleParPage : Nombre d'articles par page
     * $formData : 
     * $recordIDValue :   
     * $thisOrdre :
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return une reponse ajax
     * 
     */
    public function ordreArticleAction(): Response(string $locale, $page): Response {
        $nbArticleParPage = 20;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();

        $formData = $request->request->get('formdata');
        $data = array();
        parse_str($formData, $data);
        $recordIDValue = $data['recordsArray'];
        // print_r($recordIDValue);
        if ($page <= 1) {
            $rang = 1;
        } else {
            $rang = ($page - 1) * $nbArticleParPage + 1;
        }
        foreach ($recordIDValue as $r => $idarticle) {
            $thisOrdre = $em->getRepository("admin/Article")->findOneBy(array("id" => $idarticle));
            $thisOrdre->setOrdre($rang);
            $em->flush();
            ++$rang;
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *
     * Methode permettant d'ordonner les listes d'articles
     * par drag and drop.
     *
     * @var 
     *     
     * Les variables
     *
     * $allArticle / Liste de tous les articles du site
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return une reponse ajax
     * 
     */
    public function SetArticleOrdreAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $allArticle = $em->getRepository("utbAdminBundle:Article")->findAll();
        $rang = 1;
        foreach ($allArticle as $a) {
            $a->setOrdre($rang);
            $em->flush();
            ++$rang;
        }
        return new Response("success");
    }

    /**
     * Methode utilisee pour la modification d'un article
     * 
     * Les Formulaires de la modification varient aussi suivant la rubrique 
     * 
     * type = 2 -- Pour le Formulaire d'ajout d'actualite(modifArticleArtualite.html.twig)
     * 
     * type = 3 -- Pour le Formulaire d'ajout Presentation(modifArticleRubrique.html.twig)
     * 
     * type = 4 -- Pour le Formulaire d'ajout Publication(modifArticlePublication.html.twig)
     * 
     * type = 5 -- Pour le Formulaire d'ajout Faq (modifArticleFaq.html.twig)
     * 
     * type = 6 -- Pour le Formulaire d'ajout Breve (modifArticleBreve.html.twig)
     *  
     * type > 6 -- Pour le Formulaire d'ajout des article des nouvelles Rubriques creee (AjoutArticleRubrique.html.twig)
     * 
     * @var 
     *     
     * Les variables
     * 
     *  $larubrique : Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     *
     * $user : pour avoir l'identifiant de l'utilisateur connectï¿½
     * 
     * $unarticle : Objet de la classe Article (recupere a l'aide d'un repository)
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     * 
     * @param <string> $id identifiant de l'article qu'on veut modifier 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <string> $type   Identifiant de la rubrique dans laquelle se trouve l'article
     * 
     * @return <string> return le twig formulaire de modification d'un article suivant la rubrique
     * 
     */
    public function modifierArticleAction(): Response(int $id, string $locale, string $type, $nomarticle): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($type, $locale);

        $listeRubrique = //$em->getRepository("admin/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);

        $user = null;
        $user = $this->security->getToken()->getUser()->getId();

        $em = $this->entityManager;

        // Rï¿½cupï¿½ration du article 
        $unarticle = $em->getRepository("utbAdminBundle/Article")->find($id);

        $unarticle->setTranslatableLocale($locale);
        $em->refresh($unarticle);

        // Crï¿½ation d'un forumaire pour lequel on spï¿½cifie qu'il doit correspondre avec une entitï¿½ article 
        if ($type == 2) {
            $form = $this->createForm($this->createForm(ArticleActualiteType::class), $unarticle);
        } elseif ($type == 3 || $type > 6) {
            $form = $this->createForm($this->createForm(ArticleRubriqueType::class), $unarticle);
        } elseif ($type == 4) {
            //$undoc=$unarticle->getMedias(); 
            // var_dump( $undoc);exit;
            $form = $this->createForm($this->createForm(ArticlePublicationModifType::class), $unarticle);
        } elseif ($type == 5 || $type == 6) {
            $form = $this->createForm($this->createForm(ArticleSmallType::class), $unarticle);
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, 0, $type);

        $request = $request;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);


            $unarticle->setArticlemodifpar($user);
            $unarticle->setArticleDateModif(new \DateTime());
            $em->persist($unarticle);

            if (trim($form->get('descriptionArticle')->getData()) == '') {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');

                if ($type == 2) {

                    return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 3 || $type > 6) {

                    return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 4) {

                    return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 5) {

                    return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 6) {

                    return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                }
            }

            if ($nomarticle != $unarticle->getTitreArticle()) {

                $nomexiste = $this->entityManager
                        ->getRepository('utbAdminBundle/Article')
                        ->getTestNomArticle($unarticle->getTitreArticle(), $type);

                if ($nomexiste != 0 || trim($unarticle->getDescriptionArticle()) == "") {
                    if ($nomexiste != 0) {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                    } elseif ($unarticle->getDescriptionArticle() == "") {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');
                    }
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                    if ($type == 2) {

                        return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 3 || $type > 6) {

                        return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 4) {

                        return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 5) {

                        return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 6) {

                        return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    }
                }
            }

            $em->flush();

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successmodart');
            return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                'locale' => $locale, 'id' => $id, 'type' => $type,
            ]));
        }


        if ($type == 2) {

            return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 3 || $type > 6) {

            return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 4) {

            return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 5) {

            return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 6) {

            return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        }
    }

    /**
     *  Cette methode permet de modifier les articles dont le statut est valide
     * 
     *  Les Formulaires de la modification varie aussi suivant la rubrique 
     * 
     * type = 2 -- Pour le Formulaire d'ajout d'actualite(modifArticleArtualite.html.twig)
     * 
     * type = 3 -- Pour le Formulaire d'ajout Presentation(modifArticleRubrique.html.twig)
     * 
     * type = 4 -- Pour le Formulaire d'ajout Publication(modifArticlePublication.html.twig)
     * 
     * type = 5 -- Pour le Formulaire d'ajout Faq (modifArticleFaq.html.twig)
     * 
     * type = 6 -- Pour le Formulaire d'ajout Breve (modifArticleBreve.html.twig)
     *  
     * type > 6 -- Pour le Formulaire d'ajout des article des nouvelles Rubriques creee (AjoutArticleRubrique.html.twig)
     * 
     * @var 
     *     
     * Les variables
     * 
     * $larubrique : Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     *
     * $user :  pour avoir l'identifiant de l'utilisateur connectï¿½
     * 
     * $unarticle : Objet de la classe Article (recupere a l'aide d'un repository)
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     * 
     * @param <string> $id identifiant de l'article qu'on veut modifier 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <string> $type   Identifiant de la rubrique dans laquelle se trouve l'article
     * 
     * @return <string> return le twig d'ajout de formulaire d'un article  suivant la rubrique
     * 
     */
    public function modifierArticleValideAction(): Response(int $id, string $locale, string $type, $nomarticle): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierArticleValideAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $em = $this->entityManager;
        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($type, $locale);
        // Rï¿½cupï¿½ration du article

        $listeRubrique = //$em->getRepository("admin/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);
        //var_dump($listeRubrique);
        $user = null;
        $user = $this->security->getToken()->getUser()->getId();

        $unarticle = $em->getRepository("utbAdminBundle/Article")->find($id);

        // Crï¿½ation d'un forumaire pour lequel on spï¿½cifie qu'il doit correspondre avec une entitï¿½ article 
        if ($type == 2) {
            $form = $this->createForm($this->createForm(ArticleActualiteType::class), $unarticle);
        } elseif ($type == 3 || $type > 6) {
            $form = $this->createForm($this->createForm(ArticleRubriqueType::class), $unarticle);
        } elseif ($type == 4) {
            $form = $this->createForm($this->createForm(ArticlePublicationType::class), $unarticle);
        } elseif ($type == 5 || $type == 6) {
            $form = $this->createForm($this->createForm(ArticleSmallType::class), $unarticle);
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, 0, $type);

        $request = $request;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $em->persist($unarticle);

            /*             * ** Ajout ce 02072013 *** */
            if (trim($form->get('descriptionArticle')->getData()) == '') {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');

                if ($type == 2) {

                    return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 3 || $type > 6) {

                    return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 4) {

                    return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 5) {

                    return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 6) {

                    return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                }
            }
            /*             * ** Fin Ajout ce 02072013 *** */

            if ($nomarticle != $unarticle->getTitreArticle()) {

                $nomexiste = $this->entityManager
                        ->getRepository('utbAdminBundle/Article')
                        ->getTestNomArticle($unarticle->getTitreArticle(), $type);
                if ($nomexiste != 0 || $unarticle->getDescriptionArticle() == "") {
                    if ($nomexiste != 0) {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                    } elseif ($unarticle->getDescriptionArticle() == "") {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');
                    } $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                    if ($type == 2) {

                        return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 3 || $type > 6) {

                        return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 4) {

                        return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 5) {

                        return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 6) {

                        return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    }
                }
            }


            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successmodifart');
            return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                'locale' => $locale, 'id' => $id, 'type' => $type,
            ]));
        }


        if ($type == 2) {
            return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 3 || $type > 6) {
            return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 4) {
            return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 5) {
            return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 6) {
            return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        }
    }

    /**
     *  Cette methode permet de modifier les articles dont  le statut est publie
     * 
     *  Les Formulaires de la modification varie aussi suivant la rubrique 
     * 
     * type = 2 -- Pour le Formulaire d'ajout d'actualite(modifArticleArtualite.html.twig)
     * 
     * type = 3 -- Pour le Formulaire d'ajout Presentation(modifArticleRubrique.html.twig)
     * 
     * type = 4 -- Pour le Formulaire d'ajout Publication(modifArticlePublication.html.twig)
     * 
     * type = 5 -- Pour le Formulaire d'ajout Faq (modifArticleFaq.html.twig)
     * 
     * type = 6 -- Pour le Formulaire d'ajout Breve (modifArticleBreve.html.twig) 
     * 
     * type > 6 -- Pour le Formulaire d'ajout des article des nouvelles Rubriques creee (AjoutArticleRubrique.html.twig)
     * 
     * @var 
     *     
     * Les variables
     * 
     * $larubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     *
     * $user :  pour avoir l'identifiant de l'utilisateur connectï¿½
     * 
     * $unarticle : Objet de la classe Article (recupere a l'aide d'un repository)
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository
     * 
     * @param <integer> $id identifiant de l'article qu'on veut modifier 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type   Identifiant de la rubrique dans laquelle se trouve l'article      
     * @return <string> return le twig d'ajout de formulaire d'un article  suivant la rubrique
     * 
     */
    public function modifierArticlePublieAction(): Response(int $id, string $locale, string $type, $nomarticle): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierArticlePublieAction', $this->container->get);
       $nomarticle=  addslashes($nomarticle);
        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($type, $locale);
        // Rï¿½cupï¿½ration du article 

        $listeRubrique = //$em->getRepository("admin/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);

        $user = null;
        $user = $this->security->getToken()->getUser()->getId();
        $unarticle = $em->getRepository("utbAdminBundle/Article")->find($id);

        // Crï¿½ation d'un forumaire pour lequel on spï¿½cifie qu'il doit correspondre avec une entitï¿½ article 
        if ($type == 2) {
            $form = $this->createForm($this->createForm(ArticleActualiteType::class), $unarticle);
        } elseif ($type == 3 || $type > 6) {
            $form = $this->createForm($this->createForm(ArticleRubriqueType::class), $unarticle);
        } elseif ($type == 4) {
            $form = $this->createForm($this->createForm(ArticlePublicationType::class), $unarticle);
        } elseif ($type == 5 || $type == 6) {
            $form = $this->createForm($this->createForm(ArticleSmallType::class), $unarticle);
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 3, $locale, 0, $type);

        $request = $request;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $em->persist($unarticle);

            /*             * ** Ajout ce 02072013 *** */
            if (trim($form->get('descriptionArticle')->getData()) == '') {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');

                if ($type == 2) {

                    return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 3 || $type > 6) {

                    return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 4) {

                    return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 5) {

                    return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                } elseif ($type == 6) {

                    return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                    ));
                }
            }
            /*             * ** Fin Ajout ce 02072013 *** */


            if ($nomarticle != $unarticle->getTitreArticle()) {

                $nomexiste = $this->entityManager
                        ->getRepository('utbAdminBundle/Article')
                        ->getTestNomArticle($unarticle->getTitreArticle(), $type);

                if ($nomexiste != 0 || $unarticle->getDescriptionArticle() == "") {
                    if ($nomexiste != 0) {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                    } elseif ($unarticle->getDescriptionArticle() == "") {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');
                    }
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                    if ($type == 2) {

                        return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 3 || $type > 6) {

                        return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 4) {

                        return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 5) {

                        return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    } elseif ($type == 6) {

                        return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                                    'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
                        ));
                    }
                }
            }


            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successmodifart');
            return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                'locale' => $locale, 'id' => $id, 'type' => $type,
            ]));
        }


        if ($type == 2) {
            return $this->render('utbAdminBundle/Article/modifArticleActualite.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 3 || $type > 6) {
            return $this->render('utbAdminBundle/Article/modifArticleRubrique.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 4) {
            return $this->render('utbAdminBundle/Article/modifArticlePublication.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 5) {
            return $this->render('utbAdminBundle/Article/modifArticleFaq.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        } elseif ($type == 6) {
            return $this->render('utbAdminBundle/Article/modifArticleBref.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'nomarticle' => $unarticle->getTitreArticle(),
            ));
        }
    }

    /**
     *  Cette methode permet d'ajouter une image illustrative a un article.
     * 
     * @var 
     *     
     * Les variables
     * 
     * $larubrique :  Permet d'avoir le nom de la rubrique a laquelle appartient l'article
     *
     * $user :  pour avoir l'identifiant de l'utilisateur connecte
     * 
     * $unmedia : Objet de la classe Media
     * 
     * $extensions : Tableau de la liste des extensions acceptees lors de l'ajout d'un media
     * 
     * $unarticle : Objet de la classe Article (recupere a l'aide d'un repository)
     * 
     * $listestat :     Liste de statistiques type 2 comme explique dans StatistiqueRepository
     * 
     * @param <string> $id identifiant de l'article qu'on veut modifier 
     * @param <string> $typemedia 1:image | 2:document
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <string> $type   Identifiant de la rubrique passee dans laquelle se trouve l'article
     * 
     * @return <string> return le twig ajoutMediaArticle.html.twig
     * 
     */
    public function ajoutMediaArticleAction(): Response(int $id, string $locale, $typemedia, string $type): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;

        $checkAcces = $AccessControl->verifAcces($em, 'ajoutMediaArticleAction', $this->container->get);


        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }



        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);
        $nomArticle = $em->getRepository("utbAdminBundle:Article")->find($id);
        //var_dump($nomArticle);
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unarticle = $em->getRepository("utbAdminBundle:Article")->find($id);
        $unedimension = $em->getRepository("utbAdminBundle:Dimension")->find(2);

        $larubrique = $this->entityManager
                ->getRepository("admin/Rubrique")
                ->findOneByLocale($type, $locale);

        $unmedia = new Media();


        if ($typemedia == 1) {

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.media.nomimgvide');

            $extensions = array('jpg', 'png', 'jpeg', 'gif');
        } elseif ($typemedia == 2) {

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.media.nomdocvide');

            $extensions = array('xls', 'xlsx', 'doc', 'docx', 'pdf');
        }
        $unmedia->extensions = $extensions;


        if ($typemedia == 3) {

            //$msgnotification = '';
            //$msgnotification =  $this->translator->trans('notification.media.nomimgvide');           

            $unmedia->setTypeMedia(1);
            $unmedia->setIllustreImgMedia(1);
        } else {

            $unmedia->setTypeMedia($typemedia);
            // $unmedia ->setMediaAjoutPar(1);
            $unmedia->setIllustreImgMedia(0);
        }
        $unmedia->setMediaAjoutPar(1);
        $unarticle->addMedia($unmedia);
        $unmedia->setUrlFistMedia("----");

        if ($typemedia == 1) {

            $form = $this->createForm($this->createForm(MediaRubriqueAjoutType::class), $unmedia);
        } elseif ($typemedia == 2) {

            $form = $this->createForm($this->createForm(MediaPublicationType::class), $unmedia);
        }
        //$unmedia ->setDimension($unedimension->getId());

        $request = $request;

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 2, $locale, $valeur = 0, $article = $id);


        if ($request->isMethod('POST')) {

            $form->handleRequest($request);


            $saisieControl = $this->utb_admin.ArticleService;
            $verifvide = $saisieControl->verifVide($form->get('nomMedia')->getData());
            if ($verifvide === true) {


                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('artinexistant', $msgnotification);

                return $this->render('utbAdminBundle/Article/ajoutImageArticle.html.twig', array('id' => $id,
                            'form' => $form->createView(), 'locale' => $locale, 'typemedia' => $typemedia, 'type' => $type,
                            'listestat' => $listestat, 'larubrique' => $larubrique, 'listeRubrique' => $listeRubrique,
                            'nomArticle' => $nomArticle,
                ));
            }


            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {

                if ($typemedia == 1) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');
                } else {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficdocart');
                }

                return $this->render('utbAdminBundle/Article/ajoutImageArticle.html.twig', array('id' => $id,
                            'form' => $form->createView(), 'locale' => $locale, 'typemedia' => $typemedia, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                            'listeRubrique' => $listeRubrique, 'nomArticle' => $nomArticle,
                ));
            }

            $unmedia = $form->getData();
            //var_dump($unmedia->getUrlMedia());

            if ($unmedia->getUrlMedia() == "non") {
                return $this->render('utbAdminBundle/Article/ajoutImageArticle.html.twig', array('id' => $id,
                            'form' => $form->createView(), 'locale' => $locale, 'typemedia' => $typemedia, 'type' => $type,
                            'listestat' => $listestat, 'listeRubrique' => $listeRubrique, 'nomArticle' => $nomArticle,
                ));
            }

            $em->persist($unmedia);
            $em->flush();
            // $this->image.handling->open($unmedia->getWebPath())
            //  ->forceResize($unedimension->getLargeur(),$unedimension->getHauteur())
            //  ->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unarticle->getNomArticle().".".'jpg');              
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successajoutmediaart');
            return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                'locale' => $locale, 'id' => $id, 'typemedia' => $typemedia, 'type' => $type,]));
        }

        return $this->render('utbAdminBundle/Article/ajoutImageArticle.html.twig', array('id' => $id,
                    'form' => $form->createView(), 'locale' => $locale, 'typemedia' => $typemedia, 'type' => $type,
                    'listestat' => $listestat, 'larubrique' => $larubrique, 'listeRubrique' => $listeRubrique,
                    'nomArticle' => $nomArticle,
        ));
    }

    /**
     * Cette methode permet de gerer l'ajout d'autres images, documents a un article
     * mis a part l'image illustrative
     * 
     * @var 
     *     
     * Les variables
     * 
     * $larubrique /  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     *
     * $user :  pour avoir l'identifiant de l'utilisateur connectï¿½
     * 
     * $unmedia : Objet de la classe Media
     * 
     * $extensions : Tableau de la liste des extensions acceptees lors de l'ajout d'un media
     * 
     * $unarticle : Objet de la classe Article (recupere a l'aide d'un repository)
     * 
     * $listestat :     Liste de statistiques type 2 comme explique dans StatistiqueRepository
     * 
     * @param <string> $id identifiant de l'article a modifier 
     * @param <string> $typemedia 1- image | 2- document
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <string> $type   Identifiant de la rubrique passee dans laquelle se trouve l'article
     * 
     * @return <string> return le twig ajoutMedias.html.twig
     * 
     */
    public function ajoutMediasAction(): Response(int $id, string $locale, $typemedia, string $type): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;

        $checkAcces = $AccessControl->verifAcces($em, 'ajoutMediasAction', $this->container->get);


        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        
        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);
        
        $nomArticle = $em->getRepository("utbAdminBundle:Article")->find($id);

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $unarticle = $em->getRepository("utbAdminBundle:Article")->find($id);
        $unedimension = $em->getRepository("utbAdminBundle:Dimension")->find(2);

        $larubrique = $this->entityManager
                ->getRepository("admin/Rubrique")
                ->findOneByLocale($type, $locale);

        $unmedia = new Media();
        $unmedia->setTranslatableLocale($locale);
        
        if ($typemedia == 1) {

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.media.nomimgvide');

            $extensions = array('jpg', 'png', 'jpeg', 'gif');
        } elseif ($typemedia == 2) {

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.media.nomdocvide');

            $extensions = array('xls', 'xlsx', 'doc', 'docx', 'pdf');
        }
        $unmedia->extensions = $extensions;

        $unmedia->setUrlFistMedia("----");
        if ($typemedia == 3) {

            //$msgnotification = '';
            //$msgnotification =  $this->translator->trans('notification.media.nomimgvide');           

            $unmedia->setTypeMedia(1);
            $unmedia->setIllustreImgMedia(1);
        } else {

            $unmedia->setTypeMedia($typemedia);
            // $unmedia ->setMediaAjoutPar(1);
            $unmedia->setIllustreImgMedia(0);
        }
        $unmedia->setMediaAjoutPar(1);
        $unarticle->addMedia($unmedia);


        if ($typemedia == 1) {

            $form = $this->createForm($this->createForm(MediaRubriqueAjoutType::class), $unmedia);
        } elseif ($typemedia == 2) {

            $form = $this->createForm($this->createForm(MediaPublicationType::class), $unmedia);
        }
        //$unmedia ->setDimension($unedimension->getId());

        $request = $request;

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 2, $locale, 0,$id);


        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
       

            /*$saisieControl = $this->utb_admin.ArticleService;
            $verifvide = $saisieControl->verifVide($form->get('nomMedia')->getData());
            if ($verifvide === true) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('artinexistant', $msgnotification);

                return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                    'locale' => $locale, 'id' => $id, 'typemedia' => $typemedia, 'type' => $type,]));
            }*/


            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {
                
                if ($typemedia == 1) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');
                } else {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficartdoc');
                }

                return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                    'locale' => $locale, 'id' => $id, 'typemedia' => $typemedia, 'type' => $type,]));
            }

            $unmedia = $form->getData();
           // var_dump($unmedia->getUrlMedia());exit;

            if ($unmedia->getUrlMedia() == "non") {
                return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                    'locale' => $locale, 'id' => $id, 'typemedia' => $typemedia, 'type' => $type,]));
            }

            $em->persist($unmedia);
            $em->flush();
            // $this->image.handling->open($unmedia->getWebPath())
            //  ->forceResize($unedimension->getLargeur(),$unedimension->getHauteur())
            //  ->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unarticle->getNomArticle().".".'jpg');              
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successajoutmediaart');
            return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                'locale' => $locale, 'id' => $id, 'typemedia' => $typemedia, 'type' => $type,]));
        }

        return $this->render('utbAdminBundle/Article/ajoutMedias.html.twig', array('id' => $id,
                    'form' => $form->createView(), 'locale' => $locale, 'typemedia' => $typemedia, 'type' => $type,
        ));
    }

    /**
     * Cette methode permet de modifier l'image illustrative d'un article.
     * 
     * @var 
     *     
     * Les variables
     * 
     * $larubrique /  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     *
     * $user :   pour avoir l'identifiant de l'utilisateur connecte
     * 
     * $unmedia : Objet de la classe Media
     * 
     * $extensions : Tableau de la liste des extensions acceptees lors de l'ajout d'un media
     * 
     * $unarticle : Objet de la classe Article (recupere a l'aide d'un repository)
     * 
     * $listestat :     Liste de statistiques type 2 comme explique dans StatistiqueRepository
     * 
     * @param <integer> $id identifiant de l'article dont on veut modifier l'image illustrative
     * @param <integer> $idmedia identifiant de l'image a modifier
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type   Identifiant de la rubrique passee dans laquelle se trouve l'article
     * 
     * @return <string> return le twig modifMediaArticle.html.twig
     * 
     */
    public function modifMediaArticleAction(): Response(int $id, $idmedia, string $locale, string $type, $typemedia): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;

        $checkAcces = $AccessControl->verifAcces($em, 'modifMediaArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getListeDeRubriques($locale);
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unarticle = $em->getRepository("utbAdminBundle/Article")->find($id);
        //$unedimension = $em->getRepository("utbAdminBundle:Dimension")->find(2);
        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($type, $locale);

        $unmedia = $em->getRepository("utbAdminBundle:Media")->find($idmedia);
        $unmedia->setTranslatableLocale($locale);
        $unmedia->setNomMedia("");
        $extensions = array('jpg', 'png', 'jpeg', 'gif');
        $unmedia->extensions = $extensions;
        //$unmedia ->setTypeMedia(1);
        //var_dump($unmedia);
        $unmedia->setUrlMedia("");
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 2, $locale,0, $id);
        
        $image = $this->entityManager
                ->getRepository('App\Entity\Article')
                ->getListeMediaIllust($idmedia, 1, $locale); 
                $lecount = count($image);
        // $typemedia=1;
        //$unmedia ->setDimension($unedimension->getId());
        $form = $this->createForm($this->createForm(MediaRubriqueAjoutType::class), $unmedia);
        $request = $request;
        

        if ($request->isMethod('POST')) {

            //supprimer l'ancienne image
            //$em->remove($unmedia);
            //    
            // $em->flush();
            //Modification de l'image
            $form->handleRequest($request);

            //var_dump($unmedia->file->guessExtension());
            //var_dump($unmedia->extensions);

            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');

                return $this->render('utbAdminBundle/Article/modifImageArticle.html.twig', array('id' => $id, 'idmedia' => $idmedia,
                            'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'listestat' => $listestat, 'larubrique' => $larubrique,
                            'unarticle' => $unarticle,
                ));
            }

            $saisieControl = $this->utb_admin.ArticleService;
            $verifvide = $saisieControl->verifVide($form->get('nomMedia')->getData());
            if ($verifvide === true) {
                return $this->render('utbAdminBundle/Article/ajoutImageArticle.html.twig', array('id' => $id,
                            'form' => $form->createView(), 'locale' => $locale, 'typemedia' => $typemedia, 'type' => $type,
                            'listestat' => $listestat, 'larubrique' => $larubrique, 'listeRubrique' => $listeRubrique,
                            'unarticle' => $unarticle, 'typemedia' => $typemedia,
                ));
            }
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
            $unmedia->setUrlFistMedia($unmedia->getUrlMedia());
            //$unarticle->addMedia($unmedia); 
            //$em->persist($unarticle); 
            $em->flush();
            
            $nouveauUrl=$unmedia->getUrlMedia();
            $newmedia = $em->getRepository("admin/Media")->find($idmedia);
           // var_dump($nouveauUrl);exit;
            
            if($newmedia->getAjoutmodifMedia()==2 && $lecount != 1 ){
                
                $newmedia->setTranslatableLocale($locale);
                $em->refresh($newmedia);
                $newmedia->setUrlMedia($nouveauUrl);
                $newmedia->setPositionMedia(3);
                $newmedia->setAjoutmodifMedia(2);
                $em->persist($newmedia);
                $em->flush();
            }            
            
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successmodifmediaart');
            //fonctions du Bundle Gregwar pour redimensionner les images
            //$this->image.handling->open($unmedia->getWebPath())
            //->resize($unedimension->getLargeur(),$unedimension->getHauteur())
            // ->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unarticle->getNomArticle().".".'jpg');  
            // $this->image.handling->open($unmedia->getWebPath())
            // ->forceResize($unedimension->getLargeur(),$unedimension->getHauteur())
            //->save($unmedia->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$unmedia->getUrlMedia());                              

            return $this->redirect($this->generateUrl('utb_admin_detailarticle', ['id' => $id, 'locale' => $locale, 'type' => $type]));
        }

        return $this->render('utbAdminBundle/Article/modifImageArticle.html.twig', array('id' => $id, 'idmedia' => $idmedia,
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat, 'type' => $type, 'larubrique' => $larubrique,
                    'typemedia' => $typemedia, 'listeRubrique' => $listeRubrique, 'unarticle' => $unarticle,
        ));
    }

    /**
     * Cette methode permet de modifier l'image illustrative d'un article.
     * 
     * @var 
     *     
     * Les variables
     * 
     * $larubrique /  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     *
     * $user :   pour avoir l'identifiant de l'utilisateur connecte
     * 
     * $unmedia : Objet de la classe Media
     * $genre: genre=1: fichier ou document | genre!=1: image,video etc
     * 
     * $extensions : Tableau de la liste des extensions acceptees lors de l'ajout d'un media
     * 
     * $unarticle : Objet de la classe Article (recupere a l'aide d'un repository)
     * 
     * $listestat :     Liste de statistiques type 2 comme explique dans StatistiqueRepository
     * 
     * @param <integer> $id identifiant de l'article dont on veut modifier l'image illustrative
     * @param <integer> $idmedia identifiant de l'image a modifier
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type   Identifiant de la rubrique passee dans laquelle se trouve l'article
     * 
     * @return <string> return le twig modifMediaArticle.html.twig
     * 
     */
    public function modifierMediasAction(): Response(int $id, $idmedia, string $locale, string $type, string $genre, $typemedia): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;


        $checkAcces = $AccessControl->verifAcces($em, 'modifierMediasAction', $this->container->get);
        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $image = $this->entityManager
                ->getRepository('App\Entity\Article')
                ->getListeMediaIllust($idmedia, 0, $locale); 
        $lecount = count($image);

        $unarticle = $em->getRepository("utbAdminBundle:Article")->find($id);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($type, $locale);

        $unmedia = $em->getRepository("admin/Media")->find($idmedia);
        $unmedia->setTranslatableLocale($locale);
        $unmedia->setNomMedia("");

        if ($genre == 1) {

            $extensions = array('jpg', 'png', 'jpeg', 'gif');
        } else {

            $extensions = array('xls', 'xlsx', 'doc', 'docx', 'pdf');
        }
        $unmedia->extensions = $extensions;

        //$unmedia->setUrlMedia("");
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 2, $locale,0, $id);
        //$typemedia=1;
        if ($genre == 1) {
            $form = $this->createForm($this->createForm(MediaPublicationType::class), $unmedia);
        } else {
            $form = $this->createForm($this->createForm(MediaRubriqueAjoutType::class), $unmedia);
        }

        $request = $request;
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {

                if ($genre == 1) {

                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficart');
                } else {

                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypficartdoc');
                }

                return $this->redirect($this->generateUrl('utb_admin_detailarticle', ['id' => $id, 'locale' => $locale, 'type' => $type]));
            }

            $urlimage=$image[0]['urlMedia'];
           // var_dump($urlimage); var_dump($unmedia->getUrlMedia()); exit;
            
            if (  $urlimage!= null && $unmedia->getUrlMedia()!="default_.png" && $urlimage !="default_.png") { 
                //var_dump($urlimage); var_dump($unmedia->getUrlMedia()); exit;
                $unmedia->setTranslatableLocale($locale);                
                $em->refresh($unmedia);
                $unmedia->setPositionMedia(1);
                $unmedia->setAjoutmodifMedia(1);
                
            }elseif ($urlimage == null) {  
                
                $unmedia->setTranslatableLocale($locale);                
                $unmedia->setAjoutmodifMedia(2);
                //$unmedia->setPositionMedia(3);
                
            }elseif ($unmedia->getUrlMedia() == "default_.png" ) {
                
                $urlimage = null;
                $unmedia->setTranslatableLocale($locale);                
                $unmedia->setAjoutmodifMedia(2);
                //$unmedia->setPositionMedia(3);
            }else{
              // var_dump($unmedia->getUrlMedia());exit;
                $urlimage=null;
                $unmedia->setTranslatableLocale($locale);                
                $unmedia->setAjoutmodifMedia(2);               
            }
            
            
            $em->persist($unmedia);
            //var_dump($unmedia->getPositionMedia());var_dump($unmedia->getAjoutmodifMedia());var_dump($unmedia->getUrlMedia());
            // var_dump($em); exit;
            $em->flush();
            //var_dump($unmedia->getPositionMedia());var_dump($unmedia->getAjoutmodifMedia());var_dump($unmedia->getUrlMedia());exit;
            //enregistrement de l'image enanglais
            $nouveauUrl=$unmedia->getUrlMedia();
            //$nouveauUrlFist=$unmedia->getUrlFistMedia();
            $newmedia = $em->getRepository("utbAdminBundle/Media")->find($idmedia);
            //var_dump($unmedia->getUrlMedia()); exit;
            if($newmedia->getAjoutmodifMedia()==2 && $urlimage == null){
               // var_dump('Oui'); exit;
                $newmedia->setTranslatableLocale($locale);
                $em->refresh($newmedia);
                $newmedia->setUrlMedia($nouveauUrl);
                $newmedia->setUrlFistMedia($nouveauUrl);
                $newmedia->setPositionMedia(3);
                $newmedia->setAjoutmodifMedia(1);
                $em->persist($newmedia);
                $em->flush();
            }
            
            $em->clear();
            $_media = $em->getRepository("utbAdminBundle/Media")->find($idmedia);
            $_media->setPositionMedia(2);
            $em->persist($_media);
            $em->flush();

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successmodifmediaart');

            return $this->redirect($this->generateUrl('utb_admin_detailarticle', ['id' => $id, 'locale' => $locale, 'type' => $type]));
        }
        if ($genre == 1) {
            return $this->render('utbAdminBundle/Article/modifierDocMedias.html.twig', array('id' => $id, 'idmedia' => $idmedia,
                        'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat, 'type' => $type, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'typemedia' => $typemedia,
            ));
        } else {
            return $this->render('utbAdminBundle/Article/modifierMedias.html.twig', array('id' => $id, 'idmedia' => $idmedia,
                        'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat, 'type' => $type, 'larubrique' => $larubrique,
                        'listeRubrique' => $listeRubrique, 'typemedia' => $typemedia,
            ));
        }
    }

    /**
     *  Cette methode permet de supprimer l'image illustrative d'un article.
     * 
     * @var 
     *     
     * Les variables
     * 
     * $idmedia/ identifiant du media a supprimer
     * 
     * @param <integer> $id identifiant de l'article dont on veut supprimer l'image
     * illustrative 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type   Identifiant de la rubrique passee dans laquelle se trouve l'article
     * 
     * @return <string> return le twig oneArticle.html.twig
     * 
     */
    public function supprImageIllustrativeAction(): Response(int $id, $idmedia, string $locale, string $type): Response {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprImageIllustrativeAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unmedia = $em->getRepository("utbAdminBundle:Media")->find($idmedia);
        $unmedia->setTranslatableLocale($locale);
        $em->refresh($unmedia);
        
        if (file_exists(__DIR__ . '/../../../../web/' . "upload/articles/" . $unmedia->getUrlMedia())&& $unmedia->getUrlMedia()!="") {
            $unmedia->removeUpload(__DIR__ . '/../../../../web/' . "upload/articles/" . $unmedia->getUrlMedia());
        }        
            
        $unmedia->setNomMedia("default");
        $unmedia->setUrlMedia("default_.png");

        $em->persist($unmedia);
        $em->flush();
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successupimg');
        return $this->redirect($this->generateUrl('utb_admin_detailarticle', ['id' => $id, 'locale' => $locale, 'type' => $type]));
    }

    /**
     *  Cette methode permet d'afficher le detail d'un article.
     * 
     * 
     * @var 
     *     
     * Les variables
     * 
     * $larubrique :  Permet d'avoir le nom de la rubrique a laquelle appartient l'article
     *
     * $user :   pour avoir l'identifiant de l'utilisateur connecte
     * 
     * $article : Objet de la classe Article (recupere a l'aide d'un repository)
     * 
     * $compteur :
     * $genre:    
     * $lastrub:    
     * $unerub:   
     * $anciennerub:  
     * $larticleFrancais  
     * $image   La liste des images associees a un article. 
     * $documents  La liste des documents associees a un article. 
     * $unmedia : Objet de la classe Media 
     * $unarticle : Objet de la classe Article (recupere a l'aide d'un repository)
     * $listestat :     Liste de statistiques type 2 comme explique dans StatistiqueRepository 
     * @param <integer> $id identifiant de l'article
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $type   Identifiant de la rubrique passee dans laquelle se trouve l'article
     * @return <string> return le twig oneArticle.html.twig
     * 
     */
    public function detailsArticleAction(): Response(int $id, string $locale, string $type, string $genre): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'detailsArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);


        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($type, $locale);

        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        $listeSelection = $this->getDoctrine()->getRepository(utbAdminBundle:Rubrique::class)->getListeRubMoov($type, $locale);


        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();
        //edem - formulaire de changement de la rubrique
        $larticle = $em->getRepository("utbAdminBundle:Article")->find($id);
        if ( $larticle != null )  {
            $larticle->setTranslatableLocale($locale);
            $em->refresh($larticle);
        }   
        //var_dump($larticle);  
        $request = $this->requestStack->getCurrentRequest();

        if ($request->getMethod() != 'POST') {
            // Rï¿½cupï¿½rations 
            //if ( $em->getRepository("utbAdminBundle:Article")->findOneBy(array('id'=>$id)) != null  ){
            $titrearticle = $em->getRepository("utbAdminBundle:Article")->findOneBy(array('id' => $id))->getTitreArticle();
            //}
            $compteur = $em->getRepository("utbAdminBundle:Article")->findOneBy(array('id' => $id))->getCompteurArticle();

            $unerub = $em->getRepository("utbAdminBundle:Article")->find($id)->getRubrique();
            $lastrub = $em->getRepository("utbAdminBundle:Rubrique")->find($unerub)->getId(); //Va aussi servir ï¿½ tester la rub interactif(idrubrique=11)
            $anciennerub = $em->getRepository("admin/Rubrique")->find($unerub)->getNomRubrique();

            //creation du formulaire pour la modal box

            $form = $this->createFormBuilder()
                    /* ->add('rubrique', 'entity', array(
                      'class'    => 'utbAdminBundle/Rubrique',
                      'property' => 'nomRubrique',
                      'required' => 'true',
                      'attr'   =>  array('class'=>'idrubrique','id'=>'idrubrique'),
                      'query_builder' => function (\Doctrine\ORM\EntityRepository $repository)
                      {
                      return $repository->
                      createQueryBuilder('l')
                      ->where('l.id != ?1')
                      ->setParameter(1, '11');
                      },
                      'multiple' => false)) */
                    ->add('afficheDatePublie', 'choice', array(
                        'choices' => array('1' => 'Oui', '0' => 'Non'),
                        'required' => false,
                        'label' => 'Afficher la date publication sur le site /',
                        'multiple' => false,
                        'expanded' => true,))
                    ->add('afficheReference', 'choice', array(
                        'choices' => array('1' => 'Oui', '0' => 'Non'),
                        'required' => false,
                        'label' => "Afficher les références de l'article sur le site / ",
                        'multiple' => false,
                        'expanded' => true,))
                    ->add('afficheAuteur', 'choice', array(
                        'choices' => array('1' => 'Oui', '0' => 'Non'),
                        'required' => false,
                        'label' => "Afficher l'auteur sur le site :",
                        'multiple' => false,
                        'expanded' => true,))
                    ->add('idarticle', 'hidden', array('data' => $id,))
                    ->add('lastrub', 'hidden', array('data' => $lastrub,))
                    ->add('compteur', 'hidden', array('data' => $compteur,))
                    ->add('titrearticle', 'text', array('read_only' => true, 'data' => $titrearticle, 'label' => 'Titre article'))
                    ->add('anciennerub', 'text', array('read_only' => true, 'data' => $anciennerub, 'label' => 'Rubrique actuelle'))
                    ->getForm();
        }

        //traitement des modifs       
        //fin changement de la rubrique edem

        $larticleFrancais = $this->entityManager
                ->getRepository("utbAdminBundle:Article")
                ->findOneByLocale($id, $locale);
        //$em->refresh($larticleFrancais); 
        $larticleAnglais = $em->getRepository("utbAdminBundle:Article")->find($id);

        $lautrearticle = $this->entityManager
                ->getRepository("utbAdminBundle:Article")
                ->listeMemeRubriqueLocale($type, $locale, $id);

        // pour avoir les images
        //var_dump($listestat);  

        $image = $this->entityManager
                ->getRepository('App\Entity\Article')
                ->getListeMedia($id, 1, $locale);
        // pour avoir les documents joints
        $documents = $this->entityManager
                ->getRepository('App\Entity\Article')
                ->getListeMedia($id, 2, $locale);

        //Type de la rubrique (pas de deplacement pour les articles de la rubrique interactif      

        if ($request->getMethod() == 'POST') {

            if ($genre == 1) {

                $larticle->setAfficheAuteur($request->request->get('afficheAuteur'));
                $larticle->setAfficheDatePublie($request->request->get('afficheDatePublie'));
                $larticle->setAfficheReference($request->request->get('afficheReference'));

                //$form->handleRequest($request); 
                //var_dump($larticle);
                $em->persist($larticle);
                $em->flush();

                return $this->redirect($this->generateUrl('utb_admin_detailarticle', [
                                    'locale' => $locale, 'id' => $id, 'type' => $type,
                ]));
            } else {

                $formData = $request->request->get('formdata');
                $em = $this->entityManager;
                $data = array();
                parse_str($formData, $data);
                //return new Response(json_encode($data['form']['rubrique']));Ceci est un commentaire !!!!!!!!!!!!

                try {
                    $article = $em->getRepository("utbAdminBundle:Article")->find($data['form']['idarticle']);
                    $nomrub = $em->getRepository("utbAdminBundle:Rubrique")->find($data['form']['rubrique']);
                    $article->setRubrique($nomrub);
                    //controle pour voir si l'on doit modifier lastRubrique ou pas
                    $c = $data['form']['compteur'];
                    if ($c == 0) {
                        $article->setLastRubriqueArticle($data['form']['lastrub']);
                        $article->setCompteurArticle(1);
                    }
                    $em->persist($article);
                    $em->flush();
                    return new Response(json_encode(array("result" => "success", "data" => $data)));
                } catch (exception $e) {
                    return new Response(json_encode(array("result" => "failed", "data" => $data)));
                }
            }
        }

        return $this->render('utbAdminBundle/Article/oneArticle.html.twig', array(
                    'form' => $form->createView(), 'interactif' => $lastrub,
                    'larticleFrancais' => $larticleFrancais, 'larticleAnglais' => $larticleAnglais,
                    'image' => $image, 'documents' => $documents, 'locale' => $locale, 'type' => $type,
                    'lautrearticle' => $lautrearticle, /* 'listestat'=>$listestat, */ 'larubrique' => $larubrique,
                    'listeuser' => $listeuser, 'listeRubrique' => $listeRubrique, 'testimage' => 0, 'testdoc' => 0,
                    'listeSelection' => $listeSelection, 'larticle' => $larticle
        ));
    }

    /**
     *  Permet de gerer les statuts des articles sur la page " liste de tous les articles ".
     * 
     * Cette methode est appellee lors d'une appel Ajax (fonction utilisee : gererAll( articlesId,etat))
     * 
     * etat=1 -- Article en cours de redaction
     * 
     * etat=2 -- Etat pour soumettre des articles
     * 
     * etat=3 -- Etat pour valider des articles
     * 
     * etat=4 -- Etat pour publier des articles
     * 
     * etat=5 -- Etat pour depublier des articles
     * 
     * @var 
     *     
     * Les variables
     * 
     * $articlesIds Tableau des identifiants des articles selectionnes dans le checkbox
     *
     * $etat :   Etat d'un article
     * 
     * $unarticle : Objet de la classe Article recupere grace au repository
     * 
     * @param <integer> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * @param <integer> $etat  permet de changer l'etat ï¿½ laquelle appartient l'article
     * 
     * @return <string> return une reponse json suivant le deroulement de l'operation (twig: listeArticle.html.twig)
     * 
     */
    function gererAllarticlesAction(): Response {

        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $articlesIds = $request->request->get('articlesIds');

        $etat = $request->request->get('etat');
        $articlesIds = explode("|", $articlesIds);

        $result_traitement = 0;

        $AccessControl = $this->utb_admin.AccessControl;
        if ($etat == 2) {
            $checkAcces = $AccessControl->verifAcces($em, 'soumettreAllArticleAction', $this->container->get);
        } elseif ($etat == 3) {
            $checkAcces = $AccessControl->verifAcces($em, 'validerAllArticleAction', $this->container->get);
        } elseif ($etat == 4) {
            $checkAcces = $AccessControl->verifAcces($em, 'publierAllArticlesAction', $this->container->get);
        } elseif ($etat == 5) {
            $checkAcces = $AccessControl->verifAcces($em, 'depublierAllArticlesAction', $this->container->get);
        } elseif ($etat == 6) {
            $checkAcces = $AccessControl->verifAcces($em, 'rejeterAllArticlesAction', $this->container->get);
        }


        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($articlesIds as $key => $value) {

            if (!empty($value)) {

                $unarticle = $em->getRepository("admin/Article")->find($value);

                if ($etat == 3) {//valider
                    if ($etat != $unarticle->getStatutArticle()) {
                        if (($unarticle->getStatutArticle() == 2) || ($unarticle->getStatutArticle() == 5)) {
                            $unarticle->setArticleDateValide(new \Datetime());
                            $unarticle->setArticleValidePar($user);
                            $unarticle->setStatutArticle($etat);
                        } else {
                            //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                            $result_traitement = 1;
                        }
                    } else {
                        // return new Response( json_encode(array("result"=>"erreurstatut"))); 
                        $result_traitement = 1;
                    }
                }

                if ($etat == 4) { //Actions/ publier   
                    if ($etat != $unarticle->getStatutArticle()) {
                        if ($unarticle->getStatutArticle() == 3) {
                            $unarticle->setArticleDatePublie(new \Datetime());
                            $unarticle->setArticlePubliePar($user);
                            $unarticle->setStatutArticle($etat);
                        } else {
                            //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                            $result_traitement = 1;
                        }
                    } else {
                        //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                        $result_traitement = 1;
                    }
                }

                if ($etat == 5) {//depublier
                    if ($etat != $unarticle->getStatutArticle()) {
                        if ($unarticle->getStatutArticle() == 4) {
                            $unarticle->setArticleDateDepublie(new \Datetime());
                            $unarticle->setArticleDepubliePar($user);
                            $unarticle->setStatutArticle($etat);
                        } else {
                            //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                            $result_traitement = 1;
                        }
                    } else {
                        //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                        $result_traitement = 1;
                    }
                }

                if ($etat == 2) {//Soumettre
                    if ($etat != $unarticle->getStatutArticle()) {
                        if (( ($unarticle->getStatutArticle() == 1) || ($unarticle->getStatutArticle() == 6) ) && ($unarticle->getArticleAjoutPar() == $user)) {
                            $unarticle->setStatutArticle($etat);
                        } else {
                            //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                            $result_traitement = 1;
                        }
                    } else {
                        //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                        $result_traitement = 1;
                    }
                }

                if ($etat == 6) {//rejeter des articles
                    if ($etat != $unarticle->getStatutArticle()) {
                        if (($unarticle->getStatutArticle() == 2)) {
                            $unarticle->setStatutArticle($etat);
                        }
                        else
                            $result_traitement = 1;
                    }else {
                        //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                        $result_traitement = 1;
                    }
                }

                $em->persist($unarticle);
                $em->flush();
            }
        }

        if ($result_traitement == 1) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        } elseif ($result_traitement == 0) {
            return new Response(json_encode(array("result" => "success")));
        } elseif ($result_traitement == 2) {
            return new Response(json_encode(array("result" => "errorsubpers")));
        }
    }
    

    /**
     *
     * Methode permettant d'avoir la liste des articles de la corbeille
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique : variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listearticle :   Pour avoir la liste des rubriques requete a retrouver dans ArticleRepository          ->findAllByLocaleCorbeille($locale,$total,$page,$articles_per_page) 
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     * 
     * $boxinfos :
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticleCorbeille.html.twig
     * 
     */
    public function listeArticleCorbeilleAction(): Response($page, string $locale): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeArticleCorbeilleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        /* total des rï¿½sultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalListeCorbeilleLocale($locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $listearticle = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('admin/Article')
                ->findAllByLocaleCorbeille($locale, $total, $page, $articles_per_page);

        // Type Stat =2  -----> infos sur un article donnï¿½
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 5, $locale, $valeur = 0, $article = null);


        $boxinfos = $this->entityManager
                ->getRepository("utbAdminBundle/Parametrage")
                ->getTexteBoxInfos($locale, 3);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();
        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        return $this->render('utbAdminBundle/Article/listeArticleCorbeille.html.twig', array(
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat,
                    'infos' => $boxinfos,
                    'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     *  Permet de restaurer les articles dans la corbeille.
     * 
     * Cette methode est utilisee lors d'une appel Ajax
     * 
     * @var
     * 
     * Les variables
     * 
     * $articlesIds : Tableau des identifiants des articles selectionnes dans le checkbox
     * 
     * 
     * @param <integer> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * 
     * @return une reponse Json
     * 
     */
    function restaureAllarticlesAction(): Response {
        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'restaureAllarticlesAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $articlesIds = $request->request->get('articlesIds');
        $articlesIds = explode("|", $articlesIds);

        $user = null;
        $user = $this->security->getToken()->getUser()->getId();

        foreach ($articlesIds as $key => $value) {
            if (!empty($value)) {
                $unarticle = $em->getRepository("utbAdminBundle:Article")->find($value);
                //$unarticle->setArticleRestaurePar();
                $unarticle->setCorbeilleArticle(0); //corbeilleArticle=1:en corbeille- =0:pas dans la corbeille
                $unarticle->setArchiveArticle(0);
                $unarticle->setArticleRestaurePar($user);
                $unarticle->setArticleDateRestaure(new \DateTime());

                $em->persist($unarticle);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Permet de restaurer un article.
     * 
     * Cette methode est utilisee lors d'une appel Ajax
     * 
     * @var
     * 
     * Les variables
     * 
     * $articlesIds : Identifiant de l'article
     * 
     * 
     * @param <integer> $articlesIds Identifiant de l'article
     * 
     * @return une reponse Json
     * 
     */
    function restaureArticleAction(): Response {
        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'restaureArticleAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $articleId = $request->request->get('articlesIds');

        $user = null;
        $user = $this->security->getToken()->getUser()->getId();


        if (!empty($articleId)) {
            $unarticle = $em->getRepository("admin/Article")->find($articleId);
            //$unarticle->setArticleRestaurePar();
            $unarticle->setCorbeilleArticle(0); //corbeilleArticle=1/en corbeille- =0:pas dans la corbeille
            $unarticle->setArchiveArticle(0);
            $unarticle->setArticleRestaurePar($user);
            $unarticle->setArticleDateRestaure(new \DateTime());

            $em->persist($unarticle);
            $em->flush();
        } else {
            
        }


        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *
     * Methode permettant d'avoir la liste des articles en attente de publication
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique : variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listearticle :   Pour avoir la liste des rubriques requete a retrouver dans ArticleRepository          ->findAllByLocaleCorbeille($locale,$total,$page,$articles_per_page) 
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticleCorbeille.html.twig
     * 
     */
    public function listeArticleAttenteAction(): Response($page, string $locale): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeArticleAttenteAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        /* total des rï¿½sultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalAttenteLocale($locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        //liste articles en attente
        $listearticle = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('admin/Article')
                ->findAllByLocaleAttente($locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 5, $locale, $valeur = 0, $article = null);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 1, $locale, $valeur = 0, $article = null);

        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);



        return $this->render('utbAdminBundle/Article/listeArticleAttente.html.twig', array(
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat1,
                    'listestat1' => $listestat,
                    'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     *  Permet de publier  les articles selectionnes.
     * 
     * Cette methode est utilisee lors d'une appel Ajax
     * 
     * @var
     * 
     * Les variables
     * 
     * $articlesIds : Tableau des identifiants des articles selectionnes dans le checkbox
     * 
     * @param <integer> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * 
     * @return <string> return un reponse Json
     * 
     */
    function publierAllarticlesAction(): Response {
        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'publierAllarticlesAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $articlesIds = $request->request->get('articlesIds');
        $articlesIds = explode("|", $articlesIds);
        foreach ($articlesIds as $key => $value) {
            if (!empty($value)) {
                $unarticle = $em->getRepository("utbAdminBundle:Article")->find($value);
                //$unarticle->setArticleRestaurePar();
                $unarticle->setStatutArticle(4); //statutArticle=4:publiï¿½
                $em->persist($unarticle);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Permet d'achiver les articles.
     * 
     * Cette methode est utilisee lors d'une appel Ajax
     * 
     * @var
     * 
     * Les variables
     * 
     * $articlesIds : Tableau des identifiants des articles selectionnes dans le checkbox
     * 
     * @param <string> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * 
     * @return <string> return une reponse json
     * 
     */
    function archiveAllarticlesAction(): Response {
        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'archiveAllarticlesAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $result_trait = true;

        $request = $this->requestStack->getCurrentRequest();
        $user = null;
        $user = $this->security->getToken()->getUser()->getId();
        $articlesIds = $request->request->get('articlesIds');
        $articlesIds = explode("|", $articlesIds);
        foreach ($articlesIds as $key => $value) {
            if (!empty($value)) {
                $unarticle = $em->getRepository("admin/Article")->find($value);
                //$unarticle->setArticleRestaurePar();
                if ($unarticle->getStatutArticle() != 4) {
                    $unarticle->setArchiveArticle(1);
                    $unarticle->setArticleDateArchive(new \Datetime());
                    $unarticle->setArticleArchivePar($user);
                } else {
                    /*
                      $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('archivageimpossible', "Archivage impossible pour les articles de ce statut!");
                      return $this->redirect ( $this->generateUrl('utb_admin_listetoutarticle', ['locale' =>$locale]));
                     */
                    //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                    $result_trait = false;
                }
                $em->persist($unarticle);
                $em->flush();
            }
        }

        if ($result_trait == false) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        } else {
            return new Response(json_encode(array("result" => "success")));
        }
    }

    /**
     *
     * Methode permettant d'avoir la liste des articles archivés
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique / variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listearticle :   Pour avoir la liste des rubriques requete a retrouver dans ArticleRepository          ->findAllByLocaleCorbeille($locale,$total,$page,$articles_per_page) 
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticleCorbeille.html.twig
     * 
     */
    function archiveArticleAction(): Response {

        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'archiveArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $user = null;
        $user = $this->security->getToken()->getUser()->getId();

        $articleId = $request->request->get('articlesIds');

        if (!empty($articleId)) {
            $unarticle = $em->getRepository("admin/Article")->find($articleId);
            if ($unarticle->getStatutArticle() != 4) {
                $unarticle->setArchiveArticle(1);
                $unarticle->setArticleDateArchive(new \Datetime());
                $unarticle->setArticleArchivePar($user);
            } else {
                /*
                  $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('archivageimpossible', "Archivage impossible pour les articles de ce statut!");
                  return $this->redirect ( $this->generateUrl('utb_admin_listetoutarticle', ['locale' =>$locale]));
                 */
                return new Response(json_encode(array("result" => "erreurstatut")));
            }
            $em->persist($unarticle);
            $em->flush();
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *
     * Methode permettant d'avoir la liste des articles en attente de publication
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique / variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listearticle :   Pour avoir la liste des rubriques requete a retrouver dans ArticleRepository          ->findAllByLocaleCorbeille($locale,$total,$page,$articles_per_page) 
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticleCorbeille.html.twig
     * 
     */
    function corbeilleArticleAction(): Response {

        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'corbeilleArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $user = null;
        $user = $this->security->getToken()->getUser()->getId();

        $articleId = $request->request->get('articlesIds');

        if (!empty($articleId)) {
            $unarticle = $em->getRepository("utbAdminBundle:Article")->find($articleId);
            if ($unarticle->getStatutArticle() != 4) {
                $unarticle->setCorbeilleArticle(1);
                $unarticle->setArticleDateSupprime(new \Datetime());
                $unarticle->setArticleSupprimePar($user);
            } else {
                /*
                  $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('archivageimpossible', "Archivage impossible pour les articles de ce statut!");
                  return $this->redirect ( $this->generateUrl('utb_admin_listetoutarticle', ['locale' =>$locale]));
                 */
                return new Response(json_encode(array("result" => "erreurstatut")));
            }
            $em->persist($unarticle);
            $em->flush();
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Permet de mettre dans le corbeille tous les articles.
     * 
     * Cette methode est utilisee lors d'une appel Ajax
     *
     * @var
     * 
     * Les variables
     * 
     * $articlesIds : Tableau des identifiants des articles selectionnes dans le checkbox
     *  
     * @param <string> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * 
     * @return <string> une reponse Json
     * 
     */
    function corbeilleAllarticlesAction(): Response {
        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'corbeilleAllarticlesAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $resultat_trait = true;

        $request = $this->requestStack->getCurrentRequest();
        $user = null;
        $user = $this->security->getToken()->getUser()->getId();

        $articlesIds = $request->request->get('articlesIds');
        $articlesIds = explode("|", $articlesIds);
        foreach ($articlesIds as $key => $value) {
            if (!empty($value)) {
                $unarticle = $em->getRepository("admin/Article")->find($value);
                //var_dump($value);
                // var_dump($unarticle);
                if ($unarticle->getStatutArticle() != 4) {
                    $unarticle->setCorbeilleArticle(1); // 
                    $unarticle->setArticleSupprimePar($user);
                    $unarticle->setArticleDateSupprime(new \Datetime());
                } else {
                    /*
                      $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('suppressionimpossible', "Suppression impossible pour les articles de ce statut!");
                      return $this->redirect ( $this->generateUrl('utb_admin_listetoutarticle', ['locale' =>$locale]));
                     */
                    $resultat_trait = false;
                    //return new Response( json_encode(array("result"=>"erreurstatut"))); 
                }
                $em->persist($unarticle);
                $em->flush();
            }
        }
        if ($resultat_trait == false) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        } else {
            return new Response(json_encode(array("result" => "success")));
        }
    }

    /**
     *
     * Methode permettant d'avoir la liste des articles achives
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique / variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listearticle :   Pour avoir la liste des rubriques requete a retrouver dans ArticleRepository          ->findAllByLocaleCorbeille($locale,$total,$page,$articles_per_page) 
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticleCorbeille.html.twig
     * 
     */
    public function listeArticleArchiveAction(): Response($page, string $locale): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeArticleArchiveAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        /* total des rï¿½sultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalListeArchiveLocale($locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $listearticle = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('admin/Article')
                ->findAllByLocaleArchive($locale, $total, $page, $articles_per_page);

        // Type Stat =2  -----> infos sur un article donnï¿½
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 5, $locale, $valeur = 0, $article = null);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        $boxinfos = $this->entityManager
                ->getRepository("utbAdminBundle/Parametrage")
                ->getTexteBoxInfos($locale, 5);
        $listeRubrique = //$em->getRepository("utbAdminBundle:Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        return $this->render('utbAdminBundle/Article/listeArticleArchive.html.twig', array(
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'listestat' => $listestat,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'infos' => $boxinfos,
                    'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     *
     * Methode permettant d'avoir la liste des articles soumis pour validation
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique :  variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listearticle :   Pour avoir la liste des rubriques requete a retrouver dans ArticleRepository          ->findAllByLocaleCorbeille($locale,$total,$page,$articles_per_page) 
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 1 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticleCorbeille.html.twig
     * 
     */
    public function listeArticleSoumisAction(): Response($page, string $locale): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeArticleSoumisAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        /* total des rï¿½sultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalSoumisLocale($locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        //liste articles en attente
        $listearticle = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('admin/Article')
                ->findAllByLocaleSoumis($locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 5, $locale, $valeur = 0, $article = null);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 1, $locale, $valeur = 0, $article = null);

        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        return $this->render('utbAdminBundle/Article/listeArticleSoumis.html.twig', array(
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat1,
                    'listestat1' => $listestat,
                    'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     *  Methode utilisee lors de l'action deplacer sur d'un article
     * 
     * $articleId : identifiant ï¿½ laquelle appartient l'article a deplacer
     * 
     * $rubriqueId : identifiant de la nouvelle rubrique
     * 
     * $user : identifant de l'utilisateur connecte 
     * 
     * @param <integer> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * 
     * @return <string> return une reponse Json
     * 
     */
    function modifierRubriqueArticleAction(): Response {
        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierRubriqueArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $articleId = $request->request->get('articleId');
        $rubriqueId = $request->request->get('rubriqueId');
        $user = null;
        $user = $this->security->getToken()->getUser()->getId();

        if (!empty($articleId)) {
            $unarticle = $em->getRepository("admin/Article")->find($articleId);

            $unarticle->setRubrique($rubriqueId);

            $em->persist($unarticle);
            $em->flush();
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Methode utilisee pour la suppression d'un document ou une image sur la page de detail d'un article.
     * 
     * Methode utilisee lors d'un appel ajax 
     * 
     * @var
     * 
     * Les variables
     * 
     * $articlesIds / Tableau des identifiants des articles selectionnes dans le checkbox
     *  
     * @param <string> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * 
     * @param <integer> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * 
     * @return <string> return une reponse json (oneArticle.html.twig)
     * 
     */
    function corbeilleMediaArticleAction(): Response {

        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'corbeilleMediaArticleAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $articlesIds = $request->request->get('articlesIds');

        $articlesIds = explode("|", $articlesIds);

        foreach ($articlesIds as $key => $value) {

            if (!empty($value)) {

                $unmedia = $em->getRepository("admin/Media")->find($value);

                //$unarticle->setArticleRestaurePar();
                $unmedia->setIllustreImgMedia(5);

                //$unmedia->removeUpload(__DIR__ . '/../../../../web/'."upload/".$unmedia->getUrlMedia());

                $em->remove($unmedia);

                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *
     * Methode permettant d'avoir la liste des articles recement publies
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique /  variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listearticle :   Pour avoir la liste des rubriques [ requete a retrouver dans ArticleRepository  ->findAllByLocaleCorbeille($locale,$total,$page,$articles_per_page)] 
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 5 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valides
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticleCorbeille.html.twig
     * 
     */
    public function listeArticleRecentAction(): Response(string $locale, $page): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeArticleRecentAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        /* total des rï¿½sultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalArticleRecentLocale($locale);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $listearticle = $this->entityManager
                ->getRepository('admin/Article')
                ->findAllRecentByLocale($locale, $total, $page, $articles_per_page);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        $listestat1 = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 1, $locale, $valeur = 0, $article = null);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 5, $locale, $valeur = 0, $article = null);

        //var_dump($listestat);
        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);
        // $qb = $em->createQueryBuilder();
        //$entities = $em->getRepository("utbAdminBundle:Article")->createQueryBuilder('p')->setFirstResult(($page * $articles_per_page) - $articles_per_page)->setMaxResults($this->container->get->getParameter('max_articles_on_listepage'))->getQuery()->getResult();

        return $this->render('utbAdminBundle/Article/listeArticleRecent.html.twig', array(
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
                    'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     *  Methode utilisee dans la depublication des articles publies sur la page de la liste des articles.
     * 
     * Methode utilisee lors d'un appel ajax 
     * 
     * @var
     * 
     * Les variables
     * 
     * $articlesIds : Tableau des identifiants des articles selectionnes dans le checkbox
     *  
     * @param <string> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * @param <integer> $articlesIds Identifiant ï¿½ laquelle appartient l'article
     * 
     * @return <string> return une reponse json (oneArticle.html.twig)
     * 
     */
    function depublierAllarticlesAction(): Response {
        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'depublierAllarticlesAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $articlesIds = $request->request->get('articlesIds');
        $articlesIds = explode("|", $articlesIds);
        foreach ($articlesIds as $key => $value) {
            if (!empty($value)) {
                $unarticle = $em->getRepository("admin/Article")->find($value);
                //$unarticle->setArticleRestaurePar();
                $unarticle->setStatutArticle(5); //statutArticle=4/publiï¿½
                $em->persist($unarticle);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *
     * Methode permettant d'avoir la liste des articles para statut
     *
     * @var 
     *     
     * Les variables
     *
     * $unerubrique :  variable qui recoit la rubrique dans laquelle on va enregistrer l'article
     * 
     * $unarticle : Objet de la classe Article
     * 
     * $listeRubrique :  Permet d'avoir le nom de la rubrique ï¿½ laquelle appartient l'article
     * 
     * $total :  Total des articles sur selectionne
     * 
     * $articles_per_page :    Le nombre d'article selectionnï¿½ par page
     *
     * $last_page :   le id de page suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listearticle :   Pour avoir la liste des rubriques requete a retrouver dans ArticleRepository          ->findAllByLocaleCorbeille($locale,$total,$page,$articles_per_page) 
     * 
     * $listeuser :    Pour avoir la liste des utilisateurs
     * 
     * $listestat :     Liste de statistiques type 1 comme explique dans StatistiqueRepository
     * 
     * $listestat1 :   Avoir les  articles soumis et valide
     *     
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer>  $page   Variable passee pour gerer la pagination
     * 
     * @return <string> return le twig listeArticleCorbeille.html.twig
     * 
     */
    public function listeArticleParStatutAction(): Response($page, string $locale, $statut, int $id): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;

        $AccessControl = $this->utb_admin.AccessControl;

        if ($statut == 0) {
            $checkAcces = $AccessControl->verifAcces($em, 'listeArticleHorsligneAction', $this->container->get);
        } elseif ($statut == 1) {
            $checkAcces = $AccessControl->verifAcces($em, 'listeArticleEcrAction', $this->container->get);
        } elseif ($statut == 2) {
            $checkAcces = $AccessControl->verifAcces($em, 'listeArticleSoumisAction', $this->container->get);
        } elseif ($statut == 3) {
            $checkAcces = $AccessControl->verifAcces($em, 'listeArticleValideAction', $this->container->get);
        } elseif ($statut == 4) {
            $checkAcces = $AccessControl->verifAcces($em, 'listeArticleRecentAction', $this->container->get);
        } elseif ($statut == 5) {
            $checkAcces = $AccessControl->verifAcces($em, 'listeArticleDepubAction', $this->container->get);
        } elseif ($statut == 6) {
            $checkAcces = $AccessControl->verifAcces($em, 'listeArticleRejeteAction', $this->container->get);
        }

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        /* total des rï¿½sultats */
        $total = $em->getRepository("utbAdminBundle/Article")->getTotalArticleParStatut($locale, $statut, $id);

        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        //liste articles en attente
        $listearticle = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('admin/Article')
                ->findAllByStatutLocale($statut, $locale, $page, $articles_per_page, $id);

        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();

        if ($id == 0) {

            $listestat = $this->entityManager
                    ->getRepository('App\Entity\Statistique')
                    ->getInfoOrStat($typeStat = 1, $locale, $valeur = 0, $article = 0);

            $listestat1 = $this->entityManager
                    ->getRepository('App\Entity\Statistique')
                    ->getInfoOrStat($typeStat = 5, $locale, $valeur = 0, $article = 0);
        } else {

            $listestat = $this->entityManager
                    ->getRepository('App\Entity\Statistique')
                    ->getInfoOrStat($typeStat = 7, $locale, $valeur = 0, $article = $id);

            $listestat1 = $this->entityManager
                    ->getRepository('App\Entity\Statistique')
                    ->getInfoOrStat($typeStat = 3, $locale, $valeur = 0, $article = $id);
        }

        $listeRubrique = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);

        $larubriquemere = null;
        if ($id != 0) {
            $larubriquemere = $em->getRepository("utbAdminBundle:Rubrique")->findOneBy(array("id" => $id));
        }

        return $this->render('utbAdminBundle/Article/listeArticleStatut.html.twig', array(
                    'listearticle' => $listearticle,
                    'listeuser' => $listeuser,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat1,
                    'listestat1' => $listestat,
                    'listeRubrique' => $listeRubrique,
                    'statut' => $statut,
                    'larubriquemere' => $larubriquemere,
                    'id' => $id,
        ));
    }

    /**
     * Methode permettant d'ajouter les dimensions - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $unedimension: Instance de la classe Dimension a ajouter
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutDimension.html.twig
     *  
     */
    public function ajoutDimensionsMediaAction(): Response(int $id, $idmedia, string $locale, string $type): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutDimensionsMediaAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
             
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unedimension = new Dimension();
        $form = $this->createForm($this->createForm(DimensionType::class), $unedimension);

        $unmedia = $em->getRepository("admin/Media")->find($idmedia);

        $request = $request;

        $unmedia->setTranslatableLocale($locale);
        $em->refresh($unmedia);
        
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            $unedimension = $form->getData();

            $test = 0;
            try {
                $t = (int) $unedimension->getLargeur(); //$form->get('Largeur')->getData();
                $p = (int) $unedimension->getHauteur(); //$form->get('hauteur')->getData();
                if (($p != 0) && ($t != 0)) {
                    $test = 1;
                }
            } catch (Exception $e) {
                $test = 0;
            }
            //var_dump($t);
            //var_dump($p);

            if (($test == 1) and ($p != 0) and ($t != 0)) {

                $em->persist($unedimension);

                $unmedia->setDimension($unedimension);


                $this->image.handling->open($unmedia->getUploadRootDir() . "/" . $unmedia->getUrlFistMedia())
                        ->forceResize($unedimension->getLargeur(), $unedimension->getHauteur())
                        // ->save($uneimage->getUploadDir()."/".$unedimension->getLargeur()."x".$unedimension->getHauteur()."/".$uneimage->getUrlMedia());              
                        ->save($unmedia->getUploadDir() . "/" . $unedimension->getLargeur() . "x" . $unedimension->getHauteur() . "/" . $unmedia->getUrlMedia());

                $unmedia->setUrlMedia($unedimension->getLargeur() . "x" . $unedimension->getHauteur() . "/" . $unmedia->getUrlMedia());
                $em->persist($unmedia);

                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Dimension ajouté avec succès');

                return $this->redirect($this->generateUrl('utb_admin_detailarticle', ['locale' => $locale, 'type' => $type, 'id' => $id,]));
            } else {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', "erreurnombre");
                return $this->redirect($this->generateUrl('utb_admin_detailarticle', ['locale' => $locale, 'type' => $type, 'id' => $id,]));
            }
        }

        return $this->render('utbAdminBundle/Article/ajoutDimensionsMedia.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'type' => $type, 'id' => $id, 'idmedia' => $idmedia,));
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
    public function listeCadreArticleAction(): Response(int $id, string $type, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeCadreArticleAction', $this->container->get);

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
                ->findOneByLocale2($type, $locale);

        $larticleFrancais = $this->entityManager
                ->getRepository("utbAdminBundle/Article")
                ->findOneByLocale2($id, $locale);

        $listecadre = $this->entityManager
                ->getRepository('App\Entity\Article')
                ->getListeCadre($id, $locale);



        //$listecadre = $em->getRepository("utbAdminBundle:Cadre")->findAll();


        return $this->render('utbAdminBundle/Article/listeCadreArticle.html.twig', array('listecadre' => $listecadre, 'listestat' => $listestat,
                    'id' => $id, 'larubrique' => $larubrique, 'larticle' => $larticleFrancais, 'locale' => $locale,));
    }

    /**
     *  Methode qui s'occupe de l'ajout d'un cadre
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un cadre(ajoutCadre.html.twig)
     * 
     */
    public function ajoutCadreArticleAction(): Response(int $id, string $type, string $locale): Response {

        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutCadreArticleAction', $this->container->get);

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
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale2($type, $locale);

        $larticle = $this->entityManager
                ->getRepository("admin/Article")
                ->findOneByLocale2($id, $locale);

        $listecadre = $this->entityManager
                ->getRepository('utbAdminBundle/Article')
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
                ->getRepository('utbAdminBundle/Article')
                ->getListeCadreAbsent($ids, $locale);
        //exit;
        //$listecadre = $em->getRepository("utbAdminBundle/Cadre")->findAll();


        return $this->render('utbAdminBundle/Article/ajoutCadre.html.twig', array('listecadre' => $listecadre, 'listestat' => $listestat,
                    'larubrique' => $larubrique, 'larticle' => $larticle,
                    'listecadreabsent' => $listecadreabsent, 'locale' => $locale,));
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
    public function modifierCadreArticleAction(): Response(int $id, $idarticle, string $type, string $locale): Response {

        $unmedia = null;
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierCadreArticleAction', $this->container->get);

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
                ->findOneByLocale2($type, $locale);

        //var_dump($larubrique);

        $larticle = $this->entityManager
                ->getRepository("utbAdminBundle:Article")
                ->findOneByLocale2($idarticle, $locale);

        // Récupération du cadre 
        $uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($id);

        $uncadre->setTranslatableLocale($locale);

        if ($uncadre != null) {
            $unmedia = $uncadre->getMedias();
        }
        $typecadre = $uncadre->getTypeCadre(); //type du cadre pr organiser les champs Ã  afficher sur le twig de modif       
        $untypecadre = $em->getRepository("utbAdminBundle:TypeCadre")->find($typecadre);
        //$uncadre = $em->getRepository("admin/Cadre")->find($id);
        //$uncadre->addMedia($unmedia->getId());
        //recuperation de l'id 
        $user = $this->security->getToken()->getUser()->getId();

        //Donnée de base et non nulles Ã  renseigner
        $uncadre->setCadreModifPar($user);
        //$uncadre->setNatureCadre(1);// 1=cadre de base, 2=cadre utilisateur
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité cadre 
        $form = $this->createForm($this->createForm(CadreType::class), $uncadre);

        // On récupère les données du formulaire si il a déjÃ  été passé 
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

            return $this->redirect($this->generateUrl('utb_admin_listecadrearticle', array('id' => $idarticle, 'type' => $type, 'locale' => $locale)));
        }
        return $this->render('utbAdminBundle/Article/modifCadre.html.twig', array(
                    'form' => $form->createView(), 'listestat' => $listestat, 'unmedia' => $unmedia, 'untypecadre' => $untypecadre, 'id' => $id,
                    'typecadre' => $typecadre, 'larubrique' => $larubrique, 'larticle' => $larticle, 'locale' => $locale,));
    }

    /**
     * Methode gerant la modification  de l'image Ã  une cadre 
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
    public function modifMediaCadreArticleAction(): Response(int $id, $idarticle, string $type, $idmedia, string $locale): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifMediaCadreArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //$idmedia = intval($idmedia);
        //var_dump($idmedia);
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale2($type, $locale);

        //var_dump($larubrique);

        $larticle = $this->entityManager
                ->getRepository("utbAdminBundle:Article")
                ->findOneByLocale2($idarticle, $locale);

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


                return $this->redirect($this->generateUrl('utb_admin_modifcadrearticle', ['id' => $id, 'idarticle' => $idarticle, 'type' => $type, 'locale' => $locale]));
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
            return $this->redirect($this->generateUrl('utb_admin_modifcadrearticle', ['id' => $id, 'idarticle' => $idarticle, 'type' => $type, 'locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Article/modifImageCadre.html.twig', array('id' => $id, 'idmedia' => $idmedia,
                    'form' => $form->createView(), 'larubrique' => $larubrique, 'larticle' => $larticle, 'locale' => $locale,
        ));
    }

    /**
     * Methode permettant de valider des cadres selectionnes - Backoffice
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
    function validerAllCadresArticleAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'validerAllCadresArticleAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $cadresIds = $request->request->get('ds');
        $id = $request->request->get('id');

        $cadresIds = explode("|", $cadresIds);
        $unarticle = $em->getRepository("utbAdminBundle:Article")->find($id);

        //return new Response( json_encode($cadresIds));

        foreach ($cadresIds as $key => $value) {

            if (!empty($value)) {
                $uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($value);
                $unarticle->addCadre($uncadre);
            }
        }
        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Methode permettant de supprimer des cadres selectionnes pour un article - Backoffice
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
    function supprAllCadresArticleAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprAllCadresArticleAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest();
        $cadresIds = $request->request->get('ds');
        $cadresIds = explode("|", $cadresIds);
        $idarticle = $request->request->get('idarticle');

        foreach ($cadresIds as $key => $value) {

            if (!empty($value)) {
                $uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($value);
                $unarticle = $em->getRepository("utbAdminBundle:Article")->find($idarticle);
                $unarticle->removeCadre($uncadre);
                // return new Response( json_encode(array("result"=>"erreurstatut")));  
            }
        }

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }
    
   function presentationArticleAction(): Response {

        /* $em =$this->entityManager;	
          $AccessControl =  $this->utb_admin.AccessControl;
          $checkAcces =  $AccessControl->verifAcces($em,'corbeilleSondageAction', $this->container->get );

          if(!$checkAcces){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect ( $this->generateUrl('utb_admin_accueil', ['locale' =>$locale]));
          } */

        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $articleIds = $request->request->get('articleIds');
        $type = $request->request->get('typeId');


        //$unip=new AdresseIp(); 

        $unarticle = $em->getRepository("utbAdminBundle:Article")->find($articleIds);
        $unarticle->setTypePresentation($type);

        $em->persist($unarticle);
        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

}
