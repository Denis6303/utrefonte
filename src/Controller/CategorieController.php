<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\CategorieCompte;
use App\Form\CategorieCompteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
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
    }

    #[Route('/categorie/ajouter/{locale}', name: 'app_categorie_ajouter')]
    public function ajouter(Request $request, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $categorie = new CategorieCompte();
        $form = $this->createForm(CategorieCompteType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setSicarte(0);
            $categorie->setSicheque(0);

            $this->entityManager->persist($categorie);
            $this->entityManager->flush();

            $this->addFlash('success', 'categorie.ajout_success');
            return $this->redirectToRoute('app_categorie_liste', ['locale' => $locale]);
        }

        return $this->render('categorie/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route('/categorie/liste/{locale}', name: 'app_categorie_liste')]
    public function liste(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $categories = $this->entityManager->getRepository(CategorieCompte::class)->findAll();

        return $this->render('categorie/liste.html.twig', [
            'categories' => $categories,
            'locale' => $locale
        ]);
    }

    #[Route('/categorie/modifier/{id}/{locale}', name: 'app_categorie_modifier')]
    public function modifier(Request $request, int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $categorie = $this->entityManager->getRepository(CategorieCompte::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('categorie.not_found');
        }

        $form = $this->createForm(CategorieCompteType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'categorie.modif_success');
            return $this->redirectToRoute('app_categorie_liste', ['locale' => $locale]);
        }

        return $this->render('categorie/modifier.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
            'locale' => $locale
        ]);
    }

    #[Route('/categorie/supprimer/{id}/{locale}', name: 'app_categorie_supprimer')]
    public function supprimer(int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $categorie = $this->entityManager->getRepository(CategorieCompte::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('categorie.not_found');
        }

        $this->entityManager->remove($categorie);
        $this->entityManager->flush();

        $this->addFlash('success', 'categorie.suppr_success');
        return $this->redirectToRoute('app_categorie_liste', ['locale' => $locale]);
    }
}
