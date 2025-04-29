<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\StatistiqueType;
use App\Entity\Statistique;

class StatistiqueController extends AbstractController
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
        
    }

    public function ajoutStatistiqueAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutStatistiqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        $this->requestStack->getCurrentRequest()->setLocale($locale);


        $unelignestat = new Statistique();
        $unelignestat->setTranslatableLocale($locale);
        $form = $this->createForm($this->createForm(StatistiqueType::class), $unelignestat);

        $request = $request;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unelignestat = $form->getData();
            $em->persist($unelignestat);
            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('utb_admin_listestat', ['locale' => $locale,]));
        }

        return $this->render('utbAdminBundle/Statistique/ajoutStat.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale,
        ));
    }

    public function listeStatistiqueAction(): Response(string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeStatistiqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $listestatistique = $this->entityManager
                ->getRepository("App\Entity\Statistique")
                ->getAllStatByLocale($locale);


        $lesstatistiques = $this->entityManager
                ->getRepository("App\Entity\Statistique")
                ->AllStatistique(1, $locale);

        // var_dump($lesstatistiques);

        return $this->render('utbAdminBundle/Statistique/listeStat.html.twig', array('listestatistique' => $listestatistique, 'locale' => $locale, 'lesstatistiques' => $lesstatistiques,));
    }

    public function supprStatistiqueAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprStatistiqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //         
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $lastatistique = $em->getRepository("App\Entity\Statistique")->find($id);

        $em->remove($lastatistique);
        $em->flush();


        return $this->redirect($this->generateUrl('utb_admin_listeprofil', array(
                            'locale' => $locale,)));
    }

    public function gererEtatStatistiqueAction(): Response(int $id, int $etat, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'gererEtatStatistiqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }
        //  
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        // Récupération du profil 
        $unestat = $em->getRepository("App\Entity\Statistique")->find($id);
        $unestat->setEtatProfil($etat);

        $em->persist($unestat);

        $em->flush();

        if ($etat = 0) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Statistique désactivé avec succès');
        } else {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'Statistique activé avec succès');
        }

        return $this->redirect($this->generateUrl('utb_admin_listestat', [
                            'locale' => $locale,]));
    }

    public function modifierStatistiqueAction(): Response(int $id, string $locale): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierStatistiqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        // Récupération de la statistique
        $lastatistique = $em->getRepository("App\Entity\Statistique")->find($id);

        // Création d'un forumaire pour lequel on spécifie qu'il doit correspondre avec une entité statistique 
        $form = $this->createForm($this->createForm(StatistiqueType::class), $lastatistique);

        // On récupère les données du formulaire si il a déjà été passé 
        $request = $this->requestStack->getCurrentRequest();

        // On traite les données passées en méthode POST 
        if ($request->getMethod() == 'POST') {

            // On applique les données récupérées au formulaire */
            $form->handleRequest($request);


            /* Si le formulaire est valide, on valide et on redirige vers la liste des profils */
            if ($form->isValid()) {
                $em->persist($lastatistique);
                $em->flush();

                return $this->redirect($this->generateUrl("utb_admin_listestat"));
            }
        }
        return $this->render('utbAdminBundle/Statistique/modifStat.html.twig', array(
                    'form' => $form->createView(), 'id' => $id, 'locale' => $locale,));
    }

    public function ajoutLangueStatistiqueAction(): Response(string $locale, int $id): Response {
        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutLangueStatistiqueAction', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }


        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $unestat = $em->getRepository("App\Entity\Statistique")->find($id);
        $unestat->setTranslatableLocale($locale);

        $em->refresh($unestat);

        // Change la locale  
        $form = $this->createForm($this->createForm(StatistiqueType::class), $unestat);

        $request = $request;

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $em->persist($unestat);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_admin_listestat', ['locale' => $locale,
            ]));
        }

        return $this->render('utbAdminBundle/Statistique/ajoutLangueStat.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'id' => $id,
        ));
    }

}