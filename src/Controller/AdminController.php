<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Profil;
use App\Entity\ProfilType;
use App\Entity\RubriqueType;
use App\Entity\Module;
use App\Entity\ModuleType;
use App\Entity\Media;
use App\Entity\MediaRubriqueType;
use App\Entity\Dimension;
use App\Entity\DimensionType;
use App\Entity\Controleur;
use App\Entity\ControleurType;
use App\Entity\Action;
use App\Entity\ActionType;
use App\Entity\GroupeMenu;
use App\Entity\Menu;
use App\Entity\Parametrage;
use App\Entity\Squelettepage;
use App\Entity\NatureDoc;
use App\Entity\NatureDocType;
use App\Entity\Message;
use App\Entity\MessageReponse;
use App\Entity\Internaute;
use App\Entity\Objet;
use App\Entity\Service;
use App\Entity\InternauteType;
use App\Entity\MessageReponseType;
use App\Entity\Droit;
use App\Entity\Ordre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {}

    #[Route('/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(): Response
    {
        $em = $this->entityManager;
        
        $recentArticles = $em->getRepository('App\Entity\Article')
            ->findAllByLocaleRecent($this->requestStack->getCurrentRequest()->getLocale());

        $pendingArticles = $em->getRepository('App\Entity\Article')
            ->findAllByLocaleAttente5($this->requestStack->getCurrentRequest()->getLocale());

        $submittedArticles = $em->getRepository('App\Entity\Article')
            ->findAllByLocaleType($this->requestStack->getCurrentRequest()->getLocale(), 2, 5, 0);

        $welcomeText = $em->getRepository('App\Entity\Parametrage')
            ->getTitreDescription($this->requestStack->getCurrentRequest()->getLocale(), 0);

        $users = $em->getRepository('App\Entity\User')->findAll();
        $rubriques = $em->getRepository('App\Entity\Rubrique')
            ->getListeDeRubriques($this->requestStack->getCurrentRequest()->getLocale());

        return $this->render('admin/dashboard.html.twig', [
            'recentArticles' => $recentArticles,
            'users' => $users,
            'pendingArticles' => $pendingArticles,
            'welcomeText' => $welcomeText,
            'submittedArticles' => $submittedArticles,
            'rubriques' => $rubriques,
        ]);
    }

    #[Route('/profile/add', name: 'admin_profile_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function addProfile(Request $request): Response
    {
        $profile = new Profil();
        $defaultRights = serialize([]);
        $rights = new Droit();
        $rights->setProfil($profile);
        $rights->setDroits($defaultRights);

        $form = $this->createForm(ProfilType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile = $form->getData();
            
            $existingProfile = $this->entityManager
                ->getRepository("App\Entity\Profil")
                ->getSiProfilExiste(0, $this->requestStack->getCurrentRequest()->getLocale(), $profile->getLibProfil());

            if ($existingProfile != 0) {
                $this->addFlash('error', $this->translator->trans('profile.already_exists'));
                return $this->redirectToRoute('admin_profile_list');
            }

            $this->entityManager->persist($profile);
            $this->entityManager->persist($rights);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('profile.created_success'));
            return $this->redirectToRoute('admin_profile_list');
        }

        return $this->render('admin/profile/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/list', name: 'admin_profile_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function listProfiles(): Response
    {
        $profiles = $this->entityManager
            ->getRepository('App\Entity\Profil')
            ->findAll();

        return $this->render('admin/profile/list.html.twig', [
            'profiles' => $profiles,
        ]);
    }
} 