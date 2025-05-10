<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\{
    Rubrique,
    RubriqueType,
    RubriqueSeulType,
    RubriqueModifiableType,
    Media,
    MediaRubriqueType,
    MediaRubriqueAjoutType,
    MediaBanniereType,
    Dimension,
    DimensionType,
    Cadre,
    CadreType,
    MediaCadreType
};
use App\Service\AccessControl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, RequestStack, Response};
use Symfony\Component\HttpFoundation\Response as ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * 
 * RubriqueController pour la gestion des rubriques
 * 
 * Cette methode permet de verifier le droit de l'utilisateur avant d'effectuer une action dans une methode
 * 
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 */
class RubriqueController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     *  Methode qui s'occupe de l'ajout d'une rubrique
     * 
     *  Les Formulaires varient suivant  la rubrique 
     * 
     * @param string $locale La locale pour la gestion multilingue
     * @param int $id L'identifiant de la rubrique parente
     * @return Response Le template d'ajout de rubrique
     */
    #[Route(
        path: '/rubrique/ajouter/{locale}/{id}',
        name: 'app_rubrique_ajouter',
        requirements: [
            'locale' => '[a-z]{2}',
            'id' => '\d+'
        ]
    )]
    public function ajoutrubriqueAction(Request $request, string $locale, int $id): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutrubriqueAction');

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }

        // Définition de la locale pour la traduction
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        // Récupération de l'utilisateur connecté
        $user = $this->getUser()->getId();
        $isfaq = 0;

        $unesousrubrique = $em->getRepository(Rubrique::class)->find($id);

        if (($unesousrubrique != null) && ($unesousrubrique->getIdparent() != null)) {
            if (($unesousrubrique->getIdparent()->getId() == 0) ||
                (($unesousrubrique->getIdparent()->getId() != 0) &&
                ($unesousrubrique->getIdgrandparent() == $unesousrubrique->getIdparent()->getId()))
            ) {
                $isfaq = 1;
            } else {
                $isfaq = 0;
            }
        }

        $unerubrique = new Rubrique();
        $unerubrique->setTranslatableLocale($locale);
        $unerubrique->setRubriqueAjoutPar($user);
        $unerubrique->setTypePresentation(0);
        $unerubrique->setTypeRubrique(2);
        $unerubrique->setIdparent($unesousrubrique);

        $boxinfos = $this->entityManager
            ->getRepository(Parametrage::class)
            ->getTexteBoxInfos($locale, 11);

        $unedimension = $em->getRepository(Dimension::class)->find(2);

        $listestat = $this->entityManager
            ->getRepository(Statistique::class)
            ->getInfoOrStat(4, $locale, 0, null);

        $form = $this->createForm(RubriqueType::class, $unerubrique);

        $extensions = ['jpg', 'png', 'jpeg', 'gif'];

        $listeRubrique = $this->entityManager
            ->getRepository(Rubrique::class)
            ->getListeDeRubriques($locale);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unerubrique = $form->getData();

            if (strlen($unerubrique->getNomRubrique()) < 3) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errornombrecaratere');
                return $this->render('rubrique/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale,
                    'listestat' => $listestat,
                    'listeRubrique' => $listeRubrique,
                    'infos' => $boxinfos,
                    'id' => $id,
                    'isfaq' => $isfaq,
                ]);
            }

            if ($unerubrique->icone !== null && !in_array($unerubrique->icone->guessExtension(), $extensions)) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errortypficart');
                return $this->render('rubrique/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale,
                    'listestat' => $listestat,
                    'listeRubrique' => $listeRubrique,
                    'infos' => $boxinfos,
                    'id' => $id,
                    'isfaq' => $isfaq,
                ]);
            }

            $nomexiste = $this->entityManager
                ->getRepository(Rubrique::class)
                ->getTestNomRubrique($unerubrique->getNomRubrique());

            if ($nomexiste != 0) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorajtrubexist');
                return $this->render('rubrique/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale,
                    'listestat' => $listestat,
                    'listeRubrique' => $listeRubrique,
                    'infos' => $boxinfos,
                    'id' => $id,
                    'isfaq' => $isfaq,
                ]);
            }

            if ($unerubrique->getIsFaq() == 1) {
                $faqxiste = $this->entityManager
                    ->getRepository(Rubrique::class)
                    ->getNbre($unerubrique->getIdparent()->getId(), $unerubrique->getId());

                if ($faqxiste != 0) {
                    $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorFaqexist');
                    return $this->render('rubrique/ajout.html.twig', [
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'listestat' => $listestat,
                        'listeRubrique' => $listeRubrique,
                        'infos' => $boxinfos,
                        'id' => $id,
                        'isfaq' => $isfaq,
                    ]);
                }
            }

            $em->persist($unerubrique);
            $em->flush();

            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'successajtrub');
            return $this->redirect($this->generateUrl('app_rubrique_details', [
                'id' => $unerubrique->getId(),
                'locale' => $locale
            ]));
        }

        return $this->render('rubrique/ajout.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
            'listestat' => $listestat,
            'listeRubrique' => $listeRubrique,
            'infos' => $boxinfos,
            'id' => $id,
            'isfaq' => $isfaq,
        ]);
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
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $id   id de la rubrique pour qu'apres ajout l'on soit redirige vers le detail de la rubrique ajoutee
     * @param mixed $typeaction Type d'action à effectuer
     * 
     * @return Response Le template d'ajout d'une rubrique dans une autre langue
     */
    #[Route(
        path: '/rubrique/ajouter-langue/{locale}/{id}/{typeaction}',
        name: 'app_rubrique_ajouter_langue',
        requirements: [
            'locale' => '[a-z]{2}',
            'id' => '\d+'
        ]
    )]
    public function ajoutLangueRubriqueAction(Request $request, string $locale, int $id, $typeaction): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutLangueRubriqueAction');

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
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
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'reussitesupprdesc');
                //var_dump($unerubrique);exit;
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'echecsupprdesc');
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

                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errornombrecaratere');
                return $this->render('utbAdminBundle/Rubrique/ajoutLangueRubrique.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'listeRubrique' => $listeRubrique, 'listestat' => $listestat,
                            'languerubrique' => $languerubrique,
                ));
            }


            $em->persist($unerubrique);
            $em->flush();
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Rubrique ajouté avec succès');

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
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $id   id de la rubrique
     * 
     * @return Response Le template de liste des rubriques
     */
    #[Route(
        path: '/rubrique/supprimer/{id}/{locale}',
        name: 'app_rubrique_supprimer',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function supprrubriqueAction(Request $request, int $id, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'supprrubriqueAction');

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
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
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Rubrique supprimé avec succès');

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
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorsuprubbase');
            //}elseif ( ($unerubrique->getIdgrandparent() != 0) && ( $idduparent !=0 && $unerubrique->getIdgrandparent() == $idduparent ) ){
            //   $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorsuprubbase');  
            // }elseif ( ($unerubrique->getIdgrandparent() != 0) && ( $idduparent !=0 && $unerubrique->getIdgrandparent() != $idduparent ) ){
            //   $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorsuprubbase');  
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
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'successsuprub');
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorsuprub');
                return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
            }
            /* ... et on redirige vers la page d'administration des rubriques */
            return $this->redirect($this->generateUrl('utb_admin_listerubrique', array('locale' => $locale,)));
        } else {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorsuprubcontenu');
            // return $this->redirect($this->generateUrl('utb_admin_listerubrique',array('locale'=>$locale,))); 
            return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
        }
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
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return Response Le template de liste des rubriques
     */
    #[Route(
        path: '/rubrique/liste/{locale}',
        name: 'app_rubrique_liste',
        requirements: [
            'locale' => '[a-z]{2}'
        ]
    )]
    public function listerubriqueAction(Request $request, string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'listerubriqueAction');

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
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
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $id identifiant de la rubrique
     * @param mixed $typeaction si typeaction = 2(action supprimer une description) 
     * @param string $nomrubrique utilise pour faciliter le controle d'unicite de nom de la rubrique 
     * @return Response Le template de modification de rubrique
     */
    #[Route(
        path: '/rubrique/modifier/{id}/{locale}/{typeaction}/{nomrubrique}',
        name: 'app_rubrique_modifier',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function modifierrubriqueAction(Request $request, int $id, string $locale, $typeaction, string $nomrubrique): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'modifierrubriqueAction');

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unerubrique = $em->getRepository("admin/Rubrique")->find($id);

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
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'reussitesupprdesc');
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'echecsupprdesc');
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

                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errornombrecaratere');
                return $this->render('utbAdminBundle/Rubrique/modifRubrique.html.twig', array(
                            'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'typeaction' => $typeaction, 'unerubrique' => $unerubrique, 'listestat' => $listestat,
                            'nomrubrique' => $nomrubrique, 'listeRubrique' => $listeRubrique,
                ));
            }

            if ($nomrubrique != $unerubrique->getNomRubrique()) {

                $nomexiste = $this->entityManager
                        ->getRepository('utbAdminBundle/Rubrique')
                        ->getTestNomRubrique($unerubrique->getNomRubrique());

                if ($nomexiste != 0) {
                    $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorajtrubexist');
                    return $this->render('utbAdminBundle/Rubrique/modifRubrique.html.twig', array(
                                'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'typeaction' => $typeaction, 'unerubrique' => $unerubrique, 'listestat' => $listestat,
                                'nomrubrique' => $nomrubrique, 'listeRubrique' => $listeRubrique,
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
                    $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorFaqexist');

                    return $this->render('utbAdminBundle/Rubrique/modifRubrique.html.twig', array(
                                'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'typeaction' => $typeaction, 'unerubrique' => $unerubrique,
                                'listestat' => $listestat, 'nomrubrique' => $nomrubrique, 'listeRubrique' => $listeRubrique,
                    ));
                }
            }

            if ($unerubrique->getIsFaq() == null) {
                $unerubrique->setIsFaq(0);
            }

            $em->persist($unerubrique);
            try {
                $em->flush();
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'successmodrub');
            } catch (Exception $e) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errormodrub');
            }

            return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
        }
        return $this->render('utbAdminBundle/Rubrique/modifRubrique.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'typeaction' => $typeaction, 'unerubrique' => $unerubrique,
                    'listestat' => $listestat, 'nomrubrique' => $nomrubrique, 'listeRubrique' => $listeRubrique,
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
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $id identifiant de la rubrique
     * @param string $type Type de média à ajouter
     * 
     * @return Response Le template d'ajout de média
     */
    #[Route(
        path: '/rubrique/ajouter-media/{id}/{locale}/{type}',
        name: 'app_rubrique_ajouter_media',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}',
            'type' => '\d+'
        ]
    )]
    public function ajoutMediaRubriqueAction(Request $request, int $id, string $locale, string $type): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutMediaRubriqueAction');

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
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

                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errortypficart');

                return $this->redirect($this->generateUrl('utb_admin_detailrubrique', ['id' => $id, 'locale' => $locale,]));
            }
            $unmedia = $form->getData();
            $unmedia->setAjoutmodifMedia(0);
            $em->persist($unmedia);
            $em->flush();
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Média ajouté avec succès');
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
     * $unerubrique :   Array d'objet pour avoir la rubrique
     * 
     * $unmedia :       Objet de la classe Media
     * 
     * $extensions :     Tableau des extensions acceptées
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository 
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param int $id identifiant de la rubrique
     * @param int $idmedia identifiant du media
     * @return Response Le template de modification d'image
     */
    #[Route(
        path: '/rubrique/modifier-image/{id}/{idmedia}/{locale}',
        name: 'app_rubrique_modifier_image',
        requirements: [
            'id' => '\d+',
            'idmedia' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function modifImageRubriqueAction(Request $request, int $id, int $idmedia, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'modifImageRubriqueAction');

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }

        $idmedia = intval($idmedia);
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unerubrique = $em->getRepository(Rubrique::class)->find($id);
        $unedimension = $em->getRepository(Dimension::class)->find(2);

        $user = $this->getUser()->getId();
        $unmedia = $em->getRepository(Media::class)->find($idmedia);
        
        $lecount = $this->entityManager
            ->getRepository(Media::class)
            ->getSiMediaExist($idmedia, $locale);           

        $unmedia->setMediaModifPar($user);
        $unmedia->setNomMedia("");
        $unmedia->setMediaDateModif(new \DateTime());
        
        $extensions = ['jpg', 'png', 'jpeg', 'gif'];
        $unmedia->extensions = $extensions;
        
        $form = $this->createForm(MediaRubriqueAjoutType::class, $unmedia);

        $listestat = $this->entityManager
            ->getRepository(Statistique::class)
            ->getInfoOrStat(3, $locale, 0, $id);

        $listeRubrique = $this->entityManager
            ->getRepository(Rubrique::class)
            ->getListeDeRubriques($locale);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($unmedia->file !== null && !in_array($unmedia->file->guessExtension(), $extensions)) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errortypficart');
                return $this->redirect($this->generateUrl('app_rubrique_details', ['id' => $id, 'locale' => $locale]));
            }

            if ($lecount == 1) {
                $unmedia->setTranslatableLocale($locale);                
                $em->refresh($unmedia);
                $unmedia->setPositionMedia(1);
                $unmedia->setAjoutmodifMedia(1);
            } elseif ($lecount == 0) {
                $unmedia->setTranslatableLocale($locale);                
                $unmedia->setAjoutmodifMedia(2);
            }                         
            
            $em->persist($unmedia);
            $em->flush();

            $nouveauUrl = $unmedia->getUrlMedia();
            $newmedia = $em->getRepository(Media::class)->find($idmedia);
            
            if ($newmedia->getAjoutmodifMedia() == 2 && $lecount != 1) {
                $newmedia->setTranslatableLocale($locale);
                $em->refresh($newmedia);
                $newmedia->setUrlMedia($nouveauUrl);
                $newmedia->setPositionMedia(3);
                $newmedia->setAjoutmodifMedia(2);
                $em->persist($newmedia);
                $em->flush();
            }
            
            $em->clear();
            $_media = $em->getRepository(Media::class)->find($idmedia);
            $_media->setPositionMedia(2);
            $em->persist($_media);
            $em->flush();
            
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Media modifié avec succès');

            return $this->redirect($this->generateUrl('app_rubrique_details', ['id' => $id, 'locale' => $locale]));
        }

        return $this->render('rubrique/modifier-image.html.twig', [
            'id' => $id,
            'idmedia' => $idmedia,
            'form' => $form->createView(),
            'locale' => $locale,
            'listestat' => $listestat,
            'listeRubrique' => $listeRubrique,
        ]);
    }
}