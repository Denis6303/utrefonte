<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\StatistiqueType;
use App\Entity\Statistique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

class StatistiqueController extends AbstractController
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
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * Methode qui s'occupe de l'ajout d'une statistique
     * 
     * @param string $locale La locale
     * @return Response Le template ajoutStat.html.twig
     */
    #[Route(
        path: '/admin/statistique/ajout/{locale}',
        name: 'app_statistique_ajout',
        requirements: [
            'locale' => '[a-z]{2}'
        ]
    )]
    public function ajoutStatistique(Request $request, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutStatistique', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unelignestat = new Statistique();
        $unelignestat->setTranslatableLocale($locale);
        $form = $this->createForm(StatistiqueType::class, $unelignestat);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unelignestat = $form->getData();
            $em->persist($unelignestat);
            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', $msgnotification);

            return $this->redirect($this->generateUrl('app_admin_listestat', ['locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Statistique/ajoutStat.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    /**
     * Methode qui liste les statistiques
     * 
     * @param string $locale La locale
     * @return Response Le template listeStat.html.twig
     */
    #[Route(
        path: '/admin/statistique/liste/{locale}',
        name: 'app_statistique_liste',
        requirements: [
            'locale' => '[a-z]{2}'
        ]
    )]
    public function listeStatistique(Request $request, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'listeStatistique', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $listestatistique = $this->entityManager
                ->getRepository("App\Entity\Statistique")
                ->getAllStatByLocale($locale);

        $lesstatistiques = $this->entityManager
                ->getRepository("App\Entity\Statistique")
                ->AllStatistique(1, $locale);

        return $this->render('utbAdminBundle/Statistique/listeStat.html.twig', [
            'listestatistique' => $listestatistique,
            'locale' => $locale,
            'lesstatistiques' => $lesstatistiques
        ]);
    }

    /**
     * Methode qui s'occupe de la suppression d'une statistique
     * 
     * @param int $id L'identifiant de la statistique à supprimer
     * @param string $locale La locale
     * @return Response Une redirection vers la liste des statistiques
     */
    #[Route(
        path: '/admin/statistique/supprimer/{id}/{locale}',
        name: 'app_statistique_supprimer',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function supprStatistique(Request $request, int $id, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'supprStatistique', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $lastatistique = $em->getRepository("App\Entity\Statistique")->find($id);

        $em->remove($lastatistique);
        $em->flush();

        return $this->redirect($this->generateUrl('app_admin_listestat', [
            'locale' => $locale
        ]));
    }

    /**
     * Methode qui s'occupe de la gestion de l'état d'une statistique
     * 
     * @param int $id L'identifiant de la statistique
     * @param int $etat Le nouvel état de la statistique
     * @param string $locale La locale
     * @return Response Une redirection vers la liste des statistiques
     */
    #[Route(
        path: '/admin/statistique/gerer-etat/{id}/{etat}/{locale}',
        name: 'app_statistique_gerer_etat',
        requirements: [
            'id' => '\d+',
            'etat' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function gererEtatStatistique(Request $request, int $id, int $etat, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'gererEtatStatistique', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unestat = $em->getRepository("App\Entity\Statistique")->find($id);
        $unestat->setEtatProfil($etat);

        $em->persist($unestat);
        $em->flush();

        if ($etat == 0) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Statistique désactivée avec succès');
        } else {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Statistique activée avec succès');
        }

        return $this->redirect($this->generateUrl('app_admin_listestat', [
            'locale' => $locale
        ]));
    }

    /**
     * Methode qui s'occupe de la modification d'une statistique
     * 
     * @param int $id L'identifiant de la statistique
     * @param string $locale La locale
     * @return Response La vue de modification ou une redirection
     */
    #[Route(
        path: '/admin/statistique/modifier/{id}/{locale}',
        name: 'app_statistique_modifier',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function modifierStatistique(Request $request, int $id, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'modifierStatistique', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unestat = $em->getRepository("App\Entity\Statistique")->find($id);
        
        if (!$unestat) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Statistique non trouvée');
            return $this->redirect($this->generateUrl('app_admin_listestat', ['locale' => $locale]));
        }

        $form = $this->createForm(StatistiqueType::class, $unestat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($unestat);
            $em->flush();

            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Statistique modifiée avec succès');
            return $this->redirect($this->generateUrl('app_admin_listestat', ['locale' => $locale]));
        }

        return $this->render('admin/statistique/modifier.html.twig', [
            'form' => $form->createView(),
            'statistique' => $unestat,
            'locale' => $locale
        ]);
    }

    /**
     * Methode qui s'occupe de l'ajout d'une traduction pour une statistique
     * 
     * @param int $id L'identifiant de la statistique
     * @param string $locale La locale
     * @return Response La vue d'ajout de langue ou une redirection
     */
    #[Route(
        path: '/admin/statistique/ajout-langue/{id}/{locale}',
        name: 'app_statistique_ajout_langue',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function ajoutLangueStatistique(Request $request, int $id, string $locale): Response
    {
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutLangueStatistique', $this->container->get);

        if (!$checkAcces) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('app_admin_accueil', ['locale' => $locale]));
        }
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unestat = $em->getRepository("App\Entity\Statistique")->find($id);
        
        if (!$unestat) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', 'Statistique non trouvée');
            return $this->redirect($this->generateUrl('app_admin_listestat', ['locale' => $locale]));
        }

        $form = $this->createForm(StatistiqueType::class, $unestat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unestat->setTranslatableLocale($locale);
            $em->persist($unestat);
            $em->flush();

            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'Traduction ajoutée avec succès');
            return $this->redirect($this->generateUrl('app_admin_listestat', ['locale' => $locale]));
        }

        return $this->render('admin/statistique/ajoutLangue.html.twig', [
            'form' => $form->createView(),
            'statistique' => $unestat,
            'locale' => $locale
        ]);
    }

}