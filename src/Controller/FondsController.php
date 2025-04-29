<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Fonds;
use App\Entity\FondsType;
use App\Entity\DossierType;
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
 * FondsController pour la gestion des Fondss
 * 
 * Cette methode permet de verifier le droit de l'utilisateur avant d'effectuer une action dans une methode
 * 
 * 
 * $em = $this->entityManager;
 * $AccessControl = $this->utb_admin.AccessControl;
 * $checkAcces = $AccessControl->verifAcces($em, 'ajoutprofilAction', $this->container->get);
 * 
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class FondsController extends AbstractController
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
     *  Methode qui s'occupe de l'ajout d'un  Fonds
     * 
     * $unFonds : Un objet de la classe Fonds
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un Fonds (ajoutFonds.html.twig)
     * 
     */
    public function ajoutFondsAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('ajoutFondsAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        $unFonds = new Fonds();

        $unFonds->setEtatFonds(0);
        //$unFonds->setNatureFonds(1);
        $form = $this->createForm($this->createForm(FondsType::class), $unFonds);

        $request = $request;
        /* $listestat = $this->entityManager
          ->getRepository('utbClientBundle/Statistique')
          ->getInfoOrStat($typeStat = 4, $locale , $valeur = 0, $article = null); */
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unFonds = $form->getData();
            if ($unFonds->getLibFonds() == "") {
                return $this->render('utbClientBundle/Fonds/ajoutFonds.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale,
                ), $this->response);
            }
            $em->persist($unFonds);
            $em->flush();
            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);
            return $this->redirect($this->generateUrl('utb_client_listefonds', ['locale' => $locale,]));
        }
        return $this->render('utbClientBundle/Fonds/ajoutFonds.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale,
        ), $this->response);
    }

    /**
     *  Methode qui liste les Fondss
     * 
     * $listeFonds : Un objet de la classe Fonds
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listeFondsAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('listeFondsAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        //$listeFonds = $em->getRepository("utbClientBundle/Fonds")->findAll();
        $listeFonds = $em->getRepository("utbClientBundle/Fonds")->findFonds();



        return $this->render('utbClientBundle/Fonds/listeFonds.html.twig', array('listeFonds' => $listeFonds, 'locale' => $locale), $this->response);
    }

    /**
     *  Methode qui liste les Fonds d'un gestionnaire
     * 
     * $listeFonds : Un objet de la classe Fonds
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listeFondsGestAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('listeFondsGestAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeFonds = $em->getRepository("utbClientBundle:Fonds")->findFondsGestionnaire($currentUtilID);



        return $this->render('utbClientBundle/Fonds/listeFondsGest.html.twig', array('listeFonds' => $listeFonds, 'locale' => $locale), $this->response);
    }

    /**
     *  Methode qui soccupe de la suppression des Fondss
     * 
     * $listeFonds : Un objet de la classe Fonds
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *   
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id Identifiant du Fonds
     * 
     * @return <string> return le twig (listeFonds.html.twig)
     * 
     */
    public function supprFondsAction(): Response(int $id, string $locale, string $type): Response {

        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        $em = $this->entityManager;
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unFonds = $em->getRepository("utbClientBundle:Fonds")->find($id);

        /* $listestat = $this->entityManager
          ->getRepository('utbClientBundle:Statistique')
          ->getInfoOrStat($typeStat = 4, $locale , $valeur = 0, $article = null); */

        $nbremessage = $this->entityManager
                ->getRepository('utbClientBundle:Message')
                ->getNombreMessageFonds($id, $locale);

        if ($nbremessage == 0) {
            //$em->remove($unFonds);
            $unFonds->setSuppr(1);
            $em->flush($unFonds);
            if ($type == 1) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprdossiersuc');
                return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale]));
            } else {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprFondssuc');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Fonds supprimé avec succès');
                return $this->redirect($this->generateUrl('utb_admin_listeFonds', array(
                                    'locale' => $locale, 'listestat' => $listestat,)));
                /* ... et on redirige vers la page d'administration des categorie */
            }
        } else {

            if ($type == 1) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprdossierimp');
                return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale]));
            } else {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'suppr.Fonds.impSuppression du Fonds impossible, le Fonds contient a des message');
                return $this->redirect($this->generateUrl('utb_admin_listeFonds', array(
                                    'locale' => $locale, 'listestat' => $listestat,)));        /* ... et on redirige vers la page d'administration des categorie */
            }
        }
    }

    /**
     *  Methode qui soccupe de la suppression des Fondss
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du Fonds
     * @param <integer> $etat  etat =0 (desactive) | etat =1 (active)
     * 
     * @return <string> return le twig (listeFonds.html.twig)
     * 
     */
    public function gererEtatFondsAction(): Response(int $id, int $etat = 0, string $locale): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('gererEtatFondsAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listestat = $this->entityManager
                ->getRepository('utbClientBundle:Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        // Récupération du Fonds 
        $unFonds = $em->getRepository("utbClientBundle:Fonds")->find($id);
        $unFonds->setEtatFonds($etat);

        $em->persist($unFonds);

        $em->flush();

        if ($etat = 0) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Fonds désactivé avec succès');
        } else {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Fonds activé avec succès');
        }

        return $this->redirect($this->generateUrl('utb_client_listeFonds', [
                            'locale' => $locale, 'listestat' => $listestat,]));
    }

    function gererAllFondsAction(): Response {

        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $request = $this->requestStack->getCurrentRequest();
        $etat = $request->request->get('etatfonds');

        $em = $this->entityManager;
        $FondsIds = $request->request->get('idfonds');

        $FondsIds = explode("|", $FondsIds);

        //$user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($FondsIds as $key => $value) {
            if (!empty($value)) {
                //return new Response(json_encode($etat));
                $unFonds = $em->getRepository("utbClientBundle:Fonds")->find($value);
                //Activation|Désactivation
                $unFonds->setEtatFonds($etat);
                $em->persist($unFonds);
                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success"))); //$em->flush();   
    }

    /**
     *  Methode qui s'occupe de la modification d'un Fonds
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du Fonds
     * 
     * @return <string> return le twig (modifFonds.html.twig)
     * 
     */
    public function modifierFondsAction(): Response(int $id, string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('modifierFondsAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        // Récupération du Fonds 
        $unFonds = $em->getRepository("utbClientBundle:Fonds")->find($id);
        //$user = $this->security->getToken()->getUser()->getId();


        $form = $this->createForm($this->createForm(FondsType::class), $unFonds);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();
        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
            /* Si le formulaire est valide, on valide et on redirige vers la liste des Fondss */
            $em->persist($unFonds);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Fonds modifié avec succès');

            return $this->redirect($this->generateUrl("utb_client_listefonds"));
        }
        return $this->render('utbClientBundle/Fonds/modifFonds.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale,), $this->response);
    }

    /**
     *  Methode qui s'occupe de la suppression d'un Fonds
     * 
     *  Methode utilisant Ajax
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du Fonds
     * 
     * @return <string> return le twig (listeObjet.html.twig)
     * 
     */
    function corbeilleFondsAction(): Response {

        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $FondsIds = $request->request->get('FondsIds');
        $FondsIds = explode("|", $FondsIds);

        foreach ($FondsIds as $key => $value) {

            if (!empty($value)) {
                $unFonds = $em->getRepository("utbClientBundle:Fonds")->find($value);
                $unUtil = $em->getRepository("utbClientBundle:Utilisateur")->find($unFonds->getUtilisateur()->getId());
                /*if ($unUtil != null)
                    return new Response(json_encode(array("result" => "erreurstatut")));                
                $em->remove($unFonds);*/
                $unFonds->setSuppr(1);
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