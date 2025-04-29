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
use Symfony\Component\HttpFoundation\Response;
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

    private $response ;
    public function __construct() {
        $this->response = new Response;
        $this->response->headers->addCacheControlDirective('no-cache', true);
        $this->response->headers->addCacheControlDirective('max-age', 0);
        $this->response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response->headers->addCacheControlDirective('no-store', true);
    } 
    /**
     * Methode permettant d'ajouter un abonnné
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unUtilisateur: Variable contenant l'objet abonné créée
     * 
     * $mail: utilisée pour recuperer le mail de l'abonne
     * 
     * $atomlogin : regex pour former les carateres autorises pour le login 
     * 
     * $pattern: variable utilisée pour recueillir le regex que doit respecter le nom d'utilisateur
     * 
     * $regex: variable utilisée pour recueillir le regex que doit respecter le l'email
     * 
     * $login: Pour le login de l'abonné qu'on crée 
     * 
     * $unloginutil : Requete utilisee pour tester l'existence d'un identifiant dans le base de donnee 
     * 
     * $password: recupère le mot de passe saisie par l'utilisateur lors de l'ajout d'un abonne
     * 
     * $cpassword:deuxieme champ de saisie de mot de passe pour verifier si l'utilisateur se rappelle du 1 mot de passe saisie
     * 
     * $unlogin: variable pour recueillir  la requette qui teste l'existence du login saisi dans la table des abonnes
     * 
     * $vpassword: recupère le password de l'abonne qu'on a saisi c'est a cette variable qu'on passe la fonction de cryptage  MD5
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbClientBundle:Abonne:Edit.html.twig 
     * 
     */
    public function ajoutUtilisateurAction(): Response(string $locale): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonne est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        $idprofil = $currentConnete["idprofil_abonne"];

        //var_dump($idprofil);exit;        
        if (!in_array('ajoutUtilisateurAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unUtilisateur = new Utilisateur();
        if ($idprofil == 1) {
            $form = $this->createForm($this->createForm(UtilisateurType::class), $unUtilisateur);
        } elseif ($idprofil != 1) {
            $form = $this->createForm(new Utilisateur2Type(), $unUtilisateur);
        }

        $request = $request;


        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $unUtilisateur = $form->getData();

            if (strlen($unUtilisateur->getUsername()) < 5) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorsmalllogin');
                return $this->render('utbClientBundle/Utilisateur/ajoutUtilisateur.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale,
                ));
            }

            $mail = $unUtilisateur->getEmail();
            $login = $unUtilisateur->getUsername();

            // test de login - pas de caractères spéciaux
            $pattern = '/[][(){}<>\/+"*%&=?`^\'!$:;,À�?ÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌ�?Î�?ìíîïÙÚÛÜùúûüÿÑñ]/';
            if (preg_match($pattern, $login)) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'errorlogincaracint');
                return $this->render('utbClientBundle/Utilisateur/ajoutUtilisateur.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale
                ));
            }
            // Fin test de login - pas de caractères spéciaux
            //controle sur l'identicité des password
            $password = $form["password"]->getData();
            $cpassword = $form["cpassword"]->getData();


            $atomlogin = '`^[[:alnum:]]([-_.]?[[:alnum:]]){4,8}$`';   // caractères autorisés          
            $regexlogin = '/' . $atomlogin;  // Une ou plusieurs fois les caractères


            /*             * * Controle du format de l'email          */
            /* $atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
              $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)

              $regex = '/^' . $atom . '+' .   // Une ou plusieurs fois les caractères autorisés avant l'arobase
              '(\.' . $atom . '+)*' .         // Suivis par zéro point ou plus
              // séparés par des caractères autorisés avant l'arobase
              '@' .                           // Suivis d'un arobase
              '(' . $domain . '{1,63}\.)+' .  // Suivis par 1 à 63 caractères autorisés pour le nom de domaine
              // séparés par des points
              $domain . '{2,63}$/i';          // Suivi de 2 à 63 caractères autorisés pour le nom de domaine
             */
            // test de l'adresse e-mail
            $regex = '#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,6}$#';

            // test de l'adresse e-mail
            if (!preg_match($regex, $mail)) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'emailformaterror');
                return $this->render('utbClientBundle/Utilisateur/ajoutUtilisateur.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale
                ));
            }

            /**
             * Controle de correspondance des pwd
             * 
             */
            if ($password != $cpassword) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'passworderror');
                return $this->render('utbClientBundle/Utilisateur/ajoutUtilisateur.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale
                ));
            }
            /**
             * Controle du pwd. Pas moins de 5 caractères
             * 
             */
            if (strlen($password) <6) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'passwordtropcourt');
                return $this->render('utbClientBundle/Utilisateur/ajoutUtilisateur.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale,
                ));
            }
            //Permet de verifier si l'email existe deja
            $email = $this->entityManager
                    ->getRepository('utbClientBundle/Utilisateur')
                    ->findByEmail($mail);

            $unlogin = $this->entityManager
                    ->getRepository('utbClientBundle/Abonne')
                    ->findByLogin($login);

            $unloginutil = $this->entityManager
                    ->getRepository('utbClientBundle:Utilisateur')
                    ->findByLogin($login);

            if (($unlogin != null && $unlogin != 0) || ($unloginutil != null && $unloginutil != 0)) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'loginerror');
                return $this->render('utbClientBundle/Utilisateur/ajoutUtilisateur.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale,
                ));
            }


            //if ($email != null && $email == 0) { //plus de controle sur l'existence ou non du mail


                $vpassword = $unUtilisateur->getPassword();

                /*                 * ********************* */
                /* $pass = $vpassword;
                  $salt = $unUtilisateur->getSalt();
                  $iterations = 5000;

                  $salted = $pass.'{'.$salt.'}';
                  $digest = hash('sha512', $salted, true);

                  for ($i = 1; $i < $iterations; $i++) {
                  $digest = hash('sha512', $digest.$salted, true);
                  } */
                $unUtilisateur->setPassword(md5($vpassword));

                /*                 * ********************** */
                $unUtilisateur->setEtatUtilisateur(1);

                $em->persist($unUtilisateur);

                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'success');
           /* } else {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'emailerror');
                return $this->render('utbClientBundle/Utilisateur/ajoutUtilisateur.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale,
                ));
            }*/
            //}            
            $em->flush();

            return $this->redirect($this->generateUrl('utb_client_liste_utilisateur', ['locale' => $locale]));

            //}                               
        }
        return $this->render('utbClientBundle/Utilisateur/ajoutUtilisateur.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale,
        ));
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
     * @param string $locale Variable passee pour gerer le multilingue sur le site   
     * 
     * @return twig retourne le twig utbClientBundle:Abonne:listeAbonne.html.twig 
     * 
     */
    public function listeUtilisateurAction(): Response(string $locale): Response {
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
        $affichegest = 0;
        if (in_array('listeGestionnaireAdminAction', $listeActions)) {
            $affichegest = 1;
        }

        if ($affichegest != 1) {
            if (!in_array('listeUtilisateurAction', $listeActions)) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
                return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
            }
        }

        if ($affichegest == 1) {
            $listeProfil = $this->entityManager
                    ->getRepository("utbClientBundle/ProfilClient")
                    ->getListeProfilGest();
        } else {
            $listeProfil = $this->entityManager
                    ->getRepository("utbClientBundle/ProfilClient")
                    ->getListeProfilAdmin($locale);
        }

        foreach ($listeProfil as $unprofil) {
            $listeUtilisateur[$unprofil->getId()] = $this->entityManager
                    ->getRepository("utbClientBundle:Utilisateur")
                    ->findProfil($unprofil->getId());
        }
        // var_dump($listeProfil);  exit;      
        $utilisateurid = $currentID;

        return $this->render('utbClientBundle/Utilisateur/listeUtilisateur.html.twig', array('listeUtilisateur' => $listeUtilisateur, 'locale' => $locale, 'utilisateurid' => $utilisateurid, 'listeProfil' => $listeProfil,));
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
    public function detailUtilisateurAction(): Response(int $id, $cas, string $locale): Response {
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
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
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
     * Methode permettant de modifier les 
     * 
     * @var
     * 
     * Les Variables 
     * 
     * $unUtilisateur: Variable pour avoir les informations de la personne à modifier 
     * 
     * $ancienpwd : Recupere l'ancien mot de passe de l'utilisateur
     * 
     * $genre : Permet de 
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site  
     * 
     * @param integer $id Recupere l'identifiant de l'utilisateur auquel on veut modifier le mot de passe 
     * 
     * @param integer $genre Permet de savoir le type d'operation à affectuer (genre=1 gere la modifiation des photos|genre!=1 permet de gere la modification des informations generales)
     * 
     * @return twig  retourne le twig utbClientBundle:Abonne:listeAbonne.html.twig 
     * 
     */
    public function modifierAction(): Response(int $id, string $locale, string $genre): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('modifierAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unUtilisateur = $em->getRepository("utbClientBundle/Utilisateur")->find($id);
        $ancienpwd = $unUtilisateur->getPassword();
        //var_dump($unUtilisateur->getPassword());
        //$form = $this->createForm(RegistrationFormType($unUtilisateur)/:class,  $unUtilisateur);        
        // $extensions = array('jpg','png','jpeg','gif');


        $form = $this->createForm($this->createForm(UtilisateurType::class), $unUtilisateur);
        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            $unUtilisateur->setEtatUtilisateur(1);
            ;

            //if ($form->isValid()) { 

            if ($genre == 1) {
                $unUtilisateur->setPassword($ancienpwd);

                //$ladate="lenomdefichier";
                //$unUtilisateur->setUrlPhoto($ladate);
                //$unUtilisateur->photo->move($unUtilisateur->getUploadRootDir(), $unUtilisateur->getUrlPhoto());               
                //var_dump($unUtilisateur->getUrlPhoto());
                //$unUtilisateur->setPlainPassword($ancienpwd);           
            } else {
                //$unUtilisateur->setPlainPassword($ancienpwd); 
                $unUtilisateur->setPassword($ancienpwd);
                $em->persist($unUtilisateur);
                $em->flush();
            }
            //$unUtilisateur->photo = null;
            // var_dump($unUtilisateur->getUrlPhoto());exit;
            if ($genre == 1) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'successmodifmediaart');
                return $this->redirect($this->generateUrl('detail_utilisateur', ['id' => $id, 'locale' => $locale,]));
            } else {
                $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'modifsuccess');
                return $this->redirect($this->generateUrl('liste_utilisateur', ['locale' => $locale,]));
            }
        }

        if ($genre == 1) {
            return $this->render('utbClientBundle/Utilisateur/ajoutPhoto.html.twig', array('form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'genre' => $genre));
        } else {
            return $this->render('utbClientBundle/Utilisateur/modifUtilisateur.html.twig', array('form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'listestat' => $listestat));
        }
    }

    /**
     * Methode permet de gerer l'etat pour activer
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unAbonne: Variable contenant l'objet abonné créée
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $etat Variable indiquant le type  action effectue 1 pour une activation et 0 pour une desactivation
     * 
     * @return twig  retourne le twig utbClientBundle:Abonne:Edit.html.twig 
     * 
     */
    public function gererEtatAction(): Response(int $id, int $etat, string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('gererEtatAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unUtilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($id);
        $unUtilisateur->setEtatUtilisateur($etat);
        $em->persist($unUtilisateur);
        $em->flush();
        return $this->redirect($this->generateUrl('utb_client_liste_utilisateur', ['locale' => $locale,]));
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

    function activerUtilisateurAction(string $locale, int $id) {

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

        if (!in_array('activerUtilisateurAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $utilisateurId = $id;
        $utilisateur = null;
        $utilisateur = 1; //$this->security->getToken()->getUtilisateur()->getId();         

        if (!empty($utilisateurId)) {
            $unutilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($utilisateurId);
            $unutilisateur->setEtatUtilisateur(1);
            $em->persist($unutilisateur);
            $em->flush();
        }
        $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'activesuccess');
        /* ... et on redirige vers la page d'administration des utilisateurs */
        return $this->redirect($this->generateUrl('utb_client_liste_utilisateur', array('locale' => $locale,)));
    }

    function desactiverUtilisateurAction(string $locale, int $id) {

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

        if (!in_array('desactiverUtilisateurAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $utilisateurId = $id;
        $utilisateur = null;
        $utilisateur = 1; //$this->security->getToken()->getUtilisateur()->getId(); 


        if (!empty($utilisateurId)) {
            $unutilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($utilisateurId);
            $unutilisateur->setEtatUtilisateur(0);
            $em->persist($unutilisateur);
            $em->flush();
        }
        $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'desactivesuccess');
        /* ... et on redirige vers la page d'administration des utilisateurs */
        return $this->redirect($this->generateUrl('utb_client_liste_utilisateur', array('locale' => $locale,)));
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
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
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
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
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
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
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

    public function modifSuivantTypeAction(): Response(int $id, $cas, string $type, string $locale): Response {

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

        if (!in_array('modifSuivantTypeAction', $listeActions) && !in_array('listeGestionnaireAdminAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $id_util = "";
        if ($cas == 0) {
            $id_util = $currentID;
        } elseif ($cas == 1) {
            $id_util = $id;
        }
        $unUtilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($id_util);
        // récuperation de l'utilisateur en question	
        if ($type == 3) {
            $unUtilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($id);
        }

        //recuperation de l'ancien pwd
        $ancienpwd = $unUtilisateur->getPassword();
        //Ancien Email
        $ancienEmail = $unUtilisateur->getEmail();
        //recuperation nom prenom
        $nomprenom = $unUtilisateur->getNomPrenom();
        $leLogin = $unUtilisateur->getUsername();

        // Si l'utilisateur en question existe/ creation des formulaire suivant
        //  le type de modification type=1->modif pwd user, type=2->modif infos user
        if ($unUtilisateur != null) {
            if ($type == 2) {
                $form = $this->createForm($this->createForm(ModifFicheUtilisateurType::class), $unUtilisateur);
            } elseif ($type == 1) {
                $form = $this->createForm($this->createForm(ModifPwdUtilisateurType::class), $unUtilisateur);
            }
        }
        if ($type == 3) {
            $authentif = $this->Auth.Manager;
            $genePwd = $authentif->GenererPasswd(6);
            $geneMD5 = md5($genePwd);
            $unUtilisateur->setGenPsswd($genePwd);
            $unUtilisateur->setPassword($geneMD5);
        }

        $request = $request;

        if ($request->isMethod('POST')) {

            //application des donnees au formulaire 
            $form->handleRequest($request);
            $unUtilisateur = $form->getData();

            if ($type == 2) {
                //recuperation du mail saisi ou actuel pour test
                $mail = $unUtilisateur->getEmail();
                // test de l'adresse e-mail
                $regex = '#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,6}$#';
                // test de l'adresse e-mail
                if (!preg_match($regex, $mail)) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'emailformaterror');
                    return $this->render('utbClientBundle/Utilisateur/modifFicheUtilisateur.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'unUtilisateur' => $unUtilisateur,
                    ));
                }
                // controle de verif si le champ email existe deja//Controle abandonn� actuellement
                /*$email = null;
                if ($ancienEmail != $mail) {
                    $lemail = $this->entityManager
                            ->getRepository('utbClientBundle/Utilisateur')
                            ->findByEmail($mail);
                    if ($lemail != 0) {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'emailexiterror');
                        return $this->redirect($this->generateUrl('utb_client_detail_utilisateur', ['id' => $id_util, 'locale' => $locale, 'cas' => 1,]));
                    }
                }
                */
            }

            // cas ou on modifie juste le mot de passe 
            if ($type == 1) {
                //conservation du nomPrenom
                $unUtilisateur->setNomPrenom($nomprenom);
                $unUtilisateur->setUsername($leLogin);
                // ancien password saisi                
                $vpassword = $request->request->get('vpassword');

                /*                 * ********************* */
                $cryptedPass = $ancienpwd;

                /*                 * ********************** */

                // test pour vérifier si le pwd saisi et crypte = pwd de la bd
                if ($ancienpwd != $cryptedPass) {

                    $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'errancienpwd');

                    return $this->redirect($this->generateUrl('utb_client_detail_utilisateur', ['id' => $id, 'cas' => $cas, 'locale' => $locale,]));
                }

                // test si pwd new = pwd new confirmation
                if ($form["password"]->getData() != $form["cpassword"]->getData()) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'passworderror');
                    /*
                     * 
                     * 
                      return $this->render('utbClientBundle/Utilisateur/modifPwdUtilisateur.html.twig',array(
                      'form' =>$form->createView(),'locale' =>$locale,'listestat'=>$listestat,'unutilisateur'=>$unUtilisateur,
                      )); */
                    return $this->redirect($this->generateUrl('utb_client_detail_utilisateur', ['id' => $id, 'cas' => $cas, 'locale' => $locale,]));
                }
                if (strlen($form["password"]->getData()) < 6) {

                    $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'passwordtropcourt');
                    return $this->redirect($this->generateUrl('utb_client_detail_utilisateur', ['id' => $id, 'cas' => $cas, 'locale' => $locale,]));
                }

                $newpassword = $form["password"]->getData();
                $unUtilisateur->setPassword(md5($newpassword));
            }
            // && ($email !=null) && ( $email !=0 ) ) or ($type == 1) 
            if (($type == 2) or ($type == 1)) {

                //$unUtilisateur->setPlainPassword($unUtilisateur->getPassword());
                $unUtilisateur->setEtatUtilisateur(1);
                $em->persist($unUtilisateur);
                $em->flush();

                if ($type == 2) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'modifinfosuccess');
                    return $this->redirect($this->generateUrl('utb_client_detail_utilisateur', ['id' => $id, 'cas' => $cas, 'locale' => $locale,]));
                    //return $this->redirect($this->generateUrl("utb_client_liste_utilisateur",array('locale' =>$locale))); 
                } elseif ($type == 1) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'modifpwdsuccess');
                    return $this->redirect($this->generateUrl('utb_client_detail_utilisateur', ['id' => $id, 'cas' => $cas, 'locale' => $locale]));
                }
            }
        }
        //$utilisateur =1; //$this->security->getToken()->getUtilisateur()->getId();         
        if ($type == 2) {
            return $this->render('utbClientBundle/Utilisateur/modifFicheUtilisateur.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale
                        , 'unUtilisateur' => $unUtilisateur, 'cas' => $cas
            ));
        } elseif ($type == 1) {
            return $this->render('utbClientBundle/Utilisateur/modifPwdUtilisateur.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale
                        , 'unUtilisateur' => $unUtilisateur, 'cas' => $cas, 'type' => $type,));
        } elseif ($type == 3) {
            $em->persist($unUtilisateur);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'genepwdsuccess');
            return $this->redirect($this->generateUrl('utb_client_detail_utilisateur', ['locale' => $locale, 'id' => $id, 'cas' => 1]));
        }
    }

    public function infoUtilisateur(): Response($em, $authManager, $currentConnete, $user, string $locale): Response {
        //$currentConnete = $authManager->getFlash("utb_client_data");
        if (isset($currentConnete["id_abonne"]) && $currentConnete["id_abonne"] != "") {
            $id_abonne = $currentConnete["id_abonne"];
            $type_user = $currentConnete["type_user"];
            $nomPrenom = $currentConnete["nomPrenom_abonne"];
            $profil = $currentConnete["profil_abonne"];
            $idprofil = $currentConnete["idprofil_abonne"];
            $last_connexion = $currentConnete["last_connexion"];
            $listeActions = $currentConnete["listeActions_abonne"];  
            $subabonne = $currentConnete["sousAbonne"];
            
            $maxIdleTime = $this->container->get->getParameter('maxIdleTime');
            $session = $this->requestStack->getCurrentRequest()Stack->getSession();
            if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {
                /*         * *******  Maj historique ********** */
                    $histo = null;
                    $unuser = null;
                    $unabonne = null;
                    $idhisto = 0;
                    $currentConnete = $authManager->getFlash("utb_client_data");
                    //var_dump();exit;
                    if (isset($currentConnete["id_abonne"])) {
                        $em = $this->entityManager;

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

                            $lafin = new \Datetime();
                            $ledebut = $histo->getDateDeb();
                            $laduree = $lafin->diff($ledebut);
                            $laduree->format('%h heures %i minutes %s secondes');
                            $histo->setDateFin($lafin);
                            $histo->setDuree($laduree->format('%h h %i min %s sec'));
                            $em->persist($histo);
                            $em->flush();
                        }
                    }
                    
                    $_SESSION["utb_client_data"] = array();                

            }
            
        } else {
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        //transformation de $type_user en variable globale
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale); // gautier 404
        $this->requestStack->getCurrentRequest()->attributes->set('id_abonne', $id_abonne);
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', $type_user);
        $this->requestStack->getCurrentRequest()->attributes->set('nomPrenom', $nomPrenom);
        $this->requestStack->getCurrentRequest()->attributes->set('profil', $profil);
        $this->requestStack->getCurrentRequest()->attributes->set('idprofil', $idprofil);
        $this->requestStack->getCurrentRequest()->attributes->set('last_connexion', $last_connexion);
        $this->requestStack->getCurrentRequest()->attributes->set('listeActions', $listeActions);
        $this->requestStack->getCurrentRequest()->attributes->set('sousAbonne', $subabonne);

        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuUtil($em, $id_abonne);
        $this->requestStack->getCurrentRequest()->attributes->set('nbreluutil', $nbrelu);
        //Info non lu abonne
        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuAbonne($em, $id_abonne);

        $this->requestStack->getCurrentRequest()->attributes->set('nbrelu', $nbrelu);

        $this->requestStack->getCurrentRequest()->attributes->set('type_user', $user);
    }

}

