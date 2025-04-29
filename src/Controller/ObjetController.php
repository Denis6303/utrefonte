<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Objet;
use App\Entity\ObjetType;
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
 * ObjetController pour la gestion des objets de message 
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
class ObjetController extends AbstractController
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

    public function __construct() {
        //$this->Utils =  $this->utb_admin.utils;
    }

    /**
     *  Methode qui s'occupe de l'ajout d'une Objet de message
     *  
     * @var
     * 
     * $uneobjet : Un objet de la classe Objet
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'une Objet (ajoutObjet.html.twig)
     * 
     */
    public function ajoutObjetAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutObjetAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //        
        $this->requestStack->getCurrentRequest()->setLocale($locale);


        $uneobjet = new Objet();
        $uneobjet->setTranslatableLocale($locale);
        $user = $this->security->getToken()->getUser()->getId();

        //Donnée de base et non nulles à renseigner
        $uneobjet->setObjetAjoutPar($user);
        $uneobjet->setEtatObjet(0);
        //$uneobjet->setNatureObjet(1);
        $uneobjet->setObjetDateAjout(new \DateTime);
        $form = $this->createForm($this->createForm(ObjetType::class), $uneobjet);

        $request = $request;

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale,  0, null);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $uneobjet = $form->getData();
            if ($uneobjet->getLibObjet() == "") {

                return $this->render('utbAdminBundle/Objet/ajoutObjet.html.twig', array(
                            'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
                ));
            }
            $em->persist($uneobjet);


            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('utb_admin_listeobjet', ['locale' => $locale, 'listestat' => $listestat,]));
        }

        return $this->render('utbAdminBundle/Objet/ajoutObjet.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
        ));
    }

    /**
     *  Methode qui liste les Objets de message
     * 
     * $listeobjet : Un objet de la classe Objet
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listeobjetAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeobjetAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale, 'listestat' => $listestat,]));
        }
        //         
        //$repertoire = $this-> getDoctrine()->getManager()->getRepository();       
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale,  0, null);

        $listeobjet = $em->getRepository("admin/Objet")->findAllObjet($locale);

        return $this->render('utbAdminBundle/Objet/listeObjet.html.twig', array('listeobjet' => $listeobjet, 'locale' => $locale, 'listestat' => $listestat,));
    }

    
/**
     * Methode permettant d'ajouter un objet dans une autre langue(une traduction) - Backoffice
     * 
     * Abandonnée par la suite
     * 
     * @deprecated since version 1.0
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listestat/ Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $uneobjet: Instance de la classe Objet a modifier
     * 
     * @param <integer> $id     Identifiant  du objet
     * @param <string>  $locale, Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig ajoutLangueObjet.html.twig
     *  
     */
    public function ajoutLangueObjetAction(): Response(string $locale, int $id): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutLangueObjetAction', $this->container->get);

        /*if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }*/
        //         
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $uneobjet = $em->getRepository("admin/Objet")->find($id);
        $uneobjet->setTranslatableLocale($locale);
        $em->refresh($uneobjet);
        // Change la locale  
        $form = $this->createForm($this->createForm(ObjetType::class), $uneobjet);
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale,  0, null);
        $request = $request;

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $em->persist($uneobjet);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Objet ajouté avec succès');

            return $this->redirect($this->generateUrl('utb_admin_listeobjet', ['locale' => $locale,
            ]));
        }

        return $this->render('utbAdminBundle/Objet/ajoutLangueObjet.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id, 'listestat' => $listestat,
        ));
    }
    
    
    /**
     *  Methode qui soccupe de la suppression des objets
     * 
     * $uneobjet / Un objet de la classe Objet
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *   
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id Identifiant de l'objet
     * 
     * @return <string> return le twig (listeObjet.html.twig)
     * 
     */
    public function supprobjetAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprobjetAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $uneobjet = $em->getRepository("utbAdminBundle/Objet")->find($id);

        /* Enfin on supprime le categorie ... */
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        /* ... et on redirige vers la page d'administration des objets */
        $unUser = $this->entityManager
                ->getRepository('App\Entity\User')
                ->findObjet($id);
        if ($unUser == null) {
            /* Enfin on supprime le categorie ... */
            $em->remove($uneobjet);
            $em->flush($uneobjet);
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Objet supprimé avec succès');
            return $this->redirect($this->generateUrl('utb_admin_listeobjet', array(
                                'locale' => $locale, 'listestat' => $listestat,)));        /* ... et on redirige vers la page d'administration des categorie */
        } else {
            $listeobjet = $this->entityManager
                    ->getRepository("utbAdminBundle/Objet")
                    ->findAllByLocale($locale);

            return $this->render('utbAdminBundle/Objet/listeObjet.html.twig', array('listeobjet' => $listeobjet, 'locale' => $locale, 'listestat' => $listestat,));
        }
    }

    /**
     *  Methode qui s''occupe de la suppression des objets de message
     * 
     * 
     * $objetIds : Identifiant de l'objet
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * $user : Identifiant de l'utilisateur
     * 
     * $etat : etat de l'objet
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de l'objet
     * @param <integer> $etat  etat =0 (desactive) | etat =1 (active)
     * 
     * @return <string> return le twig (listeObjet.html.twig)
     * 
     */
    function gererAllObjetAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
