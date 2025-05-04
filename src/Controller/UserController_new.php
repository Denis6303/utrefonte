<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\ModifPwdType;
use App\Entity\PhotoType;
use App\Entity\ModifFicheUserType;
use App\Entity\ModUserType;
use App\Entity\Parametrage;
use App\Entity\Type\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

#[Route('/{_locale}/user')]
class UserController extends AbstractController
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'png', 'jpeg', 'gif'];
    private const MIN_USERNAME_LENGTH = 5;
    private const MIN_PASSWORD_LENGTH = 4;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/create', name: 'user_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->validateUser($user, $form);

            if ($form->isValid()) {
                $this->processUserCreation($user);
                return $this->redirectToRoute('user_list');
            }
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('user.updated_success'));
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/photo', name: 'user_photo')]
    #[IsGranted('ROLE_ADMIN')]
    public function photo(Request $request, User $user): Response
    {
        $form = $this->createForm(PhotoType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processPhotoUpload($user);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('user.photo_updated'));
            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/photo.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/status/{status}', name: 'user_status')]
    #[IsGranted('ROLE_ADMIN')]
    public function status(User $user, int $status): Response
    {
        $user->setEnabled($status);
        $this->entityManager->flush();

        $this->addFlash('success', $this->translator->trans('user.status_updated'));
        return $this->redirectToRoute('user_list');
    }

    #[Route('/', name: 'user_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function list(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}', name: 'user_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    private function validateUser(User $user, FormInterface $form): void
    {
        $validator = Validation::createValidator();

        // Validate username length
        if (strlen($user->getUsername()) < self::MIN_USERNAME_LENGTH) {
            $form->addError(new FormError($this->translator->trans('user.username_too_short')));
        }

        // Validate email format
        $emailViolations = $validator->validate($user->getEmail(), new Email());
        if (count($emailViolations) > 0) {
            $form->addError(new FormError($this->translator->trans('user.invalid_email')));
        }

        // Validate password
        $password = $form->get('password')->getData();
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $form->addError(new FormError($this->translator->trans('user.password_too_short')));
        }

        // Check if username exists
        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => $user->getUsername()]);
        if ($existingUser) {
            $form->addError(new FormError($this->translator->trans('user.username_exists')));
        }

        // Check if email exists
        $existingEmail = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $user->getEmail()]);
        if ($existingEmail) {
            $form->addError(new FormError($this->translator->trans('user.email_exists')));
        }
    }

    private function processUserCreation(User $user): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->getPassword())
        );

        if (!$user->getUrlPhoto()) {
            $user->setUrlPhoto('default_photo_' . uniqid() . '.png');
        }

        $user->setEnabled(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', $this->translator->trans('user.created_success'));
    }

    private function processPhotoUpload(User $user): void
    {
        if ($user->getPhoto()) {
            $extension = $user->getPhoto()->guessExtension();
            if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
                throw new \InvalidArgumentException($this->translator->trans('user.invalid_photo_type'));
            }

            $fileName = 'user_' . $user->getId() . '_' . uniqid() . '.' . $extension;
            $user->getPhoto()->move(
                $this->getParameter('user_photos_directory'),
                $fileName
            );
            $user->setUrlPhoto($fileName);
        }
    }
} 