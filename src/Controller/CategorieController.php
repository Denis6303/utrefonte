<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\CategorieCompte;
use App\Entity\CategorieCompteType;
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
 * CategorieController pour la gestion des categories de message 
 * 
 * 
 * 
 * Cette methode permet de verifier le droit de l'utilisateur avant d'effectuer une action dans une methode
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
class CategorieController extends AbstractController
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
     *  Methode qui s'occupe de l'ajout d'une Categorie de message
     *  
     * @var
     * 
     * $unecategorie : Un categorie de la classe Categorie
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'une Categorie (ajoutCategorie.html.twig)
     * 
     */
    public function ajoutCategorieAction(): Response(string $locale): Response {
        
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);


        $unecategorie = new CategorieCompte();
        //$unecategorie->setTranslatableLocale($locale);
        $form = $this->createForm($this->createForm(CategorieCompteType::class), $unecategorie);

        $request = $request;


        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unecategorie = $form->getData();
            $unecategorie->setSicarte(0);
            $unecategorie->setSicheque(0);
            if ($unecategorie->getLibCategorie() == "") {

                return $this->render('utbClientBundle/Categorie/ajoutCategorie.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale, 
                ),$this->response);
            }
            $em->persist($unecategorie);

            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('utb_client_listecategorie', ['locale' => $locale]));
        }

        return $this->render('utbClientBundle/Categorie/ajoutCategorie.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 
        ),$this->response);
    }

    /**
     *  Methode qui liste les Categories de message
     * 
     * $listeCategorie : Un categorie de la classe Categorie
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listeCategorieAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);     

        $listeCategorie = $em->getRepository("utbClientBundle:CategorieCompte")->getAllCateg($locale,1,20);

        return $this->render('utbClientBundle/Categorie/listeCategorie.html.twig', array('listeCategorie' => $listeCategorie, 'locale' => $locale),$this->response);
    }

    /**
     *  Methode qui soccupe de la suppression des categories
     * 
     * $unecategorie : Un categorie de la classe Categorie
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *   
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id Identifiant de l'categorie
     * 
     * @return <string> return le twig (listeCategorie.html.twig)
     * 
     */
    
    public function supprCategorieAction(): Response(int $id, string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unecategorie = $em->getRepository("utbClientBundle:CategorieCompte")->find($id);




            $em->remove($unecategorie);
            $em->flush($unecategorie);
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Categorie supprimé avec succès');
            return $this->redirect($this->generateUrl('utb_admin_listeCategorie', array(
                                'locale' => $locale,)));        /* ... et on redirige vers la page d'administration des categorie */

    }

    /**
     *  Methode qui s''occupe de la suppression des categories de message
     * 
     * 
     * $categorieIds : Identifiant de l'categorie
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $user : Identifiant de l'utilisateur
     * 
     * $etat : etat de l'categorie
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de l'categorie
     * @param <integer> $etat  etat =0 (desactive) | etat =1 (active)
     * 
     * @return <string> return le twig (listeCategorie.html.twig)
     * 
     */
    function gererAllCategorieAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
/*        $checkAcces = $AccessControl->verifAcces($em, 'gererAllCategorieAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }
*/
        $request = $this->requestStack->getCurrentRequest();
        $categorieIds = $request->request->get('categorieIds');
        $etat = $request->request->get('etat');
        $categorieIds = explode("|", $categorieIds);
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($categorieIds as $key => $value) {
            if (!empty($value)) {
                $unecategorie = $em->getRepository("utbClientBundle:CategorieCompte")->find($value);
                //Désactivation
                $unecategorie->setEtatCategorie($etat);
                $em->persist($unecategorie);
                $em->flush();
            }
        }
        //$em->flush();   
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui s'occupe de la modification d'une categorie de message
     * 
     * $unecategorie : Un categorie de la classe Categorie
     *  
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de l'categorie
     * 
     * @return <string> return le twig (modifCategorie.html.twig)
     * 
     */
    public function modifierCategorieAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        
        $this->requestStack->getCurrentRequest()->setLocale($locale); 
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité categorie 
        $unecategorie = $em->getRepository("utbClientBundle/CategorieCompte")->find($id);
        $form = $this->createForm($this->createForm(CategorieCompteType::class), $unecategorie);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();


        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
            /* Si le formulaire est valide, on valide et on redirige vers la liste des categories */
            $em->persist($unecategorie);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Categorie modifié avec succès');

            return $this->redirect($this->generateUrl("utb_client_listecategorie"));
        }
        return $this->render('utbClientBundle/Categorie/modifCategorie.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, ),$this->response);
    }

    /**
     *  Methode qui s'occupe de la suppression d'une categorie de message
     * 
     *  Methode utilisant Ajax
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de l'categorie
     * 
     * @return <string> return le twig (modifCategorie.html.twig)
     * 
     */
    function corbeilleCategorieAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'corbeilleCategorieAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }
        $request = $this->requestStack->getCurrentRequest();
        $categorieIds = $request->request->get('categorieIds');
        $categorieIds = explode("|", $categorieIds);
        foreach ($categorieIds as $key => $value) {
            if (!empty($value)) {
                $unecategorie = $em->getRepository("utbClientBundle:CategorieCompte")->find($value);
                $em->remove($unecategorie);
                $em->flush();
            }
        }
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


}