/*        $checkAcces = $AccessControl->verifAcces($em, 'gererAllObjetAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }
*/
        $request = $this->requestStack->getCurrentRequest();
        $objetIds = $request->request->get('objetIds');
        $etat = $request->request->get('etat');
        $objetIds = explode("|", $objetIds);
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($objetIds as $key => $value) {
            if (!empty($value)) {
                $uneobjet = $em->getRepository("admin/Objet")->find($value);
                //Désactivation
                $uneobjet->setEtatObjet($etat);
                $em->persist($uneobjet);
                $em->flush();
            }
        }
        //$em->flush();   
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui s'occupe de la modification d'une objet de message
     * 
     * $uneobjet / Un objet de la classe Objet
     *  
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de l'objet
     * 
     * @return <string> return le twig (modifObjet.html.twig)
     * 
     */
    public function modifierObjetAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierObjetAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        $user = $this->security->getToken()->getUser()->getId();
        // Récupération du objet 
        $uneobjet = $em->getRepository("utbAdminBundle/Objet")->find($id);
        $uneobjet->setObjetModifPar($user);
        //$uneobjet->setNatureObjet(1);
        $uneobjet->setObjetDateModif(new \DateTime);
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité objet 
        $form = $this->createForm($this->createForm(ObjetType::class), $uneobjet);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale,  0,null);

        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
            /* Si le formulaire est valide, on valide et on redirige vers la liste des objets */
            $em->persist($uneobjet);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Objet modifié avec succès');

            return $this->redirect($this->generateUrl("utb_admin_listeobjet"));
        }
        return $this->render('utbAdminBundle/Objet/modifObjet.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     *  Methode qui s'occupe de la suppression d'une objet de message
     * 
     *  Methode utilisant Ajax
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de l'objet
     * 
     * @return <string> return le twig (modifObjet.html.twig)
     * 
     */
    function corbeilleObjetAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'corbeilleObjetAction', $this->container->get);

        if (!$checkAcces) {
            return new Response(json_encode(array("result" => "erreurstatut")));
        }
        $request = $this->requestStack->getCurrentRequest();
        $objetIds = $request->request->get('objetIds');
        $objetIds = explode("|", $objetIds);
        foreach ($objetIds as $key => $value) {
            if (!empty($value)) {
                $uneobjet = $em->getRepository("utbAdminBundle:Objet")->find($value);
                $em->remove($uneobjet);
                $em->flush();
            }
        }
        return new Response(json_encode(array("result" => "success")));
    }

}