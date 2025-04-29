<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\ProfilClient;
use App\Entity\ProfilType;
use App\Entity\droitClient;
use App\Entity\Ordre;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;


class ProfilController extends AbstractController
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
     * Methode permettant d'ajouter un profil (définissant les droits des utilisateurs et abonnes) - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unprofil: Instance de la classe Profil pour l'ajout
     * 
     * $default: Eclatement d'un tableau vide : définissant l'ensemble des drits par defaut (donc vide) au profil
     * 
     * $droits: Instance de la classe Droit 
     * 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig ajoutProfil.html.twig avec les variables $locale,$listestat
     *  
     */
    public function ajoutProfilAction(): Response(string $locale): Response {
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
        if (!in_array('ajoutProfilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unprofil = new ProfilClient();
        $default = serialize(array());
        $droits = new droitClient();
        $droits->setProfil($unprofil);
        $droits->setDroits($default);

        $form = $this->createForm($this->createForm(ProfilType::class), $unprofil);

        $request = $request;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unprofil = $form->getData();

            $siexiste = $this->entityManager
                    ->getRepository("utbClientBundle/ProfilClient")
                    ->getSiProfilExiste($id = 0, $locale, $unprofil->getLibProfil());
            
            $sideleted = $this->entityManager
                    ->getRepository("utbClientBundle/ProfilClient")
                    ->getSiProfilDeleted($id = 0, $locale, $unprofil->getLibProfil());

            if ($siexiste != 0) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'existedeja');

                return $this->redirect($this->generateUrl('utb_client_listeprofil', ['locale' => $locale,]));
            }
            else{
                if ($sideleted != 0) {
                    $unprofil->setSuppr(0);
                }
                $em->persist($unprofil);
                $em->persist($droits);
            
            }
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'success');


            return $this->redirect($this->generateUrl('utb_client_listeprofil', ['locale' => $locale,]));
        }
        return $this->render('utbClientBundle/Profil/ajoutProfil.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, //'infos'=>$boxinfos,
        ), $this->response);
    }

    /**
     * Methode permettant d'avoir la liste des profils - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listeprofil: Liste des instances de la classe Profil
     * 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig listeProfil.html.twig 
     *  
     */
    public function listeProfilAction(): Response(string $locale, $ajoutprof): Response {

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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('listeProfilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeprofil = $this->entityManager
                ->getRepository('utbClientBundle:StatistiqueClient')
                ->getStatProfilLocale($locale, $type = 0);


        return $this->render('utbClientBundle/Profil/listeProfil.html.twig', array('listeprofil' => $listeprofil,
                    'locale' => $locale, 'ajoutprof' => $ajoutprof,), $this->response);
    }

    /**
     * Methode permettant de supprimer un profil  - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unprofil: Instance de la classe Profil a supprimer
     * 
     * $unUser: Instance de la classe User relative au profil $unprofil.S'assure si le profil contient un user
     * 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <string> $id identifiant du profil 
     * 
     * @return <string> return le twig listeProfil.html.twig 
     *  
     */
    public function supprProfilAction(): Response(int $id, string $locale): Response {
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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('supprProfilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unprofil = $em->getRepository("utbClientBundle:ProfilClient")->find($id);

        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');

        /* Enfin on supprime le categorie ... */
        /* ... et on redirige vers la page d'administration des profils */
        $unUtilisateur = $this->entityManager
                ->getRepository('utbClientBundle/Utilisateur')
                ->findProfil($id);

        //$undroit = new droit();

        if ($unUtilisateur == null) {
            //$undroit->setProfil($unprofil);
            /* Enfin on supprime le profil... */
            $unprofil->setSuppr(1);
            //$em->remove($unprofil);                      
            $em->flush($unprofil);

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Profil supprimé avec succès');
            return $this->redirect($this->generateUrl('utb_client_listeprofil', array(
                                'locale' => $locale)));        /* ... et on redirige vers la page d'administration des categorie */
        } else {

            $listeprofil = $this->entityManager
                    ->getRepository("utbClientBundle/ProfilClient")
                    ->findAllByLocale($locale);
            return $this->render('utbClientBundle/Profil/listeProfil.html.twig', array('listeprofil' => $listeprofil, 'locale' => $locale,), $this->response);
        }
    }

    /*
     * $locale=fr est mis pr donner une valeur par defaut à $locale;
     *  sans quoi l'activation|désactivation ne marche pas;
     *  pcke $locale est aussi utilisée ds infoUtilisateur.
     */

    function gererAllProfilAction(string $locale = "fr") {

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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('gererAllProfilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');
        $request = $this->requestStack->getCurrentRequest();
        $profilIds = $request->request->get('idprofil');

        $etat = $request->request->get('etatprofil');

        $profilIds = explode("|", $profilIds);
        //$utilisateur = $this->security->getToken()->getUtilisateur()->getId();
        //boucle sur les ids articles
        foreach ($profilIds as $key => $value) {
            if (!empty($value)) {
                $unprofil = $em->getRepository("utbClientBundle:ProfilClient")->find($value);

                $unUtilisateur = $em->getRepository("utbClientBundle:Utilisateur")->findAll();

                if ($unprofil->getId() == 1) {
                    return new Response(json_encode(array("result" => "administrateur")));
                } else {
                    foreach ($unUtilisateur as $keyuser => $valueuser) {
                        if ($valueuser->getProfil()->getId() == $value) {

                            $lutil = $em->getRepository("utbClientBundle:Utilisateur")->find($valueuser->getId());
                            //Desactive ou Active (valeur de $etat, 0 ou 1) tous les utilisateurs ayant ce profil
                            $lutil->setEtatUtilisateur($etat);
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
     * Methode permettant de modifier un profil - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unprofil: Instance de la classe Profil a modifier
     * 
     * @param <integer> $id     Identifiant  du profil
     * @param <string>  $locale, Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig modifProfil
     *  
     */
    public function modifierProfilAction(): Response(int $id, string $locale): Response {

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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('modifierProfilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        // Récupération du profil 
        $unprofil = $em->getRepository("utbClientBundle:ProfilClient")->find($id);

        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité profil 
        $form = $this->createForm($this->createForm(ProfilType::class), $unprofil);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();


        // On traite les données passées en méthode POST 

        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);

            //var_dump($unprofil->getLibProfil());exit;
            /* Si le formulaire est valide, on valide et on redirige vers la liste des profils */
            if ($form->isValid()) {
                $em->persist($unprofil);
                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'modifsuccess');

                return $this->redirect($this->generateUrl("utb_client_listeprofil"));
            }
        }
        return $this->render('utbClientBundle/Profil/modifProfil.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale), $this->response);
    }

    /**
     * Methode permettant de supprimer definitivement des profils selectionnes - Backoffice
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
    function supprAllprofilsAction(): Response {

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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('supprAllprofilsAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');

        $request = $this->requestStack->getCurrentRequest();
        $usersIds = $request->request->get('ds');
        $usersIds = explode("|", $usersIds);

        foreach ($usersIds as $key => $value) {

            if (!empty($value)) {

                $unuser = $em->getRepository("utbClientBundle:ProfilClient")->find($value);


                $unUtilisateur = $this->entityManager
                        ->getRepository('utbClientBundle:Utilisateur')
                        ->findProfil($value);
                //supprimer tous les utilisateurs ayant ce profil
                foreach ($unUtilisateur as $u) {
                    //$oneUser = $em->getRepository("utbClientBundle:Utilisateur")->find($u);
                    $em->remove($u);
                }

                $unAbonne = $this->entityManager
                        ->getRepository('utbClientBundle:Abonne')
                        ->findProfil($value);
                foreach ($unAbonne as $a) {
                    //$oneUser = $em->getRepository("utbClientBundle:Utilisateur")->find($u);
                    $em->remove($a);
                }

                $unDroit = $this->entityManager
                        ->getRepository('utbClientBundle:droitClient')
                        ->findProfil($value);
                $undroitsupp = $em->getRepository("utbClientBundle:droitClient")->find($unDroit[0]['iddroit']);

                $undroit = new droit();

                if ($unUtilisateur == null || $unAbonne == null) {

                    $undroit->setProfil($unuser);

                    /* Enfin on supprime le profil... */

                    $em->remove($unuser);
                    //$em->remove($undroit); 

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
     * Methode permettant d'ajouter un profil dans une autre langue(une traduction) - Espace client
     * 
     * Abandonnée par la suite
     * 
     * @deprecated since version 1.0
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unprofil: Instance de la classe Profil a modifier
     * 
     * @param <integer> $id     Identifiant  du profil
     * @param <string>  $locale, Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutLangueProfil.html.twig
     *  
     */
    /* public function ajoutLangueProfilAction(): Response(string $locale, int $id): Response {

      $authManager = $this->Auth.Manager;//on recupere le service qui gère l'authentification
      //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
      if(!$authManager->isLogged())
      return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

      //Debut verification si l'utilisateur peut acceder à cette action
      $AccessControl = $this->utb_client.AccessControlClient;
      $checkAcces = $AccessControl->verifAcces($em, 'ajoutLangueProfilAction', $authManager);
      if (!$checkAcces) {
      $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
      return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
      }
      //

      $em = $this-> getDoctrine()->getEntityManager();
      $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
      $this->infoUtilisateur($em,$authManager,$currentID,'utilisateur',$locale);
      $this->requestStack->getCurrentRequest()->setLocale($locale);
      $unprofil = $em->getRepository("utbClientBundle:ProfilClient")->find($id);
      $unprofil->setTranslatableLocale($locale);
      $em->refresh($unprofil);
      // Change la locale
      $form = $this->createForm($this->createForm(ProfilType::class), $unprofil);

      $request = $request;

      if ($request->isMethod('POST')) {

      $form->handleRequest($request);
      $em->persist($unprofil);
      $em->flush();
      $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Profil ajouté avec succès');

      return $this->redirect($this->generateUrl('utb_client_listeprofil', ['locale' => $locale,
      ]));
      }

      return $this->render('utbClientBundle/Profil/ajoutLangueProfil.html.twig', array(
      'form' => $form->createView(), 'locale' => $locale, 'id' => $id,
      ));
      } */

    public function infoUtilisateur(): Response($em, $authManager, $currentConnete, $user, string $locale): Response {
        //$currentConnete = $authManager->getFlash("utb_client_data");
        if (isset($currentConnete["id_abonne"]) && $currentConnete["id_abonne"] != "") {
            $id_abonne = $currentConnete["id_abonne"];
            $type_user = $currentConnete["type_user"];
            $nomPrenom = $currentConnete["nomPrenom_abonne"];
            $profil = $currentConnete["profil_abonne"];
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
            
        }
        else
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        //transformation de $type_user en variable globale
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale); // gautier 404
        $this->requestStack->getCurrentRequest()->attributes->set('id_abonne', $id_abonne);
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', $type_user);
        $this->requestStack->getCurrentRequest()->attributes->set('nomPrenom', $nomPrenom);
        $this->requestStack->getCurrentRequest()->attributes->set('profil', $profil);
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
