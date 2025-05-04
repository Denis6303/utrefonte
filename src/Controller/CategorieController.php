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
use App\Service\AccessControl;

/**
 * CategorieController pour la gestion des categories de message
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 */
class CategorieController extends AbstractController
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

        // Configuration des en-têtes de cache
        $response = new Response();
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('max-age', 0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);
    }

    public function ajoutCategorieAction(string $locale): Response
    {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unecategorie = new CategorieCompte();
        $form = $this->createForm(CategorieCompteType::class, $unecategorie);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unecategorie = $form->getData();
            $unecategorie->setSicarte(0);
            $unecategorie->setSicheque(0);

            if ($unecategorie->getLibCategorie() == "") {
                return $this->render('utbClientBundle/Categorie/ajoutCategorie.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale,
                ]);
            }

            $em->persist($unecategorie);
            $em->flush();

            $msgnotification = $this->translator->trans('notification.ajout');
            $this->addFlash('notice', $msgnotification);

            return $this->redirect($this->generateUrl('utb_client_listecategorie', ['locale' => $locale]));
        }

        return $this->render('utbClientBundle/Categorie/ajoutCategorie.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
        ]);
    }

    public function listeCategorieAction(string $locale): Response
    {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);     

        $listeCategorie = $em->getRepository("utbClientBundle:CategorieCompte")->getAllCateg($locale, 1, 20);

        return $this->render('utbClientBundle/Categorie/listeCategorie.html.twig', [
            'listeCategorie' => $listeCategorie,
            'locale' => $locale,
        ]);
    }

    public function supprCategorieAction(int $id, string $locale): Response
    {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unecategorie = $em->getRepository("utbClientBundle:CategorieCompte")->find($id);
        if (!$unecategorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $em->remove($unecategorie);
        $em->flush();

        $msgnotification = $this->translator->trans('notification.suppression');
        $this->addFlash('notice', $msgnotification);

        return $this->redirect($this->generateUrl('utb_client_listecategorie', ['locale' => $locale]));
    }

    public function gererAllCategorieAction(): Response
    {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request->getLocale();

        $categorieIds = $request->request->get('categorieIds');
        $etat = $request->request->get('etat');

        if (!empty($categorieIds)) {
            $categorieIds = explode('|', $categorieIds);
            foreach ($categorieIds as $id) {
                if (!empty($id)) {
                    $unecategorie = $em->getRepository("utbClientBundle:CategorieCompte")->find($id);
                    if ($unecategorie) {
                        $unecategorie->setEtatCategorie($etat);
                        $em->persist($unecategorie);
                    }
                }
            }
            $em->flush();
        }

        return $this->redirect($this->generateUrl('utb_client_listecategorie', ['locale' => $locale]));
    }

    public function modifierCategorieAction(int $id, string $locale): Response
    {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unecategorie = $em->getRepository("utbClientBundle:CategorieCompte")->find($id);
        if (!$unecategorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $form = $this->createForm(CategorieCompteType::class, $unecategorie);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unecategorie = $form->getData();
            $em->persist($unecategorie);
            $em->flush();

            $msgnotification = $this->translator->trans('notification.modification');
            $this->addFlash('notice', $msgnotification);

            return $this->redirect($this->generateUrl('utb_client_listecategorie', ['locale' => $locale]));
        }

        return $this->render('utbClientBundle/Categorie/modifierCategorie.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
            'unecategorie' => $unecategorie,
        ]);
    }

    public function corbeilleCategorieAction(): Response
    {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request->getLocale();

        $listeCategorie = $em->getRepository("utbClientBundle:CategorieCompte")->findBy(['etatCategorie' => 0]);

        return $this->render('utbClientBundle/Categorie/corbeille.html.twig', [
            'listeCategorie' => $listeCategorie,
            'locale' => $locale,
        ]);
    }

    private function infoUtilisateur(EntityManagerInterface $em, $authManager, array $currentConnete, string $user, string $locale): void
    {
        if ($user == 'utilisateur') {
            $utilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($currentConnete["id_abonne"]);
            if (!$utilisateur) {
                $authManager->logout();
                throw $this->createNotFoundException('Utilisateur non trouvé');
            }
        } else {
            $abonne = $em->getRepository("utbClientBundle:Abonne")->find($currentConnete["id_abonne"]);
            if (!$abonne) {
                $authManager->logout();
                throw $this->createNotFoundException('Abonné non trouvé');
            }
        }
    }
}