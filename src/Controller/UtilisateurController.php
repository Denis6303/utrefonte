<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Utilisateur;
use App\Entity\UtilisateurType;
use App\Entity\Utilisateur2Type;
use App\Entity\ModifPwdUtilisateurType;
use App\Entity\ModifFicheUtilisateurType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;


/**
 * UtilisateurController 
 * 
 * Le controleur gerant la pluspart des fonctionnalités du module "Gestion des Abonné"
 * 
 * 
 * Fonction utilise pour afficher les menus auquels l'utilisateurs à droit
 *
 * $em = $this-> getDoctrine()->getEntityManager();
 * $authManager = $this->Auth.Manager;
 * $this->requestStack->getCurrentRequest()->setLocale($locale);
 * 
 * if(!$authManager->isLogged())
 *     return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
 * 
 * $currentConnete = $authManager->getFlash("utb_client_data"); //avoir les information sur la personne connecté
 * $this->infoUtilisateur($em,$authManager,$currentConnete,'utilisateur',$locale);
 * $listeActions = $currentConnete["listeActions_abonne"]; //avoir la liste des actions auquelles l'utilisateur connecté à droit
 *  
 *   if ( !in_array('EditAction', $listeActions) ){ // test si l'action courant appartient à la liste des actions affectées à l'utilisateur 
 *       $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
 *       return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));// redirectionnement vers l'accueil
 *   }
 * 
 * 
 * Cette ligne de code permet de definir la langue à travers la variable locale| fr pour le francais et en pour l'anglais
 * 
 * Presente dans la majorite des methodes
 * 
 * $this->requestStack->getCurrentRequest()->setLocale($locale);
 * 
 * Declaration de formulaire relatif à une instance d'objet : Presente dans la majorite des methodes
 * 
 * $form: Instance de la classe Form
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class UtilisateurController extends AbstractController
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
     * Methode permettant d'ajouter un abonné
     * 
     * @param string $locale La locale pour la gestion multilingue
     * @return Response Le template d'ajout d'utilisateur
     */
    #[Route(
        path: '/admin/utilisateur/ajout/{locale}',
        name: 'app_utilisateur_ajout',
        requirements: [
            'locale' => '[a-z]{2}'
        ]
    )]
    public function ajoutUtilisateur(Request $request, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutUtilisateur', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unUtilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $unUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unUtilisateur = $form->getData();

            // Validation du nom d'utilisateur
            if (strlen($unUtilisateur->getUsername()) < 5) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorsmalllogin');
                return $this->render('admin/utilisateur/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale
                ]);
            }

            // Validation des caractères du login
            $pattern = '/[][(){}<>\/+"*%&=?`^\'!$:;,À?ÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌ?Î?ìíîïÙÚÛÜùúûüÿÑñ]/';
            if (preg_match($pattern, $unUtilisateur->getUsername())) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'errorlogincaracint');
                return $this->render('admin/utilisateur/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale
                ]);
            }

            // Validation de l'email
            $regex = '#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,6}$#';
            if (!preg_match($regex, $unUtilisateur->getEmail())) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'emailformaterror');
                return $this->render('admin/utilisateur/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale
                ]);
            }

            // Validation du mot de passe
            $password = $form->get('password')->getData();
            $cpassword = $form->get('cpassword')->getData();

            if ($password !== $cpassword) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'passworderror');
                return $this->render('admin/utilisateur/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale
                ]);
            }

            if (strlen($password) < 6) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'passwordtropcourt');
                return $this->render('admin/utilisateur/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale
                ]);
            }

            $em->persist($unUtilisateur);
            $em->flush();

            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Utilisateur ajouté avec succès');
            return $this->redirect($this->generateUrl('app_utilisateur_liste', ['locale' => $locale]));
        }

        return $this->render('admin/utilisateur/ajout.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    /**
     * Methode qui liste les utilisateurs
     * 
     * @param string $locale La locale
     * @return Response Le template de liste des utilisateurs
     */
    #[Route(
        path: '/admin/utilisateur/liste/{locale}',
        name: 'app_utilisateur_liste',
        requirements: [
            'locale' => '[a-z]{2}'
        ]
    )]
    public function listeUtilisateur(Request $request, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'listeUtilisateur', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $utilisateurs = $em->getRepository("App\Entity\Utilisateur")->findAll();

        return $this->render('admin/utilisateur/liste.html.twig', [
            'utilisateurs' => $utilisateurs,
            'locale' => $locale
        ]);
    }

    /**
     * Methode qui affiche les détails d'un utilisateur
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @param string $cas Le cas d'utilisation
     * @param string $locale La locale
     * @return Response Le template de détail de l'utilisateur
     */
    #[Route(
        path: '/admin/utilisateur/detail/{id}/{cas}/{locale}',
        name: 'app_utilisateur_detail',
        requirements: [
            'id' => '\d+',
            'cas' => '[a-z]+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function detailUtilisateur(Request $request, int $id, string $cas, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'detailUtilisateur', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $utilisateur = $em->getRepository("App\Entity\Utilisateur")->find($id);
        
        if (!$utilisateur) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Utilisateur non trouvé');
            return $this->redirect($this->generateUrl('app_utilisateur_liste', ['locale' => $locale]));
        }

        return $this->render('admin/utilisateur/detail.html.twig', [
            'utilisateur' => $utilisateur,
            'cas' => $cas,
            'locale' => $locale
        ]);
    }

    /**
     * Methode qui permet de modifier un utilisateur
     * 
     * @param Request $request La requête HTTP
     * @param int $id L'identifiant de l'utilisateur
     * @param string $locale La locale
     * @param string $genre Le genre de modification
     * @return Response Le template de modification ou une redirection
     */
    #[Route(
        path: '/admin/utilisateur/modifier/{id}/{locale}/{genre}',
        name: 'app_utilisateur_modifier',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}',
            'genre' => '[a-z]+'
        ]
    )]
    public function modifierAction(Request $request, int $id, string $locale, string $genre): Response
    {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('modifierAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unUtilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($id);
        if (!$unUtilisateur) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Utilisateur non trouvé');
            return $this->redirect($this->generateUrl('utb_client_liste', ['locale' => $locale]));
        }

        $ancienpwd = $unUtilisateur->getPassword();
        $form = $this->createForm(UtilisateurType::class, $unUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unUtilisateur->setEtatUtilisateur(1);

            if ($genre == 1) {
                $unUtilisateur->setPassword($ancienpwd);
            } else {
                $unUtilisateur->setPassword($ancienpwd);
                $em->persist($unUtilisateur);
                $em->flush();
            }

            if ($genre == 1) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'successmodifmediaart');
                return $this->redirect($this->generateUrl('app_utilisateur_detail', ['id' => $id, 'locale' => $locale]));
            } else {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'modifsuccess');
                return $this->redirect($this->generateUrl('app_utilisateur_liste', ['locale' => $locale]));
            }
        }

        return $this->render('admin/utilisateur/modifier.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $unUtilisateur,
            'locale' => $locale,
            'genre' => $genre
        ]);
    }

    /**
     * Methode permettant de lister les abonnés
     * 
     * @var
     * 
     * Les Variables 
     * 
     * $listeUtilisateur: Variable pour avoir la liste des utilisateurs
     * 
     * $utilisateurid : Identifiant de l'utilisateur courant
     * 
     * $cas : Pour $cas=0 pour Mes identifiants du menu "Infos du profil" - $cas=1 pour Detail utilisateur du menu "Gestion des utilisateurs"
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site   
     * 
     * @return <string>  retourne le twig utbClientBundle:Abonne:listeAbonne.html.twig 
     * 
     */
    public function detailUtilisateurAction(Request $request, int $id, string $cas, string $locale): Response
    {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('detailUtilisateurAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $id_util = "";
        if ($cas == 0) {
            $id_util = $currentID;
        } elseif ($cas == 1) {
            $id_util = $id;
        }

        $listeHistoriques = $this->entityManager
                ->getRepository("utbClientBundle:HistoriqueConnexion")
                ->getListeHistoriqueByType($id_util, 1, 5, 0, 0, 0);

        $unUtilisateur = $this->entityManager
                ->getRepository("utbClientBundle:Utilisateur")
                ->findOneByLocale($id_util, $locale);
        $passGene = md5($unUtilisateur[0]['genPsswd']);
        $listeFonds = $this->entityManager
                ->getRepository("utbClientBundle:Fonds")
                ->findFondsGestionnaire($id_util);
        /* $utilisateur = $this->security->getToken()->getUtilisateur()->getId(); */


        return $this->render('utbClientBundle/Utilisateur/detailUtilisateur.html.twig', array('unUtilisateur' => $unUtilisateur, 'cas' => $cas, 'listeHisto' => $listeHistoriques, 'locale' => $locale, 'listeFonds' => $listeFonds, 'passGene' => $passGene,));
    }

    /**
     * Methode qui permet de gérer l'état d'un utilisateur
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @param int $etat Le nouvel état de l'utilisateur
     * @param string $locale La locale
     * @return Response Une redirection vers la liste des utilisateurs
     */
    #[Route(
        path: '/admin/utilisateur/gerer-etat/{id}/{etat}/{locale}',
        name: 'app_utilisateur_gerer_etat',
        requirements: [
            'id' => '\d+',
            'etat' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function gererEtatUtilisateur(Request $request, int $id, int $etat, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'gererEtatUtilisateur', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $utilisateur = $em->getRepository("App\Entity\Utilisateur")->find($id);
        
        if (!$utilisateur) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Utilisateur non trouvé');
            return $this->redirect($this->generateUrl('app_utilisateur_liste', ['locale' => $locale]));
        }

        $utilisateur->setEtatUtilisateur($etat);
        $em->persist($utilisateur);
        $em->flush();

        if ($etat == 0) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Utilisateur désactivé avec succès');
        } else {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Utilisateur activé avec succès');
        }

        return $this->redirect($this->generateUrl('app_utilisateur_liste', [
            'locale' => $locale
        ]));
    }

    /*

      //supprimer définitivement une sélection d'utilisateurs
      function supprAllUtilisateursAction(): Response{

      $em = $this-> getDoctrine()->getEntityManager();
      $authManager = $this->Auth.Manager;//on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
      $this->requestStack->getCurrentRequest()->setLocale($locale);
      //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
      if(!$authManager->isLogged())
      return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

      $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
      $currentConnete = $authManager->getFlash("utb_client_data");
      $this->infoUtilisateur($em,$authManager,$currentConnete,'utilisateur',$locale);
      $listeActions = $currentConnete["listeActions_abonne"];

      if ( !in_array('supprAllUtilisateursAction', $listeActions) ){
      $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
      return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
      }

      $request = $this->requestStack->getCurrentRequest();
      $utilisateursIds  = $request->request->get('ds');
      $utilisateursIds = explode("|",$utilisateursIds);
      return new Response( json_encode($utilisateursIds));
      foreach($utilisateursIds as $key=>$value){

      if(!empty($value)){
      $unutilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($value);



      //suppression des comptes liés à l'utilisateur
      $lescompte = $unutilisateur -> getComptes();
      foreach($lescompte as $key=>$uncompte){
      $em->remove($uncompte);
      }
      //suppression des envois de mails liés à l'utilisateur
      $lesenvois = $unutilisateur -> getEnvois();
      foreach($lesenvois as $key=>$unenvoi){
      $em->remove($unenvoi);
      }
      //suppression des historiques liés à l'utilisateur
      $leshistoriques = $unutilisateur -> getHistoriques();
      foreach($leshistoriques as $key=>$unhistorique){
      $em->remove($unhistorique);
      }


      //suppression des fonds liés à l'utilisateur
      $lesfonds = $unutilisateur -> getFonds();
      foreach($lesfonds as $key=>$unfond){
      $em->remove($unfond);
      }

      $em->remove($unutilisateur);
      }  //

      }

      $em->flush();
      return new Response( json_encode(array("result"=>"success")));
      }
     */

    /**
     * Methode qui permet d'activer un utilisateur
     * 
     * @param string $locale La locale
     * @param int $id L'identifiant de l'utilisateur
     * @return Response Une redirection vers la liste des utilisateurs
     */
    #[Route(
        path: '/admin/utilisateur/activer/{locale}/{id}',
        name: 'app_utilisateur_activer',
        requirements: [
            'locale' => '[a-z]{2}',
            'id' => '\d+'
        ]
    )]
    public function activerUtilisateur(Request $request, string $locale, int $id): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'activerUtilisateur', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $utilisateur = $em->getRepository("App\Entity\Utilisateur")->find($id);
        
        if (!$utilisateur) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Utilisateur non trouvé');
            return $this->redirect($this->generateUrl('app_utilisateur_liste', ['locale' => $locale]));
        }

        $utilisateur->setEtatUtilisateur(1);
        $em->persist($utilisateur);
        $em->flush();

        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Utilisateur activé avec succès');
        return $this->redirect($this->generateUrl('app_utilisateur_liste', ['locale' => $locale]));
    }

    /**
     * Methode qui permet de désactiver un utilisateur
     * 
     * @param string $locale La locale
     * @param int $id L'identifiant de l'utilisateur
     * @return Response Une redirection vers la liste des utilisateurs
     */
    #[Route(
        path: '/admin/utilisateur/desactiver/{locale}/{id}',
        name: 'app_utilisateur_desactiver',
        requirements: [
            'locale' => '[a-z]{2}',
            'id' => '\d+'
        ]
    )]
    public function desactiverUtilisateur(Request $request, string $locale, int $id): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'desactiverUtilisateur', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $utilisateur = $em->getRepository("App\Entity\Utilisateur")->find($id);
        
        if (!$utilisateur) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Utilisateur non trouvé');
            return $this->redirect($this->generateUrl('app_utilisateur_liste', ['locale' => $locale]));
        }

        $utilisateur->setEtatUtilisateur(0);
        $em->persist($utilisateur);
        $em->flush();

        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Utilisateur désactivé avec succès');
        return $this->redirect($this->generateUrl('app_utilisateur_liste', ['locale' => $locale]));
    }

    function activerAllUtilisateursAction(string $locale = "fr") {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
       
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
         //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('activerAllUtilisateursAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $utilisateursIds = $request->request->get('ds');
        $utilisateursIds = explode("|", $utilisateursIds);
        //  $utilisateur = $this->security->getToken()->getUtilisateur()->getId();
        //boucle sur les id utilisateurs
        //return new Response(json_encode($utilisateursIds));exit; 
        foreach ($utilisateursIds as $key => $value) {
            if (!empty($value)) {

                $unutilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($value);
                //Activation 

                /* if($unutilisateur->getProfil()->getEtatProfil()== 0){
                  return new Response(json_encode(array("result" => "profildesactive")));
                  }else{

                  } */

                $unutilisateur->setEtatUtilisateur(1);
                $em->persist($unutilisateur);
                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success")));
    }

    function desactiverAllUtilisateursAction(string $locale = "fr") {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('desactiverAllUtilisateursAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $utilisateursIds = $request->request->get('ds');
        $utilisateursIds = explode("|", $utilisateursIds);

        foreach ($utilisateursIds as $key => $value) {
            if (!empty($value)) {

                $unutilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($value);
                //Activation                 
                /* if($unutilisateur->getProfil()->getEtatProfil()== 0){
                  return new Response(json_encode(array("result" => "profildesactive")));
                  }else{
                  } */
                $unutilisateur->setEtatUtilisateur(0);
                $em->persist($unutilisateur);
                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success")));
    }

    function supprimerAllUtilisateursAction(string $locale = "fr") {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('supprimerAllUtilisateursAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $utilisateursIds = $request->request->get('ds');
        $utilisateursIds = explode("|", $utilisateursIds);

        foreach ($utilisateursIds as $key => $value) {
            if (!empty($value)) {

                $unutilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($value);
                //Activation                 
                /* if($unutilisateur->getProfil()->getEtatProfil()== 0){
                  return new Response(json_encode(array("result" => "profildesactive")));
                  }else{
                  } */
                $unutilisateur->setSuppr(1);
                $em->persist($unutilisateur);
                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     * Methode qui permet de modifier un utilisateur selon le type de modification
     * 
     * @param Request $request La requête HTTP
     * @param int $id L'identifiant de l'utilisateur
     * @param int $cas Le cas d'utilisation (0 pour utilisateur courant, 1 pour utilisateur spécifié)
     * @param string $type Le type de modification (1: mot de passe, 2: informations, 3: génération mot de passe)
     * @param string $locale La locale
     * @return Response Le template de modification ou une redirection
     */
    #[Route(
        path: '/admin/utilisateur/modifier-type/{id}/{cas}/{type}/{locale}',
        name: 'app_utilisateur_modifier_type',
        requirements: [
            'id' => '\d+',
            'cas' => '\d+',
            'type' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function modifSuivantTypeAction(Request $request, int $id, int $cas, string $type, string $locale): Response
    {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('modifSuivantTypeAction', $listeActions) && !in_array('listeGestionnaireAdminAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $id_util = ($cas == 0) ? $currentID : $id;
        $unUtilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($id_util);

        if (!$unUtilisateur) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Utilisateur non trouvé');
            return $this->redirect($this->generateUrl('utb_client_liste', ['locale' => $locale]));
        }

        $ancienpwd = $unUtilisateur->getPassword();
        $ancienEmail = $unUtilisateur->getEmail();
        $nomprenom = $unUtilisateur->getNomPrenom();
        $leLogin = $unUtilisateur->getUsername();

        if ($type == 3) {
            $authentif = $this->Auth.Manager;
            $genePwd = $authentif->GenererPasswd(6);
            $geneMD5 = md5($genePwd);
            $unUtilisateur->setGenPsswd($genePwd);
            $unUtilisateur->setPassword($geneMD5);
            $em->persist($unUtilisateur);
            $em->flush();
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'genepwdsuccess');
            return $this->redirect($this->generateUrl('app_utilisateur_detail', ['id' => $id, 'cas' => $cas, 'locale' => $locale]));
        }

        $form = null;
        if ($type == 2) {
            $form = $this->createForm(ModifFicheUtilisateurType::class, $unUtilisateur);
        } elseif ($type == 1) {
            $form = $this->createForm(ModifPwdUtilisateurType::class, $unUtilisateur);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($type == 2) {
                $mail = $unUtilisateur->getEmail();
                $regex = '#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,6}$#';
                if (!preg_match($regex, $mail)) {
                    $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'emailformaterror');
                    return $this->render('utbClientBundle/Utilisateur/modifFicheUtilisateur.html.twig', [
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'unUtilisateur' => $unUtilisateur
                    ]);
                }
            } elseif ($type == 1) {
                $unUtilisateur->setNomPrenom($nomprenom);
                $unUtilisateur->setUsername($leLogin);
                $vpassword = $request->request->get('vpassword');
                $cryptedPass = $ancienpwd;

                if ($ancienpwd != $cryptedPass) {
                    $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'errancienpwd');
                    return $this->redirect($this->generateUrl('app_utilisateur_detail', ['id' => $id, 'cas' => $cas, 'locale' => $locale]));
                }

                if ($form["password"]->getData() != $form["cpassword"]->getData()) {
                    $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'passworderror');
                    return $this->redirect($this->generateUrl('app_utilisateur_detail', ['id' => $id, 'cas' => $cas, 'locale' => $locale]));
                }

                if (strlen($form["password"]->getData()) < 6) {
                    $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'passwordtropcourt');
                    return $this->redirect($this->generateUrl('app_utilisateur_detail', ['id' => $id, 'cas' => $cas, 'locale' => $locale]));
                }

                $newpassword = $form["password"]->getData();
                $unUtilisateur->setPassword(md5($newpassword));
            }

            $em->persist($unUtilisateur);
            $em->flush();

            if ($type == 2) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'modifinfosuccess');
            } else {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'modifpwdsuccess');
            }

            return $this->redirect($this->generateUrl('app_utilisateur_detail', ['id' => $id, 'cas' => $cas, 'locale' => $locale]));
        }

        if ($type == 2) {
            return $this->render('utbClientBundle/Utilisateur/modifFicheUtilisateur.html.twig', [
                'form' => $form->createView(),
                'locale' => $locale,
                'unUtilisateur' => $unUtilisateur,
                'cas' => $cas
            ]);
        } else {
            return $this->render('utbClientBundle/Utilisateur/modifPwdUtilisateur.html.twig', [
                'form' => $form->createView(),
                'locale' => $locale,
                'unUtilisateur' => $unUtilisateur,
                'cas' => $cas,
                'type' => $type
            ]);
        }
    }

    /**
     * Methode qui permet de gérer les informations de l'utilisateur
     * 
     * @param EntityManagerInterface $em L'entity manager
     * @param mixed $authManager Le gestionnaire d'authentification
     * @param array $currentConnete Les données de connexion actuelles
     * @param string $user Le type d'utilisateur
     * @param string $locale La locale
     * @return void
     */
    public function infoUtilisateur(EntityManagerInterface $em, $authManager, array $currentConnete, string $user, string $locale): void
    {
        if (isset($currentConnete["id_abonne"]) && $currentConnete["id_abonne"] != "") {
            $id_abonne = $currentConnete["id_abonne"];
            $type_user = $currentConnete["type_user"];
            $nomPrenom = $currentConnete["nomPrenom_abonne"];
            $profil = $currentConnete["profil_abonne"];
            $idprofil = $currentConnete["idprofil_abonne"];
            $last_connexion = $currentConnete["last_connexion"];
            $listeActions = $currentConnete["listeActions_abonne"];  
            $subabonne = $currentConnete["sousAbonne"];
            
            $maxIdleTime = $this->container->get('maxIdleTime');
            $session = $this->requestStack->getCurrentRequest()->getSession();
            
            if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {
                $histo = null;
                $unuser = null;
                $unabonne = null;
                $idhisto = 0;
                $currentConnete = $authManager->getFlash("utb_client_data");

                if (isset($currentConnete["id_abonne"])) {
                    if ($currentConnete["type_user"] == "abonne") {
                        $unabonne = $em->getRepository('utbClientBundle:Abonne')->find($currentConnete["id_abonne"]);
                        $idhisto = $em->getRepository('utbClientBundle:HistoriqueConnexion')->getMaxHistorique($unabonne->getId(), 0);
                        if (isset($idhisto) && ($idhisto != 0)) {
                            $histo = $em->getRepository("utbClientBundle:HistoriqueConnexion")->find($idhisto);
                        }
                    }

                    if ($currentConnete["type_user"] == "utilisateur") {
                        $unuser = $em->getRepository("utbClientBundle:Utilisateur")->find($currentConnete['id_abonne']);
                        $idhisto = $em->getRepository('utbClientBundle:HistoriqueConnexion')->getMaxHistorique($unuser->getId(), 1);
                        if (isset($idhisto) && ($idhisto != 0)) {
                            $histo = $em->getRepository("utbClientBundle:HistoriqueConnexion")->find($idhisto);
                        }
                    }

                    if ($histo != null) {
                        $lafin = new \DateTime();
                        $ledebut = $histo->getDateDeb();
                        $laduree = $lafin->diff($ledebut);
                        $laduree->format('%h heures %i minutes %s secondes');
                        $histo->setDateFin($lafin);
                        $histo->setDuree($laduree->format('%h h %i min %s sec'));
                        $em->persist($histo);
                        $em->flush();
                    }
                }
                
                $_SESSION["utb_client_data"] = [];
            }
        } else {
            $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
            return;
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->set('_locale', $locale);
        
        $request = $this->requestStack->getCurrentRequest();
        $request->attributes->set('id_abonne', $id_abonne);
        $request->attributes->set('type_user', $type_user);
        $request->attributes->set('nomPrenom', $nomPrenom);
        $request->attributes->set('profil', $profil);
        $request->attributes->set('idprofil', $idprofil);
        $request->attributes->set('last_connexion', $last_connexion);
        $request->attributes->set('listeActions', $listeActions);
        $request->attributes->set('sousAbonne', $subabonne);

        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuUtil($em, $id_abonne);
        $request->attributes->set('nbreluutil', $nbrelu);

        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuAbonne($em, $id_abonne);
        $request->attributes->set('nbrelu', $nbrelu);
        $request->attributes->set('type_user', $user);
    }

}

