<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;

use App\Entity\Chargement;
use App\Entity\ChargementType;
use App\Entity\PrerequisChargementType;
use App\Entity\Abonne;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder ;
use Doctrine\ORM\EntityManager;


/**
 * ChargementController pour les actions de chargement de fichiers
 * 
 * La methode suivante permet de verifier le droit de l'utilisateur à exécuter une action 
 * avant d'effectuer cette dernière dans une methode: Presente dans la majorite des méthodes
 *       
 * $authManager: récupère une instance du service s'occupant du test d'authentification
 * 
 * $currentUtilID: Identifiant de la personne connectee : Abonne/Utilisateur
 * 
 * $currentConnete: Récupération des infos contenues dans la session en cours 
 * 
 * if ( !in_array('saveFileAction', $listeActions) ){
 *         $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
 *         return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
 * }
 * 
 * La methode suivante s'assure que l'utilisateur en question est connecté et ceci à travers un service [$this->Auth.Manager]
 * $authManager = $this->Auth.Manager;
 * if(!$authManager->isLogged())
 *   return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale])); 
 * 
 * La ligne de code suivante permet de definir la langue à travers la variable locale .Presente dans la majorite des methodes
 * $this->requestStack->getCurrentRequest()->setLocale($locale);
 * 
 * Déclaration de formulaire relatif à une instance d'objet : Presente dans la majorite des methodes
 *
 * La methode suivante permet de recuperer les infos de la session en cours dans des variables
 * $this->infoUtilisateur 
 * 
 * $form: Instance de la classe Form
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class ChargementController extends AbstractController
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
    
    private $folder ;
    
    public function __construct() {
        $this->response = new Response;
        $this->response->headers->addCacheControlDirective('no-cache', true);
        $this->response->headers->addCacheControlDirective('max-age', 0);
        $this->response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response->headers->addCacheControlDirective('no-store', true);
        $this->folder = __DIR__ . '/../../../../web/upload/logsite';
    } 
    /**
     * Methode permettant d'envoyer un fichier donné sur le serveur web dans le dossier ../../web/uploads/chargement/
     * 
     * @var
     * 
     * Les Variables
     * 
     * $fileidtype: Identifiant de type de fichier
     * 
     * $unchargement: instance de l'objet Chargement
     * 
     * $listeFile: Liste des fichiers envoyes sur serveur web
     * 
     * $extensions: extension de fichiers autorisée
     * 
     * $deb: Date début de type string envoye par le post
     * 
     * $fin: date fin de type string envoye par le post
     * 
     * $datedeb: date de debut de type objet
     * 
     * $datefin: date de fin de type objet
     * 
     * $paramdeb: parametre de debut respectant un format de date debut defini
     * 
     * $interval: Type de periode( hebdomadaire, journalier...)  traduit en nombre de jour
     * 
     * $intervalajustement: Type de periode fixe (journalier) en vue de determiner la fin de la periode 
     * defini dans la periode intervallaire precedente
     * 
     * $paramfin: parametre de debut respectant un format de date fin defini
     * 
     * $datefin: date de fin pour recuperer la date fin de periode predefini
     *
     * $test: pour s'assurer si un fichier couvrant une partie ou la periode a envoyer est deja presente dans la base de donnees
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <string> $type: Variable passee pour le type d'envoi de fichier. Par defaut 0 mais lors d'une evolution future 1,2...
     * 
     * @return <string>  retourne le twig utbClientBundle:Chargement:ajoutChargementFile.html.twig 
     *    Ce twig est organise autour des champs et zones comme suit :
     *       Champ type chargement : Journalier /Mensuel
     *       Champ type compte : AFBW - AFBW2 - UWEB
     *       Champ Date du : date debut periode couvert par le fichier a charger
     *       Champ fichier : Champ de type file  pour l'ajout du fichier a envoyer
     * 
     *       Un bouton "Ajouter" pour implementer l'action d'envoi 
     *       Et une zone de liste de fichiers à charger
     */



    public function saveFileAction(): Response(string $locale, string $type): Response {

        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification

        
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        if (!in_array('saveFileAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $fileidtype = 0;

        //création de l'objet chargement
        $unchargement = new Chargement();

        $form = $this->createForm($this->createForm(ChargementType::class), $unchargement);
        $listeFile = $em->getRepository("utbClientBundle/Chargement")->findBy(array('archive' => 0), array('id' => 'DESC'));

        $request = $request;
        $extensions = 'txt';
        $lesoper=array();    
        if (!file_exists(__DIR__ . "/../../../../web/upload/chargement/uwebj.txt")) {
            $lesoper[0][0]=0;
        }else {
            $lesoper[0][0]=1;
            $lesoper[0][1]='j';
        }

        if (!file_exists(__DIR__ . "/../../../../web/upload/chargement/afbw.txt")) {
            $lesoper[3][0]=0;
        }else {
            $lesoper[3][0]=1;
            $lesoper[3][1]='afbw';
        } 
        if (!file_exists(__DIR__ . "/../../../../web/upload/chargement/afbw2.txt")) {
            $lesoper[4][0]=0;
        }else {
            $lesoper[4][0]=1;
            $lesoper[4][1]="afbw2";
        }        
        if (!file_exists(__DIR__ . "/../../../../web/upload/chargement/afbwn.txt")) {
            $lesoper[5][0]=0;
        }else {
            $lesoper[5][0]=1;
            $lesoper[5][1]="afbwn";
        }        
        
        // a
        if (!file_exists(__DIR__ . "/../../../../web/upload/chargement/uweba.txt")) {
            $lesoper[1][0]=0;
        }else {
            $lesoper[1][0]=1;
            $lesoper[1][1]='a';
        }      
        if (!file_exists(__DIR__ . "/../../../../web/upload/chargement/uwebs.txt")) {
            $lesoper[2][0]=0;
        }else {
            $lesoper[2][0]=1;
            $lesoper[2][1]='s';
        }  
        //var_dump($lesoper);exit;
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unchargement = $form->getData();

            /*             * * début Récupération des dates debut et fin ** */
            $deb = $request->request->get('datedebut');
            $fin = $request->request->get('datefin');
            /*             * * fin Récupération des dates debut et fin ** */


            /*             * ****************************  Début des contôles   ***************************** */
            
            /*
            // verification non saisie de la date debut
            if (trim($deb) == '') {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorperiodedeb');
                return $this->render('utbClientBundle/Chargement/ajoutChargementFile.html.twig', array(
                            'form' => $form->createView(),
                            'locale' => $locale, 'type' => $type,
                            'listefile' => $listeFile,
                ));
            };
            */

            //création des dates debut et fin
            $datedeb = new DateTime();
            $datefin = new DateTime();

            //if (($deb != null) && ($deb != 0)) {
                $datedeb->setDate(substr($deb, 6, 4), substr($deb, 3, 2), substr($deb, 0, 2));
                $datefin->setDate(substr($deb, 6, 4), substr($deb, 3, 2), substr($deb, 0, 2));
                $paramdeb = $datedeb->format("Y-m-d");
            //}

            $interval = new \DateInterval('P0D'); //0 jours
            $intervalajustement = new \DateInterval('P1D'); //0 jours

            if ($form->getData()->getTypeChargement() == 0) {    // Chargement Journalier               
                $interval = new \DateInterval('P1D'); //10 jours
            } elseif ($form->getData()->getTypeChargement() == 1) { // Chargement Hebdomadaire
                $interval = new \DateInterval('P1W'); //15 jours
            } elseif ($form->getData()->getTypeChargement() == 2) { // Chargement Mensuel
                $interval = new \DateInterval('P1M'); //30 jours
            } elseif ($form->getData()->getTypeChargement() == 3) { // Chargement Bimensuel
                $interval = new \DateInterval('P2M'); //60 jours
            } elseif ($form->getData()->getTypeChargement() == 4) { // Chargement Trimestriel
                $interval = new \DateInterval('P3M'); //90 jours
            } elseif ($form->getData()->getTypeChargement() == 5) { // Chargement Annuel
                $interval = new \DateInterval('P1Y'); //365 jours
            }

            $datefin->add($interval);

            // Ajustement pour obtenir la fin de période           
            $datefin->sub($intervalajustement);
            $paramfin = $datefin->format("Y-m-d");

            //test d'existence 
            $test = $this->entityManager
                    ->getRepository("utbClientBundle/Chargement")
                    ->checkSiTypeExist($form->getData()->getTypeCompte(), $form->getData()->getTypeChargement(), $deb, $fin);

            $fileidtype = $unchargement->getTypeCompte();

            if($form->getData()->getNatureChargement() == 2 ){
                if ($test != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypeexist');
                    return $this->render('utbClientBundle/Chargement/ajoutChargementFile.html.twig', array(
                                'form' => $form->createView(),
                                'locale' => $locale, 'type' => $type,
                                'listefile' => $listeFile,
                    ),$this->response);
                }
            }       
        //$i=0;

            
            
           // var_dump($unchargement->getTypeCompte());exit;

            /*             * ****************************  Fin contôle   ***************************** */
            if (($unchargement->file === null) || ($unchargement->file->guessExtension() != $extensions)) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypfic');
                return $this->render('utbClientBundle/Chargement/ajoutChargementFile.html.twig', array(
                            'form' => $form->createView(),
                            'locale' => $locale, 'type' => $type,
                            'listefile' => $listeFile,
                            'lesoper' => $lesoper,
                    
                ),$this->response);
            }


            $unchargement->setStatut(0);
            $unchargement->setArchive(0);
            $unchargement->setDateDeb($datedeb);
            $unchargement->setDateFin($datefin);
            $em->persist($unchargement);
            $em->flush();


            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successaajoutfile');
            return $this->redirect($this->generateUrl('utb_client_savefile', [
                                'type' => $type, 'locale' => $locale]));
        }
        
        return $this->render('utbClientBundle/Chargement/ajoutChargementFile.html.twig', array(
                    'form' => $form->createView(),
                    'locale' => $locale, 'type' => $type,
                    'listefile' => $listeFile,
            'lesoper' => $lesoper,
        ),$this->response);
    }

    public function runCommand(): Response($command, $arguments = array())
    {
        $kernel = $this->kernel;
        $app = new Application($kernel);

        $args = array_merge(array('command' => $command), $arguments);

        $input = new ArrayInput($args);
        $output = new NullOutput();

        return $app->doRun($input, $output);
    }    
    
    
