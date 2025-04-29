<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\ProfilClient;
use App\Entity\ProfilType;
use App\Entity\Facturation;
use App\Entity\FacturationType;
use App\Entity\Module;
use App\Entity\ModuleType;
use App\Entity\Controleur;
use App\Entity\ControleurType;
use App\Entity\Action;
use App\Entity\Abonne;
use App\Entity\ActionType;
use App\Entity\ActionClientType;
use App\Entity\ComptePrRibType;
use App\Entity\HistoriqueConnexion;
use utb\ClientBundle\Controller\SessionExpired;
use \utb\ClientBundle\Types\TypeParametre;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;


/**
 * ClientController 
 * 
 * Le controleur qui affiche la page d'accueil des principaux profils du site comporte aussi les modules qui entrent dans la gestion des droits
 * 
 * 
 * Fonction utilisee pour afficher les menus auquels les utilisateurs ont droit
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
class ClientController extends AbstractController
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

    private $response;

    public function __construct() {
        $this->response = new Response;
        $this->response->headers->addCacheControlDirective('no-cache', true);
        $this->response->headers->addCacheControlDirective('max-age', 0);
        $this->response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response->headers->addCacheControlDirective('no-store', true);
    }

    /**
     * Methode permettant d'afficher la page d'accueil - Espace client
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $type_user : Permet de distinguer les types d'utilisateur (Abonne | Utilisateur)
     * 
     * $nomPrenom : Recupere le nom et prenom de l'utilisateur
     * 
     * $profil : Recupere le profil de l'utilisateur
     * 
     * $last_connexion : Recupere la date de derniere connexion à la l'espace client
     * 
     * $listeMessageNonLu : Service qui recupere la liste des messages Lu d'un utilisateur
     * 
     * $nbrelu : Service qui recupere le nombre des messages non Lus de l'abonne
     * 
     * $nbreluutil: Instance du service qui recupere le nombre des messages non Lus de l'utilisateur (Admin | Gestionnaire etc ..)
     * 
     * $listeHistoriques: Recupere la liste des connnexions à l'espace clent
     * 
     * $listecomptes : 
     * 
     * $lalistemsg : Recupere la liste des messages de la boite de reception pour l'afficher sur le tableau de bord
     * 
     * $lalistenotification : La liste des notifications envoyées a un utilisateur de l'espace client
     * 
     * $lalistenotificationLu : La liste des notifications  lues  par un utilisateur de l'espace client
     * 
     * $noticeLU : Difference entre les notifications recues et celles lues, son resultat entre dans l'affichage des notifications sur la page d'accueil o on affiche pas le ZoomBox
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param string $typePre Permet d'afficher ou pas le Zoom box qui affiche les notifications sur le tableau de bord (1 Ne pas afficher | 0 our l'affichage)
     * 
     * @return string  retourne le twig utbClientBundle:Client:index.html.twig 
     * 
     */
    public function indexAction(): Response(string $locale, $typePre): Response {

        $em = $this->entityManager;
        $type_user = "";
        $nomPrenom = "";
        $profil = "";
        $last_connexion = "";

        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
        }

        //$abonneData = $authManager->getFlash("utb_client_data"); //Comment récuprérer les données de session de l'abonné courant
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        //Type d'utilisateur - pr savoir kels menus afficher.
        $currentConnete = $authManager->getFlash("utb_client_data");
        //var_dump($currentConnete);exit;
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (isset($currentConnete["id_abonne"]) && $currentConnete["id_abonne"] != "") {
            $id_abonne = $currentConnete["id_abonne"];
            $type_user = $currentConnete["type_user"];
            $nomPrenom = $currentConnete["nomPrenom_abonne"];
            $profil = $currentConnete["profil_abonne"];
            $last_connexion = $currentConnete["last_connexion"];
            $listeActions = $currentConnete["listeActions_abonne"];
        }

        //transformation de $type_user en variable globale
        $this->requestStack->getCurrentRequest()->attributes->set('id_abonne', $id_abonne);
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', $type_user);
        $this->requestStack->getCurrentRequest()->attributes->set('nomPrenom', $nomPrenom);
        $this->requestStack->getCurrentRequest()->attributes->set('profil', $profil);
        $this->requestStack->getCurrentRequest()->attributes->set('last_connexion', $last_connexion);
        $this->requestStack->getCurrentRequest()->attributes->set('listeActions', $listeActions);

        //Pr rendre actif le 1er menu
        $this->requestStack->getCurrentRequest()->attributes->set('actif', 1);

        // Appel du service pour avoir la liste des message Lu
        $listeMessageNonLu = $this->message.Manager;

        //Ajout Gautier
        $a = $this->entityManager->getRepository("utbClientBundle:Abonne")->find($currentID);
        $id_temporaire = 0;
        $comptesFils = array();
        if ($a instanceof Abonne && $a->getProfil()->getId() === TypeParametre::PROFIL_SOUS_ABONNE) {
            $id_temporaire = $a->getIdAbonneParent()->getId();
            $comptesFil = $a->getCompteParents();
            $comptesFils = explode("|", $comptesFil);
        } else {
            $id_temporaire = $a->getId();
        }


        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuAbonne($em, $currentID);
        $this->requestStack->getCurrentRequest()->attributes->set('nbrelu', $nbrelu);

        // Pour avoir le nombre de message non Lu       
        $nbreluutil = $listeMessageNonLu->getNombreMsgNonLuUtil($em, $currentID);
        $this->requestStack->getCurrentRequest()->attributes->set('nbreluutil', $nbreluutil);

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if ($type_user == 'utilisateur') {

            /* $listecomptes = $this->entityManager
              ->getRepository("utbClientBundle:Compte")
              ->getListeComptesByGestionnaire($currentConnete["id_abonne"], null); */
            //
            $listecomptes = "";
            $lalistemsg = $this->entityManager
                    ->getRepository("utbClientBundle:MessageClient")
                    ->getBoiteReception(5, null, null, $id_temporaire, 0, 0, 0, 0, 0, 1, 0, null, 0);

            $lalistenotification = $this->entityManager
                    ->getRepository("utbClientBundle:MessageClient")
                    ->getBoiteReception(100, null, null, $currentID, 0, 0, 0, 0, 0, 1, 0, null, 1);
            // var_dump($lalistenotification);exit;
            $lalistenotificationLu = $this->entityManager
                    ->getRepository("utbClientBundle:MessageClient")
                    ->getBoiteReception(100, null, null, $currentID, 0, 1, 0, 0, 0, 1, 0, null, 1);

            //transformation de $type_user en variable globale
            $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');
            $i = 0;
            $noticeLU = count($lalistenotification) - count($lalistenotificationLu);
            $listeMessage = array();

            if ($typePre == 0 && $noticeLU != 0) {

                $notification = "";
                foreach ($lalistenotification as $unenotification) {

                    if ($unenotification['msgLu'] == 1) {
                        //$notification.="<div class=\'notif\'><i><a href=\'client/detail/message/util/".$unenotification['idEnvoi']."/1\'><span class=\'titreNotif\'>".$unenotification['objetMessageClient']." </span><span class=\'notiftemps\'>Envoyé le 05/12/2013 à 10:42 </span></a><span class=\'notifImg\'> <a class=\'notifImgLink\'   href=\'client/supprimer/notification/".$unenotification['idEnvoi']."\'></a></span></i></div>";
                    } else {
                        $i++;
                        $date = $unenotification['dateEnvoiMsg'];
                        $nDate = date_format($date, "d-m-Y à H:i:s");
                        $notification.='<div class="notif"><a href="client/detail/message/util/' . $unenotification['idEnvoi'] . '/1"><b><span class="titreNotif">' . $unenotification['objetMessageClient'] . ' </span></b><div class="notiftemps">Envoyé le ' . $nDate . ' </div></a></div>';
                    }

                    if ($i == 5) {
                        break;
                    }
                }
                $notification.='<div class="notif rightPosition"> <strong><a href="client/boitereception/util"> Vous avez ' . (count($lalistenotification) - count($lalistenotificationLu)) . ' notification(s) non lue(s)</a> </strong>  </div>';

                if (count($lalistenotification) == 0) {
                    $notification = 0;
                }
            } else {
                $notification = 0;
            }
            $lanotification = eregi_replace("'", "\'", $notification);
            foreach ($lalistemsg as $unmessage) {

                $listeMessage[$i]['contenuMessageClient'] = $unmessage['contenuMessageClient'];
                $listeMessage[$i]['idEnvoi'] = $unmessage['idEnvoi'];
                $listeMessage[$i]['idmessage'] = $unmessage['idmessage'];
                $listeMessage[$i]['msgLu'] = $unmessage['msgLu'];
                $listeMessage[$i]['dateEnvoiMsg'] = $unmessage['dateEnvoiMsg'];
                $listeMessage[$i]['objetMessageClient'] = $unmessage['objetMessageClient'];
                $unEnvoi = $em->getRepository("utbClientBundle/Envoi")->find($unmessage['idEnvoi']);
                if ($unEnvoi->getUtilisateur() == null) {
                    $abonne = $em->getRepository("utbClientBundle/Abonne")->find($unEnvoi->getAbonne()->getId());
                    if ($abonne != null) {
                        $listeMessage[$i]['nomPrenom'] = $abonne->getNomPrenom();
                        $listeMessage[$i]['profil'] = $abonne->getProfil()->getLibProfil();
                    }
                } else {
                    $util = $em->getRepository("utbClientBundle:Utilisateur")->find($unEnvoi->getUtilisateur()->getId());
                    if ($util != null) {
                        $listeMessage[$i]['nomPrenom'] = $util->getNomPrenom();
                        $listeMessage[$i]['profil'] = $util->getProfil()->getLibProfil();
                    }
                }
                $i++;
            }

            $listeHistoriques = $this->entityManager
                    ->getRepository("utbClientBundle:HistoriqueConnexion")
                    ->getListeHistoriqueByType($currentID, 1, 5, 0, 0, 0);

            return $this->render('utbClientBundle/Client/indexAdmin.html.twig', array(
                        'locale' => $locale, 'listecpte' => $listecomptes,
                        'listeHisto' => $listeHistoriques, 'listeMail' => $listeMessage, 'notification' => $lanotification,
                            ), $this->response);
        } elseif ($type_user == 'abonne') {

            $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'abonne');

