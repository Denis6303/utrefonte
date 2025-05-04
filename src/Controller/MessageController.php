<?php

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

class MessageController extends AbstractController
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
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
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
} 