/**
     * Methode permettant d'envoyer un fichier donn� sur le serveur web dans le dossier ../../web/uploads/chargement/
     * 
     * @var
     * 
     * Les Variables
     * 
     * $fileidtype: Identifiant de type de fichier
     * 
     * $unchargement: instance de l'objet Chargement
     * 
     * $listeFile: Liste des fichiers envoyes sur serveur web
     * 
     * $extensions: extension de fichiers autoris�e
     * 
     * $deb: Date d�but de type string envoye par le post
     * 
     * $fin: date fin de type string envoye par le post
     * 
     * $datedeb: date de debut de type objet
     * 
     * $datefin: date de fin de type objet
     * 
     * $paramdeb: parametre de debut respectant un format de date debut defini
     * 
     * $interval: Type de periode( hebdomadaire, journalier...)  traduit en nombre de jour
     * 
     * $intervalajustement: Type de periode fixe (journalier) en vue de determiner la fin de la periode 
     * defini dans la periode intervallaire precedente
     * 
     * $paramfin: parametre de debut respectant un format de date fin defini
     * 
     * $datefin: date de fin pour recuperer la date fin de periode predefini
     *
     * $test: pour s'assurer si un fichier couvrant une partie ou la periode a envoyer est deja presente dans la base de donnees
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <string> $type: Variable passee pour le type d'envoi de fichier. Par defaut 0 mais lors d'une evolution future 1,2...
     * 
     * @return <string>  retourne le twig utbClientBundle:Chargement:ajoutChargementFile.html.twig 
     *    Ce twig est organise autour des champs et zones comme suit :
     *       Champ type chargement : Journalier /Mensuel
     *       Champ type compte : AFBW - AFBW2 - UWEB
     *       Champ Date du : date debut periode couvert par le fichier a charger
     *       Champ fichier : Champ de type file  pour l'ajout du fichier a envoyer
     * 
     *       Un bouton "Ajouter" pour implementer l'action d'envoi 
     *       Et une zone de liste de fichiers � charger
     */
    public function envoieFileAction(): Response(string $locale, string $type): Response {

        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui g�re l'authentification

        
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        if (!in_array('saveFileAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $fileidtype = 0;

        //cr�ation de l'objet chargement
        $unchargement = new Chargement();

        $form = $this->createForm($this->createForm(ChargementType::class), $unchargement);
        $listeFile = $em->getRepository("utbClientBundle/Chargement")->findBy(array('archive' => 0), array('id' => 'DESC'));

        $request = $request;
        $extensions = 'txt';

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unchargement = $form->getData();

            /*             * * d�but R�cup�ration des dates debut et fin ** 
            $deb = $request->request->get('datedebut');
            $fin = $request->request->get('datefin');*/
            /*             * * fin R�cup�ration des dates debut et fin ** */


            /*             * ****************************  D�but des cont�les   ***************************** */
            
            /*
            // verification non saisie de la date debut
            if (trim($deb) == '') {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errorperiodedeb');
                return $this->render('utbClientBundle/Chargement/ajoutChargementFile.html.twig', array(
                            'form' => $form->createView(),
                            'locale' => $locale, 'type' => $type,
                            'listefile' => $listeFile,
                ));
            };
            */

            //cr�ation des dates debut et fin
            $datedeb = new DateTime();
            $datefin = new DateTime();

            //if (($deb != null) && ($deb != 0)) {
              /*  $datedeb->setDate(substr($deb, 6, 4), substr($deb, 3, 2), substr($deb, 0, 2));
                $datefin->setDate(substr($deb, 6, 4), substr($deb, 3, 2), substr($deb, 0, 2));
                $paramdeb = $datedeb->format("Y-m-d");*/
            //}

         /*   $interval = new \DateInterval('P0D'); //0 jours
            $intervalajustement = new \DateInterval('P1D'); //0 jours

            if ($form->getData()->getTypeChargement() == 0) {    // Chargement Journalier               
                $interval = new \DateInterval('P1D'); //10 jours
            } elseif ($form->getData()->getTypeChargement() == 1) { // Chargement Hebdomadaire
                $interval = new \DateInterval('P1W'); //15 jours
            } elseif ($form->getData()->getTypeChargement() == 2) { // Chargement Mensuel
                $interval = new \DateInterval('P1M'); //30 jours
            } elseif ($form->getData()->getTypeChargement() == 3) { // Chargement Bimensuel
                $interval = new \DateInterval('P2M'); //60 jours
            } elseif ($form->getData()->getTypeChargement() == 4) { // Chargement Trimestriel
                $interval = new \DateInterval('P3M'); //90 jours
            } elseif ($form->getData()->getTypeChargement() == 5) { // Chargement Annuel
                $interval = new \DateInterval('P1Y'); //365 jours
            }

            $datefin->add($interval);

            // Ajustement pour obtenir la fin de p�riode           
            $datefin->sub($intervalajustement);
            $datefin->setDate(substr($deb, 6, 4), substr($deb, 3, 2), substr($deb, 0, 2));                
            $paramfin = $datefin->format("Y-m-d");*/

            //test d'existence 
           /* $test = $this->entityManager
                    ->getRepository("utbClientBundle:Chargement")
                    ->checkSiTypeExist($form->getData()->getTypeCompte(), $form->getData()->getTypeChargement(), $deb, $fin);*/

            $fileidtype = $unchargement->getTypeCompte();

           /* if($form->getData()->getNatureChargement() == 2 ){
                //if ($test != 0) {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypeexist');
                    return $this->render('utbClientBundle/Chargement/ajoutChargementFile.html.twig', array(
                                'form' => $form->createView(),
                                'locale' => $locale, 'type' => $type,
                                'listefile' => $listeFile,
                    ));
                }
            }*/
           // var_dump($unchargement->getTypeCompte());exit;

            /*             * ****************************  Fin cont�le   ***************************** */
            if (($unchargement->file === null) || ($unchargement->file->guessExtension() != $extensions)) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypfic');
                return $this->render('utbClientBundle/Chargement/envoieFile.html.twig', array(
                            'form' => $form->createView(),
                            'locale' => $locale, 'type' => $type,
                            'listefile' => $listeFile,
                ),$this->response);
            }


//            //            $unchargement2=$em->getRepository("utbClientBundle:Chargement")->findOneBy(array("nomFile"=>'GAS')/*$unchargement->getId()*/);
//            $unchargement2=$em->getRepository("utbClientBundle:Chargement")->findOneBy(array("nomFile"=>$unchargement->file->getClientOriginalName())/*$unchargement->getId()*/);
//            
//            if($unchargement->file->getClientOriginalName() != "afbwn.txt"){
//                if($unchargement2 != null){
//                        $em->remove($unchargement2);
//                        $em->flush();
//                }
//            }
//           // var_dump($unchargement2) ;
            
            
            
            
            $unchargement->setStatut(0);
            $unchargement->setArchive(0);
            $unchargement->setNatureChargement(2);
            $unchargement->setTypeChargement(0);
            $unchargement->setDateDeb($datedeb);
            $unchargement->setDateFin($datefin);
            $em->persist($unchargement);
            $em->flush();
            
            $unchargement2=$em->getRepository("utbClientBundle:Chargement")->find($unchargement->getId())/*$unchargement->getId()*/;

                //if(count($unchargement2) > 0){
                        $em->remove($unchargement2);
                        $em->flush();
                //}
           
          //  exit ;
            
          /*  if($unchargement->getNomFile()=="uweba.txt"){
                $commnade_a='php /home/utb/www/utbrefonte/app/console utb:loadfile "/home/utb/www/utbrefonte/web/upload/chargement/a"';
                exec($commnade_a);
            }elseif($unchargement->getNomFile()=="uwebs.txt"){
                $commnade_s='php /home/utb/www/utbrefonte/app/console utb/loadfile "/home/utb/www/utbrefonte/web/upload/chargement/s"';
                exec($commnade_s);
            }elseif($unchargement->getNomFile()=="uwebj.txt"){
                $commnade_j='php /home/utb/www/utbrefonte/app/console utb/loadfile "/home/utb/www/utbrefonte/web/upload/chargement/j"';
                exec($commnade_j);
            }*/

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successaajoutfile');
            return $this->redirect($this->generateUrl('utb_client_savefile', [
                                'type' => $type, 'locale' => $locale]));
        }
        
        return $this->render('utbClientBundle/Chargement/envoieFile.html.twig', array(
                    'form' => $form->createView(),
                    'locale' => $locale, 'type' => $type,
                    'listefile' => $listeFile,
        ),$this->response);
    }        
   
    public function prerequisFileAction(): Response(string $locale, string $type): Response {

        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui g�re l'authentification

        
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        if (!in_array('saveFileAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $fileidtype = 0;

        //cr�ation de l'objet chargement
        $unchargement = new Chargement();

        $form = $this->createForm($this->createForm(PrerequisChargementType::class), $unchargement);
        $listeFile = $em->getRepository("utbClientBundle/Chargement")->findBy(array('archive' => 0), array('id' => 'DESC'));

        $request = $request;
        $extensions = 'txt';

        if ($request->isMethod('POST')) {
            
             
            $sql = "TRUNCATE uwebtmp ;";              
            $sql2 = "TRUNCATE operation ;";
            $sql3 = "TRUNCATE afbwtmp ;";              
            $sql4 = "TRUNCATE afbwntmp ;"; 
            $sql5 = "TRUNCATE afbw2tmp ;"; 
            $sql6 = "TRUNCATE uwebjtmp ;"; 
              
              $stmt = $em->getConnection()
                           ->prepare($sql);
              
              $stmt2 = $em->getConnection()
                           ->prepare($sql2);
              
              $stmt3 = $em->getConnection()
                           ->prepare($sql3);
              
              $stmt4 = $em->getConnection()
                           ->prepare($sql4);
              
              $stmt5 = $em->getConnection()
                           ->prepare($sql5);
              
              
              $stmt6 = $em->getConnection()
                           ->prepare($sql6);
              
            
             
              try{
                  $stmt->execute();  
                  $stmt2->execute();  
                  $stmt3->execute();  
                  $stmt4->execute();  
                  $stmt5->execute();  
                  $stmt6->execute();  
                  
              }catch ( \Exception $e){
                   var_dump($e);exit;  
              }    
              
              
//            
//            $form->handleRequest($request);
//            $unchargement = $form->getData();
//
//           
//
//            //cr�ation des dates debut et fin
//            $datedeb = new DateTime();
//            $datefin = new DateTime();
//
//
//            $fileidtype = $unchargement->getTypeCompte();
//
//           
//
//            /*             * ****************************  Fin cont�le   ***************************** */
//            if (($unchargement->file === null) || ($unchargement->file->guessExtension() != $extensions)) {
//                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errortypfic');
//                return $this->render('utbClientBundle/Chargement/envoieFile.html.twig', array(
//                            'form' => $form->createView(),
//                            'locale' => $locale, 'type' => $type,
//                            'listefile' => $listeFile,
//                ),$this->response);
//            }
//
//            $unchargement->setStatut(0);
//            $unchargement->setArchive(0);
//            $unchargement->setNatureChargement(2);
//            $unchargement->setTypeChargement(0);
//            $unchargement->setDateDeb($datedeb);
//            $unchargement->setDateFin($datefin);
//            $em->persist($unchargement);
//            $em->flush();
//            
//            $unchargement2=$em->getRepository("utbClientBundle:Chargement")->find($unchargement->getId())/*$unchargement->getId()*/;
//
//                        $em->remove($unchargement2);
//                        $em->flush();
//             
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successaajoutfileprerequis');
            return $this->redirect($this->generateUrl('utb_client_envoiefile', [
                                'type' => $type, 'locale' => $locale]));
        }
        
        return $this->render('utbClientBundle/Chargement/prerequisFile.html.twig', array(
                    'form' => $form->createView(),
                    'locale' => $locale, 'type' => $type,
                    'listefile' => $listeFile,
        ),$this->response);
    }        
  

  public function executerFileAction(): Response(string $locale,int $id,$typeaction): Response {

        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui g�re l'authentification

        
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        if (!in_array('saveFileAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $fileidtype = 0;

        //cr�ation de l'objet chargement
             $unchargement= $em->getRepository("utbClientBundle:Chargement")->find($id);
			 
			 //var_dump($id) ; exit ;
			 
       if($unchargement!=null){
            if($unchargement->getNomFile()=="uweba.txt" ){
                $commnade_a=array();
                $commnade_a['path']=  "/home/utb/www/utbrefonte/web/upload/chargement/a";
                //shell_exec($commnade_j);
                
                $this->runCommand('utb:loadfile',$commnade_a);
                
            }elseif($unchargement->getNomFile()=="uwebs.txt" ){
                $commnade_s=array();
                $commnade_s['path']=  "/home/utb/www/utbrefonte/web/upload/chargement/s";
                //shell_exec($commnade_j);
                
                $this->runCommand('utb:loadfile',$commnade_s);
            }elseif($unchargement->getNomFile()=="uwebj.txt" ){
                $commnade_j=array();
                $commnade_j['path']=  "/home/utb/www/utbrefonte/web/upload/chargement/j";
                //shell_exec($commnade_j);
                
                $this->runCommand('utb:loadfile',$commnade_j);
                

                //var_dump($commnade_j);exit;
                //exec($commnade_j);
            }
			
			 elseif($unchargement->getNomFile()=="afbwn.txt" ){
			 
				//var_dump('Tirer') ; exit ;
                $commnade_a=array();
                $commnade_a['path']=  "/home/utb/www/utbrefonte/web/upload/chargement/n";
                //shell_exec($commnade_j);
                
                $this->runCommand('utb:loadfile',$commnade_a);
                
            }
			
           $em->remove($unchargement);
                $em->flush(); 
       }else{
            if( $typeaction =="a"){
                $commnade_a=array();
                $commnade_a['path']=  "/home/utb/www/utbrefonte/web/upload/chargement/a";
                //shell_exec($commnade_j);
                
                $this->runCommand('utb:loadfile',$commnade_a);
                
            }elseif( $typeaction =="s"){
                $commnade_s=array();
                $commnade_s['path']=  "/home/utb/www/utbrefonte/web/upload/chargement/s";
                //shell_exec($commnade_j);
                
                $this->runCommand('utb:loadfile',$commnade_s);
            }elseif($typeaction =="j"){
                $commnade_j=array();
                $commnade_j['path']=  "/home/utb/www/utbrefonte/web/upload/chargement/j";
                //shell_exec($commnade_j);
                
                $this->runCommand('utb:loadfile',$commnade_j);
                

                //var_dump($commnade_j);exit;
                //exec($commnade_j);
            }           
       }
                 

           
            //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successaajoutfile');
            return $this->redirect($this->generateUrl('utb_client_savefile', [
                                 'locale' => $locale]));
        
        
    }        
    
    /**
     * Methode permettant de consulter la liste des informations suite au traitement du fichier effectif du fichier envoye
     * 
     * @var
     * 
     * Les Variables
     * 
     * $total: Nombre total des comptes inexistants dans la base et existants dans le fichier
     * 
     * $articles_per_page: Nombre de comptes inexistnats par page
     * 
     * $last_page: Numero de la derniere page
     * 
     * $next_page: Numero de la page suivante
     * 
     * $cptes_inexistants: Liste des comptes inexistants 
     * 
     * $chargements_Stat: Liste des statistiques relatives au chargement du fichier en cours
     * 
     * @param <string> $page Variable passee pour gerer la pagination de la liste des comptes inexistants
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <string> $type: Variable passee pour le type d'envoi de fichier. Par defaut 0 mais lors d'une evolution future 1,2...
     * 
     * @return <string>  retourne le twig utbClientBundle:Chargement:ajoutChargementFile.html.twig 
     *    Ce twig est organise autour des champs et zones comme suit :
     *       Champ type chargement : Journalier /Mensuel
     *       Champ type compte : AFBW - AFBW2 - UWEB
     *       Champ Date du : date debut periode couvert par le fichier a charger
     *       Champ fichier : Champ de type file  pour l'ajout du fichier a envoyer
     * 
     *       Et une zone de liste de fichiers à charger
     */
    public function showLodingInfosAction(): Response($page, $idfile, string $locale): Response {

        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        
        $currentID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('showLodingInfosAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        /* total des resultats */
        $total = $em->getRepository("utbClientBundle/CompteInexistant")->getTotalCompteInexistant($locale, 0);

        $articles_per_page = $this->container->get->getParameter('max_messages_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $cptes_inexistants = $this->entityManager
                ->getRepository("utbClientBundle:CompteInexistant")
                ->getCptesInexistants($locale, $idfile, $total, $page, $articles_per_page);

        $chargements_Stat = $this->entityManager
                ->getRepository("utbClientBundle:Chargement")
                ->getInfosSurChargement($idfile);


        return $this->render('utbClientBundle/Chargement/listeStatChargement.html.twig', array(
                    'locale' => $locale,
                    'cptesinexistants' => $cptes_inexistants,
                    'chargementsStat' => $chargements_Stat,
                    'last_page' => $last_page,
                    'previous_page' => $previous_page,
                    'current_page' => $page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'idfile' => $idfile
        ),$this->response);
    }

    /*
      public function loadFilesContentsAction(): Response($page,string $type,string $locale): Response{

      $em = $this->entityManager;
      $request = $this->requestStack->getCurrentRequest();

      $authManager = $this->Auth.Manager;//on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
      $currentID = $authManager->getCurrentId();
      $this->infoUtilisateur($em,$authManager,$currentID,'utilisateur',$locale);

      $listeFile = $this->entityManager
      ->getRepository("utbClientBundle/Chargement")
      ->getListeFiles( $type );

      $path = __DIR__.'/../../../../web/'."upload/chargement/";
      $result_trait = $this->entityManager
      ->getRepository("utbClientBundle/Chargement")
      ->loadMethod($listeFile, $path);




      $total = $em->getRepository("utbClientBundle:CompteInexistant")->getTotalCompteInexistant($locale,0);

      $articles_per_page = $this->container->get->getParameter('max_messages_on_listepage');
      $last_page         = ceil($total / $articles_per_page);
      $previous_page     = $page > 1 ? $page - 1 : 1;
      $next_page         = $page < $last_page ? $page + 1 : $last_page;

      $cptes_inexistants = $this->entityManager
      ->getRepository("utbClientBundle:CompteInexistant")
      ->getCptesInexistants($locale,0,$total,$page,$articles_per_page);

      return $this->render('utbClientBundle/Chargement/listeStatChargement.html.twig',array(
      'locale' => $locale,
      'cptesinexistants' => $cptes_inexistants,
      'stat'=>1,
      'last_page' => $last_page,
      'previous_page' => $previous_page,
      'current_page' => $page,
      'next_page' => $next_page,
      'total' => $total
      ));
      }
     */

    /**
     * 
     */
    public function deleteFileAction(): Response(string $locale): Response {

        $em = $this->entityManager;

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('deleteFileAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $idfile = $request->request->get('fileIds');

        $del = $this->entityManager
                ->getRepository("utbClientBundle:Chargement")
                ->deleteHistoryFile($idfile);

        if (($idfile != null) && ($idfile != 0) && ( $del == 0)) {
            $unchargement = $em->getRepository("utbClientBundle:Chargement")->find($idfile);

            if (file_exists(__DIR__ . '/../../../../web/' . "upload/chargement/" . $unchargement->getUrlFile())&& $unchargement->getUrlFile()!="") {
                $unchargement->removeUpload(__DIR__ . '/../../../../web/' . "upload/chargement/" . $unchargement->getUrlFile());
            }
            $em->remove($unchargement);
            $em->flush();
        }
        return new Response(json_encode(array("result" => "success")));
    }

    public function loadOneFileAction(): Response(string $locale): Response {

        $em = $this->entityManager;
        $result_trait = 0;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        
        //Debut verification si l'utilisateur peut acceder à cette action    
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('loadOneFileAction', $listeActions)) {
            //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            //return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
            return new Response(json_encode(array("result" => "accessdenied")));
        }
        $request = $this->requestStack->getCurrentRequest();
        $type = $request->request->get('idFile');
        $listeFile = $this->entityManager
                ->getRepository("utbClientBundle:Chargement")
                ->getListeFiles($type);

        if (($type != null) && ($type != 0)) {
            //$path = __DIR__ . '/../../../../web/' . "upload/chargement/";
            $path = $this->container->get->getParameter('cheminfichier');
            $result_trait = $this->entityManager
                    ->getRepository("utbClientBundle:Chargement")
                    ->loadMethod($listeFile, $path);
        }

        if ($type != 0) {
            $fichier = null;
            $fichier = $em->getRepository("utbClientBundle:Chargement")->find($type);
            if (($fichier != null) && ($result_trait == 0)) {
                $fichier->setStatut(2);
                $em->persist($fichier);
                $em->flush();
            }
        }

        if ($result_trait == 1) {
            return new Response(json_encode(array("result" => "errortrait")));
        } elseif ($result_trait == 0) {
            return new Response(json_encode(array("result" => "success")));
        }
    }

    public function archiveChargementAction(): Response(string $locale): Response {

        $em = $this->entityManager;

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        
        //Debut verification si l'utilisateur peut acceder à cette action    
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('archiveChargementAction', $listeActions)) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }
        $request = $this->requestStack->getCurrentRequest();

        $idFile = $request->request->get('idFile');

        $fichier = null;
        $fichier = $em->getRepository("utbClientBundle:Chargement")->find($idFile);

        if ($fichier != null) {
            $fichier->setArchive(1);
            $em->persist($fichier);
            $em->flush();
            return new Response(json_encode(array("result" => "success")));
        }
        else
            return new Response(json_encode(array("result" => "errortrait")));
    }

    // Archiver une sélection  
    public function supprimerAllArchiveChargementAction(): Response(string $locale): Response {

        $em = $this->entityManager;
        $resultat = 0;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        
        //Debut verification si l'utilisateur peut acceder à cette action    
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('archiveAllChargementAction', $listeActions)) {
            return new Response(json_encode(array("result" => "accessdenied")));
        }
        $request = $this->requestStack->getCurrentRequest();

        $idFiles = $request->request->get('idFile');
        $idFiles = explode("|", $idFiles);

        foreach ($idFiles as $idFile) {
            $fichier = null;
            $fichier = $em->getRepository("utbClientBundle:Chargement")->find($idFile);

            if ($fichier != null) {
                $fichier->setArchive(1);
                $em->persist($fichier);
                $em->flush();
            } else {
                $resultat = 1;
            }
        }

        if ($resultat == 0) {
            return new Response(json_encode(array("result" => "success")));
        } else {
            return new Response(json_encode(array("result" => "errortrait")));
        }
    }

    public function listeArchiveChargementAction(): Response(string $locale): Response {

        $em = $this->entityManager;
        $resultat = 0;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        
        //Debut verification si l'utilisateur peut acceder à cette action    
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('listeArchiveChargementAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();

        $listeChargement = null;
        $listeChargement = $em->getRepository("utbClientBundle/Chargement")->findBy(array('archive' => 1), array('id' => 'DESC'));

        $listeTypeCompte = $em->getRepository("utbClientBundle/TypeCompte")->findAll();

        return $this->render('utbClientBundle/Chargement/historiqueChargement.html.twig', array('locale' => $locale, 'listeChargement' => $listeChargement,
                    'listeTypeCompte' => $listeTypeCompte,
        ),$this->response);
    }

    public function chargeDansTmpAction(): Response(string $locale): Response {

        $em = $this->entityManager;
        $resulte = 0;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        
        //Debut verification si l'utilisateur peut acceder à cette action    
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('chargeDansTmpAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            //return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
            return new Response(json_encode(array("result" => "accessdenied")));
        }

        $request = $this->requestStack->getCurrentRequest();
        //$path = __DIR__ . '/../../../../web/' . "upload/chargement/";
        $path = $this->container->get->getParameter('cheminfichier');
        $fileidtype = $request->request->get('idFile');

        // récupération du fichier via le type de fichier
        // et mise à jour du statut à 1 (chargé)
        $lefichier = null;


        //recupère le tableau de fichiers à charger
        $idfile = $this->entityManager
                ->getRepository("utbClientBundle:Chargement")
                ->getListeFiles($fileidtype);
        
        
       

        // au cas ou le tableau est non nul et que le seul contenu du tableau est 
        // également non null je lance le chargement dans les tables tmp de chaque type
        if (( $idfile != null ) && ($idfile[0] != null)) {

            $lefichier = $em->getRepository("utbClientBundle:Chargement")->find($idfile[0]['id']);

            //return new Response( json_encode(array("0"=>$lefichier->getId(),"1"=>$lefichier->getNomFile(),"2"=>$lefichier->getUrlFile())));
            $resulte = $this->entityManager
                    ->getRepository("utbClientBundle:Chargement")
                    ->loadFile($idfile[0]['id'], $path);

            if (($lefichier != null) && ($resulte == 0)) {
                $lefichier->setStatut(1);
                $em->persist($lefichier);
                $em->flush();
            }
        }

        // suivant le résultat j'envoie une variable json 
        if ($resulte == 1) {
            return new Response(json_encode(array("result" => "errorload")));
        } elseif ($resulte == 0) {
            return new Response(json_encode(array("result" => "success")));
        }
    }

    /*
     * 
     */

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
    public function rapportChargementAction(): Response(string $locale,string $type): Response {
    //$currentConnete = $authManager->getFlash("utb_client_data");
        
        $em = $this->entityManager;
        //$resulte = 0;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        
        //Debut verification si l'utilisateur peut acceder à cette action    
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('chargeDansTmpAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            //return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
            return new Response(json_encode(array("result" => "accessdenied")));
        }
        
        
        $dateaujour = new \DateTime();
        $jour =$dateaujour->format("d");
        $annee = $dateaujour->format("Y");
        $heure = $dateaujour->format("H");
        $mois= $dateaujour->format("m");
        
        $listeMatin=$this->traiterChargementAction($jour,$mois,$annee,'am');
        $listeSoir=$this->traiterChargementAction($jour,$mois,$annee,'pm');
        
       /* if($heure>=12){
            $valtemps="am";
        }else{
            $valtemps="pm";
        }
        $lesoper=array();
        $i=0;
        if (!$fp = fopen(__DIR__ . "/../../../../traceload/".$annee."/".$mois."/".$jour."/"."load-".$annee."-".$mois."-".$jour."-".$type.".txt", "r")) {
            $lesoper=null;
        }else {

            while(!feof($fp)) {
                // On r�cup�re une ligne
                $Ligne = fgets($fp, 255);
                // On stocke l'ensemble des lignes dans une variable
                $lesoper[$i] = $Ligne;
                $i++;
            }
            fclose($fp); // On ferme le fichier
        }*/
        
        //var_dump($lesoper);exit;
        return $this->render('utbClientBundle/Chargement/rapportChargementFile.html.twig', array(
                    'locale' => $locale,'listeaction'=>$listeMatin,'listeactionSoir'=>$listeSoir,
        ),$this->response);
}

    public function traiterChargementAction(): Response($jour,$mois,$annee,string $type): Response {
    //$currentConnete = $authManager->getFlash("utb_client_data");
        
        $lesoper=array();
        $i=0;
         if(file_exists(__DIR__ . "/../../../../traceload/".$annee."/".$mois."/".$jour."/"."load-".$annee."-".$mois."-".$jour."-".$type.".txt")){
        if (!$fp = fopen(__DIR__ . "/../../../../traceload/".$annee."/".$mois."/".$jour."/"."load-".$annee."-".$mois."-".$jour."-".$type.".txt", "r")) {
            $lesoper=null;
        }else {

            while(!feof($fp)) {
                // On r�cup�re une ligne
                $Ligne = fgets($fp, 255);
                // On stocke l'ensemble des lignes dans une variable
                $lesoper[$i] = $Ligne;
                $i++;
            }
            fclose($fp); // On ferme le fichier
        }
         } 
        //var_dump($lesoper);exit;
        return $lesoper;
       
    }
    
    public function scriptChargementAction(): Response(string $locale): Response {
        
        //Exemple de script
        echo exec('/home/serveur/Domotique/ordres/e1-on.sh'); 
       
    }

    public function supprimerChargementAction(): Response(string $locale,$typeaction): Response {
         $this->requestStack->getCurrentRequest()->setLocale($locale);
            if( $typeaction =="a"){
                if (file_exists(__DIR__ . "/../../../../web/upload/chargement/uweba.txt")) {
                    
                    @unlink(__DIR__ . "/../../../../web/upload/chargement/uweba.txt");
                }
                
            }elseif( $typeaction =="s"){
                if (file_exists(__DIR__ . "/../../../../web/upload/chargement/uwebs.txt")) {
                    
                    @unlink(__DIR__ . "/../../../../web/upload/chargement/uwebs.txt");
                }
            }elseif($typeaction =="j"){
                if (file_exists(__DIR__ . "/../../../../web/upload/chargement/uwebj.txt")) {
                    
                    @unlink(__DIR__ . "/../../../../web/upload/chargement/uwebj.txt");
                }
            } elseif($typeaction =="afbw"){
                if (file_exists(__DIR__ . "/../../../../web/upload/chargement/afbw.txt")) {
                    
                    @unlink(__DIR__ . "/../../../../web/upload/chargement/afbw.txt");
                }
            } elseif($typeaction =="afbw2"){
                if (file_exists(__DIR__ . "/../../../../web/upload/chargement/afbw2.txt")) {
                    
                    @unlink(__DIR__ . "/../../../../web/upload/chargement/afbw2.txt");
                }
            } 
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successsuppfile');
            return $this->redirect($this->generateUrl('utb_client_savefile', [
                                'type' => 0, 'locale' => $locale]));
                //var_dump($commnade_j);exit;
                //exec($commnade_j);
    }   
            
    public function telechargementAction(): Response($nomFichier): Response {

        $folder = $this->folder;
        $finder = new Finder\Finder();
        $liste  = $finder->files()->in($folder)->name($nomFichier)->depth('<3'); 
        
        foreach ($finder as $file) {
           
            $chemin = $file->getRealPath();   

            if (file_exists($chemin)) {
                $response = new Response();
                $response->setContent(file_get_contents($chemin ));
                $response->headers->set('Content-Type', 'application/force-download'); // modification du content-type pour forcer le téléchargement (sinon le navigateur internet essaie d'afficher le document)
                $response->headers->set('Content-disposition', 'filename=' . $file->getFilename() );  
                return $response;
            } 
            
        }          
    }          
            
    public function lookForLogsAction(): Response(string $locale, $username, $deb , $fin, $post): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $folder = $this->folder;
        
        $listefichier = null;
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        /*if (!in_array('lookForLogsAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }*/
        
        $abonne_courant =$em->getRepository("utbClientBundle:Abonne")->find($currentID);
        
        if ($abonne_courant instanceof Abonne) {
           $radical = $abonne_courant->getRadicalAbonne();
           $listeSousAbonne = $em->getRepository("utbClientBundle:Abonne")->findBy(array("radicalAbonne" =>$radical,"idAbonneParent"=>$abonne_courant->getId() )); 
        } else $listeSousAbonne = null;
        
        $request = $request;
        
        
        if ($request->getMethod()=='POST'){            
            
            $username = strtolower($request->request->get('sousabonne'));
            $this->requestStack->getCurrentRequest()->attributes->set('username', $username);

            $deb = strtolower($request->request->get('deb'));           
            $this->requestStack->getCurrentRequest()->attributes->set('deb', $deb);

            $fin = strtolower($request->request->get('fin'));            
            $this->requestStack->getCurrentRequest()->attributes->set('fin', $fin); 
            
            //var_dump($username,$deb,$fin);
                       
        }
        
        $page =1;
        
        $listefile = null; 
        
        $pattern = '';  $d = null;  $f = null;
        
        
        if ( trim($username)=='' || trim($username)=='0' ) {
            $pattern .= '*log-*'.'.txt';
        }else{
            $pattern .= '*log-'.$username.'*.txt';
        }       
        
        if (file_exists($folder)) {

            $finder = new Finder\Finder();
            $liste  = $finder->files()->in($folder)->name($pattern)->depth('<3'); 
           
            foreach ($finder as $file) {
                
                $nomfichier = $file->getFilename();
                //var_dump($nomfichier);
                $nf = explode('-',$nomfichier);
                // 
                $datep = $nf[2]; $d = null; $f = null;
                $datep = str_replace('.txt', '', $datep);
            
                if ( trim($deb)!='' && trim($deb)!='0' ) {
                    $d = new \Datetime($deb); 
                }else{
                    $d = new \Datetime('2010-01-01'); 
                }

                if ( trim($fin)!='' && trim($fin)!='0' ) {            
                    $f = new \Datetime($fin);           
                }else{
                    $f = new \Datetime('2099-12-31'); 
                } 
                                                              
                if ( $d instanceof \DateTime && $f instanceof \DateTime  ) {
                    if ($datep >= $d->format('Ymd') &&  $datep <= $f->format('Ymd')) {
                       $listefichier[] = array('filename'=>$nomfichier, 'subabonne'=>$nf[1]); 
                    }                    
                }else{
                    $listefichier[] = array('filename'=>$nomfichier, 'subabonne'=>$nf[1]);
                }
                

            }

        } else $listefichier = null;
               
        return $this->render('utbClientBundle/Recherche/rechercheLog.html.twig', array('page' => $page,'username' => $username,'listefile' => $listefichier,
                    'locale' => $locale, 'deb' => $deb, 'fin' => $fin, 'listesousab' => $listeSousAbonne,'post' => $post,
        ), $this->response);
        
    }        
            

}

