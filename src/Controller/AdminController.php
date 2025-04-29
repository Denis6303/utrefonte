<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Profil;
use App\Entity\ProfilType;
use App\Entity\RubriqueType;
use App\Entity\Module;
use App\Entity\ModuleType;
use App\Entity\Media;
use App\Entity\MediaRubriqueType;
use App\Entity\Dimension;
use App\Entity\DimensionType;
use App\Entity\Controleur;
use App\Entity\ControleurType;
use App\Entity\Action;
use App\Entity\ActionType;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;

use App\Entity\GroupeMenu;
use App\Entity\Menu;
use App\Entity\Parametrage;
use App\Entity\Squelettepage;
use App\Entity\NatureDoc;
use App\Entity\NatureDocType;
use App\Entity\Message;
use App\Entity\MessageReponse;
use App\Entity\Internaute;
use App\Entity\Objet;
use App\Entity\Service;
use App\Entity\InternauteType;
use App\Entity\MessageReponseType;
use App\Entity\droit;
use App\Entity\Ordre;

/**
 * AdminController 
 * 
 * Cette classe regroupe l'ensemble des fonctions qui sont communes
 * a toutes les parties de l'espace administration ainsi que les fonctions de parametrage.
 * 
 * @author utb <mail@utb.com>
 * 
 */
class AdminController extends AbstractController {
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
    }//$this->Utils =  $this->utils;
    //}

    /**
     * Fonction permettant d'afficher la page d'accueil de l'espace d'administration (BAckoffice)
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listearticlerecent: Liste des 5 plus recents articles publies.
     * 
     * $listearticleattente: Liste des 5 derniers articles en attente de publication
     * 
     * $listearticlesoumis: Liste des 5 derniers articles soumis pour validation.
     * 
     * $letexte: Message de bienvenu de la page d'accueil.
     * 
     * $listeuser: Liste des utilisateurs du backoffice pour déterminer les informations des auteurs 
     * des articles.
     * 
     * $listeRubrique: Liste de toutes les rubriques du backoffice
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig admin/Admin/index.html.twig 
     * 
     */
    #[Route('/admin/index', name: 'admin_index')]
    public function indexAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        //Texte à  afficher dans 
        $listearticlerecent = $em
                ->getRepository('App\Entity\Article')
                ->findAllByLocaleRecent($locale);

        //Texte Ã  afficcher dans l'accueil    
        $listearticleattente = $em
                ->getRepository('App\Entity\Article')
                ->findAllByLocaleAttente5($locale);

        $listearticlesoumis = $em->getRepository('App\Entity\Article')
                ->findAllByLocaleType($locale, 2, 5, 0);

        //Texte Ã  afficcher dans l'accueil      
        $letexte = $em->getRepository('App\Entity\Parametrage')
                ->getTitreDescription($locale, 0);

        //liste des utilisateurs
        $listeuser = $em
                ->getRepository('App\Entity\User')
                ->findAll();

        $listeRubrique = $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getListeDeRubriques($locale);

        return $this->render('admin/index.html.twig', array(
                    'locale' => $locale,
                    'listearticlerecent' => $listearticlerecent,
                    'listeuser' => $listeuser,
                    'listearticleattente' => $listearticleattente,
                    'letexte' => $letexte,
                    'listearticlesoumis' => $listearticlesoumis,
                    'letexte' => $letexte,
                    'listeRubrique' => $listeRubrique,
        ));
    }

    /**
     * Fonction permettant d'ajouter un profil (définissant les droits des utilisateurs) - Backoffice     * 
     * 
     * @var 
     * 
     * Les variables
     * 
     * $unprofil/ Instance de la classe Profil pour l'ajout
     * 
     * $default: Eclatement d'un tableau vide : définissant l'ensemble des droits par defaut (donc vide) du profil
     * 
     * $droits: Instance de la classe Droit 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig ajoutProfil.html.twig avec la variable $locale
     *  
     */
    #[Route('/admin/ajoutprofil', name: 'admin_ajoutprofil')]
    public function ajoutprofilAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajoutprofilAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //        
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $unprofil = new Profil();
        $default = serialize(array());
        $droits = new droit();
        $droits->setProfil($unprofil);
        $droits->setDroits($default);


        //$unprofil->setTranslatableLocale($locale);
        $form = $this->createForm(ProfilType::class, $unprofil);

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unprofil = $form->getData();

            $siexiste = $this->entityManager
                    ->getRepository("App\Entity\Profil")
                    ->getSiProfilExiste(0, $locale, $unprofil->getLibProfil());

            if ($siexiste != 0) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'existedeja');

                return $this->redirect($this->generateUrl('admin_listeprofil', ['locale' => $locale,]));
            }

            $em->persist($unprofil);
            $em->persist($droits);

            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'success');

            /* if($this->isTest){
              return $this->redirect($this->generateUrl('admin_listeprofil', ['locale' => $locale,]));
              } */
            return $this->redirect($this->generateUrl('admin_listeprofil', ['locale' => $locale,]));
        }
        return $this->render('admin/ajoutProfil.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, //'infos'=>$boxinfos,
        ));
    }

    /**
     * Fonction permettant d'avoir la liste des profils - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listeprofil: Liste des profils d'utilisateurs disponibles.
     * 
     * $boxinfos: Texte a afficher dans le bos d'information a gauche de la 
     * page des profils. 
     * 
     * @param <string> $locale, Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $ajoutprof, Variable qui permet d'afficher ou non le formulaire d'ajout de profil
     * au dessus de la liste des profils. 
     * $ajoutprof==1 -> Afficher le formulaire d'ajout
     * $ajoutprof==0 -> Ne pas afficher le formulaire d'ajout; Afficher uniquement la liste des profils.
     * 
     * @return <string> return le twig listeProfil.html.twig 
     *  
     */
    #[Route('/admin/listeprofil', name: 'admin_listeprofil')]
    public function listeprofilAction(): Response(string $locale, $ajoutprof): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeprofilAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        //$repertoire = $this-> getDoctrine()->getManager()->getRepository();       
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $listeprofil = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getStatProfilLocale($locale, int $type = 0);

        $boxinfos = $this->entityManager
                ->getRepository("utbAdminBundle/Parametrage")
                ->getTexteBoxInfos($locale, int $type = 6);

        return $this->render('admin/listeProfil.html.twig', array('listeprofil' => $listeprofil,
                    'locale' => $locale, 'infos' => $boxinfos, 'ajoutprof' => $ajoutprof,));
    }

    /**
     * Fonction permettant de supprimer un profil  - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unprofil: Instance de la classe Profil a supprimer
     * 
     * $unUser: Instance de la classe User relative au profil $unprofil.S'assure si le profil contient un user
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <string> $id identifiant du profil a supprimer 
     * 
     * @return <string> return le twig listeProfil.html.twig 
     *  
     */
    #[Route('/admin/supprprofil', name: 'admin_supprprofil')]
    public function supprprofilAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'supprprofilAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $unprofil = $em->getRepository("utbAdminBundle/Profil")->find($id);


        /**
         *  on recupere la liste des utilisateurs ayant ce profil 
         */
        $unUser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findProfil($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);
        $undroit = new droit();

        // Si la liste est vide on supprime les droits qui y sont liés puis le profil.
        if ($unUser == null) {
            $undroit->setProfil($unprofil);
            /* Enfin on supprime le profil... */
            $em->remove($unprofil);

            $em->remove($undroit);

            $em->flush($unprofil);

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Profil supprimé avec succès');
            return $this->redirect($this->generateUrl('admin_listeprofil', array(
                                'locale' => $locale, 'listestat' => $listestat)));        /* ... et on redirige vers la page d'administration des categorie */
        } else {
            // Le profil a des users et lÃ  on ne peut pas le supprimer.
            $listeprofil = $this->entityManager
                    ->getRepository("utbAdminBundle/Profil")
                    ->findAllByLocale($locale);
            return $this->render('admin/listeProfil.html.twig', array('listeprofil' => $listeprofil, 'locale' => $locale, 'listestat' => $listestat,));
        }
    }

    /**
     * Fonction permettant d'activer ou de desactiver un profil  - Backoffice
     * 
     * Abandonnée par la suite
     * 
     * @deprecated
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unprofil: Instance de la classe Profil a activer/desactiver
     * 
     * @param <string> $locale, Variable passee pour gerer le multilingue sur le site
     * @param <string> $etat, Variable passee pour gerer les etats (1=Desactive, 0=active)
     * @param <string> $id  id du profil que l'on Activer|Désactiver.
     * 
     * @return <string> return le twig listeProfil.html.twig 
     *  
     */
    #[Route('/admin/gererEtatProfil', name: 'admin_gererEtatProfil')]
    public function gererEtatProfilAction(): Response(int $id, int $etat, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        int $etat = 0;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'gererEtatProfilAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        // Récupération du profil 
        $unprofil = $em->getRepository("admin/Profil")->find($id);
        $unprofil->setEtatProfil($etat);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0, null);

        $em->persist($unprofil);

        $em->flush();

        if (int $etat = 0) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Profil désactivé avec succès');
        } else {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Profil activé avec succès');
        }

        return $this->redirect($this->generateUrl('admin_listeprofil', [
                            'locale' => $locale,]));
    }

    /**
     * Fonction permettant d'activer ou de desactiver un ou plusieurs profils  - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $profilIds/ Tableau des profils a activer/desactiver
     * 
     * $etat, Variable passee pour gerer les etats (1=Desactive, 0=active)
     * 
     * @return <string> return le twig listeProfil.html.twig 
     *  
     */
    function gererAllProfilAction(): Response: Response {

        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'gererAllProfilAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $profilIds = $request->request->get('profilIds');
        $etat = $request->request->get('etat');

        $profilIds = explode("|", $profilIds);
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($profilIds as $key => $value) {
            if (!empty($value)) {
                $unprofil = $em->getRepository("App\Entity\Profil")->find($value);

                $unUser = $em->getRepository("App\Entity\User")->findAll();

                if ($unprofil->getId() == 1) {
                    return new Response(json_encode(array("result" => "administrateur")));
                } else {
                    foreach ($unUser as $keyuser => $valueuser) {
                        if ($valueuser->getProfil()->getId() == $value) {

                            $lutil = $em->getRepository("App\Entity\User")->find($valueuser->getId());
                            $lutil->setEnabled($etat);
                            $em->persist($lutil);
                        }
                    }
                }

                //Désactivation  

                $unprofil->setEtatProfil($etat);
                $em->persist($unprofil);
                $em->flush();
            }
        }
        //$em->flush();   
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de modifier un profil - Backoffice     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unprofil: Instance de la classe Profil a modifier
     * 
     * @param <integer> $id     Identifiant  du profil a modifier.
     * @param <string>  $locale, Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig modifProfil
     *  
     */
    #[Route('/admin/modifierProfil', name: 'admin_modifierProfil')]
    public function modifierProfilAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'modifierProfilAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        // Récupération du profil 
        $unprofil = $em->getRepository("App\Entity\Profil")->find($id);

        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité profil 
        $form = $this->createForm(ProfilType::class, $unprofil);

        // On récupère les données du formulaire si il a déjÃ  été passé 
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);


            /* Si le formulaire est valide, on valide et on redirige vers la liste des profils */
            if ($form->isValid()) {
                $em->persist($unprofil);
                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'modifsuccess');

                return $this->redirect($this->generateUrl("utb_admin_listeprofil"));
            }
        }
        return $this->render('admin/modifProfil.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale));
    }

    /**
     * Fonction permettant d'ajouter un profil dans une autre langue(une traduction) - Backoffice
     * 
     * Abandonnée par la suite
     * 
     * @deprecated since version 1.0
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unprofil: Instance de la classe Profil a modifier
     * 
     * @param <integer> $id     Identifiant  du profil
     * @param <string>  $locale, Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutLangueProfil.html.twig
     *  
     */
    #[Route('/admin/ajoutLangueProfil', name: 'admin_ajoutLangueProfil')]
    public function ajoutLangueProfilAction(): Response(string $locale, int $id): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajoutLangueProfilAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $unprofil = $em->getRepository("App\Entity\Profil")->find($id);
        $unprofil->setTranslatableLocale($locale);
        $em->refresh($unprofil);
        // Change la locale  
        $form = $this->createForm(ProfilType::class, $unprofil);

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $em->persist($unprofil);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Profil ajouté avec succès');

            return $this->redirect($this->generateUrl('admin_listeprofil', ['locale' => $locale,
            ]));
        }

        return $this->render('admin/ajoutLangueProfil.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id,
        ));
    }

    /**
     * Fonction permettant d'ajouter une action - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unaction: Instance de la classe Action a ajouter
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutAction.html.twig
     *  
     */
    #[Route('/admin/ajoutaction', name: 'admin_ajoutaction')]
    public function ajoutactionAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajoutactionAction', $this->container->get);
