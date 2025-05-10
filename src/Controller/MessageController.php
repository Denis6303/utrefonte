<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Message;
use App\Entity\MessageType;
use App\Entity\MessageReponse;
use App\Entity\MessageReponseType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;
use Symfony\Component\HttpFoundation\Response as ResponseInterface;

class MessageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->accessControl = $accessControl;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * @Route("/message", name="message_index")
     */
    public function index(): Response
    {
        $messages = $this->entityManager->getRepository(Message::class)->findAll();
        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/message/new", name="message_new")
     */
    public function new(Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/message/{id}", name="message_show")
     */
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/message/{id}/edit", name="message_edit")
     */
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/message/{id}/delete", name="message_delete")
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($message);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('message_index');
    }

    /**
     * @Route("/message/{id}/reponse", name="message_reponse")
     */
    public function reponse(Request $request, Message $message): Response
    {
        $reponse = new MessageReponse();
        $form = $this->createForm(MessageReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setMessage($message);
            $this->entityManager->persist($reponse);
            $this->entityManager->flush();

            return $this->redirectToRoute('message_show', ['id' => $message->getId()]);
        }

        return $this->render('message/reponse.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        path: '/message/boite-reception/{locale}',
        name: 'app_message_boite_reception',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function boiteReception(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();

        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $messages = $this->entityManager->getRepository(Message::class)
            ->findReceptionMessages($user, $limit, $offset);

        $total = $this->entityManager->getRepository(Message::class)
            ->countReceptionMessages($user);

        return $this->render('message/boite_reception.html.twig', [
            'messages' => $messages,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'locale' => $locale
        ]);
    }

    #[Route(
        path: '/message/envoyes/{locale}',
        name: 'app_message_envoyes',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function messagesEnvoyes(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();

        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $messages = $this->entityManager->getRepository(Message::class)
            ->findSentMessages($user, $limit, $offset);

        $total = $this->entityManager->getRepository(Message::class)
            ->countSentMessages($user);

        return $this->render('message/messages_envoyes.html.twig', [
            'messages' => $messages,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'locale' => $locale
        ]);
    }

    #[Route(
        path: '/message/nouveau/{locale}',
        name: 'app_message_nouveau',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function nouveau(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDateEnvoi(new \DateTimeImmutable());
            $message->setExpediteur($this->getUser());

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->addFlash('success', 'message.envoi_success');
            return $this->redirectToRoute('app_message_envoyes', ['locale' => $locale]);
        }

        return $this->render('message/nouveau.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route(
        path: '/message/repondre/{id}/{locale}',
        name: 'app_message_repondre',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function repondre(Request $request, int $id, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $messageOriginal = $this->entityManager->getRepository(Message::class)->find($id);

        if (!$messageOriginal) {
            throw $this->createNotFoundException('message.not_found');
        }

        $reponse = new Message();
        $reponse->setObjet('Re: ' . $messageOriginal->getObjet());
        $reponse->setDestinataire($messageOriginal->getExpediteur());

        $form = $this->createForm(MessageType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setDateEnvoi(new \DateTimeImmutable());
            $reponse->setExpediteur($this->getUser());
            $reponse->setMessageOriginal($messageOriginal);

            $this->entityManager->persist($reponse);
            $this->entityManager->flush();

            $this->addFlash('success', 'message.reponse_success');
            return $this->redirectToRoute('app_message_envoyes', ['locale' => $locale]);
        }

        return $this->render('message/repondre.html.twig', [
            'form' => $form->createView(),
            'messageOriginal' => $messageOriginal,
            'locale' => $locale
        ]);
    }

    #[Route(
        path: '/message/supprimer/{id}/{locale}',
        name: 'app_message_supprimer',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function supprimer(int $id, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $message = $this->entityManager->getRepository(Message::class)->find($id);

        if (!$message) {
            throw $this->createNotFoundException('message.not_found');
        }

        $this->entityManager->remove($message);
        $this->entityManager->flush();

        $this->addFlash('success', 'message.suppr_success');
        return $this->redirectToRoute('app_message_boite_reception', ['locale' => $locale]);
    }
}
