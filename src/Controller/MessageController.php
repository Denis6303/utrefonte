<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Abonne;
use App\Entity\Envoi;
use App\Entity\MessageClient;
use App\Entity\MessageClientType;
use App\Entity\AbonneType;
use App\Entity\ModifPwdType;
use App\Entity\ModifFicheAbonneType;
use App\Entity\ModAbonneType;
use App\Entity\Parametrage;
use App\Entity\Type\RegistrationFormType;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;

use App\Entity\HistoriqueConnexion;
use \utb\ClientBundle\Types\TypeParametre;


/**
 * MessageController 
 * 
 * Le controleur qui gere le module de la messagerie de l'espace client
 * 
 * 
 * Fonction utilisee pour afficher les menus auquels l'utilisateurs à droit
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
 * Cette ligne de code permet de definir la langue à travers la variable locale| (fr) pour le francais et (en) pour l'anglais
 * 
 * Presente dans la majorite des methodes
 * 
 * $this->requestStack->getCurrentRequest()->setLocale($locale);
 * 
 * Declaration du formulaire relatif à une instance d'objet : Presente dans la majorite des methodes
 * 
 * $form: Instance de la classe Form
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class MessageController extends AbstractController
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
     * Methode permettant de gerer les onglets de la page de messagerie de l'abonne
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $total :Permet d'avoir la liste de tous les messages(affiché au niveau de la pagination)
     * 
     * $articles_per_page :    Le nombre de message selectionn� par page
     *
     * $last_page :   Le id de page la dernierre page 
     * 
     * $next_page : Le id de page la dernierre suivante
     * 
     * $previous_page : Le id de page precedent
     * 
     * $listeMessageNonLu : Service qui recupere la liste de message Lu d'un utilisateur
     * 
     * $nbrelu : Service qui recupere le nombre des messages non Lus de l'abonne
     * 
     * $datedebut : Recupere le premier champ date pour les recherches au niveau de la messagerie
     * 
     * $datefin : Recupere le deuxieme champ date pour les recherches au niveau de la messagerie
     * 
     * $contenu : Permet d'effectuer les recherches par rapport au contenu des messages
     * 
     * $listeMessage: Recupere la liste des messages qui doivent s'afficher dans la boite de reception 
     * 
     * $lalisteMessageEnvoye: Recupere la liste des messages evoyés par l'abonné ( les données seront traitees pour permettre d'ajouter d'autres informations )
     * 
     * $unAbonne : Instance de l'objet Abonné
     * 
     * $listegestionnaire : Recupere la liste des gestionnaires d'un abonne
     * 
     * $unmessage : Permet d'avoir les informations concernant un message 
     * 
     * $unenvoi : Instance de l'objet Envoi
     * 
     * $lesadmin: Avoir la liste des admins pour leur envoyer une copie du message des abonnes
     * 
     * $idadmin: Recupere l'id du profil des admins la requette permettant d'avoir la liste de tous les "Super Admin "
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $typePre Permet d'afficher ou pas le Zoom box qui affiche les notifications sur le tableau de bord (1 Ne pas afficher | 0 our l'affichage)
     * 
     * @return twig  retourne le twig utbClientBundle:Message:messagerieAbonne.html.twig 
     * 
     */
    
    public function messagerieAbonneAction(): Response($page, string $type, string $locale, $typemsg): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('messagerieAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        /*
         * Pagination
         */
        $articles_per_page = $this->container->get->getParameter('max_messages_on_listepage');
        //var_dump($articles_per_page);exit;
        $total_me = $em->getRepository("utbClientBundle:MessageClient")->getTotalMessageEnvoyeAbonLocale(null, null, $currentID, 0, 0, 0, 0);

        $last_page_me = ceil($total_me / $articles_per_page);
        $previous_page_me = $page > 1 ? $page - 1 : 1;
        $next_page_me = $page < $last_page_me ? $page + 1 : $last_page_me;

        $total_br = $em->getRepository("utbClientBundle:MessageClient")->getTotalBoiteRecepAbonLocale(null, null, 0, $currentID, 0, 0, 0);
        $last_page_br = ceil($total_br / $articles_per_page);
        $previous_page_br = $page > 1 ? $page - 1 : 1;
        $next_page_br = $page < $last_page_br ? $page + 1 : $last_page_br;

        $request = $request;
        $datedebut = $request->request->get('datedebut');
        $datefin = $request->request->get('datefin');
        $contenu = strtolower($request->request->get('contenu'));
        //var_dump($datefin);
        //var_dump($datedebut);
        
        //Ajout Gautier
        $a = $this->entityManager->getRepository("utbClientBundle:Abonne") ->find($currentID);
        
        $id_temporaire = 0;        
 
        if ($a instanceof Abonne && $a->getProfil()->getId()===TypeParametre::PROFIL_SOUS_ABONNE) {            
            $id_temporaire = $a->getIdAbonneParent()->getId();
        }else{            
            $id_temporaire = $a->getId();
        } 
        
        $listeMessage = $this->entityManager
                ->getRepository("utbClientBundle:MessageClient")
                ->getBoiteReceptionAbonne(10000,$datedebut, $datefin, 0, $id_temporaire, 0, 0, 0, $total_br, $page, $articles_per_page, $contenu, 0);

        $nbretout = count($listeMessage);

        $lalisteMessageEnvoye = $this->entityManager
                ->getRepository("utbClientBundle:MessageClient")
                ->getListMsgAbonneToUser(null, null, $id_temporaire, 0, 0, 0, 0, $total_me, $page, $articles_per_page);
        
       // var_dump($lalisteMessageEnvoye);exit;
        
        $total_ab=count($lalisteMessageEnvoye);

        $i = 0;
        $listeMessageEnvoye = array();

        foreach ($lalisteMessageEnvoye as $unmessage) {

            $listeMessageEnvoye[$i]['contenuMessageClient'] = $unmessage['contenuMessageClient'];
            $listeMessageEnvoye[$i]['idEnvoi'] = $unmessage['idEnvoi'];
            $listeMessageEnvoye[$i]['idmessage'] = $unmessage['idmessage'];
            $listeMessageEnvoye[$i]['msgLu'] = $unmessage['msgLu'];
            $listeMessageEnvoye[$i]['dateEnvoiMsg'] = $unmessage['dateEnvoiMsg'];
            $listeMessageEnvoye[$i]['objetMessageClient'] = $unmessage['objetMessageClient'];
            $unEnvoi = $em->getRepository("utbClientBundle:Envoi")->find($unmessage['idEnvoi']);
            $util = $em->getRepository("utbClientBundle:Utilisateur")->find($unEnvoi->getDestUtil());            
            if(count($util)!=0){
                $listeMessageEnvoye[$i]['nomPrenom'] = $util->getNomPrenom();
            }            
            else{
                $listeMessageEnvoye[$i]['nomPrenom'] = "";
            }            
            $i++;
        }

        //envoi de message

        $unmessage = new MessageClient();
        if ($type == 4) {

            $unmessage->setObjetMessageClient("Demande changement d'adresse");
        }
        //Créer l'objet Abonné
        $unAbonne = $em->getRepository("utbClientBundle:Abonne")->find($id_temporaire);
        // La liste des gestionnaires 
        $listegestionnaire = $this->entityManager
                ->getRepository("utbClientBundle:Abonne")
                ->getListeGestionnaireAbonne($id_temporaire);
        // var_dump($listegestionnaire);                        
        $unenvoi = new Envoi();
        //$idadmin = 1; // identifiant du profil  de l'abonné
        $idadmin = $this->container->get->getParameter('idprofiladmin'); // identifiant du profil  de l'admin
        $lesadmin = $this->entityManager
                ->getRepository("utbClientBundle:Utilisateur")
                ->findAllGestionnaireByLocale($idadmin, $locale);
        // var_dump($lesadmin);exit;

        $form = $this->createForm($this->createForm(MessageClientType::class), $unmessage);

        /* On ne traite que les données passées en méthode POST */

        if ($type != 2) {
            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                $unmessage = $form->getData();
                $idgestionnaire = $request->request->get('idgestionnaire');
                
                $em->persist($unmessage);
                //Abonné l'Expectiteur du message
                $unenvoi->setAbonne($unAbonne);
                //Abonné Recepteur du message à utiliser dans la messagerie des gestionnaires
                $unenvoi->setDestAb(0);
                //Destinateur du message
                $testGest = $em->getRepository("utbClientBundle:Compte")->getGestForAbonne($idgestionnaire,$id_temporaire);                
                
                if($testGest== 0){
                  $type=1;
                  $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorsongest');
                  return $this->redirect($this->generateUrl('utb_client_envoimessagerieabonne', ['locale' => $locale,'type'=>$type]));                    
                }
				
                
                $unenvoi->setDestUtil($idgestionnaire);
                //var_dump($idgestionnaire);exit;                
                
                $unenvoi->setStatutMsg(0);
                $unenvoi->setStatutMsgEnvoye(0);
                $unenvoi->setMsgLu(0);
                $unenvoi->setMsgParent(0);
                $unenvoi->setTypeMessage($typemsg);
                $unenvoi->setTypeEnvoi(1);
                $unenvoi->setDateEnvoiMsg(new \DateTime());
                $unenvoi->setMessageclient($unmessage);
                //A renseigner au cas ou l'expediteur du message est un utilisateur
                $unenvoi->setUtilisateur(null);
                $em->persist($unenvoi);
                $em->flush();
                
                //Envoi de copie pour le message à l'administrateur
				 $gestActif = $em->getRepository("utbClientBundle:Utilisateur")->find($idgestionnaire); 
				//nouveau message au gestionnaire
				$messageAdmin = new MessageClient();
				$votremessage = $unmessage->getContenuMessageClient().'<br/><br/><br/><b><i>'.'Ce message a été anvoyé à '.$gestActif->getNomPrenom().'</i></b>';
				$messageAdmin->setContenuMessageClient($votremessage);
				$messageAdmin->setObjetMessageClient($unmessage->getObjetMessageClient());
				$messageAdmin->setMessageSysteme(0);
				$em->persist($messageAdmin);
                $em->flush();				
				//var_dump($messageAdmin);exit;
                foreach ($lesadmin as $unadmin) {
                    $unenvoi = new Envoi();
                    $unenvoi->setAbonne($unAbonne);
                    //Abonné Recepteur du message à utiliser dans la messagerie des gestionnaires
                    $unenvoi->setDestAb(0);
                    //Destinateur du message
                    $idgest = $unadmin['id'];
                    //var_dump($idgestionnaire);exit;
                    $unenvoi->setDestUtil($idgest);
                    $unenvoi->setStatutMsg(0);
                    $unenvoi->setStatutMsgEnvoye(0);
                    $unenvoi->setMsgLu(0);
                    $unenvoi->setMsgParent(0);
                    $unenvoi->setTypeMessage($typemsg);
                    $unenvoi->setTypeEnvoi(2);
                    $unenvoi->setDateEnvoiMsg(new \DateTime());
                    $unenvoi->setMessageclient($messageAdmin);
                    //A renseigner au cas ou l'expediteur du message est un utilisateur
                    $unenvoi->setUtilisateur(null);
                    $em->persist($unenvoi);
                    $em->flush();
                }
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
                return $this->redirect($this->generateUrl('utb_client_envoimessagerieabonne', ['locale' => $locale,]));
                
            }
        }
        if($contenu!=null || $datedebut != null || $datefin!=null){
               $type=6;
        }

        //return $this->render('utbClientBundle/Message/boiteReceptionAbonne.html.twig', array(
        return $this->render('utbClientBundle/Message/messagerieAbonne.html.twig', array(
                    'locale' => $locale, 'listeMessage' => $listeMessage,
                    'listeMessageEnvoye' => $listeMessageEnvoye,
                    'form' => $form->createView(), 'listegestionnaire' => $listegestionnaire,
                    'nbretout' => $nbretout,
                    'last_page_me' => $last_page_me,
                    'previous_page_me' => $previous_page_me,
                    'current_page' => $page,
                    'next_page_me' => $next_page_me,
                    'total_ab' => $total_ab,
                    'type' => $type,
                    'last_page_br' => $last_page_br,
                    'previous_page_br' => $previous_page_br,
                    'next_page_br' => $next_page_br,
                    'total_br' => $total_br,
        ), $this->response);
    }

    /**
     * Methode permettant d'afficher le detail des messages et notifications de l'abonne
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $listeMessageNonLu : Service qui recupere la liste de message Lu d'un utilisateur
     * 
     * $nbrelu : Service qui recupere le nombre des messages non Lus de l'abonne
     * 
     * $detailMessage: requette pour avoir  le message dont l'abonne veut voir le detail
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $idenvoi Identifiant de la table envoi qui regroupe toutes les onformations a savoir sur la messagerie 
     * 
     * @return twig  retourne le twig utbClientBundle:Message:detailMsgAbonne.html.twig 
     * 
     */
    public function detailMsgAbonneAction(): Response(string $locale, $idenvoi): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);        

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }
        $listeActions = $currentConnete["listeActions_abonne"];        
        
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('detailMsgAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuAbonne($em, $currentID);
        $this->requestStack->getCurrentRequest()->attributes->set('nbrelu', $nbrelu);

        $detailMessage = $this->entityManager
                ->getRepository("utbClientBundle/MessageClient")
                ->getBoiteReceptionAbonne(10000,null, null, 0, 0, 0, 0, $idenvoi, 0, 0, 0, null, 0);

        $unEnvoi = $em->getRepository("utbClientBundle/Envoi")->find($idenvoi);
        $unEnvoi->setMsgLu(1);
        $em->persist($unEnvoi);
        $em->flush();


        return $this->render('utbClientBundle/Message/detailMsgAbonne.html.twig', array(
                    'locale' => $locale, 'detailMessage' => $detailMessage,), $this->response);
    }

    /**
     * Methode permettant d'afficher le detail des messages et notifications de l'utilisateur
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $listeMessageNonLu : Service qui recupere la liste de message Lu d'un utilisateur
     * 
     * $nbrelu instance: du service qui recupere le nombre des messages non Lus de l'abonne
     * 
     * $detailMessage: requette pour avoir  le message dont l'abonne veut voir le detail
     * 
     * $abonne : Instance de l'objet Abonne pour recueillir les informations sur l'abonne a afficher sur la page de detail
     * 
     * $msgde: Recupere le nom et prenom de l'abonne
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $idenvoi Identifiant de la table envoi qui regroupe toutes les onformations a savoir sur la messagerie 
     * 
     * @param integer $type Va permettre d'assigne un message comme lu ou part
     * 
     * @return twig  retourne Le twig utbClientBundle:Message:detailMsgUtil.html.twig 
     * 
     */
    public function detailMsgUtilAction(): Response(string $locale, $idenvoi, string $type): Response {

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

        if (!in_array('detailMsgUtilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        $detailMessage = $this->entityManager
                ->getRepository("utbClientBundle/MessageClient")
                ->getDetailMessage(100, null, null, 0, 0, 0, 0, $idenvoi);

        $unEnvoi = $em->getRepository("utbClientBundle/Envoi")->find($idenvoi);
        if ($unEnvoi->getUtilisateur() == null) {

            $abonne = $em->getRepository("utbClientBundle:Abonne")->find($unEnvoi->getAbonne()->getId());
            $msgde = $abonne->getNomPrenom();
        } else {

            $util = $em->getRepository("utbClientBundle:Utilisateur")->find($unEnvoi->getUtilisateur()->getId());
            $msgde = $util->getNomPrenom();
        }
        if ($type == 1) {
            $unEnvoi = $em->getRepository("utbClientBundle:Envoi")->find($idenvoi);
            $unEnvoi->setMsgLu(1);
            $em->persist($unEnvoi);
            $em->flush();
        }
        return $this->render('utbClientBundle/Message/detailMsgUtil.html.twig', array(
                    'locale' => $locale, 'detailMessage' => $detailMessage, 'msgde' => $msgde, 'type' => $type), $this->response);
    }

    /**
     * Methode permettant d'afficher le detail des messages et notifications de l'abonne
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $listeMessageNonLu : Service qui recupere la liste de message Lu d'un utilisateur
     * 
     * $nbrelu : Service qui recupere le nombre des messages non Lus de l'abonne
     * 
     * $undetail : Requette pour avoir  le message dont l'abonne veut voir le detail
     * 
     * $detailMessage: Tableau qui recupere le message dont l'abonne veut voir le detail
     * 
     * $util : Instance de l'objet Utilisateur pour avoir l'emeteur du message
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $idenvoi Identifiant de la table envoi qui regroupe toutes les onformations a savoir sur la messagerie 
     * 
     * @return twig  retourne le twig utbClientBundle:Message:detailMsgAbonneEnv.html.twig 
     * 
     */
    public function detailMsgAbonneEnvAction(): Response(string $locale, $idenvoi): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }
        
        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('detailMsgAbonneEnvAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $undetail = $this->entityManager
                ->getRepository("utbClientBundle:MessageClient")
                ->getUndetailMsgEnvoye($idenvoi);
        $detailMessage = array();

        $detailMessage['idEnvoi'] = $undetail[0]['idEnvoi'];
        $detailMessage['idmessage'] = $undetail[0]['idmessage'];
        $detailMessage['dateEnvoiMsg'] = $undetail[0]['dateEnvoiMsg'];
        $detailMessage['objetMessageClient'] = $undetail[0]['objetMessageClient'];
        $detailMessage['contenuMessageClient'] = $undetail[0]['contenuMessageClient'];

        $util = $em->getRepository("utbClientBundle:Utilisateur")->find($undetail[0]['destUtil']);
        // var_dump($undetail[0]['destUtil']);   exit;    
        $detailMessage['nomPrenom'] = $util->getNomPrenom();
        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuAbonne($em, $currentID);
        $this->requestStack->getCurrentRequest()->attributes->set('nbrelu', $nbrelu);

        return $this->render('utbClientBundle/Message/detailMsgAbonneEnv.html.twig', array('locale' => $locale, 'detailMessage' => $detailMessage,), $this->response);
    }

    
    /**
     * Methode permettant d'afficher le detail des messages et notifications de l'abonne
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $listeMessageNonLu : Service qui recupere la liste de message Lu d'un utilisateur
     * 
     * $nbrelu : Service qui recupere le nombre des messages non Lus de l'abonne
     * 
     * $undetail : Requette pour avoir  le message dont l'abonne veut voir le detail
     * 
     * $detailMessage: Tableau qui recupere le message dont l'abonne veut voir le detail
     * 
     * $util : Instance de l'objet Utilisateur pour avoir l'emeteur du message
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $idenvoi Identifiant de la table envoi qui regroupe toutes les onformations a savoir sur la messagerie 
     * 
     * @return twig  retourne le twig utbClientBundle:Message:detailMsgAbonneEnv.html.twig 
     * 
     */    
    public function envoiMessageAbonneAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('envoiMessageAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unmessage = new MessageClient();

        //Créer l'objet Abonné
        $unAbonne = $em->getRepository("utbClientBundle/Abonne")->find($currentID);


        $listegestionnaire = $this->entityManager
                ->getRepository("utbClientBundle/Abonne")
                ->getListeGestionnaireAbonne($currentID);

        // var_dump($listegestionnaire);
        $unenvoi = new Envoi();

        $form = $this->createForm($this->createForm(MessageClientType::class), $unmessage);

        /* On ne traite que les données passées en méthode POST */
        $request = $request;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unmessage = $form->getData();

            $em->persist($unmessage);

            //Abonné l'Expectiteur du message
            $unenvoi->setAbonne($unAbonne);
            //Abonné Recepteur du message à utiliser dans la messagerie des gestionnaires
            $unenvoi->setDestAb(0);
            //Destinateur du message
            $idgestionnaire = $request->request->get('idgestionnaire');
            $unenvoi->setDestUtil($idgestionnaire);
            $unenvoi->setStatutMsg(0);
            $unenvoi->setMsgLu(0);
            $unenvoi->setStatutMsgEnvoye(0);
            $unenvoi->setMsgParent(0);
            $unenvoi->setTypeMessage(0);
            $unenvoi->setTypeEnvoi(1);
            $unenvoi->setDateEnvoiMsg(new \DateTime());

            $unenvoi->setMessageclient($unmessage);
            //A renseigner au cas ou l'expediteur du message est un utilisateur
            $unenvoi->setUtilisateur(null);
            $em->persist($unenvoi);
            
            
        
            if ($unAbonne instanceof Abonne && $unAbonne->getProfil()->getId()===TypeParametre::PROFIL_SOUS_ABONNE) {
                
                $a = $unAbonne->getIdAbonneParent();
                $unenvoi1 = new Envoi();
                $unenvoi1->setAbonne($a);
                //Abonné Recepteur du message à utiliser dans la messagerie des gestionnaires
                $unenvoi->setDestAb(0);
                //Destinateur du message
                $unenvoi1->setDestUtil($idgestionnaire);
                $unenvoi1->setStatutMsg(0);
                $unenvoi1->setMsgLu(0);
                $unenvoi1->setStatutMsgEnvoye(0);
                $unenvoi1->setMsgParent(0);
                $unenvoi1->setTypeMessage(0);
                $unenvoi1->setTypeEnvoi(1);
                $unenvoi1->setDateEnvoiMsg(new \DateTime());

                $unenvoi1->setMessageclient($unmessage);
                //A renseigner au cas ou l'expediteur du message est un utilisateur
                $unenvoi1->setUtilisateur(null);
                $em->persist($unenvoi1);
                
                $message='ECRITURE: ENVOI DE MAIL AVEC OBJET ';
                $authManager->writeLogMessage($message,$authManager->getLogin() ,$code='110');
            }
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_boite_reception_abonne', ['locale' => $locale,]));
            
        }

        return $this->render('utbClientBundle/Message/ajoutMessage.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listegestionnaire' => $listegestionnaire,), $this->response);
    }

    /**
     * Methode permettant a un utilisateur d'envoyer  des messages aux abonnés
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unmessage : Instance de la classe MessageClient
     * 
     * $unUtilisateur : Instance de la classe Utilisateur
     * 
     * $listeAbonne Avoir la liste de tous les abonnes pour un Superadmin | et ses abonne pour un gestionnaire (une condition illustree dans la methode)
     * 
     * $undetail : Requette pour avoir  le message dont l'abonne veut voir le detail
     * 
     * $detailMessage: Tableau qui recupere le message dont l'abonne veut voir le detail
     * 
     * $unenvoi: Instance de la classe Envoi
     * 
     * $abonnesIds : Tableau permettant de recuperer la liste des abonnes concat
     * 
     * $util : Instance de l'objet Utilisateur pour avoir l'emeteur du message
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return integer  retourne le twig utbClientBundle:Message:ajoutMessageUtilAbonne.html.twig 
     * 
     */
    public function envoiMessageUtilAbonnesAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $request = $request;
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }
        
        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;
        if (!in_array('envoiMessageUtilAbonnesAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        $unmessage = new MessageClient();
        $unUtilisateur = $em->getRepository("utbClientBundle/Utilisateur")->find($currentID);
        $idgestionnaire = $request->request->get('idgestionnaire');

        if ($currentConnete["idprofil_abonne"] == $idgestionnaire) {
            $listeAbonne = $this->entityManager
                    ->getRepository('utbClientBundle/Abonne')
                    ->getListeAbonneGestionnaire($unUtilisateur->getId());
        } else {
            $listeAbonne = $this->entityManager
                    ->getRepository('utbClientBundle:Abonne')
                    ->findAllByLocale($locale);
        }
        $form = null;
        $form = $this->createForm($this->createForm(MessageClientType::class), $unmessage);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $unmessage = $form->getData();
            //var_dump($unmessage);   
            $abonnesIds = $request->request->get('lesabonnes');
            $abonnesIds = explode("|", $abonnesIds);
            $abonnesIds=array_unique($abonnesIds);
            $em->persist($unmessage);

            for ($i = 1; $i < count($abonnesIds); $i++) {
                $unenvoi = new Envoi();
                $unenvoi->setDestAb($abonnesIds[$i]);
                $unenvoi->setDestUtil(0);
                $unenvoi->setStatutMsg(0);
                $unenvoi->setStatutMsgEnvoye(0);
                $unenvoi->setMsgLu(0);
                $unenvoi->setMsgParent(0);
                $unenvoi->setTypeMessage(0);
                $unenvoi->setTypeEnvoi(1);
                $unenvoi->setDateEnvoiMsg(new \DateTime());
                $unenvoi->setMessageclient($unmessage);
                //A renseigner au cas ou l'expediteur du message est un utilisateur
                $unenvoi->setUtilisateur($unUtilisateur);
                //Mettre setAbonne null car le message n'est pas envoyé un abonne
                $unenvoi->setAbonne(null);
                $em->persist($unenvoi);
            }
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_boitereceptionutil', ['locale' => $locale,]));
        }
        return $this->render('utbClientBundle/Message/ajoutMessageUtilAbonne.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listeAbonne' => $listeAbonne,), $this->response);
    }
    
    
 /**
     * Methode permettant a un utilisateur d'ajouter les messages systemes
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unmessage : Instance de la classe MessageClient
     * 
     * $unUtilisateur : Instance de la classe Utilisateur
     * 
     * $listeAbonne Avoir la liste de tous les abonnes pour un Superadmin | et ses abonne pour un gestionnaire (une condition illustree dans la methode)
     * 
     * $undetail : Requette pour avoir  le message dont l'abonne veut voir le detail
     * 
     * $detailMessage: Tableau qui recupere le message dont l'abonne veut voir le detail
     * 
     * $unenvoi: Instance de la classe Envoi
     * 
     * $abonnesIds : Tableau permettant de recuperer la liste des abonnes concat
     * 
     * $util : Instance de l'objet Utilisateur pour avoir l'emeteur du message
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return integer  retourne le twig utbClientBundle:Message:ajoutMessageUtilAbonne.html.twig 
     * 
     */
    public function messageSystemeAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $request = $request;
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }
        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;
       if (!in_array('messageSystemeAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        $unmessage = new MessageClient();

        $form = null;
        $form = $this->createForm($this->createForm(MessageClientType::class), $unmessage);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $unmessage = $form->getData();
            $unmessage->setMessageSysteme(1);
            $em->persist($unmessage);
     
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_listemessagesysteme', ['locale' => $locale,]));
        }
        return $this->render('utbClientBundle/Message/messageSysteme.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale,), $this->response);
    }
    
 /**
     * Methode permettant a un utilisateur de modifier les messages systemes
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unmessage : Instance de la classe MessageClient
     * 
     * $unUtilisateur : Instance de la classe Utilisateur
     * 
     * $util : Instance de l'objet Utilisateur pour avoir l'emeteur du message
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return twig  retourne le twig utbClientBundle:Message:ajoutMessageUtilAbonne.html.twig 
     * 
     */
    public function modifMessageSystemeAction(): Response(string $locale,$idmessage): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $request = $request;
        
       // $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $listeActions = $currentConnete["listeActions_abonne"];
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }
        //var_dump($currentConnete["listeActions_abonne"]);exit;
        if (!in_array('modifMessageSystemeAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        $unmessage = $em->getRepository("utbClientBundle:MessageClient")->find($idmessage);
        
        $form = null;
        $form = $this->createForm($this->createForm(MessageClientType::class), $unmessage);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $em->persist($unmessage);
     
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_listemessagesysteme', ['locale' => $locale,]));
        }
        return $this->render('utbClientBundle/Message/modifMessageSysteme.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale,'idmessage'=>$idmessage,), $this->response);
    }
    
    

/**
     * Methode qui envoie la liste des messages systemes 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $listeMessageSysteme : le liste des messages systeme
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return twig  retourne le twig utbClientBundle:Message:ajoutMessageUtilAbonne.html.twig 
     * 
     */
    public function listeMessageSystemeAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $request = $request;
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }
        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;
        if (!in_array('listeMessageSystemeAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeMessageSysteme = $em->getRepository("utbClientBundle:MessageClient")->findBy(array("messageSysteme"=>1));

        return $this->render('utbClientBundle/Message/listeMessageSysteme.html.twig', array(
                     'locale' => $locale,'listeMessageSysteme'=>$listeMessageSysteme,), $this->response);
    }
    
    

    /**
     * Methode permettant a un utilisateur d'envoyer  des messages à d'autres utilisateurs
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unmessage : instance de la classe MessageClient
     * 
     * $unUtilisateur : instance de la classe Utilisateur
     * 
     * $listeAutreUtilisateur : Avoir la liste des utilisateurs autres que l'utilisateur actif
     * 
     * $listeUtil: Tableau recupere les utilisateurs auxquels l'utilisateur veut envoyer le message
     * 
     * $unenvoi: Instance de la classe Envoi
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbClientBundle:Message:ajoutMessageUtil.html.twig 
     * 
     */
    public function envoiMessageUtilAction(): Response(string $locale): Response {

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

        if (!in_array('envoiMessageUtilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unmessage = new MessageClient();

        $unUtilisateur = $em->getRepository("utbClientBundle/Utilisateur")->find($currentID);

        $listeAutreUtilisateur = $this->entityManager
                ->getRepository('utbClientBundle/Utilisateur')
                ->findAutreUtilisateur($unUtilisateur->getId());

        $form = $this->createForm($this->createForm(MessageClientType::class), $unmessage);

        /* On ne traite que les données passées en méthode POST */
        $request = $request;
        $listeUtil = array();
        $listeUtil[] = $request->request->get('util', array());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unmessage = $form->getData();

            $em->persist($unmessage);
            //var_dump(count($listeAb[0]));
            for ($i = 0; $i < count($listeUtil[0]); $i++) {
                $unenvoi = new Envoi();
                //var_dump($listeAb[0][$i]);
                $unenvoi->setDestAb(0);
                $unenvoi->setDestUtil($listeUtil[0][$i]);
                $unenvoi->setStatutMsg(0);
                $unenvoi->setStatutMsgEnvoye(0);
                $unenvoi->setMsgLu(0);
                $unenvoi->setMsgParent(0);
                $unenvoi->setTypeEnvoi(1);
                $unenvoi->setDateEnvoiMsg(new \DateTime());
                $unenvoi->setTypeMessage(0);
                $unenvoi->setMessageclient($unmessage);
                //A renseigner au cas ou l'expediteur du message est un utilisateur
                $unenvoi->setUtilisateur($unUtilisateur);
                //Mettre setAbonne null car le message n'est pas envoyé un abonne
                $unenvoi->setAbonne(null);
                $em->persist($unenvoi);
                $em->flush();
            }
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_boitereceptionutil', ['locale' => $locale,]));
        }

        return $this->render('utbClientBundle/Message/ajoutMessageUtil.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listeAutreUtilisateur' => $listeAutreUtilisateur,), $this->response);
    }

    /**
     * Methode permettant a un abonne de repondre à un message
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unmessage : Instance de la classe MessageClient
     * 
     * $unAbonne : Instance de la classe Abonne
     * 
     * $ancienMessage : Instance de la classe messageClient pour avoir le message auquel l'abonne veut repondre
     * 
     * $listeAutreUtilisateur : Avoir la liste des utilisateurs autres que l'utilisateur actif
     * 
     * $listeUtil Tableau recupere les utilisateurs auxquels l'utilisateur veut envoyer le message
     * 
     * $unenvoi: Instance de la classe Envoi
     * 
     * $nouveaumessage : Instance de la classe MessageClient pour reuperer la reponse de l'abonne
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $idmessage Variable passee pour gerer le multilingue sur le site 
     * 
     * @return twig  retourne le twig utbClientBundle:Message:ajoutMessageUtil.html.twig 
     * 
     */
    public function repondreMessageAbonneAction(): Response(string $locale, $idmessage): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('repondreMessageAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unmessage = new MessageClient();

        //Créer l'objet Abonné
        $unAbonne = $em->getRepository("utbClientBundle:Abonne")->find($currentID);
        $ancienMessage = $em->getRepository("utbClientBundle:MessageClient")->find($idmessage);

        $bar = "<br/>" . "..................................................................................................................................................................";
        $ancienMessage->setObjetMessageClient('Re/ ' . $ancienMessage->getObjetMessageClient());
        $ancienMessage->setContenuMessageClient($bar . "<i>" . $ancienMessage->getContenuMessageClient() . "</i>");

        $form = $this->createForm($this->createForm(MessageClientType::class), $ancienMessage);

        /* On ne traite que les données passées en méthode POST */
        $request = $request;
        $unenvoi = new Envoi;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $unmessage = $form->getData();

            $nouveaumessage = new MessageClient();
            $ancienenvoi = $em->getRepository("utbClientBundle/Envoi")->findBy(array('messageclient' => $ancienMessage));

            $destutil = $ancienenvoi[0]->getUtilisateur()->getId();
            $em->persist($unmessage);
            $nouveaumessage->setObjetMessageClient($unmessage->getObjetMessageClient());
            $nouveaumessage->setContenuMessageClient($unmessage->getContenuMessageClient());

            $em->persist($nouveaumessage);
            //Abonné l'Expectiteur du message
            $unenvoi->setAbonne($unAbonne);
            $unenvoi->setStatutMsg(0);
            $unenvoi->setStatutMsgEnvoye(0);
            $unenvoi->setMsgLu(0);
            $unenvoi->setTypeEnvoi(1);
            $unenvoi->setTypeMessage(0);
            $unenvoi->setDateEnvoiMsg(new \DateTime());

            //Abonné Recepteur du message à utiliser dans la messagerie des gestionnaires
            $unenvoi->setDestAb(0);
            //Destinateur du message
            /* $idgestionnaire=$request->request->get('idgestionnaire'); */
            $unenvoi->setDestUtil($destutil);

            //Message auquel il repond
            $unenvoi->setMsgParent($idmessage);

            $unenvoi->setMessageclient($nouveaumessage);
            //A renseigner au cas ou l'expediteur du message est un utilisateur
            $unenvoi->setUtilisateur(null);
            $em->persist($unenvoi);
            $em->flush($nouveaumessage);
            $em->flush($unenvoi);
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_envoimessagerieabonne', ['locale' => $locale,]));
            
        }

        return $this->render('utbClientBundle/Message/repondreMessage.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'idmessage' => $idmessage,), $this->response);
    }

    /**
     * Methode permettant a un utilisateur de repondre à un message
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unmessage : Instance de la classe MessageClient
     * 
     * $unUtil : Instance de la classe Utilisateur
     * 
     * $ancienMessage : Instance de la classe messageClient pour avoir le message auquel l'abonne veut repondre
     * 
     * $listeAutreUtilisateur : Avoir la liste des utilisateurs autres que l'utilisateur actif
     * 
     * $listeUtil Tableau recupere les utilisateurs auxquels l'utilisateur veut envoyer le message
     * 
     * $ancienenvoi :Instance de la classe Envoi pour avoir les Information de la table Envoi concernant le message auquel on repond 
     * 
     * $unenvoi: Instance de la classe Envoi
     * 
     * $nouveaumessage : Instance de la classe MessageClient pour reuperer la reponse de l'abonne
     * 
     * $abonne : Recupere l'Id de l'abonne à l'origine du message auquel on repond
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $idmessage Variable passee pour gerer le multilingue sur le site 
     * 
     * @return twig  retourne le twig utbClientBundle:Message:ajoutMessageUtil.html.twig 
     * 
     */
    public function repondreMessageUtilAction(): Response(string $locale, $idmessage): Response {

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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('repondreMessageUtilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unmessage = new MessageClient();

        //Créer l'objet Abonné
        $unUtil = $em->getRepository("utbClientBundle:Utilisateur")->find($currentID);
        $ancienMessage = $em->getRepository("utbClientBundle:MessageClient")->find($idmessage);
        //Avoir la liste des abonnés
        $bar = "<br/>" . "..................................................................................................................................................................";
        $ancienMessage->setObjetMessageClient('Re/ ' . $ancienMessage->getObjetMessageClient());
        $ancienMessage->setContenuMessageClient($bar . "<i>" . $ancienMessage->getContenuMessageClient() . "</i>");

        $form = $this->createForm($this->createForm(MessageClientType::class), $ancienMessage);

        /* On ne traite que les données passées en méthode POST */
        $request = $request;
        $unenvoi = new Envoi;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $unmessage = $form->getData();

            $nouveaumessage = new MessageClient();
            $ancienenvoi = $em->getRepository("utbClientBundle/Envoi")->findBy(array('messageclient' => $ancienMessage));



            $em->persist($unmessage);
            $nouveaumessage->setObjetMessageClient($unmessage->getObjetMessageClient());
            $nouveaumessage->setContenuMessageClient($unmessage->getContenuMessageClient());

            $em->persist($nouveaumessage);
            //Abonné l'Expectiteur du message
            $unenvoi->setAbonne(null);
            $unenvoi->setStatutMsg(0);
            $unenvoi->setStatutMsgEnvoye(0);

            $unenvoi->setMsgLu(0);
            $unenvoi->setTypeEnvoi(1);
            $unenvoi->setTypeMessage(0);
            $unenvoi->setDateEnvoiMsg(new \DateTime());

            //Abonné Recepteur du message à utiliser dans la messagerie des gestionnaires

            if ($ancienenvoi[0]->getUtilisateur() == null) {

                $abonne = $ancienenvoi[0]->getAbonne()->getId();
                $unenvoi->setDestAb($abonne);
                //Destinateur du message
                $unenvoi->setDestUtil(0);
            } else {
                $util = $ancienenvoi[0]->getUtilisateur()->getId();
                $unenvoi->setDestAb(0);
                //Destinateur du message
                $unenvoi->setDestUtil($util);
            }

            //Message auquel il repond
            $unenvoi->setMsgParent($idmessage);

            $unenvoi->setMessageclient($nouveaumessage);
            //A renseigner au cas ou l'expediteur du message est un utilisateur
            $unenvoi->setUtilisateur($unUtil);
            $em->persist($unenvoi);
            $em->flush($nouveaumessage);
            $em->flush($unenvoi);
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_boitereceptionutil', ['locale' => $locale,]));
        }

        return $this->render('utbClientBundle/Message/repondreMessageUtil.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'idmessage' => $idmessage,), $this->response);
    }

    /**
     * Methode permettant a un utilisateur de transferer un message a un abonne
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unmessage : Instance de la classe MessageClient
     * 
     * $unUtilisateur : Instance de la classe Utilisateur
     * 
     * $ancienMessage : Instance de la classe messageClient pour avoir le message auquel l'abonne veut repondre
     * 
     * $listeAbonne : Recupere la liste des abonnes
     * 
     * $bar : Pour separer les messages
     * 
     * $listeAutreUtilisateur : Avoir la liste des utilisateurs autres que l'utilisateur actif
     * 
     * $listeUtil Tableau recupere les utilisateurs auxquels l'utilisateur veut envoyer le message
     * 
     * $ancienenvoi :Instance de la classe Envoi pour avoir les Informations de la table Envoi concernant le message auquel on veut repondre 
     * 
     * $unenvoi: Instance de la classe Envoi
     * 
     * $nouveaumessage : Instance de la classe MessageClient pour reuperer la reponse de l'abonne
     * 
     * $abonne : Recupere l'Id de l'abonne à l'origine du message auquel on repond
     * 
     * $abonnesIds : Tableau permettant de recuperer la liste concat des abonnes choisis dans la liste
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <string> $idmessage Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbClientBundle:Message:ajoutMessageUtil.html.twig 
     * 
     */
    public function transfereMessageUtilAbonneAction(): Response(string $locale, $idmessage): Response {

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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('transfereMessageUtilAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        $unmessage = new MessageClient();

        //Créer l'objet Abonné
        $unUtilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($currentID);
        $ancienMessage = $em->getRepository("utbClientBundle:MessageClient")->find($idmessage);

        if ($currentConnete["idprofil_abonne"] == $currentID) {
            $listeAbonne = $this->entityManager
                    ->getRepository('utbClientBundle:Abonne')
                    ->getListeAbonneGestionnaire($unUtilisateur->getId());
        } else {
            $listeAbonne = $this->entityManager
                    ->getRepository('utbClientBundle:Abonne')
                    ->findAllByLocale($locale);
        }
        //Avoir la liste des abonnés
        $bar = "<br/>" . "..................................................................................................................................................................";
        $ancienMessage->setObjetMessageClient('Fwd/ ' . $ancienMessage->getObjetMessageClient());
        $ancienMessage->setContenuMessageClient($bar . "<i>" . $ancienMessage->getContenuMessageClient() . "</i>");

        $form = $this->createForm($this->createForm(MessageClientType::class), $ancienMessage);


        $request = $request;


        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $unmessage = $form->getData();

            $abonnesIds = $request->request->get('lesabonnes');
            $abonnesIds = explode("|", $abonnesIds);

            $nouveaumessage = new MessageClient();
            $ancienenvoi = $em->getRepository("utbClientBundle/Envoi")->findBy(array('messageclient' => $ancienMessage));
            $unmessage->setMessageSysteme(0);
            $em->persist($unmessage);
            $nouveaumessage->setObjetMessageClient($unmessage->getObjetMessageClient());
            $nouveaumessage->setContenuMessageClient($unmessage->getContenuMessageClient());

            $em->persist($nouveaumessage);

            //var_dump(count($listeAb[0]));
            //var_dump($abonnesIds);exit;
            for ($i = 1; $i < count($abonnesIds); $i++) {
                $unenvoi = new Envoi();
                // $unenvoi->setDestUtil(0);                                
                $unenvoi->setDestAb($abonnesIds[$i]);

                $unenvoi->setDestUtil(0);
                $unenvoi->setStatutMsg(0);
                $unenvoi->setStatutMsgEnvoye(0);
                $unenvoi->setMsgLu(0);
                $unenvoi->setMsgParent($ancienenvoi[0]->getMessageClient()->getId());
                $unenvoi->setTypeEnvoi(1);
                $unenvoi->setDateEnvoiMsg(new \DateTime());
                $unenvoi->setMessageclient($nouveaumessage);
                //A renseigner au cas ou l'expediteur du message est un utilisateur
                $unenvoi->setUtilisateur($unUtilisateur);
                //Mettre setAbonne null car le message n'est pas envoyé un abonne
                $unenvoi->setAbonne(null);
                $em->persist($unenvoi);
                $em->flush($nouveaumessage);
                $em->flush($unenvoi);
            }
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_boitereceptionutil', ['locale' => $locale,]));
        }


        return $this->render('utbClientBundle/Message/transfererMessageUtilAbonne.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'idmessage' => $idmessage, 'listeAbonne' => $listeAbonne), $this->response);
    }

    /**
     * Methode permettant a un utilisateur de transferer un message a un abonne
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unmessage : Instance de la classe MessageClient
     * 
     * $unUtilisateur : Instance de la classe Utilisateur
     * 
     * $ancienMessage : Instance de la classe messageClient pour avoir le message auquel l'abonne veut repondre
     * 
     * $listeAutreUtilisateur : La liste des utilisateurs autres que celui connecté
     * 
     * $listeUtil : Tableau recuperant la liste des utilisateurs coches dans la liste de ceux a qui on veut envoyer le message
     * 
     * $bar : Pour separer les messages
     * 
     * $listeAutreUtilisateur : Avoir la liste des utilisateurs autres que l'utilisateur actif
     * 
     * $listeUtil Tableau recupere les utilisateurs auxquels l'utilisateur veut envoyer le message
     * 
     * $ancienenvoi :Instance de la classe Envoi pour avoir les Information de la table Envoi concernant le message auquel on repond 
     * 
     * $unenvoi: Instance de la classe Envoi
     * 
     * $nouveaumessage : Instance de la classe MessageClient pour reuperer la reponse de l'abonne
     * 
     * $abonne : recupere l'Id de l'abonne à l'origine du message auquel on repond
     * 
     * $abonnesIds : tableau permettant de recuperer la liste concat des abonnes choisis dans la liste
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer $idmessage Variable passee pour gerer le multilingue sur le site 
     * 
     * @return twig  retourne le twig utbClientBundle:Message:ajoutMessageUtil.html.twig 
     * 
     */
    public function transfereMessageUtilAction(): Response(string $locale, $idmessage): Response {

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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('transfereMessageUtilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unmessage = new MessageClient();


        //Créer l'objet Abonné
        $unUtilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($currentID);
        $ancienMessage = $em->getRepository("utbClientBundle:MessageClient")->find($idmessage);

        $listeAutreUtilisateur = $this->entityManager
                ->getRepository('utbClientBundle:Utilisateur')
                ->findAutreUtilisateur($unUtilisateur->getId());
        //Avoir la liste des abonnés
        $bar = "<br/>" . "..................................................................................................................................................................";
        $ancienMessage->setObjetMessageClient('Fwd/ ' . $ancienMessage->getObjetMessageClient());
        $ancienMessage->setContenuMessageClient($bar . "<i>" . $ancienMessage->getContenuMessageClient() . "</i>");

        $form = $this->createForm($this->createForm(MessageClientType::class), $ancienMessage);


        $request = $request;
        $listeUtil = array();
        $listeUtil[] = $request->request->get('util', array());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unmessage = $form->getData();
            $nouveaumessage = new MessageClient();
            $ancienenvoi = $em->getRepository("utbClientBundle/Envoi")->findBy(array('messageclient' => $ancienMessage));
            $em->persist($unmessage);
            $nouveaumessage->setObjetMessageClient($unmessage->getObjetMessageClient());
            $nouveaumessage->setContenuMessageClient($unmessage->getContenuMessageClient());
            $em->persist($nouveaumessage);
            //var_dump(count($listeAb[0]));
            for ($i = 0; $i < count($listeUtil[0]); $i++) {
                $unenvoi = new Envoi();
                //$unenvoi->setDestUtil(0);                                
                $unenvoi->setDestAb(0);

                $unenvoi->setDestUtil($listeUtil[0][$i]);
                ;
                $unenvoi->setStatutMsg(0);
                $unenvoi->setStatutMsgEnvoye(0);
                $unenvoi->setMsgLu(0);
                $unenvoi->setMsgParent($ancienenvoi[0]->getMessageClient()->getId());
                $unenvoi->setTypeEnvoi(1);
                $unenvoi->setDateEnvoiMsg(new \DateTime());

                $unenvoi->setMessageclient($nouveaumessage);
                //A renseigner au cas ou l'expediteur du message est un utilisateur
                $unenvoi->setUtilisateur($unUtilisateur);
                //Mettre setAbonne null car le message n'est pas envoyé un abonne
                $unenvoi->setAbonne(null);

                $em->persist($unenvoi);
                $em->flush($nouveaumessage);
                $em->flush($unenvoi);
            }
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'messagesuccess');
            return $this->redirect($this->generateUrl('utb_client_boitereceptionutil', ['locale' => $locale,]));
        }

        return $this->render('utbClientBundle/Message/transfererMessageUtil.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'idmessage' => $idmessage, 'listeAutreUtilisateur' => $listeAutreUtilisateur), $this->response);
    }

    /**
     * Methode permettant de lister la liste des messages envoyés par un utilisateur
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: Variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $total :Permet d'avoir la liste de tous les messages(affiché au niveau de la pagination)
     * 
     * $articles_per_page :    Le nombre de message selectionn� par page
     *
     * $last_page :   le id de page la dernierre page 
     * 
     * $next_page : le id de page la dernierre suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $lalistemsg : recupere la liste des messages
     * 
     * $listeMessage : Tableau recuperant la liste des informations neccessaires a afficher sur les messages
     * 
     * $abonne : instance de la classe Abonne
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <string> page Variable pour avoir le numero de la page a afficher
     * 
     * @return <string>  retourne le twig utbClientBundle:Message:ajoutMessageUtil.html.twig 
     * 
     */
    public function listeMessageEnvoyeUtilAction(): Response($page, string $locale): Response {
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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('listeMessageEnvoyeUtilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        /* total des resultats */
        $total = $em->getRepository("utbClientBundle/MessageClient")->getTotalMessageEnvoyeUtilLocale($currentID);

        $articles_per_page = $this->container->get->getParameter('max_messages_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 / 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $lalistemsg = $this->entityManager
                ->getRepository("utbClientBundle:MessageClient")
                ->getListMsgEnvoye($currentID, $total, $page, $articles_per_page);

        $i = 0;
        $listeMessage = array();

        foreach ($lalistemsg as $unmessage) {

            $listeMessage[$i]['contenuMessageClient'] = $unmessage['contenuMessageClient'];
            $listeMessage[$i]['idEnvoi'] = $unmessage['idEnvoi'];
            $listeMessage[$i]['idmessage'] = $unmessage['idmessage'];
            $listeMessage[$i]['msgLu'] = $unmessage['msgLu'];
            $listeMessage[$i]['objetMessageClient'] = $unmessage['objetMessageClient'];
            $listeMessage[$i]['dateEnvoiMsg'] = $unmessage['dateEnvoiMsg'];
            $unEnvoi = $em->getRepository("utbClientBundle:Envoi")->find($unmessage['idEnvoi']);
            if ($unEnvoi->getDestUtil() == 0) {

                $abonne = $em->getRepository("utbClientBundle:Abonne")->find($unEnvoi->getDestAb());
                $listeMessage[$i]['nomPrenom'] = $abonne->getNomPrenom();
            } else {
                $util = $em->getRepository("utbClientBundle:Utilisateur")->find($unEnvoi->getDestUtil());
                $listeMessage[$i]['nomPrenom'] = $util->getNomPrenom();
            }
            $i++;
        }

        return $this->render('utbClientBundle/Message/messageEnvoyeUtil.html.twig', array(
                    'locale' => $locale, 'listeMessage' => $listeMessage,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total
        ), $this->response);
    }

    /**
     * Methode permettant de lister la liste des messages envoyés par un utilisateur
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $total :Permet d'avoir la liste de tous les messages(affiché au niveau de la pagination)
     * 
     * $articles_per_page :    Le nombre de message selectionn� par page
     *
     * $last_page :   le id de page la dernierre page 
     * 
     * $next_page : le id de page la dernierre suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $lalistemsg : recupere la liste des messages
     * 
     * $listeMessage : tableau recuperant la liste des informations neccessaires a afficher sur les messages
     * 
     * $abonne : instance de la classe Abonne
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param integer page Variable pour avoir le numero de la page a afficher
     * 
     * @return twig  retourne le twig utbClientBundle:Message:ajoutMessageUtil.html.twig 
     * 
     */
    public function boiteReceptionUtilAction(): Response($page, string $locale): Response {
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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('boiteReceptionUtilAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $request;
        $datedebut = $request->request->get('datedebut');
        $datefin = $request->request->get('datefin');
        $contenu = strtolower($request->request->get('contenu'));

        $listeMessageLu = $this->entityManager
                ->getRepository("utbClientBundle/MessageClient")
                ->getMsgLuAbonne(100, null, null, $currentID, 0, 0, 0);
        $nbrelu = count($listeMessageLu);
        /* total des resultats */
        $total = $em->getRepository("utbClientBundle/MessageClient")->getTotalBoiteRecepUtilLocale(null, null, $currentID, 0, 0, 0, 0);

        $articles_per_page = $this->container->get->getParameter('max_messages_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;
        /**/
        $lalistemsg = $this->entityManager
                ->getRepository("utbClientBundle:MessageClient")
                ->getBoiteReception(10000, $datedebut, $datefin, $currentID, 0, 0, 0, 0, $total, $page, $articles_per_page, $contenu, 0);
        $nbretout = count($lalistemsg);
        //var_dump($nbretout);
        $i = 0;
        $listeMessage = array();

        foreach ($lalistemsg as $unmessage) {

            $listeMessage[$i]['contenuMessageClient'] = $unmessage['contenuMessageClient'];
            $listeMessage[$i]['idEnvoi'] = $unmessage['idEnvoi'];
            $listeMessage[$i]['typeEnvoi'] = $unmessage['typeEnvoi'];
            $listeMessage[$i]['idmessage'] = $unmessage['idmessage'];
            $listeMessage[$i]['msgLu'] = $unmessage['msgLu'];
            $listeMessage[$i]['typeMessage'] = $unmessage['typeMessage'];
            $listeMessage[$i]['dateEnvoiMsg'] = $unmessage['dateEnvoiMsg'];
            $listeMessage[$i]['objetMessageClient'] = $unmessage['objetMessageClient'];
            $unEnvoi = $em->getRepository("utbClientBundle:Envoi")->find($unmessage['idEnvoi']);
            if ($unEnvoi->getUtilisateur() == null) {

                $abonne = $em->getRepository("utbClientBundle:Abonne")->find($unEnvoi->getAbonne()->getId());
                $listeMessage[$i]['nomPrenom'] = $abonne->getNomPrenom();
            } else {
                $util = $em->getRepository("utbClientBundle:Utilisateur")->find($unEnvoi->getUtilisateur()->getId());
                $listeMessage[$i]['nomPrenom'] = $util->getNomPrenom();
            }
            $i++;
        }
        return $this->render('utbClientBundle/Message/boiteReceptionUtil.html.twig', array(
                    'locale' => $locale, 'listeMessage' => $listeMessage, 'nbrelu' => $nbrelu,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'nbretout' => $nbretout,), $this->response);
    }

    /**
     * Methode permettant de lister la liste des messages envoyés par un utilisateur
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unenvoie :Instance de la classe Envoi
     * 
     * $msgsIds: id concat des messages a supprimer
     * 
     * @return integer  Si statutMsg=1 le message est supprimé | Si statutMsg=0 le message est valide
     * 
     */
    public function SupprimerAction(): Response(): Response {

        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $msgsIds = $request->request->get('msgsIds');
        $msgsIds = explode("|", $msgsIds);

        foreach ($msgsIds as $key => $value) {

            if (!empty($value)) {
                $unenvoie = $em->getRepository("utbClientBundle:Envoi")->find($value);
                //pour supprimer le message                     
                $unenvoie->setStatutMsg(1);

                $em->persist($unenvoie);
            }
        }
        $em->flush();
        return new Response(json_encode(array("result" => "supprsuccess")));
    }

    /**
     * Methode permettant de lister la liste des messages envoyés par un utilisateur
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unenvoie :Instance de la classe Envoi
     * 
     * $msgsIds: id concat des messages a supprimer
     * 
     * @return integer  Si statutMsg=1 le message est supprimé | Si statutMsg=0 le message est valide
     * 
     */
    public function SupprimerNotificationAction(): Response($idmsg): Response {
        $em = $this->entityManager;

        if (!empty($idmsg)) {
            $unenvoie = $em->getRepository("utbClientBundle:Envoi")->find($idmsg);
            //pour supprimer le message                     
            $unenvoie->setStatutMsg(1);
            $em->persist($unenvoie);
        }
        $em->flush();
        return $this->redirect($this->generateUrl("utb_client_accueil"));
    }

    /**
     * Methode permettant de lister la liste des messages envoyés par un utilisateur
     * 
     * @var
     * 
     * Les Variables
     * 
     * $em: variable pour recueillir le getManager pour le traitement des requêttes 
     * 
     * $unenvoie :Instance de la classe Envoi
     * 
     * $msgsIds: id concat des messages a supprimer
     * 
     * @return integer  Si statutMsg=1 le message est supprimé | Si statutMsg=0 le message est valide
     * 
     */
    public function SupprimerMsgEnvoyeAction(): Response(): Response {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $msgsIds = $request->request->get('msgsIds');
        $msgsIds = explode("|", $msgsIds);
        foreach ($msgsIds as $key => $value) {
            if (!empty($value)) {
                $unenvoie = $em->getRepository("utbClientBundle:Envoi")->find($value);
                $unenvoie->setStatutMsgEnvoye(1);
                $em->persist($unenvoie);
            }
        }
        $em->flush();
        return new Response(json_encode(array("result" => "supprsuccess")));
    }

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
            
        } else {
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

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

    public function abonneChoisiAction(): Response(): Response {
        //$em =$this->entityManager;
        $request = $request;

        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax
            $key = $request->request->get('key');
            $selection = $request->request->get('selection');

            //if ($key != null)  
            // {   
            $listeAbonne = $this->entityManager
                    ->getRepository("utbClientBundle:Abonne")
                    ->getAbonneChoisiLocale($key);
            $i = 0;
            //$listeAbonne=array();

            foreach ($listeAbonne as $unabonne) {

                $listeAbonne[$i]['id'] = $unabonne['id'];
                $listeAbonne[$i]['nomPrenom'] = $unabonne['nomPrenom'];
                $listeAbonne[$i]['email'] = $unabonne['email'];
                $listeAbonne[$i]['telAbonne'] = $unabonne['telAbonne'];
                $i++;
            }

            $response = new Response();
            
            $labonne = json_encode(array('unabonne' => $listeAbonne));
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($labonne);
            return $response;
            //}
            // return new Response('OK'); 
        }
    }

}