//            $listecomptes = $this->getDoctrine()
//                    ->getManager()
//                    ->getRepository("utbClientBundle:Compte")
//                    ->getListeComptesByCategorie($id_temporaire, '', null);

            $ids = "";
            $compteFils = array();
            if (is_array($comptesFils) && count($comptesFils) > 0) {
                foreach ($comptesFils as $id) {
                    if ($id != "") {
                        $compteFils[] = "'" . $id . "'";
                    }
                }
            }
            $ids = implode(',', $compteFils);
//        var_dump($ids);exit;

            $listecomptes = $this->entityManager
                    ->getRepository("utbClientBundle:Compte")
                    ->getListeComptesByCategorie($id_temporaire, '', null, $ids);
//            var_dump($listecomptes);exit;

            $listeMessage = $this->entityManager
                    ->getRepository("utbClientBundle:MessageClient")
                    ->getBoiteReceptionAbonne(5, null, null, 0, $id_temporaire, 0, 0, 0, 0, 0, 0, null, 0);



            $lalistenotification = $this->entityManager
                    ->getRepository("utbClientBundle:MessageClient")
                    ->getBoiteReceptionAbonne(10000, null, null, 0, $id_temporaire, 0, 0, 0, 0, 0, 0, null, 1);
            //var_dump($lalistenotification);exit;           
            $lalistenotificationLu = $this->entityManager
                    ->getRepository("utbClientBundle:MessageClient")
                    ->getBoiteReceptionAbonne(10000, null, null, 0, $id_temporaire, 1, 0, 0, 0, 0, 0, null, 1);

            $noticeLU = count($lalistenotification) - count($lalistenotificationLu);
            //var_dump($noticeLU);exit;
            $listeHistoriques = $this->entityManager
                    ->getRepository("utbClientBundle:HistoriqueConnexion")
                    ->getListeHistoriqueByType($id_temporaire, 0, 5, 0, 0, 0);
            $i = 0;
            if ($typePre == 0 && $noticeLU != 0) {

                $notification = "";

                foreach ($lalistenotification as $unenotification) {
                    if ($unenotification['msgLu'] == 1) {
                        // $notification.="<div class=\'notif\'><i><a href=\'client/detail/message/".$unenotification['idEnvoi']."/1\'><span class=\'titreNotif\'>".$unenotification['objetMessageClient']." </span><span class=\'notiftemps\'>Envoyé le 05/12/2013 à 10:42 </span></a><span class=\'notifImg\'> <a class=\'notifImgLink\'   href=\'client/supprimer/notification/".$unenotification['idEnvoi']."\'></a></span></i></div>";
                    } else {
                        $i++;
                        $date = $unenotification['dateEnvoiMsg'];
                        $nDate = date_format($date, "d-m-Y à H:i:s"); // Sun 1819-02-14 10:51:00 - semaine 06
                        $notification.='<div class="notif"><a href="client/detail/message/' . $unenotification['idEnvoi'] . '"><b><span class="titreNotif">' . $unenotification['objetMessageClient'] . ' </span></b><div class="notiftemps">Envoyé le ' . $nDate . ' </div></a></div>';
                    }
                    if ($i == 5) {
                        break;
                    }
                }

                $notification.='<div class="notif rightPosition"> <strong><a href="client/boitereception/util"> Vous avez ' . (count($lalistenotification) - count($lalistenotificationLu)) . ' notification(s) non lue(s)</a> </strong>  </div>';

                if (count($lalistenotification) == 0) {
                    $notification = 0;
                }
            } else {
                $notification = 0;
            }
            $lanotification = eregi_replace("'", "\'", $notification);

            return $this->render('utbClientBundle/Client/index.html.twig', array(
                        'locale' => $locale, 'listecpte' => $listecomptes, 'listeHisto' => $listeHistoriques,
                        'listeMail' => $listeMessage, 'notification' => $lanotification,
                            ), $this->response);
        }
    }

    public function definirCompteFacturationAction(): Response($idcompte, $idabonne, string $locale): Response {
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

        if (!in_array('definirCompteFacturationAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        //transformation de $type_user en variable globale
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');

        $unAbonne = $this->entityManager
                ->getRepository("utbClientBundle/Abonne")
                ->findOneByLocale($idabonne, $locale);

        $listeCompte = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getListeComptes($idabonne);

        //idabonne pr faire le controle: max 1 compte de facturation pr 1 abonne
        $totalCompte = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getTotalCompteFacturation($idabonne);

        if ($totalCompte >= 1) {//Il ne peut pas avoir plus d'un compte de facturation
            $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'error_defcomptefacturation');
            return $this->redirect($this->generateUrl('utb_client_detail_abonneadmin', ['id' => $idabonne, 'locale' => $locale,]));

            /*
              //mettre le compte de facturation actuel à 0 et le nouveau à 1
              $compteFacturation = $this->entityManager
              ->getRepository("utbClientBundle:Compte")
              ->getCompteFacturation($idabonne);

              var_dump($compteFacturation);exit;

              $idOldCompte = $compteFacturation['numeroCompte'];

              $oldCompte = $em->getRepository("utbClientBundle:Compte")->find($idOldCompte);
              $oldCompte->setFacturation(0);
              $newCompte = $em->getRepository("utbClientBundle:Compte")->find($idcompte);
              $newCompte->setFacturation(1);

              $em->persist($oldCompte);
              $em->persist($newCompte);

              $em->flush();
              $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'success_modifcomptefacturation');
             */
        } else {//definition ou changement de compte de facturation
            //Pour le faire definir la propriete facturation=1  au niveau du compte N° idcompte
            $uncompte = $em->getRepository("utbClientBundle:Compte")->find($idcompte);
            $uncompte->setFacturation(1);
            $em->persist($uncompte);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'success_defcomptefacturation');
        }

        return $this->render('utbClientBundle/Abonne/detailAbonneAdmin.html.twig', array('unAbonne' => $unAbonne, 'locale' => $locale, 'listeCompte' => $listeCompte, /* ,'listestat'=>$listestat,'abonneid'=>$abonne, */), $this->response);
    }

    public function definirTarifsFacturationAction(): Response(string $locale): Response {
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

        if (!in_array('definirTarifsFacturationAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $id = 1;
        $result = "";
        $form = "";
        //definir les tarifs pour les comptes (AFBW, AFBW2) et UWEB        

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        // Récupération des forfaits 
        $unforfait = $em->getRepository("utbClientBundle/Facturation")->find($id);

        //transformation de $type_user en variable globale
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');

        // Création d'un formulaire
        if ($unforfait != null) {
            $form = $this->createForm($this->createForm(FacturationType::class), $unforfait);
            $result = " ccess";
        } else {
            $unforfait = new Facturation();
            $form = $this->createForm($this->createForm(FacturationType::class), $unforfait);
            $result = "addsuccess";
            //var_dump($unforfait);
        }

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        // On traite les données passées en méthode POST 

        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);

            //var_dump($unprofil->getLibProfil());exit;
            /* Si le formulaire est valide, on valide et on redirige vers la liste des profils */
            // if ($form->isValid()) {
            $em->persist($unforfait);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $result);

            return $this->redirect($this->generateUrl('utb_client_definir_tarifs_facturation', [
                                'locale' => $locale]));
            //}
        }
        return $this->render('utbClientBundle/Client/definirTarifsFacturation.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale), $this->response);
    }

    /**
     * Methode permettant d'ajouter un controlleur - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $uncontroleur: Instance de la classe Controleur a ajouter
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutControleur.html.twig
     *  
     */
    public function ajoutControleurAction(): Response(string $locale): Response {
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

        /* if (!in_array('ajoutControleurAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $uncontroleur = new Controleur();
        $form = $this->createForm($this->createForm(ControleurType::class), $uncontroleur);

        $request = $request;

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $uncontroleur = $form->getData();
            $uncontroleur->setClient(1);
            $em->persist($uncontroleur);
            $em->flush();

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);



            return $this->redirect($this->generateUrl('utb_client_listecontroleur', [
                                'locale' => $locale, //'listestat' => $listestat,
            ]));
        }

        return $this->render('utbClientBundle/Client/ajoutControleur.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale,
                    'listestat' => $listestat,
                        ), $this->response);
    }

    /**
     * Methode permettant de modifier un controlleur - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $uncontroleur: Instance de la classe Controleur a modifier
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $id  Identifiant  du controleur
     * 
     * @return <string> return sur le twig modifControleur.html.twig
     *  
     */
    public function modifierControleurAction(): Response(int $id, string $locale): Response {
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

        /* if (!in_array('modifierControleurAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        // Récupération du controleur
        $unControleur = $em->getRepository("admin/Controleur")->find($id);

        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité profil 
        $form = $this->createForm($this->createForm(ControleurType::class), $unControleur);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

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
                return $this->redirect($this->generateUrl('utb_client_listecontroleur', [
                                    'locale' => $locale, //'listestat'=>$listestat,
                ]));
            }
        }
        return $this->render('utbClientBundle/Client/modifControleur.html.twig', array(
                    'form' => $form->createView(), 'id' => $id,
                    'locale' => $locale,
                    'listestat' => $listestat,), $this->response);
    }

    //************ Debut methode a documenter

    /**
     * Methode permettant d'afficher la liste des controleurs - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat/ Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listecontroleur: Liste de tous les controleurs
     * 
     * $total: Total des instances de la classe Controleur
     * 
     * $articles_per_page: Nombre d'articles par page
     * 
     * $last_page: Numero de la derniere page
     * 
     * $previous_page: Numero de la page precedente
     * 
     * $next_page: Numero de la page suivante 
     * 
     * $entities: Liste de n controleurs par rapport au numero de page
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param <integer> $page  numero de page
     * 
     * @return <string> return sur le twig listeControleur.html.twig
     *  
     */
    public function listeControleurAction(): Response(string $locale, $page): Response {
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

        /* if (!in_array('listeControleurAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $listecontroleur = $em->getRepository("utbAdminBundle:Controleur")->findAllClient($locale, 0);
        /* total des résultats */
        $total = count($listecontroleur);
        $articles_per_page = $this->container->get->getParameter('max_articles_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $entities = $em->getRepository("utbAdminBundle/Controleur")
                        ->createQueryBuilder('p')
                        ->where('p.client = /client')
                        ->setParameter('client', 1)
                        ->setFirstResult(($page * $articles_per_page) - $articles_per_page)
                        ->setMaxResults($this->container->get->getParameter('max_articles_on_listepage'))
                        ->getQuery()->getResult();

        return $this->render('utbClientBundle/Client/listeControleur.html.twig', array(
                    'entities' => $entities, 'locale' => $locale, 'last_page' => $last_page,
                    'previous_page' => $previous_page, 'current_page' => $page, 'next_page' => $next_page,
                    'total' => $total,'listestat' => $listestat,), $this->response);

    }

    /**
     * Methode permettant de supprimer des controleurs - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques type 4 comme explique dans StatistiqueRepository
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
    public function supprControleurAction(): Response(int $id, string $locale): Response {
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

        /* if (!in_array('supprControleurAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        //transformation de $type_user en variable globale
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        //          
        //$dialog = $this->gethelperSet()->get('dialog') ;
        //if (!$dialog->askConfirmation($output,'<question>'Supprimer cet enregistrement?'</question>'){
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $lecontroleur = $em->getRepository("admin/Controleur")->find($id);
        /* Enfin on supprime le categorie ... */
        $em->remove($lecontroleur);
        $em->flush();


        $msgnotification = '';
        $msgnotification = $this->translator->trans('notification.suppression');
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

        /* ... et on redirige vers la page d'clientistration des profils */
        return $this->redirect($this->generateUrl('utb_client_listecontroleur', array(
                            'locale' => $locale,
                            'listestat' => $listestat)));
    }

    /**
     * Methode permettant de lister les modules - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat/ Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listemodule: Liste des modules a afficcher
     * 
     * @return <string> return sur le twig listeModule.html.twig
     *  
     */
    public function listeModuleAction(): Response(string $locale): Response {
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

        /* if (!in_array('listeModuleAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $listemodule = $em->getRepository("admin/Module")->findAllClient($locale);

        return $this->render('utbClientBundle/Client/listeModule.html.twig', array('listemodule' => $listemodule,
                    'locale' => $locale, 'listestat' => $listestat,), $this->response);
    }

    /**
     * Methode permettant de modifier un module - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat/ Liste de statistiques type 4 comme explique dans StatistiqueRepository
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
    public function modifierModuleAction(): Response(int $id, string $locale): Response {

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

        /* if (!in_array('modifierModuleAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $unemodule = $em->getRepository("utbAdminBundle:Module")->find($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        /* Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité Genre */
        $form = $this->createForm($this->createForm(ModuleType::class), $unemodule);



        /* On récupère les données du formulaire si il a déjà été passé */
        $request = $this->requestStack->getCurrentRequest();

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

                return $this->redirect($this->generateUrl('utb_client_listemodule', array(
                                    'locale' => $locale,
                                    'listestat' => $listestat,
                )));
            }
        }
        return $this->render('utbClientBundle/Client/modifModule.html.twig', array(
                    'form' => $form->createView(), 'id' => $id,
                    'locale' => $locale,
                    'listestat' => $listestat,), $this->response);
    }

    /**
     * Methode permettant de supprimer un module - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques type 4 comme explique dans StatistiqueRepository
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
    public function supprModuleAction(): Response(int $id, string $locale): Response {
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

        /* if (!in_array('supprModuleAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $unemodule = $em->getRepository("admin/Module")->find($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        /* Enfin on supprime le categorie ... */
        $em->remove($unemodule);
        $em->flush();
        $msgnotification = '';
        $msgnotification = $this->translator->trans('notification.suppression');
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

        /* ... et on redirige vers la page d'clientistration des categorie */
        return $this->redirect($this->generateUrl('utb_client_listemodule', array(
                            'locale' => $locale, 'listestat' => $listestat)));
    }

    /**
     * Methode permettant d'ajouter un module - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat/ Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $unemodule: Instance de la classe Module a ajouter
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutModule.html.twig
     *  
     */
    public function ajouterModuleAction(): Response(string $locale): Response {
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

        /* if (!in_array('ajouterModuleAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $unemodule = new Module();
        $form = $this->createForm($this->createForm(ModuleType::class), $unemodule);

        $request = $request;

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unemodule = $form->getData();
            $unemodule->setClient(1);
            $em->persist($unemodule);
            $em->flush();
            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('utb_client_listemodule', ['locale' => $locale, 'listestat' => $listestat]));
        }


        return $this->render('utbClientBundle/Client/ajoutModule.html.twig', array(
                    'form' => $form->createView(),
                    'locale' => $locale, 'listestat' => $listestat), $this->response);
    }

    /**
     * Methode permettant de lister les actions - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $listeaction: Liste des actions
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig listeAction.html.twig
     *  
     */
    public function listeActionAction(): Response(string $locale): Response {

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

        /* if (!in_array('listeActionAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $listeaction = $em->getRepository("utbAdminBundle:Action")->findAllClient($locale);


        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        return $this->render('utbClientBundle/Client/listeAction.html.twig', array('listeaction' => $listeaction,
                    'locale' => $locale, 'listestat' => $listestat,), $this->response);
    }

    public function ajoutActionAction(): Response(string $locale): Response {
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

        /* if (!in_array('ajoutActionAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $unaction = new Action();

        $form = $this->createForm($this->createForm(ActionClientType::class), $unaction);

        $request = $request;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unaction = $form->getData();
            $unaction->setClient(1);
            $em->persist($unaction);
            $em->flush();

            $msgnotification = '';
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('utb_client_listeaction', [
                                'locale' => $locale,
            ]));
        }

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        return $this->render('utbClientBundle/Client/ajoutAction.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
                        ), $this->response);
    }

    /**
     * Methode permettant de modifier une action - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques type 4 comme explique dans StatistiqueRepository
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
    public function modifierActionAction(): Response(int $id, string $locale): Response {
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

        /* if (!in_array('modifierActionAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $unaction = $em->getRepository("utbAdminBundle:Action")->find($id);
        //var_dump($id);exit;
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité profil 
        $form = $this->createForm($this->createForm(ActionClientType::class), $unaction);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $request = $request;

        if ($request->isMethod('POST')) {


            $form->handleRequest($request);
            if ($form->isValid()) {
                $unaction = $form->getData();
                $em->persist($unaction);
                $em->flush();
                $msgnotification = '';
                $msgnotification = $this->translator->trans('notification.suppression');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

                return $this->redirect($this->generateUrl('utb_client_listeaction', [
                                    'locale' => $locale,
                                    'listestat' => $listestat,]));
            }
        }
        return $this->render('utbClientBundle/Client/modifAction.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'listestat' => $listestat,), $this->response);
    }

    /**
     * Methode permettant de supprimer une action - Espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat: Liste de statistiques type 4 comme explique dans StatistiqueRepository
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
    public function supprActionAction(): Response(int $id, string $locale): Response {
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

        /* if (!in_array('supprActionAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $unaction = $em->getRepository("utbAdminBundle:Action")->find($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        /* Enfin on supprime le categorie ... */
        $em->remove($unaction);
        $em->flush();
        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Action supprimée avec succès');

        /* ... et on redirige vers la page d'clientistration des profils */
        return $this->redirect($this->generateUrl('utb_client_listeaction', array(
                            'locale' => $locale, 'listestat' => $listestat,)));
    }

    /**
     * Methode permettant de supprimer une action - Espace client
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
    /* function supprAllActionsAction(): Response {
      $em = $this->entityManager;
      $request = $this->requestStack->getCurrentRequest();
      $actionsIds = $request->request->get('actionsIds');
      $actionsIds = explode("|", $actionsIds);
      foreach ($actionsIds as $key => $value) {
      if (!empty($value)) {
      $unaction = $em->getRepository("utbAdminBundle/Action")->find($value);
      $em->remove($unaction);
      $em->flush();
      }
      }

      return new Response(json_encode(array("result" => "success")));
      } */

    /*
     * $type=1 historique espace utilisateur - $type=0 historique espace abonne
     */

    function historiqueTypeAction($page, $idAbonne, string $type, string $locale) {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        //Debut verification si l'utilisateur peut acceder à cette action  
        if ($type == 1) {
            if (!in_array('historiqueTypeUtilAction', $listeActions)) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
                return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
            }
        } elseif ($type == 0) {
            if (!in_array('historiqueTypeAbonneAction', $listeActions)) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
                return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
            }
        }

        if ($type == 1) {
            $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        } elseif ($type == 0) {
            $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $id = "";
        if ($idAbonne == 0) {
            $idAbonne = $currentID;
        }

        /*  if ($type_user == 'utilisateur') {
          $letype = 1;
          }elseif ($type_user == 'abonne') {
          $letype = 0;
          }
         */

        /* total des resultats */

        $total = $em->getRepository("utbClientBundle/HistoriqueConnexion")->getTotalHistorique($currentID, $type, 0);

        if ($total > $this->container->get->getParameter('nbrehistocon')) {
            $total = $this->container->get->getParameter('nbrehistocon');
        }


        $articles_per_page = $this->container->get->getParameter('nbhistoconpage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        //var_dump($next_page);          
        $listeHistoriques = $this->entityManager
                ->getRepository("utbClientBundle:HistoriqueConnexion")
                ->getListeHistoriqueByType($idAbonne, $type, 0, $total, $page, $articles_per_page);

        return $this->render('utbClientBundle/Historique/historyConnexion.html.twig', array(
                    'type' => $type, 'locale' => $locale,
                    'listeHisto' => $listeHistoriques,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                        ), $this->response);
    }

    function listeGestionnaireAction($idAbonne, string $locale) {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('listeGestionnaireAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        //Ajout Gautier
        $a = $this->entityManager->getRepository("utbClientBundle:Abonne")->find($currentID);


        $id_temporaire = 0;
        $comptesFils = array();
        if ($a instanceof Abonne && $a->getProfil()->getId() === TypeParametre::PROFIL_SOUS_ABONNE) {
            $id_temporaire = $a->getIdAbonneParent()->getId();
            $comptesFil = $a->getCompteParents();
            $comptesFils = explode("|", $comptesFil);
        } else {
            $id_temporaire = $a->getId();
        }

//           $ids = "";
//            $compteFils = array();
//            if (is_array($comptesFils) && count($comptesFils) > 0) {
//                foreach ($comptesFils as $id) {
//                    if ($id != "") {
//                        $compteFils[] = "'" . $id . "'";
//                    }
//                }
//            }
//            $ids = implode(',', $compteFils);
//        var_dump($ids);exit;


        $listegestionnaires = $this->entityManager
                ->getRepository("utbClientBundle/Compte")
                ->getListeComptesGestByAbonne(0, $id_temporaire,$comptesFils);


        if ($a instanceof Abonne && $a->getProfil()->getId() === TypeParametre/:PROFIL_SOUS_ABONNE) {
            $authManager->writeLogMessage($message = 'CONSULTATION: ACCES EFFECTIF A LA LISTE DES GESTIONNAIRES DES COMPTES!', $authManager->getLogin(), $code = '100');
        }

        return $this->render('utbClientBundle/Gestionnaire/listeGestionnaire.html.twig', array(
                    'idAbonne' => $currentID, 'locale' => $locale, 'listeGestionnaire' => $listegestionnaires,), $this->response);
    }

    public function droitCltAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        /*
          if ( !in_array('listeGestionnaireAction', $listeActions) ){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          }
         */

        //on récupère la liste des profils
        $listeProfiles = $em->getRepository("utbClientBundle:ProfilClient")
                ->getListeProfileActifs();
        //on récupère la liste des modules 
        $listeModules = $em->getRepository("utbAdminBundle:Module")->findBy(array("client" => 1));

        //on recupere les actions juste pour l'affiche
        $actions = $actionsByProfil = array();
        foreach ($listeModules as $modules) {
            $actions[$modules->getLibmodule() . "|" . $modules->getId()] = array();
            //nous allons cherchons les action pour chaque modules 
            $listeActions = $em->getRepository("utbAdminBundle:Action")
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


            $thisDroits = $em->getRepository("utbClientBundle:droitClient")->findBy(array("profil" => $idProfil));
            //var_dump($thisDroits);

            /**  ajout d'un   */
            if (!empty($thisDroits)) {
                $thisDroits = unserialize($thisDroits[0]->getDroits());
            }


            foreach ($listeModules as $modules) {

                $actionsByProfil[$idProfil][$modules->getLibmodule() . "|" . $modules->getId()] = array();
                //nous allons cherchons les action pour chaque modules 
                $listeActions = $em->getRepository("utbAdminBundle:Action")
                        ->getActionsByModule($modules->getId());
                foreach ($listeActions as $action) {
                    $checkaction = (!empty($thisDroits) && isset($thisDroits[$modules->getId()]) && in_array($action->getId(), $thisDroits[$modules->getId()]) ) ? 1 : 0;
                    $actionsByProfil[$idProfil][$modules->getLibmodule() . "|" . $modules->getId()][] = array(
                        'libAction' => $action->getLibAction(),
                        'DescriptionAction' => $action->getDescriptionAction(),
                        'idAction' => $action->getId() . "|" . $checkaction,
                        'idcontroleur' => $action->getControleur(),
                    );
                }
            }
        }

        return $this->render('utbClientBundle/Client/droit.html.twig', array("listeProfiles" => $listeProfiles,
                    "listeModules" => $listeModules,
                    "actions" => $actions,
                    "actionsByProfil" => $actionsByProfil,
                    "locale" => $locale
                        ), $this->response);
    }

    public function updateDroitsAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        /*
          if ( !in_array('listeGestionnaireAction', $listeActions) ){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          }
         */
        $request = $this->requestStack->getCurrentRequest();

        //on recupere les donnees envoyees depuis le formulaire des droits
        $formData = $request->request->get('formdata');

        $data = array();
        parse_str($formData, $data);
        //on récupère la liste des profils
        $listeProfiles = $em->getRepository("utbClientBundle:ProfilClient")
                ->getListeProfileActifs();
        //on récupère la liste des modules 
        $listeModules = $em->getRepository("utbAdminBundle:Module")->findBy(array("client" => 1));
        try {
            foreach ($listeProfiles as $profile) {
                $droit = array();
                $idprofil = $profile->getId();
                foreach ($listeModules as $l) {
                    if (isset($data['action_module_' . $l->getId() . '_' . $idprofil]) && !empty($data['action_module_' . $l->getId() . '_' . $idprofil])) {
                        $droit[$l->getId()] = $data['action_module_' . $l->getId() . '_' . $idprofil];
                    }
                }
                // Récupération des droits du profil 
                $thisDroits = $em->getRepository("utbClientBundle:droitClient")->findOneBy(array("profil" => $idprofil));
                $thisDroits->setDroits(serialize($droit));
                $em->flush();
            }
            return new Response(json_encode(array("result" => "success")));
        } catch (exception $e) {
            return new Response(json_encode(array("result" => "error")));
        }
    }

    public function showMesComptesAction(): Response(string $locale): Response {
        $em = $this->entityManager;

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('showMesComptesAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        
        //Ajout Gautier
        $a = $this->entityManager->getRepository("utbClientBundle:Abonne")->find($currentID);

        $id_temporaire = 0;
        $comptesFils = array();
        if ($a instanceof Abonne && $a->getProfil()->getId() === TypeParametre::PROFIL_SOUS_ABONNE) {
            $id_temporaire = $a->getIdAbonneParent()->getId();
            $comptesFil = $a->getCompteParents();
            $comptesFils = explode("|", $comptesFil);
        } else {
            $id_temporaire = $a->getId();
        }
        

//        $listecomptes = $this->getDoctrine()
//                ->getManager()
//                ->getRepository("utbClientBundle:Compte")
//                ->getListeComptesByCategorie($id_temporaire, '', null);
        
         $ids = "";
            $compteFils = array();
            if (is_array($comptesFils) && count($comptesFils) > 0) {
                foreach ($comptesFils as $id) {
                    if ($id != "") {
                        $compteFils[] = "'" . $id . "'";
                    }
                }
            }
            $ids = implode(',', $compteFils);

            $listecomptes = $this->entityManager
                    ->getRepository("utbClientBundle/Compte")
                    ->getListeComptesByCategorie($id_temporaire, '', null, $ids);
            

        if ($a instanceof Abonne && $a->getProfil()->getId() === TypeParametre/:PROFIL_SOUS_ABONNE) {
            $authManager->writeLogMessage($message = 'CONSULTATION: ACCES EFFECTIF A LA SYNTHESE DES COMPTES!', $authManager->getLogin(), $code = '100');
        }

        return $this->render('utbClientBundle/Compte/comptesPerso.html.twig', array(
                    'locale' => $locale, 'listecpte' => $listecomptes,
                        ), $this->response);
    }

    public function infoUtilisateur(): Response($em, $authManager, $currentConnete, $user, string $locale): Response {
        //$currentConnete = $authManager->getFlash("utb_client_data");
        $maxIdleTime = $this->container->get->getParameter('maxIdleTime');
//var_dump($currentConnete) ;exit ;
        if (isset($currentConnete["id_abonne"]) && $currentConnete["id_abonne"] != "") {
            $id_abonne = $currentConnete["id_abonne"];
            $type_user = $currentConnete["type_user"];
            $nomPrenom = $currentConnete["nomPrenom_abonne"];
            $profil = $currentConnete["profil_abonne"];
            $last_connexion = $currentConnete["last_connexion"];
            $listeActions = $currentConnete["listeActions_abonne"];
            $subabonne = $currentConnete["sousAbonne"];

            $session = $this->requestStack->getCurrentRequest()Stack->getSession();
            if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {

                /*                 * *******  Maj historique ********** */
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
                            $histo = $em->getRepository("utbClientBundle/HistoriqueConnexion")->find($idhisto);
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
        } else
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
 //var_dump($subabonne) ;exit; 
        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuUtil($em, $id_abonne);
        $this->requestStack->getCurrentRequest()->attributes->set('nbreluutil', $nbrelu);
        //Info non lu abonne
        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuAbonne($em, $id_abonne);

        $this->requestStack->getCurrentRequest()->attributes->set('nbrelu', $nbrelu);

        $this->requestStack->getCurrentRequest()->attributes->set('type_user', $user);
    }

    public function motpasseOublieAction(): Response(string $locale): Response {

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        return $this->render('utbClientBundle/Abonne/pwdOublie.html.twig', array(
                    'locale' => $locale,
                        ), $this->response);
    }

    public function tableauBordHistoriqueAction(): Response(string $locale, $an, $mois): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        /* if (!in_array('listeGestionnaireAction', $listeActions)) {
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */

        $limitan = $this->container->get->getParameter('nbreconan');

        $listeAn = $this->entityManager->getRepository("utbClientBundle/HistoriqueConnexion")->getAnneeHisto($limitan);

        $today = new \Datetime();
        if ($an == 0)
            $an = $today->format('Y');
        $nbjr = 0;
        if ($mois == 0) {
            $limit = $this->container->get->getParameter('nbreconliste');
        } else {
            $limit = 0;
            $interval = new \DateInterval('P1M'); //30 jours
            $intervalajustement = new \DateInterval('P1D'); //1 jour

            $ladeb = new \DateTime();
            $ladeb->setDate($an, $mois, 1);
            $ladeb = $ladeb->format('d-m-Y');
            $lafin = new \DateTime();
            $lafin->setDate($an, $mois, 1);
            $lafin->add($interval);
            $lafin->sub($intervalajustement);

            $lafin = $lafin->format('d-m-Y');

            $thedeb = new \DateTime();
            $thedeb->setDate($an, $mois, 1);
            $thedeb->add($interval);
            $thedeb->sub($intervalajustement);
            $nbjr = $thedeb->format('d');
        }
        //var_dump($nbjr);
        $listehistor = $this->entityManager->getRepository("utbClientBundle:HistoriqueConnexion")->tableauHistorique($limitan, $limit, $an, $mois);

        if ($mois == 0) {
            return $this->render('utbClientBundle/Historique/tabloBordConnexion.html.twig', array('listehistor' => $listehistor, 'locale' => $locale, 'listean' => $listeAn, 'an' => $an,), $this->response);
        } else {
            return $this->render('utbClientBundle/Historique/rechercheHistorik.html.twig', array('listehistor' => $listehistor, 'locale' => $locale,
                        'listean' => $listeAn, 'an' => $an, 'mois' => $mois, 'ladeb' => $ladeb, 'lafin' => $lafin,
                        'afficher' => 0, 'datedeb' => 0, 'datefin' => 0, 'typecon' => 0, 'connecte' => 0, 'nbjr' => $nbjr,), $this->response);
        }
    }

    public function viderCacheAction(): Response(string $locale): Response {

        shell_exec('rm -rf ' . __DIR__ . '/../../../../app/cache/* ');

        //$em = $this->entityManager;
        return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
    }

}
