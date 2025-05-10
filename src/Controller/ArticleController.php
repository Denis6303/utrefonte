<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use App\Entity\Media;
use App\Entity\Dimension;
use App\Entity\Rubrique;
use App\Entity\Recherche;
use App\Entity\Cadre;
use App\Entity\CadreType;
use App\Entity\MediaCadreType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;
use App\Form\ArticleType;

/**
 * ArticleController pour la gestion des articles du site.
 * Pour rappel tout contenu sur le site est un article que ce soit une brève,
 * une actualité, une agence etc.
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 */
class ArticleController extends AbstractController
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

    #[Route('/article/ajouter/{locale}/{type}', name: 'app_article_ajouter')]
    public function ajouter(Request $request, string $locale, string $type): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash('success', 'article.ajout_success');
            return $this->redirectToRoute('app_article_liste', ['locale' => $locale, 'type' => $type]);
        }

        return $this->render('article/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
            'type' => $type
        ]);
    }

    #[Route('/article/liste/{locale}/{type}', name: 'app_article_liste')]
    public function liste(string $locale, string $type): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $articles = $this->entityManager->getRepository(Article::class)->findBy(['type' => $type]);

        return $this->render('article/liste.html.twig', [
            'articles' => $articles,
            'locale' => $locale,
            'type' => $type
        ]);
    }

    #[Route('/article/modifier/{id}/{locale}/{type}', name: 'app_article_modifier')]
    public function modifier(Request $request, int $id, string $locale, string $type): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $article = $this->entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException('article.not_found');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'article.modif_success');
            return $this->redirectToRoute('app_article_liste', ['locale' => $locale, 'type' => $type]);
        }

        return $this->render('article/modifier.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'locale' => $locale,
            'type' => $type
        ]);
    }

    #[Route('/article/supprimer/{id}/{locale}/{type}', name: 'app_article_supprimer')]
    public function supprimer(int $id, string $locale, string $type): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $article = $this->entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException('article.not_found');
        }

        $article->setSuppr(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'article.suppr_success');
        return $this->redirectToRoute('app_article_liste', ['locale' => $locale, 'type' => $type]);
    }
}