/*
        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }*/
        //          
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $unaction = new Action();

        $form = $this->createForm(ActionType::class), $unaction);

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unaction = $form->getData();
            $unaction->setClient(0);

           /* //controle des elmts du form
            $verificateur = $this->ArticleService;
            //$verifSaisie=true => pas de caracteres interdits
            $verifSaisie = $verificateur->verifSaisie($form->get('libAction')->getData(), array('/', '%'));
            //$verifVide=true => champ vide
            $verifVide = $verificateur->verifVide($form->get('libAction')->getData());

            //Controle de l'existence du nom ou libelle
            $nomexiste = $this->entityManager
                    ->getRepository('App\Entity\Action')
                    ->getTestNomAction($unaction->getLibAction());

            //var_dump($uncontroleur->getNomControleur());exit;|| ($nomexiste != 0)

            if (($verifVide == true)  || ($verifSaisie == false)) {

                if ($verifVide == true) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');
                }
                if ($verifSaisie == false) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartcarfaux');
                }
                if ($nomexiste != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                }

                return $this->redirect($this->generateUrl('admin_ajoutmodule', [
                                    'locale' => $locale,
                ]));
            }*/

            $em->persist($unaction);
            $em->flush();

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('admin_listeaction', [
                                'locale' => $locale,
            ]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, null);

        return $this->render('admin/ajoutAction.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
        ));
    }

    /**
     * Fonction permettant d'ajouter un controlleur - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $uncontroleur: Instance de la classe Controleur a ajouter
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutControleur.html.twig
     *  
     */
    #[Route('/admin/ajoutControleur', name: 'admin_ajoutControleur')]
    public function ajoutControleurAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajoutControleurAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //          
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $uncontroleur = new Controleur();
        $form = $this->createForm(ControleurType::class), $uncontroleur);

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $uncontroleur = $form->getData();

            //controle des elmts du form
            $verificateur = $this->ArticleService;
            //$verifSaisie=true => pas de caracteres interdits
            $verifSaisie = $verificateur->verifSaisie($form->get('nomControleur')->getData(), array('/', '%'));
            //$verifVide=true => champ vide
            $verifVide = $verificateur->verifVide($form->get('nomControleur')->getData());

            //Controle de l'existence du nom ou libelle
            $nomexiste = $this->entityManager
                    ->getRepository('App\Entity\Controleur')
                    ->getTestNomControleur($uncontroleur->getNomControleur());

            //var_dump($uncontroleur->getNomControleur());exit;

            if (($verifVide == true) || ($nomexiste != 0) || ($verifSaisie == false)) {

                if ($verifVide == true) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');
                }
                if ($verifSaisie == false) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartcarfaux');
                }
                if ($nomexiste != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                }

                return $this->redirect($this->generateUrl('admin_ajoutcontroleur', [
                                    'locale' => $locale,
                ]));
            }


            $uncontroleur->setClient(0);
            $em->persist($uncontroleur);

            $em->flush();

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('admin_listecontroleur', [
                                'locale' => $locale,
            ]));
        }

        return $this->render('admin/ajoutControleur.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale,
                    'listestat' => $listestat,
        ));
    }

    /**
     * Fonction permettant de modifier un controlleur - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $uncontroleur: Instance de la classe Controleur a modifier
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant  du controleur a modifier.
     * 
     * @return <string> return sur le twig modifControleur.html.twig
     *  
     */
    #[Route('/admin/modifierControleur', name: 'admin_modifierControleur')]
    public function modifierControleurAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'modifierControleurAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //    
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        // Récupération du controleur
        $unControleur = $em->getRepository("App\Entity\Controleur")->find($id);

        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité profil 
        $form = $this->createForm(ControleurType::class), $unControleur);

        // On récupère les données du formulaire si il a déjÃ  été passé 
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);


            /* Si le formulaire est valide, on valide et on redirige vers la liste des profils */
            if ($form->isValid()) {
                $em->persist($unControleur);


                $em->flush();
                $msgnotification = '';
                $msgnotification = $this->translator->trans('notification.modification');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Controleur modifié avec succès');
                return $this->redirect($this->generateUrl('admin_listecontroleur', [
                                    'locale' => $locale, //'listestat'=>$listestat,
                ]));
            }
        }
        return $this->render('admin/modifControleur.html.twig', array(
                    'form' => $form->createView(), 'id' => $id,
                    'locale' => $locale,
                    'listestat' => $listestat,));
    }

    //************ Debut methode a documenter

    /**
     * Fonction permettant d'afficher la liste des controleurs - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $listecontroleur: Liste de tous les controleurs
     * 
     * $total: Total des instances de la classe Controleur
     * 
     * $articles_per_page: Nombre de controlleurs a afficher par page
     * 
     * $last_page: Numero de la derniere page
     * 
     * $previous_page: Numero de la page precedente
     * 
     * $next_page: Numero de la page suivante 
     * 
     * $entities: Liste de controleurs par rapport au numero de page
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $page  numero de la page en cours.
     * 
     * @return <string> return sur le twig listeControleur.html.twig
     *  
     */
    #[Route('/admin/listecontroleur', name: 'admin_listecontroleur')]
    public function listecontroleurAction(): Response(string $locale, $page): Response {

        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listecontroleurAction', $this->container->get);
        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $listecontroleur = $em->getRepository("App\Entity\Controleur")->findAllAdmin($locale, 0);
        /* total des résultats */
        $total = count($listecontroleur);
        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        //$entities = $em->getRepository("App\Entity\Controleur")->createQueryBuilder('p')->setFirstResult(($page * $articles_per_page) - $articles_per_page)->setMaxResults($this->container->get->getParameter('max_articles_on_listepage'))->getQuery()->getResult();
        $entities = $em->getRepository("admin/Controleur")
                        ->createQueryBuilder('p')
                        ->where('p.client = /client')
                        ->orderBy('p.id', 'DESC')
                        ->setParameter('client', 0)
                        ->setFirstResult(($page * $articles_per_page) - $articles_per_page)
                        ->setMaxResults($this->container->get->getParameter('max_articles_on_listepage'))
                        ->getQuery()->getResult();
        //$entities = $this->getDoctrine()->getRepository('CliniqueGynecoBundle/Article')->createQueryBuilder('p')->setFirstResult(($page * $articles_per_page) - $articles_per_page)->setMaxResults($this->container->get->getParameter('max_articles_on_listepage'))->getQuery()->getResult();

        return $this->render('admin/listeControleur.html.twig', array(
                    'entities' => $entities,
                    'locale' => $locale,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'listestat' => $listestat,
        ));
        /* $listecontroleur = $em->getRepository("utbAdminBundle/Controleur")->findAll();        
          return $this->render('admin/listeControleur.html.twig', array( ));
         */
    }

    /**
     * Fonction permettant de supprimer des controleurs - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $lecontroleur: Instance de la classe controleur a supprimer
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant de l'instance de la classe Controleur a supprimer
     * 
     * @return une redirection sur la liste des controleurs 
     *  
     */
    #[Route('/admin/supprcontroleur', name: 'admin_supprcontroleur')]
    public function supprcontroleurAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'supprcontroleurAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        //          
        //$dialog = $this->gethelperSet()->get('dialog') ;
        //if (!$dialog->askConfirmation($output,'<question>'Supprimer cet enregistrement?'</question>'){
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $lecontroleur = $em->getRepository("App\Entity\Controleur")->find($id);
        /* Enfin on supprime le categorie ... */
        $em->remove($lecontroleur);
        $em->flush();


        $msgnotification = '';
        $msgnotification = $this->translator->trans('notification.suppression');
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

        /* ... et on redirige vers la page d'administration des profils */
        return $this->redirect($this->generateUrl('admin_listecontroleur', array(
                            'locale' => $locale,
                            'listestat' => $listestat)));
    }

    /**
     * Fonction permettant de supprimer definitivement des controleurs selectionnes - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $controleursIds: Tableau regroupant les Ids des instances de controleurs a supprimer
     * 
     * $uncontroleur: Instance de la classe controleur a supprimer au niveau de la boucle
     * 
     * @return <json> return etat de traitement effectue 
     *  
     */
    function supprAllcontroleursAction(): Response: Response {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $controleursIds = $request->request->get('controleursIds');
        $controleursIds = explode("|", $controleursIds);
        foreach ($controleursIds as $key => $value) {
            if (!empty($value)) {
                $uncontroleur = $em->getRepository("App\Entity\Controleur")->find($value);
                $em->remove($uncontroleur);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de lister les modules - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $listemodule: Liste des modules a afficcher
     * 
     * @return <string> return sur le twig listeModule.html.twig
     *  
     */
    #[Route('/admin/listeModule', name: 'admin_listeModule')]
    public function listeModuleAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeModuleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }


        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $listemodule = $em->getRepository("App\Entity\Module")->findAllAdmin($locale);

        return $this->render('admin/listeModule.html.twig', array('listemodule' => $listemodule,
                    'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     * Fonction permettant de modifier un module - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unemodule: Instance de la classe Module a modifier
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant de l'instance de la classe Module a modifier
     * 
     * @return <string> return sur le twig modifModule.html.twig
     *  
     */
    #[Route('/admin/modifierModule', name: 'admin_modifierModule')]
    public function modifierModuleAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'modifierModuleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $unemodule = $em->getRepository("App\Entity\Module")->find($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        /* Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité Genre */
        $form = $this->createForm(ModuleType::class), $unemodule);



        /* On récupère les données du formulaire si il a déjÃ  été passé */
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        /* On ne traite que les données passées en méthode POST */
        if ($request->getMethod() == 'POST') {
            /* On applique les données récupérées au formulaire */
            $form->handleRequest($request);

            /* Si le formulaire est valide, on valide et on redirige vers la liste des genres */
            if ($form->isValid()) {
                // $em=$this->entityManager;
                $em->persist($unemodule);
                $em->flush();
                $msgnotification = '';
                $msgnotification = $this->translator->trans('notification.modification');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

                return $this->redirect($this->generateUrl('admin_listemodule', array(
                                    'locale' => $locale,
                                    'listestat' => $listestat,
                )));
            }
        }
        return $this->render('admin/modifModule.html.twig', array(
                    'form' => $form->createView(), 'id' => $id,
                    'locale' => $locale,
                    'listestat' => $listestat,));
    }

    /**
     * Fonction permettant de supprimer un module - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unemodule: Instance de la classe Module a supprimer
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant de l'instance de la classe Module a supprimer
     * 
     * @return <string> return une redirection
     *  
     */
    #[Route('/admin/supprModule', name: 'admin_supprModule')]
    public function supprModuleAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'supprModuleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //          
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $unemodule = $em->getRepository("App\Entity\Module")->find($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        /* Enfin on supprime le categorie ... */
        $em->remove($unemodule);
        $em->flush();
        $msgnotification = '';
        $msgnotification = $this->translator->trans('notification.suppression');
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

        /* ... et on redirige vers la page d'administration des categorie */
        return $this->redirect($this->generateUrl('admin_listemodule', array(
                            'locale' => $locale, 'listestat' => $listestat)));
    }

    /**
     * Fonction permettant de supprimer definitivement des modules selectionnes - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $modulesIds: Tableau regroupant les Ids des instances de modules a supprimer
     * 
     * $unmodule: Instance de la classe module a supprimer au niveau de la boucle
     * 
     * @return <json> return etat de traitement effectue 
     *  
     */
    function supprAllmodulesAction(): Response: Response {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $modulesIds = $request->request->get('modulesIds');
        $modulesIds = explode("|", $modulesIds);
        foreach ($modulesIds as $key => $value) {
            if (!empty($value)) {
                $unmodule = $em->getRepository("App\Entity\Module")->find($value);
                $em->remove($unmodule);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant d'ajouter un module - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unemodule: Instance de la classe Module a ajouter
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutModule.html.twig
     *  
     */
    #[Route('/admin/ajouterModule', name: 'admin_ajouterModule')]
    public function ajouterModuleAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajouterModuleAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $unemodule = new Module();
        $form = $this->createForm(ModuleType::class), $unemodule);
        $unemodule->setClient(0);
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unemodule = $form->getData();

            //controle des elmts du form
            $verificateur = $this->ArticleService;
            //$verifSaisie=true => pas de caracteres interdits
            $verifSaisie = $verificateur->verifSaisie($form->get('libModule')->getData(), array('/', '%'));
            //$verifVide=true => champ vide
            $verifVide = $verificateur->verifVide($form->get('libModule')->getData());

            //Controle de l'existence du nom ou libelle
            $nomexiste = $this->entityManager
                    ->getRepository('App\Entity\Module')
                    ->getTestNomModule($unemodule->getLibModule());

            //var_dump($uncontroleur->getNomControleur());exit;

            if (($verifVide == true) || ($nomexiste != 0) || ($verifSaisie == false)) {

                if ($verifVide == true) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartdescvide');
                }
                if ($verifSaisie == false) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartcarfaux');
                }
                if ($nomexiste != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorajtartexist');
                }

                return $this->redirect($this->generateUrl('admin_ajoutmodule', [
                                    'locale' => $locale,
                ]));
            }

            $em->persist($unemodule);
            $em->flush();
            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('admin_listemodule', [
                                'locale' => $locale, 'listestat' => $listestat]));
        }


        return $this->render('admin/ajoutModule.html.twig', array(
                    'form' => $form->createView(),
                    'locale' => $locale, 'listestat' => $listestat));
    }

    /**
     * Fonction permettant de lister les actions - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $listeaction: Liste des actions
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig listeAction.html.twig
     *  
     */
    #[Route('/admin/listeaction', name: 'admin_listeaction')]
    public function listeactionAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeactionAction', $this->container->get);

      /*  if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }*/
        //          
        //$repertoire = $this-> getDoctrine()->getManager()->getRepository();
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $listeaction = $em->getRepository("App\Entity\Action")->findAllAdmin($locale);


        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        return $this->render('admin/listeAction.html.twig', array('listeaction' => $listeaction,
                    'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     * Fonction permettant de modifier une action - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unaction: Instance de la classe Action a modifier
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant de l'instance de la classe Action a modifier
     * 
     * @return <string> return sur le twig modifAction.html.twig
     *  
     */
    #[Route('/admin/modifieraction', name: 'admin_modifieraction')]
    public function modifieractionAction(): Response(int $id, string $locale): Response {
        //           
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $em = $this->entityManager;

        //code qui verifie si l'utilisateur courant a acces a cette action
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'modifieractionAction', $this->container->get);

        /*if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }*/

        // Récupération du profil 
        $unaction = $em->getRepository("App\Entity\Action")->find($id);
        //var_dump($id);exit;
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité profil 
        $form = $this->createForm(ActionType::class), $unaction);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        if ($request->isMethod('POST')) {


            $form->handleRequest($request);
            if ($form->isValid()) {
                $unaction = $form->getData();
                $em->persist($unaction);
                $em->flush();
                $msgnotification = '';
                $msgnotification = $this->translator->trans('notification.suppression');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

                return $this->redirect($this->generateUrl('admin_listeaction', [
                                    'locale' => $locale,
                                    'listestat' => $listestat,]));
            }
        }
        return $this->render('admin/modifAction.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     * Fonction permettant de supprimer une action - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.
     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,
     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unaction: Instance de la classe Action a supprimer
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant de l'instance de la classe Action a supprimer
     * 
     * @return <string> return une redirection
     *  
     */
    #[Route('/admin/suppraction', name: 'admin_suppraction')]
    public function suppractionAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'suppractionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $unaction = $em->getRepository("App\Entity\Action")->find($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        /* Enfin on supprime le categorie ... */
        $em->remove($unaction);
        $em->flush();
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Action supprimée avec succès');

        /* ... et on redirige vers la page d'administration des profils */
        return $this->redirect($this->generateUrl('admin_listeaction', array(
                            'locale' => $locale, 'listestat' => $listestat,)));
    }

    /**
     * Fonction permettant de supprimer une action - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $actionsIds: Tableau regroupant les Ids des instances de la classe Action selectionnes
     * 
     * $unaction: Instance de la classe Action a supprimer dans la boucle
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant de l'instance de la classe Action a supprimer
     * 
     * @return <string> return une redirection
     *  
     */
    function supprAllactionsAction(): Response: Response {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $actionsIds = $request->request->get('actionsIds');
        $actionsIds = explode("|", $actionsIds);
        foreach ($actionsIds as $key => $value) {
            if (!empty($value)) {
                $unaction = $em->getRepository("App\Entity\Action")->find($value);
                $em->remove($unaction);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant d'afficher la page des droits - Backoffice
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $listeProfiles: Instance de la classe Profil pour l'affichage de ses droits relatifs
     * 
     * $Utils: Instance de la classe Utils pour la gestion des messages d'erreurs
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig droit.html.twig
     *  
     */
    #[Route('/admin/droit', name: 'admin_droit')]
    public function droitAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'droitAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        //         
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $Utils = $this->utils;
        $Utils->log(__METHOD__);

        //on récupère la liste des profils
        $listeProfiles = $em->getRepository("App\Entity\Profil")
                ->getListeProfileActifs();
        //on récupère la liste des modules 
        $listeModules = $em->getRepository("App\Entity\Module")->findBy(array("client" => 0),array('ordre' => 'ASC'));

        //on recupere les actions juste pour l'affiche
        $actions = $actionsByProfil = array();
        foreach ($listeModules as $modules) {
            $actions[$modules->getLibmodule() . "|" . $modules->getId()] = array();
            //nous allons cherchons les action pour chaque modules 
            $listeActions = $em->getRepository("App\Entity\Action")
                    ->getActionsByModule($modules->getId());
            foreach ($listeActions as $action) {
                $actions[$modules->getLibmodule() . "|" . $modules->getId()][] = array(
                    'libAction' => $action->getLibAction(),
                    'DescriptionAction' => $action->getDescriptionAction(),
                    'idAction' => $action->getId(),
                    'idcontroleur' => $action->getControleur(),
                );
            }
        }

        //Nous allons chercher les droits pour chaque profil en meme tps
        foreach ($listeProfiles as $lp) {
            $idProfil = $lp->getId();


            $thisDroits = $em->getRepository("App\Entity\droit")->findBy(array("profil" => $idProfil));
            //var_dump($thisDroits);

            /**  ajout d'un   */
            if ($thisDroits != null) {
                $thisDroits = unserialize($thisDroits[0]->getDroits());
            }


            foreach ($listeModules as $modules) {

                $actionsByProfil[$idProfil][$modules->getLibmodule() . "|" . $modules->getId()] = array();
                //nous allons cherchons les action pour chaque modules 
                $listeActions = $em->getRepository("App\Entity\Action")
                        ->getActionsByModule($modules->getId());
                foreach ($listeActions as $action) {
                    $checkaction = ($thisDroits != null) && (isset($thisDroits[$modules->getId()]) && in_array($action->getId(), $thisDroits[$modules->getId()]) ) ? 1 : 0;
                    $actionsByProfil[$idProfil][$modules->getLibmodule() . "|" . $modules->getId()][] = array(
                        'libAction' => $action->getLibAction(),
                        'DescriptionAction' => $action->getDescriptionAction(),
                        'idAction' => $action->getId() . "|" . $checkaction,
                        'idcontroleur' => $action->getControleur(),
                    );
                }
            }
        }

        // var_dump($actions);
        return $this->render('admin/droit.html.twig', array("listeProfiles" => $listeProfiles,
                    "listeModules" => $listeModules,
                    /* 'locale' => $locale,'listestat'=> $listestat, */
                    "actions" => $actions,
                    "actionsByProfil" => $actionsByProfil
        ));
    }

    /**
     * Fonction permettant de lister les dimensions - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $listedimension: Instances de la classe Dimension 
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig droit.html.twig
     *  
     */
    #[Route('/admin/listeDimension', name: 'admin_listeDimension')]
    public function listeDimensionAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeDimensionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        //          
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        //     $repertoire = $this-> getDoctrine()->getManager()->getRepository();
        $listedimension = $em->getRepository("App\Entity\Dimension")->findAll();

        return $this->render('admin/listeDimension.html.twig', array('listedimension' => $listedimension, 'locale' => $locale, 'listestat' => $listestat));
    }

    /**
     * Fonction permettant de modifier les dimensions - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unedimension: Instance de la classe Dimension a modifier
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant de la dimension
     * 
     * @return <string> return sur le twig modifDimension.html.twig
     *  
     */
    #[Route('/admin/modifierDimension', name: 'admin_modifierDimension')]
    public function modifierDimensionAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'modifierDimensionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        //        
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $unedimension = $em->getRepository("admin/Dimension")->find($id);

        /* Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité Genre */
        $form = $this->createForm(DimensionType::class), $unedimension);

        /* On récupère les données du formulaire si il a déjÃ  été passé */
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        /* On ne traite que les données passées en méthode POST */
        if ($request->getMethod() == 'POST') {
            /* On applique les données récupérées au formulaire */
            $form->handleRequest($request);

            /* Si le formulaire est valide, on valide et on redirige vers la liste des genres */
            if ($form->isValid()) {
                // $em=$this->entityManager;
                $em->persist($unedimension);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_listedimension', array(
                                    'locale' => $locale, //'listestat'=> $listestat
                )));
            }
        }
        return $this->render('admin/modifDimension.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'listestat' => $listestat));
    }

    /**
     * Fonction permettant de supprimer les dimensions - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unedimension/ Instance de la classe Dimension a supprimer
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant de la dimension
     * 
     * @return <string> return sur le twig modifDimension.html.twig
     *  
     */
    #[Route('/admin/supprDimension', name: 'admin_supprDimension')]
    public function supprDimensionAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'supprDimensionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $unedimension = $em->getRepository("App\Entity\Dimension")->find($id);

        $em->remove($unedimension);
        $em->flush();
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Dimension supprimé avec succès');

        return $this->redirect($this->generateUrl('admin_listedimension', array(
                            'locale' => $locale,)));
    }

    /**
     * Fonction permettant d'ajouter les dimensions - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $unedimension: Instance de la classe Dimension a ajouter
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutDimension.html.twig
     *  
     */
    #[Route('/admin/ajouterDimension', name: 'admin_ajouterDimension')]
    public function ajouterDimensionAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajouterDimensionAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $unedimension = new Dimension();
        $form = $this->createForm(DimensionType::class), $unedimension);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unedimension = $form->getData();
            $em->persist($unedimension);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Dimension ajouté avec succès');

            return $this->redirect($this->generateUrl('admin_listedimension', [
                                'locale' => $locale,]));
        }

        return $this->render('admin/ajoutDimension.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     * Fonction permettant d'avoir les droits d'un profil donne - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $idProfil: id de l'instance de profil recuperee par session
     * 
     * $thisProfil: Instance de la classe Profil dont on veut lister les droits
     * 
     * $thisDroits: Liste des droits relatifs au profil en question
     * 
     * $listeModules:  Liste des modules
     * 
     * $listestat: Liste de statistiques sur les articles et rubriques.     * Total des articles sur le site, total des articles en cours de rédaction, en attende publication,     * soumis pour validation etc ainsi que des rubriques, sous-rubriques, catégories...
     * 
     * $listeActions: Liste des actions relatives a un module
     * 
     * $checkall: Verifie si un module(ensemble d'action) existe ou pas dans la liste des action (1-existe/0 pas)
     * 
     * $checkaction: Verifie si une action existe ou pas dans la liste des droits (1-existe/0 pas)
     * 
     * $actions: Tableau allant prendre les actions (droits) formatees
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig listeDroits.html.twig
     *  
     */
    #[Route('/admin/getListedroit', name: 'admin_getListedroit')]
    public function getListedroitAction(): Response(string $locale): Response {

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        $idProfil = $request->request->get('idProfil');

        $em = $this->entityManager;

        //recuperons le profil
        $thisProfil = $em->getRepository("App\Entity\Profil")->find($idProfil);
        //recuperons les droits de ce profil
        $thisDroits = unserialize($em->getRepository("App\Entity\droit")->find($idProfil)->getDroits());
        // var_dump($thisDroits);
        $actions = array();
        //on récupère la liste des modules
        $listeModules = $em->getRepository("App\Entity\Module")->findBy(array('client' => 0));
        //var_dump($listeModules);
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        foreach ($listeModules as $l) {
            //Pour chaque module trouvé on recupère la liste de ses actions
            $listeActions = $em->getRepository("App\Entity\Action")
                    ->getActionsByModule($l->getId());

            foreach ($listeActions as $act) {
                $checkall = ( isset($thisDroits[$l->getId()]) ) ? 1 : 0;
                $checkaction = ( isset($thisDroits[$l->getId()]) && in_array($act->getId(), $thisDroits[$l->getId()]) ) ? 1 : 0;
                // on format la liste des actions
                $actions[$l->getId() . "|" . $checkall . "|" . $l->getLibmodule()][] = array(
                    'libAction' => $act->getLibAction(),
                    'DescriptionAction' => $act->getDescriptionAction(),
                    'idAction' => $act->getId() . "|" . $checkaction,
                    'idcontroleur' => $act->getControleur(),
                );
            }
        }
        return $this->render('admin/listeDroits.html.twig', array("idProfil" => $idProfil, 'listeActions' => $actions, 'thisProfil' => $thisProfil, 'locale' => $locale, 'listestat' => $listestat));
    }

    /**
     * Fonction permettant de mettre a jour les droits d'un profil donne - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $formData: donnee recuperee par session
     * 
     * $data: donnee recuperee par session formatee
     * 
     * $droit: Liste des droits relatifs au profil en question
     * 
     * $listeModules:  Liste des modules
     * 
     * $thisDroits: Liste des droits relatifs a un profil
     * 
     * $droit: Tableau allant prendre les actions (droits) formatees
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <array> resulat d'execution
     *  
     */
    function updateDroitsAction(string $locale) {
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        //on recupere les donnees envoyees depuis le formulaire des droits
        $formData = $request->request->get('formdata');

        $data = array();
        parse_str($formData, $data);

        /* Exemple de contenu du tableau droits */
        /* Array
          (
          [1] => Array
          (
          [0] => 3,
          [1] => 2,
          [2] => 1,
          [3] => 6
          )

          [2] => Array
          (
          [0] => 7,
          [1] => 5,
          [2] => 4,
          [3] => 8
          )

          [3] => Array
          (
          [0] => 11,
          [1] => 10,
          [2] => 9,
          [3] => 12
          )

          ) */

        //on récupère la liste des profils
        $listeProfiles = $em->getRepository("App\Entity\Profil")
                ->getListeProfileActifs();
        //on récupère la liste des modules 
        $listeModules = $em->getRepository("App\Entity\Module")->findBy(array("client" => 0));
        try {
            foreach ($listeProfiles as $profile) {
                $droit = array();
                $idprofil = $profile->getId();
                foreach ($listeModules as $l) {
                    if (isset($data['action_module_' . $l->getId() . '_' . $idprofil]) && !empty($data['action_module_' . $l->getId() . '_' . $idprofil])) {
                        $droit[$l->getId()] = $data['action_module_' . $l->getId() . '_' . $idprofil];
                    }
                }
                //var_dump($droit);
                //echo $idprofil;
                // Récupération des droits du profil 
                $thisDroits = $em->getRepository("App\Entity\droit")->findOneBy(array("profil" => $idprofil));
                $thisDroits->setDroits(serialize($droit));
                $em->flush();
            }
            return new Response(json_encode(array("result" => "success")));
        } catch (exception $e) {
            $Utils = $this->utils;
            $Utils->log(__METHOD__ . " : " . $e);
            return new Response(json_encode(array("result" => "error")));
        }
    }

    /**
     * Fonction permettant de lister les menus donne - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $formData: donnee recuperee par session
     * 
     * $gpMenu: Instance d'un groupe de menu donne
     * 
     * $groupeMenus: Liste des groupes de menus question
     * 
     * $listestat:  Liste de statistiques type 5 comme explique dans StatistiqueRepository
     * 
     * $boxinfos: Texte d'informations 
     * 
     * $listePmenu: Liste des menus principaux
     * 
     * $listeRubrique: Liste dezs rubriques 
     * 
     * $listeArticle: Liste des articles
     * 
     * $listePage: Liste des pages
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig menus.html.twig
     *  
     */
    #[Route('/admin/menus', name: 'admin_menus')]
    public function menusAction(): Response(string $locale): Response {

        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'menusAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        
        if ($request->getMethod() == 'POST') {
            $formData = $request->request->get('formdata');
            $data = array();
            parse_str($formData, $data);
            $data['locale'] = $locale;
            try {
                $gpMenu = $em->getRepository("utbAdminBundle/GroupeMenu")->find($data["idgroupe"]);
                $gpMenu->setLibGroupeMenu($data['libelle']);
                $gpMenu->setCommentaireGroupeMenu($data['commentaire']);
                $local = $request->request->get('locals');
                $gpMenu->setTranslatableLocale($local);
                $em->persist($gpMenu);
                $em->flush();
                return new Response(json_encode(array("result" => "success", "data" => $data)));
            } catch (exception $e) {
                return new Response(json_encode(array("result" => "failed", "data" => $data)));
            }
        }

        //Récuperons la liste des Groupes menu
        $groupeMenus = $em->getRepository("utbAdminBundle/GroupeMenu")->findByLocale($locale);
        //recuperons la liste des menu parents 
        $listePmenu = $em->getRepository("admin/Menu")->findParent($locale);
        //récupérons la liste des rubrique
        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findAll();
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);


        //récupérons la liste de toutes les articles
        $listeArticle = $em->getRepository("admin/Article")->findBy(array("statutArticle" => 3));
        //recuperons la liste des pages standard
        $listePage = $em->getRepository("utbAdminBundle/Squelettepage")->findAll();

        $boxinfos = $this->entityManager
                ->getRepository("App\Entity\Parametrage")
                ->getTexteBoxInfos($locale, int $type = 4);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat( 5, $locale, 0, null);

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        return $this->render('admin/menus.html.twig', array('locale' => $locale, 'GroupeMenu' => $groupeMenus, "listePmenu" => $listePmenu, "rubriques" => $listeRubrique, "articles" => $listeArticle, "pages" => $listePage, 'infos' => $boxinfos, 'listestat' => $listestat));
    }

    /**
     * Fonction permettant de sauvegarder des menus ajoutes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $formData: donnee recuperee par session
     * 
     * $data = Tableau de donnees formatees
     * 
     * $Menu: Instance de la classe Menu recuperee si elle existe/ sinon creee
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <json> Etat du traitement succes/erreur
     *  
     */
    #[Route('/admin/ajoutMenuSave', name: 'admin_ajoutMenuSave')]
    public function ajoutMenuSaveAction(): Response(): Response: Response {

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $formData = $request->request->get('formdata');
        $locale = $request->request->get('locale');
        $em = $this->entityManager;

        $data = array();
        parse_str($formData, $data);
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        //return new Response(json_encode(array("locale" => $locale, )));
        try {

            if ($data['modif_menu_id'] != "") {


                $Menu = $em->getRepository("admin/Menu")->find($data['modif_menu_id']);
                $gpMenu = $em->getRepository("admin/GroupeMenu")->find($data["idgroupemenu"]);

                $Menu->setTranslatableLocale($data["locale"]);
                $em->refresh($Menu);
                $Menu->setGroupeMenu($gpMenu);
                $Menu->setIdParentMenu($data["menuParent"]);
                $Menu->setLibMenu($data["titreMemu"]);
                $Menu->setTypeMenu($data["typeMenu"]);
                $Menu->setUrlExterneMenu($data["url_menu"]);
                $Menu->setMenuDateModif(new \Datetime());
                $Menu->setMenuModifPar($this->security->getToken()->getUser()->getProfil()->getId());
                $em->persist($Menu);
                $em->flush();
                //ar_dump($data);
                return new Response(json_encode(array("result" => "success", "data" => $data)));
            } else {
                $menu = new Menu();
                $gpMenu = $em->getRepository("admin/GroupeMenu")->find($data["idgroupemenu"]);
                $menu->setGroupeMenu($gpMenu);
                $menu->setIdParentMenu($data["menuParent"]);
                $menu->setLibMenu($data["titreMemu"]);
                $menu->setTypeMenu($data["typeMenu"]);
                $menu->setUrlExterneMenu($data["url_menu"]);
                $menu->setMenuAjoutPar($this->security->getToken()->getUser()->getProfil()->getId());
                $em->persist($menu);
                $em->flush();
                //on va essayer d'ajouter en meme temps le menu dans l'ordre
                if ($data["menuParent"] == 0) {//s'il d'agit d'un menu parent
                    $thisOrdre = $em->getRepository("utbAdminBundle/Ordre")->findOneBy(array("nomTable" => "MenuParent"));
                    $ordre = unserialize($thisOrdre->getOrdre());
                    array_push($ordre, $menu->getId());
                    $thisOrdre->setOrdre(serialize($ordre));
                    $em->flush();
                } else {//S'il sagit d'un menu fils
                    $thisOrdre = $em->getRepository("utbAdminBundle/Ordre")->findOneBy(array("nomTable" => "MenuFils"));
                    $ordre = unserialize($thisOrdre->getOrdre());
                    if (isset($ordre[$data["menuParent"]]))
                        array_push($ordre[$data["menuParent"]], $menu->getId());
                    else
                        $ordre[$data["menuParent"]] = $menu->getId();

                    $thisOrdre->setOrdre(serialize($ordre));
                    $em->flush();
                }
                return new Response(json_encode(array("result" => "success", "data" => $data)));
            }
        } catch (exception $e) {
            return new Response(json_encode(array("result" => "failed", "data" => $data)));
        }
    }

    /**
     * Fonction permettant de creer des groupes de menu - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $groupeMenu/ Instance de la classe Groupemenu a ajouter
     * 
     * $listestat:  Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $Menu: Instance de la classe Menu recuperee si elle existe/ sinon creee
     * 
     * @return <string> return sur le twig ajoutGroupeMenu.html.twig
     *  
     */
    #[Route('/admin/ajouterGroupeMenu', name: 'admin_ajouterGroupeMenu')]
    public function ajouterGroupeMenuAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajouterGroupeMenuAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //          

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $groupeMenu = new GroupeMenu;
        $formBuilder = $this->createFormBuilder($groupeMenu);
        $formBuilder
                ->add('libGroupeMenu', 'text', array('required' => true));
        $form = $formBuilder->getForm();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        if ($request->getMethod() == 'POST') {

            $form->bind($request);
            $em->persist($groupeMenu);
            $em->flush();
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('admin_menus', [
                                'locale' => $locale, 'listestat' => $listestat,]));
        }
        return $this->render('admin/ajoutGroupeMenu.html.twig', array('locale' => $locale, 'form' => $form->createView(), 'listestat' => $listestat,));
    }

    /**
     * Fonction permettant d'ajouter des menus - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $menu: Instance de la classe Menu a ajouter
     * 
     * $listeGmenu: Instances de la classe Groupemenu
     * 
     * $listePmenu: Instances de la classe Menu de type menu principal
     * 
     * $listestat:  Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listeRubrique: Liste des rubriques
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutMenu.html.twig
     *  
     */
    #[Route('/admin/ajoutMenu', name: 'admin_ajoutMenu')]
    public function ajoutMenuAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajoutMenuAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $menu = new Menu();
        //recuperons la liste des groupe de menu
        $listeGmenu = $em->getRepository("admin/GroupeMenu")->findAll();
        $gMenu = array();

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        foreach ($listeGmenu as $l) {
            $gMenu[$l->getId()] = $l->getLibGroupeMenu();
        }
        //recuperons la liste des menu parents 
        $listePmenu = $em->getRepository("utbAdminBundle/Menu")->findBy(array('idParentMenu' => 0));
        $pMenu = array("Choisir un parent");
        foreach ($listePmenu as $l) {
            $pMenu[$l->getId()] = $l->getLibGroupeMenu();
        }
        $formBuilder = $this->createFormBuilder($menu);
        $formBuilder
                ->add('groupeMenu', 'choice', array(
                    'choices' => $gMenu,
                    'data' => 1
                ))
                ->add('libMenu', 'text', array('required' => true))
                ->add('urlExterneMenu', 'text', array('required' => true, "disabled" => true))
                ->add('idParentMenu', 'choice', array(
                    'choices' => $pMenu,
                    'data' => 0
                ))
                ->add('menuAjoutPar', 'hidden', array('data' => $user = $this->security->getToken()->getUser()->getId()))
                ->add('typeMenu', 'choice', array(
                    'choices' => array("Sélectionnez une entrée", "Accueil", "Article d'une rubrique", "Liste ou arborescence de rubriques", "Rubriques", "Article", "Se connecter", "Lien arbitraire", "Lien vers un squelette de page"),
        ));
        $form = $formBuilder->getForm();

        //récupérons la liste des rubrique
        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findAll();
                $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getListeDeRubriques($locale);

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        return $this->render('admin/ajoutMenu.html.twig', array('locale' => $locale, 'form' => $form->createView(), 'rubriques' => $listeRubrique));
    }

    /**
     * Fonction permettant d'ajouter/modifier des parametres de type 1 ou 2 donne - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $count0: Cas de ou le texte accueil est un texte Ã  saisir
     * 
     * $count1: Cas de ou le texte accueil est un descriptif d'accueil
     * 
     * $letablo: Tableau listant les options d'enregistrement
     * 
     * $parametrage = Instance de la classe Parametrage a ajouter ou modifier 
     * 
     * $listestat:  Liste de statistiques type 5 comme explique dans StatistiqueRepository
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutParametres.html.twig
     *  
     */
    #[Route('/admin/ajoutParametres', name: 'admin_ajoutParametres')]
    public function ajoutParametresAction(): Response(string $locale): Response {

        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        //code qui verifie si l'utilisateur courant a acces a cette action

        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajoutParametresAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }



        $count0 = $this->entityManager
                ->getRepository("App\Entity\Parametrage")
                ->getSiExistInfos(1, $locale); //texte a saisir

        $count1 = $this->entityManager
                ->getRepository("App\Entity\Parametrage")
                ->getSiExistInfos(2, $locale); //description article       

        if (($count0 == 1) || ($count1 == 1)) {

            if ($count0 == 1) {
                $parametrage = $em->getRepository("App\Entity\Parametrage")->findOneBy(array('paramType' => 1));
                $parametrage->setTranslatableLocale($locale);
                $em->refresh($parametrage);
            }

            if ($count1 == 1) {
                $parametrage = $em->getRepository("App\Entity\Parametrage")->findOneBy(array('paramType' => 2));
                $parametrage->setTranslatableLocale($locale);
                $em->refresh($parametrage);
            }

            if ($locale == 'en') {
                $letablo = array(0 => "Select an option", 1 => "Written text ", 2 => "An article's description");
            } elseif ($locale == 'fr') {
                $letablo = array(0 => "Sélectionnez une entrée", 1 => "Texte à saisir ", 2 => "Description d'un article");
            }

            if (($count0 == 1) && ($count1 == 0)) {

                $formBuilder = $this->createFormBuilder($parametrage);
                $formBuilder
                        ->add('paramType', 'choice', array(
                            'choices' => $letablo,
                            'data' => 0))
//                ->add('paramDescription', 'textarea', array('required' => false, 'data' => $parametrage->getParamDescription()))
                        ->add('paramDescription', 'ckeditor', array(
                            //'required'=>true,
                            'transformers' => array('strip_js', 'strip_css', 'strip_comments'),
                            'toolbar' => array('skins', 'tools', 'insert', 'styles', 'document', 'links', 'basicstyles', 'editing', 'clipboard', 'paragraph'),
                            'toolbar_groups' => array(
                                'document' => array('Source', 'Code'),
                                'basicstyles' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                                'editing' => array('Find', 'Replace', '-', 'SelectAll'),
                                'clipboard' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                                'paragraph' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                                'links' => array('Link', 'Unlink', 'Anchor'),
                                'insert' => array('Image', 'Table', 'HorizontalRule'),
                                'styles' => array('Styles', 'Format'),
                                'tools' => array('Maximize', 'ShowBlocks'),
                                'skins' => array('moonocolor', 'kama')
                            ),
                            'filebrowser_image_browse_url' => array(
                                'url' => 'relative-url.php?type=file',
                            ),
                            'ui_color' => '#CCC',
                            'startup_outline_blocks' => false,
                            'width' => '98%',
                            'height' => '250',
                            'language' => $locale,
                            'data' => $parametrage->getParamDescription(),
                        ))
                        ->add('paramValeur', 'text', array('required' => false, 'data' => $parametrage->getParamValeur()))
                        ->add('paramTitre', 'text', array('required' => false, 'data' => $parametrage->getParamTitre()))
                        ->add('paramAjoutPar', 'hidden', array('data' => $user = $this->security->getToken()->getUser()->getId()));
            } elseif (($count1 == 1) && ($count0 == 0)) {

                $formBuilder = $this->createFormBuilder($parametrage);
                $formBuilder
                        ->add('paramType', 'choice', array(
                            'choices' => $letablo,
                            'data' => 0))
//                ->add('paramDescription', 'textarea', array('required' => true))      
                        ->add('paramDescription', 'ckeditor', array(
                            //'required'=>true,
                            'transformers' => array('strip_js', 'strip_css', 'strip_comments'),
                            'toolbar' => array('skins', 'tools', 'insert', 'styles', 'document', 'links', 'basicstyles', 'editing', 'clipboard', 'paragraph'),
                            'toolbar_groups' => array(
                                'document' => array('Source', 'Code'),
                                'basicstyles' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                                'editing' => array('Find', 'Replace', '-', 'SelectAll'),
                                'clipboard' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                                'paragraph' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                                'links' => array('Link', 'Unlink', 'Anchor'),
                                'insert' => array('Image', 'Table', 'HorizontalRule'),
                                'styles' => array('Styles', 'Format'),
                                'tools' => array('Maximize', 'ShowBlocks'),
                                'skins' => array('moonocolor', 'kama')
                            ),
                            'filebrowser_image_browse_url' => array(
                                'url' => 'relative-url.php?type=file',
                            ),
                            'ui_color' => '#CCC',
                            'startup_outline_blocks' => false,
                            'width' => '98%',
                            'height' => '250',
                            'language' => $locale,
                            'data' => $parametrage->getParamDescription(),
                        ))
                        ->add('paramTitre', 'text', array('required' => false, 'data' => $parametrage->getParamTitre()))
                        ->add('paramValeur', 'text', array('required' => false, 'data' => $parametrage->getParamValeur()))
                        ->add('paramAjoutPar', 'hidden', array('data' => $user = $this->security->getToken()->getUser()->getId()));
            }
        } elseif (($count0 == 0) && ($count1 == 0)) {

            $parametrage = new Parametrage();
            $parametrage->setTranslatableLocale($locale);

            $formBuilder = $this->createFormBuilder($parametrage);
            $formBuilder
                    ->add('paramType', 'choice', array(
                        'choices' => array(0 => "Sélectionnez une entrée", 1 => "Texte Ã  saisir ", 2 => "Description d'un article"),
                        'data' => 0
                    ))
                    ->add('paramTitre', 'text', array('data' => 'None'))
                    ->add('paramValeur', 'text', array('required' => false, 'data' => $parametrage->getParamValeur()))
                    //->add('paramDescription', 'textarea', array('required' => true))
                    ->add('paramDescription', 'ckeditor', array(
                        //'required'=>true,
                        'transformers' => array('strip_js', 'strip_css', 'strip_comments'),
                        'toolbar' => array('skins', 'tools', 'insert', 'styles', 'document', 'links', 'basicstyles', 'editing', 'clipboard', 'paragraph'),
                        'toolbar_groups' => array(
                            'document' => array('Source', 'Code'),
                            'basicstyles' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                            'editing' => array('Find', 'Replace', '-', 'SelectAll'),
                            'clipboard' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                            'paragraph' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                            'links' => array('Link', 'Unlink', 'Anchor'),
                            'insert' => array('Image', 'Table', 'HorizontalRule'),
                            'styles' => array('Styles', 'Format'),
                            'tools' => array('Maximize', 'ShowBlocks'),
                            'skins' => array('moonocolor', 'kama')
                        ),
                        'filebrowser_image_browse_url' => array(
                            'url' => 'relative-url.php?type=file',
                        ),
                        'ui_color' => '#CCC',
                        'startup_outline_blocks' => false,
                        'width' => '98%',
                        'height' => '250',
                        'language' => $locale,
                    ))
                    ->add('paramAjoutPar', 'hidden', array('data' => $user = $this->security->getToken()->getUser()->getId()));
        }
        //var_dump($parametrage);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 5, $locale, 0, null);

        $tab = array();

        $tab[] = $count0;
        $tab[] = $count1;

        $form = $formBuilder->getForm();
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            $parametrage = $form->getData();

            if ($parametrage->getParamTitre() == null)
                $parametrage->setParamTitre('None');

           $unarticle = null;

            if ($parametrage->getParamType() == 1) {
                $parametrage->setParamValeur(0);
            }

            if ($parametrage->getParamType() == 2) {
                $parametrage->setParamTitre('None');
                $parametrage->setParamDescription('None');
            }

            //if ($count1 == 1) {
            //$unarticle = $em->getRepository("App\Entity\Article")->findOneBy(array('id' => $parametrage->getParamValeur()));
            $unarticle = $em->getRepository("admin/Article")->find($parametrage->getParamValeur());
            // }

            if (($unarticle == null) && ($parametrage->getParamValeur() > 0)) {
                
                $msgnotification = '';
                $msgnotification = $this->translator->trans('notification.parametres.articleinexistant');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);
                return $this->render('admin/ajoutParametres.html.twig', array('locale' => $locale, 'form' => $form->createView(), 'listestat' => $listestat, 'tab' => $tab));
            } elseif (($unarticle != null) && ( ($unarticle->getDescriptionArticle() == null) || (trim($unarticle->getDescriptionArticle()) == ""))) {
               
                $msgnotification = '';
                $msgnotification = $this->translator->trans('notification.parametres.descinexsitant');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);
                return $this->render('admin/ajoutParametres.html.twig', array('locale' => $locale, 'form' => $form->createView(), 'listestat' => $listestat, 'tab' => $tab));
            } else {
                // var_dump('koko');
                $em->persist($parametrage);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale,]));
        }


        return $this->render('admin/ajoutParametres.html.twig', array('locale' => $locale, 'form' => $form->createView(), 'listestat' => $listestat, 'tab' => $tab));




        /* $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

          //code qui verifie si l'utilisateur courant a acces a cette action
          $em = $this->entityManager;
          $accessControl = $this->accessControl;
          $checkAcces = $accessControl->verifAcces($em, 'ajoutMenuAction', $this->container->get);

          if (!$checkAcces) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
          }

          // Table ne  devant pas Ãªtre vide
          // la première ligne de la table gère le cas d'affichage
          $parametrage = $em->getRepository("admin/Parametrage")->findOneBy(array('id' => 1));

          //if ($parametrage !=null){
          $formBuilder = $this->createFormBuilder($parametrage);
          $formBuilder
          ->add('paramType', 'choice', array(
          'choices' => array(0 => "Sélectionnez une entrée", 1 => "Texte Ã  saisir ", 2 => "Description d'un article")
          ))
          ->add('paramDescription', 'textarea', array('required' => false, 'data' => $parametrage->getParamDescription()))
          ->add('paramTitre', 'text', array('required' => false, 'data' => $parametrage->getParamTitre()))
          ->add('paramValeur', 'text', array('required' => false, 'data' => $parametrage->getParamValeur()))
          ->add('paramAjoutPar', 'hidden', array('data' => $user = $this->security->getToken()->getUser()->getId()));

          $form = $formBuilder->getForm();

          $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

          if ($request->isMethod('POST')) {
          $form->handleRequest($request);
          $parametrage = $form->getData();

          if (($parametrage->getparamTitre() == '') or ($parametrage->getparamTitre() == null))
          $parametrage->setparamTitre('None');

          if (($parametrage->getparamDescription() == '') or ($parametrage->getparamDescription() == null))
          $parametrage->setparamDescription('None');

          if (($parametrage->getparamValeur() == '') or ($parametrage->getparamValeur() == null))
          $parametrage->setparamValeur(0);

          $unarticle = $em->getRepository("admin/Article")->findOneBy(array('id' => $parametrage->getParamValeur()));

          if ( ($unarticle == null) && ($parametrage->getParamValeur()>0) ) {
          $msgnotification = '';
          $msgnotification = $this->translator->trans('notification.inexistant', array(
          '%variable%' => 'Article'
          ));

          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);
          } else {

          $em->persist($parametrage);
          $em->flush();

          $msgnotification = '';
          $msgnotification = $this->translator->trans('notification.Mise Ã  jour');
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

          return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale,]));
          }
          }

          $listestat = $this->entityManager
          ->getRepository('App\Entity\Statistique')
          ->getInfoOrStat(int $typeStat = 5, $locale = 'fr', int $valeur = 0, $article = null);

          return $this->render('admin/ajoutParametres.html.twig', array('locale' => $locale, 'form' => $form->createView(), 'listestat' => $listestat, 'parametrage' => $parametrage)); */
    }

    /**
     * Fonction permettant d'ajouter/modifier des parametres de type 3,4,5,6 ou 7 - Backoffice
     * Ce sont les textes d'information qui seront affichés dans la partie gauche des pages concernées. 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $type/ Type de parametres
     *   
     *    int $type = 3 Parametres = Texte box infos sur la corbeille
     * 
     *    int $type = 4 Parametres = Texte box infos sur les Menus
     * 
     *    int $type = 5 Parametres = Texte box infos sur la liste des Archives
     * 
     *    int $type = 6 Parametres = Texte box infos sur les Profils
     * 
     *    int $type = 7 Parametres = Texte a propos des rubriques
     * 
     *    int $type = 8 Parametres = Texte a propos des rubriques
     * 
     *    int $type = 9 Parametres = Texte a propos des rubriques
     * 
     *    int $type = 10 Parametres = Texte a propos des rubriques
     * 
     *    int $type = 11 Parametres = Texte a propos des rubriques
     * 
     *    int $type = 17 Parametres = Texte a propos des rubriques
     * 
     *    int $type = 18 Parametres = Texte a propos des rubriques
     * 
     *    int $type = 19 Parametres = Texte a propos des rubriques
     * 
     *    int $type = 20 Parametres = Texte a propos des rubriques 
     * 
     *    int $type = 21 Parametres = Texte a propos des rubriques
     * 
     * $lecount/ Verifie si le le parametre type existe deja ou pas
     * 
     * $parametrage = Instance de la classe Parametrage a ajouter ou modifier 
     * 
     * $listestat/  Liste de statistiques type 5 comme explique dans StatistiqueRepository
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $type Variable passee pour gerer le type de parametres 
     * 
     * @return <string> return sur le twig ajoutInfosBox.html.twig
     *  
     */
    #[Route('/admin/ajoutTexteMenuArchCorb', name: 'admin_ajoutTexteMenuArchCorb')]
    public function ajoutTexteMenuArchCorbAction(): Response(string $type, string $locale): Response {
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;

        $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteMenuArchCorbAction', $this->container->get);
            
        /*if ($type == 3) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteCorbAction', $this->container->get);
        } elseif ($type == 4) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteMenuAction', $this->container->get);
        } elseif ($type == 5) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteArchAction', $this->container->get);
        } elseif ($type == 6) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteProfilAction', $this->container->get);
        } elseif ($type == 7) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteRubAction', $this->container->get);
        } elseif ($type == 8) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteLinksAction', $this->container->get);
        } elseif ($type == 9) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteUsersAction', $this->container->get);
        } elseif ($type == 11) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteRubGeneraleAction', $this->container->get);
        } elseif ($type == 21) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteArtGeneralAction', $this->container->get);
        } elseif ($type == 17) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteArtFaqAction', $this->container->get);
        } elseif ($type == 18) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteArtBreveAction', $this->container->get);
        } elseif ($type == 19) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteArtActualAction', $this->container->get);
        } elseif ($type == 20) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteArtPubAction', $this->container->get);
        } elseif ($type == 10) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteGroupeAction', $this->container->get);
        }elseif ($type == 22) {
            $checkAcces = $accessControl->verifAcces($em, 'ajoutTexteArbreAction', $this->container->get);
        }*/

        if (!isset($checkAcces) or !$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $lecount = $this->entityManager
                ->getRepository("App\Entity\Parametrage")
                ->getSiExistInfos($type, $locale);


        if ($lecount == 1) {   //traduction ou modification si la version traduite existe déjÃ 
            $parametrage = $em->getRepository("App\Entity\Parametrage")->findOneBy(array('paramType' => $type));
            $parametrage->setTranslatableLocale($locale);
            $em->refresh($parametrage);

            ///var_dump($parametrage);

            $formBuilder = $this->createFormBuilder($parametrage);
            $formBuilder
                    ->add('paramType', 'hidden', array('data' => $type))
                    ->add('paramTitre', 'hidden', array('data' => 'None'))
                    ->add('TypeDescription', 'text', array('required' => true, 'data' => $parametrage->getTypeDescription()))
                    ->add('paramDescription', 'ckeditor', array(
                        //'required'=>true,
                        'transformers' => array('strip_js', 'strip_css', 'strip_comments'),
                        'toolbar' => array('skins', 'tools', 'insert', 'styles', 'document', 'links', 'basicstyles', 'editing', 'clipboard', 'paragraph'),
                        'toolbar_groups' => array(
                            'document' => array('Source', 'Code'),
                            'basicstyles' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                            'editing' => array('Find', 'Replace', '-', 'SelectAll'),
                            'clipboard' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                            'paragraph' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                            'links' => array('Link', 'Unlink', 'Anchor'),
                            'insert' => array('Image', 'Table', 'HorizontalRule'),
                            'styles' => array('Styles', 'Format'),
                            'tools' => array('Maximize', 'ShowBlocks'),
                            'skins' => array('moonocolor', 'kama')
                        ),
                        'filebrowser_image_browse_url' => array(
                            'url' => 'relative-url.php?type=file',
                        ),
                        'ui_color' => '#CCC',
                        'startup_outline_blocks' => false,
                        'width' => '98%',
                        'height' => '250',
                        'language' => $locale,
                        'data' => $parametrage->getParamDescription(),
                    ))
                    ->add('paramAjoutPar', 'hidden', array('data' => $user = $this->security->getToken()->getUser()->getId()));
        } elseif ($lecount == 0) {

            $parametrage = new Parametrage();
            $parametrage->setTranslatableLocale($locale);

            $formBuilder = $this->createFormBuilder($parametrage);
            $formBuilder
                    ->add('paramType', 'hidden', array('data' => $type))
                    ->add('paramTitre', 'hidden', array('data' => 'None'))
                    ->add('TypeDescription', 'text', array('required' => true, 'data' => $parametrage->getTypeDescription()))
                    ->add('paramDescription', 'ckeditor', array(
                        //'required'=>true,
                        'transformers' => array('strip_js', 'strip_css', 'strip_comments'),
                        'toolbar' => array('skins', 'tools', 'insert', 'styles', 'document', 'links', 'basicstyles', 'editing', 'clipboard', 'paragraph'),
                        'toolbar_groups' => array(
                            'document' => array('Source', 'Code'),
                            'basicstyles' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                            'editing' => array('Find', 'Replace', '-', 'SelectAll'),
                            'clipboard' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                            'paragraph' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                            'links' => array('Link', 'Unlink', 'Anchor'),
                            'insert' => array('Image', 'Table', 'HorizontalRule'),
                            'styles' => array('Styles', 'Format'),
                            'tools' => array('Maximize', 'ShowBlocks'),
                            'skins' => array('moonocolor', 'kama')
                        ),
                        'filebrowser_image_browse_url' => array(
                            'url' => 'relative-url.php?type=file',
                        ),
                        'ui_color' => '#CCC',
                        'startup_outline_blocks' => false,
                        'width' => '98%',
                        'height' => '250',
                        'language' => 'fr',
                    ))
                    ->add('paramAjoutPar', 'hidden', array('data' => $user = $this->security->getToken()->getUser()->getId()));
        }
        $form = $formBuilder->getForm();

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            $parametrage = $form->getData();

            if ($parametrage->getParamTitre() == null)
                $parametrage->setParamTitre('None');

            if ($type == 10) {
               //$parametrage->setTypeDescription($parametrage->getParamTitre());
              $parametrage->setParamTitre($parametrage->getTypeDescription());  
            }

            $em->persist($parametrage);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_listeboxinfos', ['locale' => $locale,]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 5, $locale, 0, null);
        return $this->render('admin/ajoutInfosBox.html.twig', array('locale' => $locale, 'form' => $form->createView(), 'listestat' => $listestat, 'type' => $type,));
    }

    /**
     * Fonction permettant d'afficher la liste des liens - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $typedemenu: Pour différencier les menus parents des menus fils
     * 
     * $listestat:  Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $boxinfos: Le message d'information a afficher sur la page
     * 
     * $menuParents: Ce sont les menus du plus haut niveau ou 1er niveau.
     * Exemple du menu PRESENTATION du site publique.
     * 
     * $typedemenu: Type d'appartenance du menu
     * int $typedemenu = 0 :  
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $groupe Variable passee pour gerer ...
     * 
     * @return <string> return sur le twig ajoutInfosBox.html.twig
     *  
     */
    #[Route('/admin/listeLiens', name: 'admin_listeLiens')]
    public function listeLiensAction(): Response(int $groupe = 1, string $locale, int $typedemenu = 0): Response {
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeLiensAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        // Texte d'informations Ã  afficher sur la liste des liens de menus
        $boxinfos = $this->entityManager
                ->getRepository("App\Entity\Parametrage")
                ->getTexteBoxInfos($locale, int $type = 8);

        //Recuperons les menus du groupes
        $menuParents = $em->getRepository("App\Entity\Menu")->findMenusParent($groupe, $locale);
        $listeLiens = array();

        foreach ($menuParents as $mp) {

            $t = $em->getRepository("App\Entity\Menu")->getTextTypeMenu($mp->getTypeMenu());
            $im = $em->getRepository("App\Entity\Menu")->getImageTypeMenu($mp->getTypeMenu());

            if ($typedemenu == 0) { //Je fais en mï¿½me temps le tri pour les menus fils.
                $thisOrdre = $em->getRepository("App\Entity\Ordre")->findOneBy(array("nomTable" => "MenuFils"));
                //var_dump(unserialize($thisOrdre->getOrdre()));
                $ordre = unserialize($thisOrdre->getOrdre());
                if (array_key_exists($mp->getId(), $ordre)) {
                    $menuFilsIDs = $ordre[$mp->getId()];
                    $listeLiens[$mp->getLibMenu() . "|" . $mp->getId() . "|" . $t . "|" . $im . "|" . $groupe] = array();
                    // print_r($menuFilsIDs);
                    if (!empty($menuFilsIDs)) {
                        foreach ($menuFilsIDs as $mID) {
                            $mFils = $em->getRepository("App\Entity\Menu")->find($mID);

                            if ($mFils != null) {
                                $type = $em->getRepository("App\Entity\Menu")->getTextTypeMenu($mFils->getTypeMenu());
                                $image = $em->getRepository("App\Entity\Menu")->getImageTypeMenu($mFils->getTypeMenu());
                                $listeLiens[$mp->getLibMenu() . "|" . $mp->getId() . "|" . $t . "|" . $im . "|" . $groupe][] = array(
                                    "id" => $mFils->getId(),
                                    "libelle" => $mFils->getLibMenu(),
                                    "typeMenu" => $type,
                                    "imageMenu" => $image,
                                );
                            }
                        }
                    }
                }

                /*
                  $menuFils = $em->getRepository("admin/Menu")->findMenuFils($mp->getId(),$locale);
                  $listeLiens[$mp->getLibMenu() . "|" . $mp->getId() . "|" . $t . "|" . $im. "|".$groupe] = array();
                  foreach ($menuFils as $mf) {

                  $type = $em->getRepository("admin/Menu")->getTextTypeMenu($mf->getTypeMenu());
                  $image = $em->getRepository("admin/Menu")->getImageTypeMenu($mf->getTypeMenu());
                  $listeLiens[$mp->getLibMenu() . "|" . $mp->getId() . "|" . $t . "|" . $im. "|".$groupe][] = array(
                  "id" => $mf->getId(),
                  "libelle" => $mf->getLibMenu(),
                  "typeMenu" => $type,
                  "imageMenu" => $image,
                  );
                  }
                 */
            } elseif ($typedemenu != 0) {

                $listeLiens[] = array(
                    "id" => $mp->getId(),
                    "libelle" => $mp->getLibMenu(),
                    "typeMenu" => $t,
                    "imageMenu" => $im,
                );
            }
        }

        //Trie des menus parents selon l'ordre dans la table ordre
        if ($typedemenu != 0) {
            $thisOrdre = $em->getRepository("utbAdminBundle/Ordre")->findOneBy(array("nomTable" => "MenuParent"));
            //
            $ordre = unserialize($thisOrdre->getOrdre());

            $listeLiensTemp = array();
            if (count($ordre) == count($listeLiens)) { //ce controle est juste pour au cas ou l'ordre n'est pas encore definit ou
                //un nouveau menu ï¿½ ï¿½tï¿½ ajoutï¿½ mais n'existe pas dans le rang. C'est juste pour etre sur
                //pour ï¿½viter cela, il faudrait s'assurer que quand un menu est crï¿½e, on l'ajoute dans l'ordre aussitot.
                foreach ($ordre as $pos => $id) {
                    foreach ($listeLiens as $p => $d) {
                        if ($id == $d['id']) {
                            $listeLiensTemp[] = array(
                                "id" => $d['id'],
                                "libelle" => $d['libelle'],
                                "typeMenu" => $d['typeMenu'],
                                "imageMenu" => $d['imageMenu'],
                            );
                        }
                    }
                }
            } else {//Tant qu'un menu n'existe pas dans l'ordre on zape l'ordre et on prends l'ajustement par defaut ds la table menu.
                $listeLiensTemp = $listeLiens;
            }
        }
        //Récuperons la liste des Groupes menu
        $groupeMenus = $em->getRepository("utbAdminBundle/GroupeMenu")->findAll();
        //recuperons la liste des menu parents 
        $listePmenu = $em->getRepository("utbAdminBundle/Menu")->findBy(array('idParentMenu' => 0));
        //récupérons la liste des rubrique
        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findAll();
                $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getListeDeRubriques($locale);

        // var_dump($listeLiens);exit;
        //récupérons la liste de toutes les articles
        $listeArticle = $em->getRepository("App\Entity\Article")->findBy(array("statutArticle" => 4));
        //recuperons la liste des pages standard
        $listePage = $em->getRepository("App\Entity\Squelettepage")->findAll();

        if ($typedemenu == 0) {
            return $this->render('admin/listeLiens.html.twig', array('locale' => $locale, "listeLiens" => $listeLiens, 'GroupeMenu' => $groupeMenus, "listePmenu" => $listePmenu,
                        "rubriques" => $listeRubrique, "articles" => $listeArticle, "pages" => $listePage,
                        'listestat' => $listestat, 'infos' => $boxinfos, 'groupe' => $groupe,
                        'typedemenu' => $typedemenu,));
        } else {
            return $this->render('admin/listeMenusParents.html.twig', array('locale' => $locale, "listeLiens" => $listeLiensTemp, 'GroupeMenu' => $groupeMenus, "listePmenu" => $listePmenu,
                        "rubriques" => $listeRubrique, "articles" => $listeArticle, "pages" => $listePage,
                        'listestat' => $listestat, 'infos' => $boxinfos, 'groupe' => $groupe,
                        'typedemenu' => $typedemenu,));
        }
    }

    /**
     * Fonction permettant d'avoir un groupe - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $idGroupe:  Identifiant d'un groupe recupere par ajax
     * 
     * $gpMenu: Objet Groupe de menu correspondant Ã  $idGroupe
     * 
     * $data: Tableau contenant le libelle et le commentaire d'un groupe de menu.
     * 
     * @return <json> Etat de traitement et $data
     *  
     */
    #[Route('/admin/getGroupe', name: 'admin_getGroupe')]
    public function getGroupeAction(): Response(): Response: Response {
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        
        $idGroupe = $request->request->get('idgroupe');
        $em = $this->entityManager;
        $locale = $request->request->get('local');
        $data = array();
        $gpMenu = $em->getRepository("App\Entity\GroupeMenu")->find($idGroupe);
        $gpMenu->setTranslatableLocale($locale);
        $em->refresh($gpMenu);
        $data["libelle"] = $gpMenu->getLibGroupeMenu();
        $data["commentaire"] = $gpMenu->getCommentaireGroupeMenu();
        $data["locale"] = $locale;
        return new Response(json_encode(array("result" => "success", "data" => $data)));
    }

    /**
     * Fonction permettant d'avoir un menu donné - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $idMenu:  Identifiant d'un menu recupere par ajax.
     * 
     * $locale: Variable de langue.
     * 
     * $data: Tableau contenant les informations du menu:
     * identifiant,libelle,idParent,type,locale,url du Menu.
     * 
     * @return <json> Etat de traitement et $data
     *  
     */
    #[Route('/admin/getMenu', name: 'admin_getMenu')]
    public function getMenuAction(): Response(): Response: Response {
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $idMenu = $request->request->get('idmenu');
        $locale = $request->request->get('locale');
        $em = $this->entityManager;

        $data = array();
        $Menu = $em->getRepository("App\Entity\Menu")->findOneMenuByLocale($idMenu, $locale);
        $data["id"] = $Menu[0]["id"];
        $data["libelle"] = $Menu[0]["libMenu"];
        $data["idParent"] = $Menu[0]["idParentMenu"];
        $data["type"] = $Menu[0]["typeMenu"];
        $data["locale"] = $locale;
        $data["urlExterneMenu"] = $Menu[0]["urlExterneMenu"];
        return new Response(json_encode(array("result" => "success", "data" => $data)));
    }

    /**
     * Fonction permettant d'ajouter une nature de document - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $user: Instance de la classe Utilisateur responsable de l'operation
     * 
     * $listestat:  Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $unenaturedoc: Instance de la classe NatureDoc a ajouter.
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutNatureDoc.html.twig
     *  
     */
    #[Route('/admin/ajoutNatureDoc', name: 'admin_ajoutNatureDoc')]
    public function ajoutNatureDocAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'ajoutNatureDocAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //          
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $user = $this->security->getToken()->getUser()->getId();
        $unenaturedoc = new NatureDoc();
        $form = $this->createForm(NatureDocType::class), $unenaturedoc);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        //$listenaturedoc = $em->getRepository("admin/NatureDoc")->findAll();

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unenaturedoc = $form->getData();
            $lib = $unenaturedoc->getLibNatureDoc();
            /**
             * Controle de champ vide libNatureDoc
             * 
             */
            if ($lib == "") {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'libnaturedocvide');
                return $this->render('admin/ajoutNatureDoc.html.twig', array(
                            'locale' => $locale,
                ));
            }

            $unenaturedoc = $unenaturedoc->setNatureDocAjoutPar($user);
            $em->persist($unenaturedoc);
            $em->flush();

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('admin_listenaturedoc', [
                                'locale' => $locale,
            ]));
        }


        return $this->render('admin/ajoutNatureDoc.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
        ));

        /* return $this->render('admin/listeNatureDoc.html.twig', array(
          'listenaturedoc' => $listenaturedoc,
          'locale' => $locale,'listestat'=>$listestat,'ajoutnatdoc'=>1,
          )); */
    }

    /**
     * Fonction permettant de modifier une nature de document - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $user/ Instance de la classe User responsable de l'operation
     * 
     * $listestat:  Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $unenature: Instance de la classe NatureDoc a modifier
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id Variable identifiant de l'instance de NatureDoc a modifier
     * 
     * @return <string> return sur le twig modifNatureDoc.html.twig
     *  
     */
    #[Route('/admin/modifierNatureDoc', name: 'admin_modifierNatureDoc')]
    public function modifierNatureDocAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'modifierNatureDocAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //    
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        // Récupération
        $unenature = $em->getRepository("admin/NatureDoc")->find($id);
        $user = $this->security->getToken()->getUser()->getId();
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité naturedoc 
        $form = $this->createForm(NatureDocType::class), $unenature);

        // On récupère les données du formulaire si il a déjÃ  été passé 
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {
            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
            /* Si le formulaire est valide, on valide et on redirige */
            // if ($form->isValid()) { 
            $lib = $unenature->getLibNatureDoc();
            /**
             * Controle de champ vide libNatureDoc
             * 
             */
            if ($lib == "") {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'libnaturedocvide');
                return $this->render('admin/modifNatureDoc.html.twig', array(
                            'locale' => $locale,
                ));
            }
            $unenature->setNatureDocModifPar($user);
            $unenature->setNatureDocDateModif(new \DateTime());
            $em->persist($unenature);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Nature Document modifiée avec succès');
            return $this->redirect($this->generateUrl('admin_listenaturedoc', ['locale' => $locale]));
            //  }
        }
        return $this->render('admin/modifNatureDoc.html.twig', array(
                    'form' => $form->createView(), 'id' => $id,
                    'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     * Fonction permettant de lister les natures de document - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listenaturedoc/ Liste des natures de documents
     * 
     * $listestat:  Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id Variable identifiant de l'instance de NatureDoc a modifier
     * 
     * @return <string> return sur le twig listeNatureDoc.html.twig
     *  
     */
    #[Route('/admin/listeNatureDoc', name: 'admin_listeNatureDoc')]
    public function listeNatureDocAction(): Response(string $locale, $ajoutnat): Response {

        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeNatureDocAction', $this->container->get);
        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        $listenaturedoc = $em->getRepository("utbAdminBundle/NatureDoc")->findAll();

        return $this->render('admin/listeNatureDoc.html.twig', array(
                    'listenaturedoc' => $listenaturedoc,
                    'locale' => $locale, 'listestat' => $listestat, 'ajoutnat' => $ajoutnat,
        ));
    }

    /**
     * Fonction permettant de supprimer une nature de document - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat:  Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $lanaturedoc: Instance de la classe NatureDoc a supprimer
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id Variable identifiant de l'instance de NatureDoc a supprimer
     * 
     * @return <string> retourne une redirection vers listeNatureDoc.html.twig, la liste des natures doc.
     *  
     */
    #[Route('/admin/supprNatureDoc', name: 'admin_supprNatureDoc')]
    public function supprNatureDocAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'supprNatureDocAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }
        //          
        //$dialog = $this->gethelperSet()->get('dialog') ;
        //if (!$dialog->askConfirmation($output,'<question>'Supprimer cet enregistrement?'</question>'){
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $lanaturedoc = $em->getRepository("admin/NatureDoc")->find($id);
        /* Enfin on supprime la categorie ... */
        $em->remove($lanaturedoc);
        $em->flush();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        $msgnotification = '';
        $msgnotification = $this->translator->trans('notification.suppression');
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

        /* ... et on redirige vers la page d'administration des profils */
        return $this->redirect($this->generateUrl('admin_listenaturedoc', array(
                            'locale' => $locale, 'listestat' => $listestat,)));
    }

    /**
     * Fonction permettant de supprimer une selection de natures de document - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $naturesIds/ Tableau regorgeant les ids des instances de la classe NatureDoc selectionnees ( recup par session)
     * 
     * $unenaturedoc: Instance de la classe NatureDoc a supprimer au niveau de la boucle
     * 
     * @return <json> return etat du traitement et redirection vers listeNatureDoc.html.twig
     *  
     */
    function supprAllnaturedocsAction(): Response: Response {
        $em = $this->entityManager;

        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'supprAllnaturedocsAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $naturesIds = $request->request->get('naturesIds');
        $naturesIds = explode("|", $naturesIds);
        foreach ($naturesIds as $key => $value) {
            if (!empty($value)) {
                $unenaturedoc = $em->getRepository("admin/NatureDoc")->find($value);
                $em->remove($unenaturedoc);
                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant d'activer une selection de natures de document - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $naturesIds/ Tableau regorgeant les ids des instances de la classe NatureDoc selectionnees ( recup par session)
     * 
     * $unenaturedoc: Instance de la classe NatureDoc a activer au niveau de la boucle
     * 
     * $user: Instance de la classe User responsable de l'operation
     * 
     * @return <json> return etat du traitement et redirection vers listeNatureDoc.html.twig
     *  
     */
    function activerAllnaturedocsAction(): Response: Response {

        $em = $this->entityManager;

        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'activerAllnaturedocsAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $naturesIds = $request->request->get('naturesIds');
        $naturesIds = explode("|", $naturesIds);
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($naturesIds as $key => $value) {
            if (!empty($value)) {
                $unenaturedoc = $em->getRepository("admin/NatureDoc")->find($value);

                //Activation     
                if (($unenaturedoc->getStatutNatureDoc() == 0) && ( $unenaturedoc != null )) {

                    $unenaturedoc->setNatureDocDateActive(new \Datetime());
                    $unenaturedoc->setNatureDocActivePar($user);
                    $unenaturedoc->setStatutNatureDoc(1);
                    $em->persist($unenaturedoc);
                    $em->flush();
                } else {
                    return new Response(json_encode(array("result" => "erreurvide")));
                }
            }/* else {
              return new Response( json_encode(array("result"=>"erreur")));
              } */
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de desactiver une selection de natures de document - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $naturesIds/ Tableau regorgeant les ids des instances de la classe NatureDoc selectionnees ( recup par session)
     * 
     * $unenaturedoc: Instance de la classe NatureDoc a desactiver au niveau de la boucle
     * 
     * $user: Instance de la classe User responsable de l'operation
     * 
     * @return <json> return etat du traitement et redirection vers listeNatureDoc.html.twig
     *  
     */
    function desactiverAllnaturedocsAction(): Response: Response {

        $em = $this->entityManager;

        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'desactiverAllnaturedocsAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $naturesIds = $request->request->get('naturesIds');
        $naturesIds = explode("|", $naturesIds);
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($naturesIds as $key => $value) {

            if (!empty($value)) {

                $unenaturedoc = $em->getRepository("admin/NatureDoc")->find($value);

                if ($unenaturedoc->getStatutNatureDoc() == 1) {
                    //Désactivation
                    $unenaturedoc->setNatureDocDateDesactive(new \Datetime());
                    $unenaturedoc->setNatureDocDesactivePar($user);
                    $unenaturedoc->setStatutNatureDoc(0);
                    $em->persist($unenaturedoc);
                    $em->flush();
                } else {
                    return new Response(json_encode(array("result" => "erreur")));
                }
            } /* else {
              return new Response( json_encode(array("result"=>"erreur")));
              } */
        }

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de supprimer un menu donne - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $idMenu/ Id de l'instance de la classe Menu (recup par session)
     * 
     * $Menus: Instances de la classe Menu a supprimer par boucle
     * 
     * $Menu: Instance de la classe Menu a supprimer
     * 
     * @return <json> return etat du traitement
     *  
     */
    #[Route('/admin/deleMenu', name: 'admin_deleMenu')]
    public function deleMenuAction(): Response(): Response: Response {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $idMenu = $request->request->get('idMenu');

        $Menus = $em->getRepository("admin/Menu")->findBy(array('idParentMenu' => $idMenu));
        foreach ($Menus as $m) {
            $em->remove($m);
        }

        $Menu = $em->getRepository("utbAdminBundle/Menu")->find($idMenu);
        $em->remove($Menu);

        $em->flush();

        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de repondre a un message donne - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $messagerep: Instance de la classe MessageReponse  a enregistrer
     * 
     * $unmessage: Instance de la classe Message auquel on doit repondre
     * 
     * $user: Instance de la classe User responsable de l'operation
     * 
     * $detailmessage: Details du message en question auquel on doit repondre
     * 
     * $envoimail: Atteste l'envoi reussi ou echoue du mail+
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id Variable identifiant de l'instance de Message auquel il faut repondre     
     * 
     * @return return sur le twig repondreMessage.html.twig
     *  
     */
    #[Route('/admin/repondreMessage', name: 'admin_repondreMessage')]
    public function repondreMessageAction(): Response(int $id, string $locale): Response {

        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'repondreMessageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }


        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $messagerep = new MessageReponse;

        $unmessage = $em->getRepository("App\Entity\Message")->find($id);
        $messagerep->setMessage($unmessage);

        $user = $this->security->getToken()->getUser()->getId();
        $unuser = $em->getRepository("App\Entity\User")->find($user);
        $messagerep->setUser($unuser);
        //avoir l'email de la personne qui Ã  envoyer le message    
        $detailmessage = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getdetailMessage($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);
        $nbreMsgNonLu = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getNombreNouveauMessage();

        $nbreMsgEnv = $this->entityManager
                ->getRepository('App\Entity\MessageReponse')
                ->getNombreMessageEnv();


        $nbreMsg = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getNombreMessage();

        $nbreMsgCorb = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getNombreCorbeille();


        $listeservice = $em->getRepository("admin/Service")->findAll();

        //var_dump($detailmessage);
        $email = $detailmessage[0]['mailInternaute'];

        $form = $this->createForm(MessageReponseType::class), $messagerep);
        /* On ne traite que les données passées en méthode POST */
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();


        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $messagerep = $form->getData();
            //var_dump($messagerep);
            //$uncontact = $unenaturedoc->setNatureDocAjoutPar($user);
            //var_dump($messagerep);
            $messagerep->setDestinatairesMsg($email);
            $messagerep->setMessageLu(0);
            $em->persist($messagerep);
            $em->flush();
             $emailEmetteur=$this->container->get->getParameter('mailer_user');
            $envoimail = $this->Mailer->sendMessage($emailEmetteur, $email, $messagerep->getTitreMessage(), $messagerep->getContenuMessage());

            /* $lemail = new \Swift_Message();
              $lemail->setSubject($messagerep->getTitreMessage());
              $lemail->setFrom('fessou-atasse.tevi@utb.com');
              $lemail->setTo($email);
              $lemail->setBody($messagerep->getContenuMessage());
              if($this->mailer->send($lemail)){
              // $msgmulti = $this->translator->trans('Contact.sessionmesg');
              //$this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('noticemail',$msgmulti );

              } */
            //$em->flush();
            //$msgnotification = '';
            // $msgnotification = $this->translator->trans('notification.ajout');
            // $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);
            return $this->redirect($this->generateUrl("utb_admin_listemessageenvoye"));
        }
        return $this->render('utbAdminBundle/Message/repondreMessage.html.twig', array(
                    'nbreMsgEnv' => $nbreMsgEnv, 'nbreMsgCorb' => $nbreMsgCorb, 'nbreMsg' => $nbreMsg, 'listeservice' => $listeservice, 'nbreMsgNonLu' => $nbreMsgNonLu, 'form' => $form->createView(), 'email' => $email, 'id' => $id, 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     * Fonction permettant de lister des messages recues - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listemessage/ Liste des messages de la boite de reception
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site  
     * 
     * @return return sur le twig boiteReception.html.twig
     *  
     */
    #[Route('/admin/listeMessage', name: 'admin_listeMessage')]
    public function listeMessageAction(): Response(string $locale, $idservice): Response {

        //controle des droits
        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeMessageAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        $nbreMsgNonLu = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreNouveauMessage();

        $nbreMsg = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreMessage();

        $nbreMsgCorb = $this->entityManager
                ->getRepository('admin/Message')
                ->getNombreCorbeille();


        $listeservice = $em->getRepository("utbAdminBundle/Service")->findAll();

        $listemessage = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getListeMessage($idservice);

        $nbreMsgEnv = $this->entityManager
                ->getRepository('App\Entity\MessageReponse')
                ->getNombreMessageEnv();
        //->findAllByLocale($locale);

        return $this->render('utbAdminBundle/Message/boiteReception.html.twig', array('nbreMsgEnv' => $nbreMsgEnv, 'nbreMsgCorb' => $nbreMsgCorb, 'nbreMsg' => $nbreMsg, 'listeservice' => $listeservice, 'nbreMsgNonLu' => $nbreMsgNonLu, 'listemessage' => $listemessage, 'locale' => $locale, 'listestat' => $listestat,
        ));
    }

    /**
     * Fonction permettant de lister des messages envoyes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listemessage: Liste des messages de la boite d'envoi
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site  
     * 
     * @return return sur le twig messageEnvoye.html.twig
     *  
     */
    #[Route('/admin/listeMessageEnvoye', name: 'admin_listeMessageEnvoye')]
    public function listeMessageEnvoyeAction(): Response(string $locale): Response {

        //controle des droits
        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeMessageEnvoyeAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        $nbreMsgNonLu = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getNombreNouveauMessage();

        $nbreMsg = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getNombreMessage();

        $nbreMsgEnv = $this->entityManager
                ->getRepository('App\Entity\MessageReponse')
                ->getNombreMessageEnv();

        $nbreMsgCorb = $this->entityManager
                ->getRepository('admin/Message')
                ->getNombreCorbeille();

        $listeservice = $em->getRepository("utbAdminBundle/Service")->findAll();

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listemessage = $this->entityManager
                ->getRepository('utbAdminBundle/MessageReponse')
                ->getListeMessageEnvoye();
        //->findAllByLocale($locale);

        return $this->render('utbAdminBundle/Message/messageEnvoye.html.twig', array('nbreMsgEnv' => $nbreMsgEnv, 'nbreMsgCorb' => $nbreMsgCorb, 'nbreMsg' => $nbreMsg, 'listeservice' => $listeservice, 'nbreMsgNonLu' => $nbreMsgNonLu, 'listemessage' => $listemessage, 'locale' => $locale, 'listestat' => $listestat,
        ));
    }

    /**
     * Fonction permettant de lister des messages envoyes a la corbeille - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listemessage/ Liste des messages envoyes a la corbeille
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site  
     * 
     * @return return sur le twig listeCorbeilleMessage.html.twig
     *  
     */
    #[Route('/admin/listeCorbeilleMessage', name: 'admin_listeCorbeilleMessage')]
    public function listeCorbeilleMessageAction(): Response(string $locale): Response {

        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'listeCorbeilleMessageAction', $this->container->get);
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }


        $nbreMsgNonLu = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreNouveauMessage();

        $nbreMsg = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreMessage();


        $listeservice = $em->getRepository("admin/Service")->findAll();

        $nbreMsgEnv = $this->entityManager
                ->getRepository('utbAdminBundle/MessageReponse')
                ->getNombreMessageEnv();

        $nbreMsgCorb = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getNombreCorbeille();

        $listemessage = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getListeCorbeilleMessage();
        //->findAllByLocale($locale);

        return $this->render('utbAdminBundle/Message/listeCorbeilleMessage.html.twig', array('nbreMsgEnv' => $nbreMsgEnv, 'nbreMsgCorb' => $nbreMsgCorb, 'nbreMsg' => $nbreMsg, 'listeservice' => $listeservice, 'nbreMsgNonLu' => $nbreMsgNonLu, 'listemessage' => $listemessage, 'locale' => $locale, 'listestat' => $listestat,
        ));
    }

    /**
     * Fonction permettant d'avoir le detail des messages recus - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $detailmessage: Detail du message recu en question
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site  
     * 
     * @param <integer> $id Variable identifiant une instance de la classe Message
     * 
     * @return return sur le twig detailMessage.html.twig
     *  
     */
    #[Route('/admin/detailMessage', name: 'admin_detailMessage')]
    public function detailMessageAction(): Response(int $id, string $locale): Response {

        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'detailMessageAction', $this->container->get);


        $unmessage = $em->getRepository("utbAdminBundle/Message")->find($id);
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $listeservice = $em->getRepository("utbAdminBundle/Service")->findAll();

        $nbreMsgNonLu = $this->entityManager
                ->getRepository('admin/Message')
                ->getNombreNouveauMessage();

        $nbreMsg = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreMessage();

        $nbreMsgCorb = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getNombreCorbeille();

        $nbreMsgEnv = $this->entityManager
                ->getRepository('App\Entity\MessageReponse')
                ->getNombreMessageEnv();


        $detailmessage = $this->entityManager
                ->getRepository('App\Entity\Message')
                ->getdetailMessage($id);
        //->findAllByLocale($locale);
        $unmessage->setMessageLu(1);
        $em->persist($unmessage);
        $em->flush();

        return $this->render('utbAdminBundle/Message/detailMessage.html.twig', array('nbreMsgEnv' => $nbreMsgEnv, 'nbreMsgCorb' => $nbreMsgCorb, 'nbreMsg' => $nbreMsg, 'listeservice' => $listeservice, 'nbreMsgNonLu' => $nbreMsgNonLu, 'listeservice' => $listeservice, 'detailmessage' => $detailmessage, 'locale' => $locale, 'listestat' => $listestat,
        ));
    }

    /**
     * Fonction permettant d'avoir le detail des messages envoyes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $detailmessage: Detail du message envoye en question
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site  
     * 
     * @param <integer> $id Variable identifiant une instance de la classe Message
     * 
     * @return return sur le twig detailMessageEnvoye.html.twig
     *  
     */
    #[Route('/admin/detailMessageEnvoye', name: 'admin_detailMessageEnvoye')]
    public function detailMessageEnvoyeAction(): Response(int $id, string $locale): Response {

        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'detailMessageEnvoyeAction', $this->container->get);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        $nbreMsgNonLu = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreNouveauMessage();

        $nbreMsg = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreMessage();

        $nbreMsgEnv = $this->entityManager
                ->getRepository('admin/MessageReponse')
                ->getNombreMessageEnv();
        $nbreMsgCorb = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreCorbeille();

        $listeservice = $em->getRepository("admin/Service")->findAll();

        $unmessage = $em->getRepository("utbAdminBundle/MessageReponse")->find($id);
        $detailmessage = $this->entityManager
                ->getRepository('App\Entity\MessageReponse')
                ->getdetailMessageEnvoye($id);
        //->findAllByLocale($locale);

        $unmessage->setMessageLu(1);
        $em->persist($unmessage);
        $em->flush();

        return $this->render('utbAdminBundle/Message/detailMessageEnvoye.html.twig', array('nbreMsgEnv' => $nbreMsgEnv, 'nbreMsgCorb' => $nbreMsgCorb, 'nbreMsg' => $nbreMsg, 'listeservice' => $listeservice, 'nbreMsgNonLu' => $nbreMsgNonLu, 'detailmessage' => $detailmessage, 'locale' => $locale, 'listestat' => $listestat,
        ));
    }

    /**
     * Fonction permettant d'envoyer des messages selectionnes a la corbeille ou de les restaurer - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $messageriesIds: Tableau regoupants les Ids des instances de la classe Message selectionnes
     * 
     * $etat: Nouvel etat a passer aux instances selectionnes (ici 1 pour mise a la corbeille et 0 pour la restauration)
     * 
     * $unmessagerie: Instance de la classe Message Ã  envoyer Ã  la corbeille ou a restaurer
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site  
     * 
     * @param <integer> $id Variable identifiant une instance de la classe Message
     * 
     * @return <json> return etat du traitement effectue
     *  
     */
    function corbeilleMessagerieAction(): Response: Response {

        $em = $this->entityManager;
        //$this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);        
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'corbeilleMessagerieAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }


        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        //$user = $this->security->getToken()->getUser()->getId();                
        $messageriesIds = $request->request->get('messageriesIds');
        $etat = $request->request->get('etat');

        $messageriesIds = explode("|", $messageriesIds);

        foreach ($messageriesIds as $key => $value) {


            if (!empty($value)) {

                $unmessagerie = $em->getRepository("App\Entity\Message")->find($value);

                //var_dump($value);
                // var_dump($unmessagerie);
                $unmessagerie->setCorbeilleMessage($etat); // 

                /*
                  $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('suppressionimpossible', "Suppression impossible pour les messageries de ce statut!");
                  return $this->redirect ( $this->generateUrl('admin_listetoutmessagerie', ['locale' =>$locale]));
                 */
                $em->persist($unmessagerie);
            }
        }

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de supprimer definitivement des messages recus selectionnes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $messageriesIds: Tableau regoupants les Ids des instances de la classe Message selectionnes
     * 
     * $unmessageries: Instance de la classe Message supprimer definitivement
     * 
     * @return <json> return etat du traitement effectue
     *  
     */
    function deleteAllMessagesAction(): Response: Response {

        $em = $this->entityManager;
        //$this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);        
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'deleteAllMessagesAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        $messageriesIds = $request->request->get('messageriesIds');

        $messageriesIds = explode("|", $messageriesIds);
        
        foreach ($messageriesIds as $key => $value) {
            
            if (!empty($value)) {
                $unmessageries = $em->getRepository("App\Entity\Message")->find($value);
                $messagereponses = $unmessageries->getMessagereponses();
                
                if($messagereponses!=null){
                    
                        foreach ($messagereponses as $key => $unmessagereponse) {

                           $unmsgreponse= $em->getRepository("App\Entity\MessageReponse")->find($unmessagereponse->getId());
                           $em->remove($unmsgreponse);
                           $em->flush();
                        }
                        
                }
                $em->remove($unmessageries);
                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de supprimer definitivement des messages envoyes selectionnes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $messageriesIds: Tableau regoupants les Ids des instances de la classe MessageResponse selectionnes
     * 
     * $unmessageries: Instance de la classe MessageResponse a supprimer definitivement
     * 
     * @return <json> return etat du traitement effectue
     *  
     */
    function deleteAllMessagesEnvoyeAction(): Response: Response {
        $em = $this->entityManager;
        //$this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'deleteAllMessagesEnvoyeAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $messageriesIds = $request->request->get('messageriesIds');
        $messageriesIds = explode("|", $messageriesIds);

        foreach ($messageriesIds as $key => $value) {

            if (!empty($value)) {
                $unmessageries = $em->getRepository("App\Entity\MessageReponse")->find($value);
                $em->remove($unmessageries);
                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de supprimer definitivement des profils selectionnes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $usersIds: Tableau regoupants les Ids des instances de la classe Profil selectionnes
     * 
     * $unuser: Instance de la classe Profil a supprimer definitivement
     * 
     * @return <json> return etat du traitement effectue
     *  
     */
    function supprAllprofilsAction(): Response: Response {

        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'supprAllprofilsAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $usersIds = $request->request->get('ds');
        $usersIds = explode("|", $usersIds);
        foreach ($usersIds as $key => $value) {

            if (!empty($value)) {

                $unuser = $em->getRepository("App\Entity\Profil")->find($value);
                $unUtilisatateur = $this->entityManager
                        ->getRepository('App\Entity\User')
                        ->findProfil($value);
                $unDroit = $this->entityManager
                        ->getRepository('App\Entity\droit')
                        ->findProfil($value);
                $undroitsupp = $em->getRepository("App\Entity\droit")->find($unDroit[0]['iddroit']);
                $undroit = new droit();

                if ($unUtilisatateur == null) {

                    $undroit->setProfil($unuser);

                    /* Enfin on supprime le profil... */

                    $em->remove($unuser);
                    // $em->remove($undroit); 

                    $em->remove($undroitsupp);
                } else {
                    return new Response(json_encode(array("result" => "erreurstatut")));
                }
            }
        }
        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de lister les textes des box d'information.  - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $infosliste: Box d'informations se trouvant sur des pages de liste.
     * 
     * $infosajoutrub: Box d'information de la page d'ajout de rubrique.
     * 
     * $infosajoutart: Box d'information des pages d'ajout d'article.
     * 
     * $listestat: Statistiques générales du site.
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig listeBoxInfos.html.twig
     *  
     */
    #[Route('/admin/listeBoxInfos', name: 'admin_listeBoxInfos')]
    public function listeBoxInfosAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        /* $em = $this->entityManager;
          $accessControl = $this->accessControl;
          $checkAcces = $accessControl->verifAcces($em, 'listeBoxInfosAction', $this->container->get);

          if (!$checkAcces) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
          } */
        //          
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $infosliste = $this->entityManager
                ->getRepository('utbAdminBundle/Parametrage')
                ->getListeBoxInfos($locale, 1);

        $infosajoutrub = $this->entityManager
                ->getRepository('utbAdminBundle/Parametrage')
                ->getListeBoxInfos($locale, 2);

        $infosajoutart = $this->entityManager
                ->getRepository('admin/Parametrage')
                ->getListeBoxInfos($locale, 3);
        
        $infosarbre = $this->entityManager
                ->getRepository('utbAdminBundle/Parametrage')
                ->getListeBoxInfos($locale, 4);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(int $typeStat = 4, $locale, 0, null);

        return $this->render('admin/listeBoxInfos.html.twig', array(
                    'locale' => $locale,
                    'infosliste' => $infosliste, 'infosajoutart' => $infosajoutart, 'infosajoutrub' => $infosajoutrub,
                    'infosarbre' => $infosarbre,'listestat' => $listestat
        ));
    }

    /**
     * Fonction permettant d'autoriser l'affichage ou non des box d'information .  - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $paramIds: Tableau des identifiants de box d'information sélectionnés Ã  gérer.
     * 
     * $etat: Etat a donner aux Box d'informations sélectionnés.
     * 
     * @return <string> retourne un etat ajax, succes ou erreur.
     *  
     */
    function gererAllParametrageAction(): Response: Response {

        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'gererAllParametrageAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();
        $paramIds = $request->request->get('objetIds');
        $etat = $request->request->get('etat');
        $paramIds = explode("|", $paramIds);

        $resultat = true;
        //boucle sur les ids articles
        foreach ($paramIds as $key => $value) {
            if (!empty($value)) {
                $parametrage = $em->getRepository("admin/Parametrage")->find($value);

                if (($etat == 1)) {
                    $parametrage->setActif($etat);
                } else
                if (($etat == 0)) {
                    $parametrage->setActif($etat);
                } else {
                    //return new Response(json_encode(array("result" => "erreurstatut")));
                    $resultat = false;
                }
            } else {
                //return new Response(json_encode(array("result" => "erreurstatut")));
                $resultat = false;
            }
        }
        $em->persist($parametrage);
        $em->flush();

        if ($resultat == false) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        } else {
            return new Response(json_encode(array("result" => "success")));
        }
    }

    /**
     * Fonction permettant de determiner le taux de traduction des articles d'une rubrique
     * ou le taux de traduction par rapport au nombre total d'articles.
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listetraduction/ 
     * 
     * $listetraduit:
     * 
     * $tauxtraduit
     * 
     * @return <string> retourne listeTauxTraduction.html.twig
     *  
     */
    #[Route('/admin/tauxTraduction', name: 'admin_tauxTraduction')]
    public function tauxTraductionAction(): Response(int $id, string $locale, string $type,$page): Response {
        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $listetraduction = array(
            0 => $this->translator->trans('admin.titre'),
            1 => $this->translator->trans('admin.statut'),
            2 => $this->translator->trans('admin.rubrique')
        );

        $tauxtraduit = $em
                ->getRepository('App\Entity\Article')
                ->getTauxTraduction($id, $locale, $type);
        
        $total =$tauxtraduit[0]['total'];
        
        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;        
        
        $listetraduit = $em->getRepository('App\Entity\Article')
                ->getSiTraductionArtExiste($id, $locale, $listetraduction, $type , $page, $articles_per_page);

        

        //var_dump($tauxtraduit);

        return $this->render('admin/listeTauxTraduction.html.twig', array(
                    'locale' => $locale,
                    'listetraduction' => $listetraduit,
                    'taux' => $tauxtraduit,
                    'type' => $type,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
        ));
    }

    /**
     * Fonction permettant d'afficher l'arbre du site - Backoffice
     * 
     * @var
     * 
     * $listerubrique: Liste de toutes les rubriques du site.
     * 
     * $listeRubrique: Liste de toutes les rubriques, ss-rubriques, categories du site. 
     * 
     * $listeuser: Liste de tous les utilisateurs (administrateurs) du site.
     * 
     * $listessrubrique: Liste des ss srubriques du site.
     * 
     * $listearticle: Liste de tous les articles du site.
     *     
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig admin/Admin/arbre.html.twig 
     * 
     */
    #[Route('/admin/arbre', name: 'admin_arbre')]
    public function arbreAction(): Response(string $locale): Response {
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);

        $em = $this->entityManager;
        $accessControl = $this->accessControl;
        $checkAcces = $accessControl->verifAcces($em, 'arbreAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_index', ['locale' => $locale]));
        }

        //liste des rubriques
        $listerubrique = $em
                ->getRepository('utbAdminBundle/Rubrique')
                ->getAllRubrique($locale);
        //->findAllByLocale($locale);
        //liste des utilisateurs
        $listeuser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findAll();


        //liste des rubriques,ss-rubriques et catégories utilisée lorsqu'on click sur "Ajouter un article"
        $listeRubrique = //$em->getRepository("utbAdminBundle/Rubrique")->findBy( array("idparent"=>0) );  
                $this->entityManager
                ->getRepository('admin/Rubrique')
                ->getListeDeRubriques($locale);

        /**
         * liste des rubriques,ss-rubriques et eventuellement categories      
         */
        //$em=$this->entityManager;
        $listessrubrique = $this->entityManager
                ->getRepository('utbAdminBundle/Rubrique')
                ->getAllRubriques($locale);

        //liste de tous les articles du site
        $listearticle = $this->entityManager
                ->getRepository('App\Entity\Article')
                ->findAllByLocaleArbre($locale);
        
        $boxinfos = $this->entityManager
                ->getRepository("App\Entity\Parametrage")
                ->getTexteBoxInfos($locale, int $type = 22);

        return $this->render('admin/arbre.html.twig', array('listerubrique' => $listerubrique,
                    'locale' => $locale,
                    'listeuser' => $listeuser,
                    'listessrubrique' => $listessrubrique,
                    'listeRubrique' => $listeRubrique,
                    'listearticle' => $listearticle,
                    'infos' => $boxinfos
        ));
    }

    /**
     * Fonction permettant de gerer l'ordre des menus parents(premier niveau) - Backoffice
     * 
     * @var
     *     
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne un etat ajax. 
     * 
     */
    function updateMenuParentsAction(string $locale) {
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        //on recupere les donnees envoyees depuis le formulaire des droits
        $formData = $request->request->get('formdata');
        $data = array();
        parse_str($formData, $data);
        $recordIDValue = $data['recordsArray'];
        //print_r($recordIDValue);
        $thisOrdre = $em->getRepository("App\Entity\Ordre")->findOneBy(array("nomTable" => "MenuParent"));
        $thisOrdre->setOrdre(serialize($recordIDValue));
        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Fonction permettant de gérer l'ordre des menus fils(second niveau) - Backoffice
     * 
     * @var
     *     
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne un etat ajax. 
     * 
     */
    function updateMenuFilsAction(string $locale) {
        $this->requestStack->getCurrentRequest()Stack->getCurrentRequest()->setLocale($locale);
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest()Stack->getCurrentRequest();

        //on recupere les donnees envoyees depuis le formulaire des droits
        $formData = $request->request->get('formdata');
        $data = $dataToSave = array();
        parse_str($formData, $data);
        $recordIDValue = $data['recordsArray'];

        $thisOrdre = $em->getRepository("App\Entity\Ordre")->findOneBy(array("nomTable" => "MenuFils"));
        $ordre = unserialize($thisOrdre->getOrdre());
        print_r($ordre);
        foreach ($recordIDValue as $val) {
            $tab = explode("|", $val);
            $dataToSave[$tab[1]][] = $tab[0];
            $ordre[$tab[1]] = $dataToSave[$tab[1]];
        }
        //print_r($dataToSave);        
        $thisOrdre->setOrdre(serialize($ordre));
        $em->flush();
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * 
     * Cette fonction ne sera utilise nullpart sur le site. 
     * Il a juste été créée pour corriger une incompatibilite de donnees eventuelle entre
     * les tables ordre et menu
     * 
     */
    function setOrdreAction(string $locale) {
        $em = $this->entityManager;
        $thisOrdre = $em->getRepository("App\Entity\Ordre")->findOneBy(array("nomTable" => "MenuFils"));

        $menuParents = $em->getRepository("App\Entity\Menu")->findParent($locale);
        $ordre = array();
        // var_dump($menuParents);exit;
        foreach ($menuParents as $mp) {
            $ordre[$mp->getId()] = array();
            $menuFils = $em->getRepository("App\Entity\Menu")->findMenuFils($mp->getId(), $locale);
            foreach ($menuFils as $mf) {
                $ordre[$mp->getId()][] = $mf->getId();
            }
        }
        //print_r($ordre);
        $thisOrdre->setOrdre(serialize($ordre));
        $em->flush();
        return new Response("success");
    }

}
