<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Message;
use App\Entity\Envoi;
use App\Entity\Utilisateur;
use App\Entity\Abonne;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
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