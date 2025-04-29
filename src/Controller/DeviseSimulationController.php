<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Devise;
use App\Entity\DeviseType;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;


/**
 * DeviseSimulationController 
 * 
 * Le controleur qui gere les operations lie au devise sur le site 
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

class DeviseSimulationController extends AbstractController
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
     * Methode permettant d'ajouter une devise à l'espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $uneDevise: Instance de la classe Devise
     * 
     * $deviseExist : Pour avoir la devise locale
     * 
     * $controledevise : Pour voir si le code de la devise est dans la boite de reception
     * 
     * $lexistencedevice : compte le nombre de resultat de la requette $deviseExist pour l'affichage du champ Devise locale sur le site (quand une devise locale existe plus besoin de l'ajouter encore)
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutDevise.html.twig
     *  
     */
    public function ajoutDeviseAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $uneDevise = new Devise();
        $form = $this->createForm($this->createForm(DeviseType::class), $uneDevise);
        $request = $request;
        
        $deviseExist =$em
                ->getRepository('utbClientBundle:Devise')
                ->getTestDeviseLocale(1);
        $lexistencedevice=count($deviseExist);
        
        
        if ($request->isMethod('POST')) {
            //$typeDevise = $request->request->get('typeDevise');
            //var_dump($typeDevise);exit;
            $form->handleRequest($request);
            $uneDevise = $form->getData();
            $uneDevise->setAffiche(1);
            
            $controledevise = $em->getRepository("utbClientBundle:Devise")->findBy(array('codeDevise'=>$uneDevise->getCodeDevise()));
            
            if(count($controledevise)!=0){
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'erruerdeviseexist');

                    return $this->redirect($this->generateUrl('utb_client_listeDevise', ['locale' => $locale,]));                
            }
            
            if ($uneDevise->getUrlIcone() == "") {
                $uneDevise->setUrlIcone('default_icone.png');
            }        
            
            $em->persist($uneDevise);
            
            $em->flush();

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'successdevise');

            return $this->redirect($this->generateUrl('utb_client_listeDevise', ['locale' => $locale,]));
        }

        return $this->render('utbClientBundle/DeviseSimulation/ajoutDevise.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale,'lexistencedevice'=>$lexistencedevice,
        ), $this->response);
    }
    
    /**
     * Methode permettant de modifier une devise à l'espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unedevise: Instance de la classe Devise
     * 
     * $deviseExist : Pour avoir la devise locale
     * 
     * $lexistencedevice : compte le nombre de resultat de la requette $deviseExist pour l'affichage du champ Devise locale sur le site (quand une devise locale existe plus besoin de l'ajouter encore)
     * 
     * @param $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param $id Identifiant de la devise dont on veut modifier
     * 
     * @return <string> return sur le twig modifDevise.html.twig
     *  
     */
    
    public function modifierDeviseAction(): Response(int $id, string $locale): Response {

        $em = $this->entityManager;  
        // Récupération du devise 
        $undevise = $em->getRepository("utbClientBundle/Devise")->find($id);
        //var_dump($undevise);exit;
        $deviseExist =$em
                ->getRepository('utbClientBundle:Devise')
                ->getTestDeviseLocale(1);
        $lexistencedevice=count($deviseExist); 

        if($lexistencedevice !=0) {$idlocale =1 ;}else{$idlocale=0;}
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité devise 
        $form = $this->createForm($this->createForm(DeviseType::class), $undevise);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {
            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
			$undevise = $form->getData();			
            
            //var_dump($undevise->getLibDevise());exit;
            /* Si le formulaire est valide, on valide et on redirige vers la liste des devises */
            //if ($form->isValid()) {
                $em->persist($undevise);
                $em->flush();
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'modifsuccess');

                return $this->redirect($this->generateUrl("utb_client_listeDevise"));
            //}
        }
        return $this->render('utbClientBundle/DeviseSimulation/modifDevise.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale,'lexistencedevice'=>$lexistencedevice,'idlocale'=>$idlocale), $this->response);
    }

    
    /**
     * Methode permettant de lister les devises dans l'espace client
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listeDevise: Permet d'afficher la liste de tous les devises enregistrees
     * 
     * $deviseExist : Pour avoir la devise locale
     * 
     * $autreDevise : Permet d'afficher la liste des devises autres que la devise locale
     * 
     * @param $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @param $id Identifiant de la devise dont on veut modifier
     * 
     * @return <string> return sur le twig modifDevise.html.twig
     *  
     */    
    
    public function listeDeviseAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager;

        // definition de la locale
        $this->requestStack->getCurrentRequest()->setLocale($locale);

         $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

         if ( !in_array('listeDeviseAction', $listeActions) ){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } 

        $listeDevise = $this->entityManager
                ->getRepository('utbClientBundle/Devise')
                ->findAll();  
        $autreDevise =$em
                ->getRepository('utbClientBundle:Devise')
                ->getTestDeviseLocale(0);
       //var_dump($autreDevise);exit;

        return $this->render('utbClientBundle/DeviseSimulation/listeDevise.html.twig', array(
                    'locale' => $locale,'listeDevise' => $listeDevise,'autreDevise'=>$autreDevise,
        ), $this->response);
    }

    
    /**
     * Methode permettant de gerer  l'activation et la desactivation des devises
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unedevise: Instance de la classe Devise
     * 
     * $deviseIds : Recupere les id des devises dont on veut changer la statut
     * 
     * $etat : Valeur de l'etat qu'on veut changer
     * 
     * @return <string> return un Response(json_encode(array("result" => "success"))) si les operations sont bien passees
     *  
     */    
    function gererAllDeviseAction(): Response {

        $em = $this->entityManager;        
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');
        $request = $this->requestStack->getCurrentRequest();
        $deviseIds = $request->request->get('iddevise');

        $etat = $request->request->get('etatdevise');

        $deviseIds = explode("|", $deviseIds);
        //$utilisateur = $this->security->getToken()->getUtilisateur()->getId();
        //boucle sur les ids articles
        foreach ($deviseIds as $key => $value) {
            if (!empty($value)) {
                $unedevise = $em->getRepository("utbClientBundle:Devise")->find($value);
                //Désactivation  
                $unedevise->setAffiche($etat);
                $em->persist($unedevise);
                $em->flush();
            }
        }
        //$em->flush();   
        return new Response(json_encode(array("result" => "success")));
    }
    
    
    /**
     * Methode permettant de definir une devise comme "devise locale"
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unedevise: Instance de la classe Devise
     * 
     * $deviseIds : Recupere les id des devises dont on veut changer la statut
     * 
     * $etat : Valeur de l'etat qu'on veut changer
     * 
     * @return <string> return un Response(json_encode(array("result" => "success"))) si les operations sont bien passees
     *  
     */    
    function definirLocaledeviseAction(): Response {

        $em = $this->entityManager;        
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');
        $request = $this->requestStack->getCurrentRequest();
        $deviseIds = $request->request->get('iddevise');

        $etat = $request->request->get('etatdevise');

        $deviseIds = explode("|", $deviseIds);
        //$utilisateur = $this->security->getToken()->getUtilisateur()->getId();
        $lesdevise = $em->getRepository("utbClientBundle:Devise")->findAll();
        foreach ($lesdevise as $ledevise) {
            $ledevise->setSiLocale(0);
            $em->persist($ledevise);
        }
        $em->flush();        
        //boucle sur les ids articles
        foreach ($deviseIds as $key => $value) {
            if (!empty($value)) {
                $unedevise = $em->getRepository("utbClientBundle:Devise")->find($value);
                //Désactivation  
                $unedevise->setSiLocale(1);
                $em->persist($unedevise);
                $em->flush();
            }
        }
        //$em->flush();   
        return new Response(json_encode(array("result" => "success")));
    }

    
    /**
     * Methode permettant de gerer  l'activation et la desactivation des devises
     * 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unedevise: Instance de la classe Devise
     * 
     * $deviseIds : Recupere les id des devises dont on veut supprimer
     * 
     * $etat : Valeur de l'etat qu'on veut changer
     * 
     * @return <string> return un Response(json_encode(array("result" => "success"))) si les operations sont bien passees
     *  
     */ 
    
    function supprAlldevisesAction(): Response {
        

        $em = $this->entityManager;

        $request = $this->requestStack->getCurrentRequest();
        $devisesIds = $request->request->get('ds');
        $devisesIds = explode("|", $devisesIds);

        foreach ($devisesIds as $key => $value) {

            if (!empty($value)) {

                $unedevise = $em->getRepository("utbClientBundle:Devise")->find($value);
                 $em->remove($unedevise);
                }
            }        

        $em->flush();
        return new Response(json_encode(array("result" => "success")));
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

