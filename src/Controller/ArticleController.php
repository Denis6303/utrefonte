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
use App\Service\ArticleService;
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
    private ArticleService $articleService;

    public function __construct(
        EntityManagerInterface $entityManager,
        AccessControl $accessControl,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        ArticleService $articleService
    ) {
        $this->entityManager = $entityManager;
        $this->accessControl = $accessControl;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->articleService = $articleService;
    }

    protected function addFlash(string $type, mixed $message): void
    {
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add($type, $message);
    }

    private function getSession()
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }

    /**
     * Methode qui s'occupe de l'ajout d'un article 
     * 
     * Les Formulaires varient suivant  la rubrique 
     * 
     * type = 2 -- Pour le Formulaire d'ajout d'actualite(ajoutArticleArtualite.html.twig)
     * 
     * type = 3 -- Pour le Formulaire d'ajout Presentation(ajoutArticleRubrique.html.twig)
     * 
     * @param string $locale Variable passee pour gerer le multilingue sur le site
     * @param string $type   Identifiant de la rubrique  dans laquelle se trouve l'article
     * 
     * @return Response le twig d'ajout de formulaire d'un article suivant la rubrique (ajoutArticle.html.twig)
     */
    #[Route('/article/ajout/{locale}/{type}', name: 'ajout_article')]
    public function ajoutArticleAction(string $locale, string $type): Response
    {
        // Code pour gerer la gestion des droits
        $em = $this->entityManager;
        $checkAcces = $this->accessControl->verifAcces($em, 'ajoutArticleAction');

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirectToRoute('utb_admin_accueil', ['locale' => $locale]);
        }

        $id = $type;
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm(ArticleType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $verifSaisie = $this->articleService->verifSaisie($form->get('titreArticle')->getData(), array('/', '%'));

            if ((trim($form->get('descriptionArticle')->getData()) == '' && $type != 4) || (!$verifSaisie)) {
                if (trim($form->get('descriptionArticle')->getData()) == '' && $type != 4) {
                    $this->addFlash('notice', 'errorajtartdescvide');
                }

                if (!$verifSaisie) {
                    $this->addFlash('notice', 'errorajtartcarfaux');
                }

                if ($type == 2) {
                    return $this->render('utbAdminBundle/Article/ajoutArticleActualite.html.twig', array(
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'type' => $type
                    ));
                }
            }

            // ... rest of the method implementation ...
        }

        // ... rest of the method implementation ...

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