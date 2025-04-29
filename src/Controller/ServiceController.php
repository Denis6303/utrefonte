<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Service;
use App\Entity\ServiceType;
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
 * ServiceController pour la gestion des services
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
class ServiceController extends AbstractController
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
     *  Methode qui s'occupe de l'ajout d'un  Service
     * 
     * $unservice : Un objet de la classe Service
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un Service (ajoutService.html.twig)
     * 
     */
    public function ajoutServiceAction(): Response(string $locale, string $type): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutServiceAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //        
        $this->requestStack->getCurrentRequest()->setLocale($locale);


        $unservice = new Service();
        //$unservice->setTranslatableLocale($locale);
        $user = $this->security->getToken()->getUser()->getId();

        //Donnée de base et non nulles à renseigner
        $unservice->setServiceAjoutPar($user);
        $unservice->setEtatService(0);
        //$unservice->setNatureService(1);
        $unservice->setServiceDateAjout(new \DateTime);

        if ($type == 1) {
            $form = $this->createForm($this->createForm(DossierType::class), $unservice);
            $unservice->setEmailService("info@utb.tg");
            $unservice->setDescriptionService("Nouveau dossier");
            $unservice->setTypeService(1);
        } else {
            $unservice->setTypeService(0);
            $form = $this->createForm($this->createForm(ServiceType::class), $unservice);
        }

        $request = $request;
        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat(4, $locale, 0,null);


        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unservice = $form->getData();
            if ($unservice->getLibService() == "") {


                if ($type == 1) {
                    return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale, 'listestat' => $listestat,]));
                } else {
                    return $this->render('utbAdminBundle/Service/ajoutService.html.twig', array(
                                'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
                    ));
                }
            }
            $em->persist($unservice);


            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');

            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);
            if ($type == 1) {
                return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale,]));
            } else {
                return $this->redirect($this->generateUrl('utb_admin_listeservice', ['locale' => $locale,]));
            }
        }

        if ($type == 1) {
            return $this->render('utbAdminBundle/Service/ajoutDossier.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
            ));
        } else {
            return $this->render('utbAdminBundle/Service/ajoutService.html.twig', array(
                        'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
            ));
        }
    }

    /**
     *  Methode qui liste les Services
     * 
     * $listeservice : Un objet de la classe Service
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listeserviceAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeserviceAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        //$repertoire = $this-> getDoctrine()->getManager()->getRepository();       
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listeservice = $em->getRepository("admin/Service")->findAll();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        return $this->render('utbAdminBundle/Service/listeService.html.twig', array('listeservice' => $listeservice, 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     *  Methode qui liste les Services
     * 
     * $listeservice / Un objet de la classe Service
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function deplacerDossierAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeserviceAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        //$repertoire = $this-> getDoctrine()->getManager()->getRepository();       
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $request = $this->requestStack->getCurrentRequest();
        $idservice = $request->request->get('idservice');
        $idmessage = $request->request->get('idmessage');
        $message = $em->getRepository("admin/Message")->find($idmessage);
        $service = $em->getRepository("utbAdminBundle/Service")->find($idservice);
        $message->setService($service);

        $em->persist($message);
        $em->flush();

        return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale,]));
    }

    /**
     *  Methode qui soccupe de la suppression des services
     * 
     * $listeservice : Un objet de la classe Service
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *   
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id Identifiant du Service
     * 
     * @return <string> return le twig (listeService.html.twig)
     * 
     */
    public function supprserviceAction(): Response(int $id, string $locale, string $type): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprserviceAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unservice = $em->getRepository("utbAdminBundle:Service")->find($id);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        /* $unUser = $this->entityManager
          ->getRepository('App\Entity\User')
          ->findService($id);

          if($unUser == null){ */

        /// test pour voir si le service / dossier à supprimer contient des messages
        $nbremessage = $this->entityManager
                ->getRepository('utbAdminBundle/Message')
                ->getNombreMessageService($id, $locale);

        if ($nbremessage == 0) {
            $em->remove($unservice);
            $em->flush($unservice);
            if ($type == 1) {

                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprdossiersuc');
                return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale]));
            } else {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprservicesuc');
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Service supprimé avec succès');
                return $this->redirect($this->generateUrl('utb_admin_listeservice', array(
                                    'locale' => $locale, 'listestat' => $listestat,)));
                /* ... et on redirige vers la page d'administration des categorie */
            }
            /*  }else{
              $listeservice = $this->entityManager
              ->getRepository("utbAdminBundle/Service")
              ->findAllByLocale($locale);
              return $this->render('utbAdminBundle/Service/listeService.html.twig', array('listeservice' => $listeservice,'locale' =>$locale,'listestat'=>$listestat, ));
              } */
        } else {

            if ($type == 1) {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'supprdossierimp');
                return $this->redirect($this->generateUrl('utb_admin_listemessage', ['locale' => $locale]));
            } else {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'suppr.service.impSuppression du service impossible, le service contient a des message');
                return $this->redirect($this->generateUrl('utb_admin_listeservice', array(
                                    'locale' => $locale, 'listestat' => $listestat,)));        /* ... et on redirige vers la page d'administration des categorie */
            }
        }
    }

    /**
     *  Methode qui soccupe de la suppression des services
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du service
     * @param <integer> $etat  etat =0 (desactive) | etat =1 (active)
     * 
     * @return <string> return le twig (listeService.html.twig)
     * 
     */
    public function gererEtatServiceAction(): Response(int $id, int $etat = 0, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'gererEtatServiceAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        // Récupération du service 
        $unservice = $em->getRepository("admin/Service")->find($id);
        $unservice->setEtatService($etat);

        $em->persist($unservice);

        $em->flush();

        if ($etat = 0) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Service désactivé avec succès');
        } else {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Service activé avec succès');
        }

        return $this->redirect($this->generateUrl('utb_admin_listeservice', [
                            'locale' => $locale, 'listestat' => $listestat,]));
    }

    function gererAllServiceAction(): Response {

        $request = $this->requestStack->getCurrentRequest();
        $etat = $request->request->get('etat');

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;

        if ($etat == 0) {
            $checkAcces = $AccessControl->verifAcces($em, 'desactiverServiceAction', $this->container->get);
        } elseif ($etat == 1) {
            $checkAcces = $AccessControl->verifAcces($em, 'activerServiceAction', $this->container->get);
        }
        if (!$checkAcces) {
            //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            //return $this->redirect ( $this->generateUrl('utb_admin_accueil', ['locale' =>$locale]));
            return new Response(json_encode(array("result" => "accesdenied")));
        }

        $serviceIds = $request->request->get('serviceIds');

        $serviceIds = explode("|", $serviceIds);
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les ids articles
        foreach ($serviceIds as $key => $value) {
            if (!empty($value)) {
                $unservice = $em->getRepository("utbAdminBundle/Service")->find($value);
                //Désactivation
                $unservice->setEtatService($etat);
                $em->persist($unservice);
                $em->flush();
            }
        }
        //$em->flush();   
        return new Response(json_encode(array("result" => "success")));
    }

    /**
     *  Methode qui s'occupe de la modification d'un service
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du service
     * 
     * @return <string> return le twig (modifService.html.twig)
     * 
     */
    public function modifierServiceAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierServiceAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        // Récupération du service 
        $unservice = $em->getRepository("utbAdminBundle:Service")->find($id);
        $user = $this->security->getToken()->getUser()->getId();

        $unservice->setServiceModifPar($user);
        //$unservice->setNatureService(1);
        $unservice->setServiceDateModif(new \DateTime);
        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité service 
        $form = $this->createForm($this->createForm(ServiceType::class), $unservice);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        $listestat = $this->entityManager
                ->getRepository('App\Entity\Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);
            /* Si le formulaire est valide, on valide et on redirige vers la liste des services */
            $em->persist($unservice);
            $em->flush();
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Service modifié avec succès');

            return $this->redirect($this->generateUrl("utb_admin_listeservice"));
        }
        return $this->render('utbAdminBundle/Service/modifService.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale, 'listestat' => $listestat,));
    }

    /**
     *  Methode qui s'occupe de la suppression d'un service
     * 
     *  Methode utilisant Ajax
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du service
     * 
     * @return <string> return le twig (listeObjet.html.twig)
     * 
     */
    function corbeilleServiceAction(): Response {

        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'corbeilleServiceAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $serviceIds = $request->request->get('serviceIds');
        $serviceIds = explode("|", $serviceIds);
        foreach ($serviceIds as $key => $value) {
            if (!empty($value)) {
                $unservice = $em->getRepository("utbAdminBundle:Service")->find($value);
                $em->remove($unservice);
                $em->flush();
            }
        }

        return new Response(json_encode(array("result" => "success")));
    }

}