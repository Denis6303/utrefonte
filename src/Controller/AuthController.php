<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Abonne;
use App\Entity\Utilisateur;
use App\Entity\HistoriqueConnexion;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private AccessControl $accessControl;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private UserPasswordHasherInterface $passwordHasher;
    private AuthenticationUtils $authenticationUtils;

    public function __construct(
        EntityManagerInterface $entityManager,
        AccessControl $accessControl,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UserPasswordHasherInterface $passwordHasher,
        AuthenticationUtils $authenticationUtils
    ) {
        $this->entityManager = $entityManager;
        $this->accessControl = $accessControl;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->passwordHasher = $passwordHasher;
        $this->authenticationUtils = $authenticationUtils;
    }

    #[Route('/login/{locale}', name: 'app_login')]
    public function login(string $locale): Response
    {
        if ($this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_accueil', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()->getSession()->set('_locale', $locale);

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'locale' => $locale,
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/login/process/{locale}', name: 'app_login_process', methods: ['POST'])]
    public function loginProcess(Request $request, string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()->getSession()->set('_locale', $locale);

        $login = $request->request->get('login');
        $password = $request->request->get('passwd');

        if (empty($login) || empty($password)) {
            $this->addFlash('error', $this->translator->trans('auth.fields_required'));
            return $this->redirectToRoute('app_login', ['locale' => $locale]);
        }

        // Tentative de connexion comme abonné
        $abonne = $this->entityManager->getRepository(Abonne::class)->findOneBy(['username' => $login]);
        if ($abonne) {
            if ($abonne->getEtatAbonne() === 2) {
                $this->addFlash('error', $this->translator->trans('auth.account_blocked'));
                return $this->redirectToRoute('app_login', ['locale' => $locale]);
            }

            if ($abonne->getPassword() === md5($password)) {
                $abonne->setAttempt(0);
                $this->entityManager->flush();

                $this->requestStack->getCurrentRequest()->getSession()->set('user', $abonne);
                $this->requestStack->getCurrentRequest()->getSession()->set('user_type', 'abonne');

                return $this->redirectToRoute('app_accueil', ['locale' => $locale]);
            }

            $attempt = $abonne->getAttempt() + 1;
            $abonne->setAttempt($attempt);

            if ($attempt >= 3) {
                $abonne->setEtatAbonne(2);
                $this->addFlash('error', $this->translator->trans('auth.account_blocked_attempts'));
            } else {
                $this->addFlash('error', $this->translator->trans('auth.invalid_credentials'));
            }

            $this->entityManager->flush();
            return $this->redirectToRoute('app_login', ['locale' => $locale]);
        }

        // Tentative de connexion comme utilisateur
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['username' => $login]);
        if ($utilisateur) {
            if ($utilisateur->getEtatUtilisateur() === 2) {
                $this->addFlash('error', $this->translator->trans('auth.account_blocked'));
                return $this->redirectToRoute('app_login', ['locale' => $locale]);
            }

            $profil = $utilisateur->getProfil();
            if (!$profil || $profil->getEtatProfil() === 0 || $profil->getSuppr() === true) {
                $this->addFlash('error', $this->translator->trans('auth.profile_inactive'));
                return $this->redirectToRoute('app_login', ['locale' => $locale]);
            }

            if ($this->passwordHasher->isPasswordValid($utilisateur, $password)) {
                $utilisateur->setAttempt(0);
                $this->entityManager->flush();

                $this->requestStack->getCurrentRequest()->getSession()->set('user', $utilisateur);
                $this->requestStack->getCurrentRequest()->getSession()->set('user_type', 'utilisateur');

                return $this->redirectToRoute('app_accueil', ['locale' => $locale]);
            }

            $attempt = $utilisateur->getAttempt() + 1;
            $utilisateur->setAttempt($attempt);

            if ($attempt >= 3) {
                $utilisateur->setEtatUtilisateur(2);
                $this->addFlash('error', $this->translator->trans('auth.account_blocked_attempts'));
            } else {
                $this->addFlash('error', $this->translator->trans('auth.invalid_credentials'));
            }

            $this->entityManager->flush();
            return $this->redirectToRoute('app_login', ['locale' => $locale]);
        }

        $this->addFlash('error', $this->translator->trans('auth.invalid_credentials'));
        return $this->redirectToRoute('app_login', ['locale' => $locale]);
    }

    #[Route('/logout/{locale}', name: 'app_logout')]
    public function logout(string $locale): Response
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->remove('user');
        $session->remove('user_type');
        $session->invalidate();

        return $this->redirectToRoute('app_login', ['locale' => $locale]);
    }
}
