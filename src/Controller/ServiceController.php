<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\{
    Service,
    ServiceType,
    DossierType
};
use App\Service\AccessControl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, RequestStack, Response};
use Symfony\Component\HttpFoundation\Response as ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * ServiceController pour la gestion des services
 * 
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link http://www.utb.tg
 */
class ServiceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(
        path: '/service/ajouter/{locale}/{type}',
        name: 'app_service_ajouter',
        requirements: [
            'locale' => '[a-z]{2}',
            'type' => '\d+'
        ]
    )]
    public function ajouter(Request $request, string $locale, int $type): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('ajout_service', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $service = new Service();
        $service->setServiceAjoutPar($user->getId());
        $service->setEtatService(0);
        $service->setServiceDateAjout(new \DateTime());

        if ($type === 1) {
            $form = $this->createForm(DossierType::class, $service);
            $service->setEmailService("info@utb.tg");
            $service->setDescriptionService("Nouveau dossier");
            $service->setTypeService(1);
        } else {
            $service->setTypeService(0);
            $form = $this->createForm(ServiceType::class, $service);
        }

        $listestat = $this->entityManager
            ->getRepository('App\Entity\Statistique')
            ->getInfoOrStat(4, $locale, 0, null);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service = $form->getData();
            
            if (empty($service->getLibService())) {
                if ($type === 1) {
                    return $this->redirectToRoute('app_message_liste', ['locale' => $locale]);
                }
                return $this->render('service/ajout.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale,
                    'listestat' => $listestat
                ]);
            }

            $this->entityManager->persist($service);
            $this->entityManager->flush();

            $this->addFlash('success', 'notification.ajout');

            if ($type === 1) {
                return $this->redirectToRoute('app_message_liste', ['locale' => $locale]);
            }
            return $this->redirectToRoute('app_service_liste', ['locale' => $locale]);
        }

        return $this->render($type === 1 ? 'service/ajout_dossier.html.twig' : 'service/ajout.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
            'listestat' => $listestat
        ]);
    }

    #[Route(
        path: '/service/liste/{locale}',
        name: 'app_service_liste',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function liste(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('liste_service', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $services = $this->entityManager
            ->getRepository(Service::class)
            ->findAll();

        $listestat = $this->entityManager
            ->getRepository('App\Entity\Statistique')
            ->getInfoOrStat(4, $locale, 0, null);

        return $this->render('service/liste.html.twig', [
            'services' => $services,
            'locale' => $locale,
            'listestat' => $listestat
        ]);
    }

    #[Route(
        path: '/service/deplacer/{locale}',
        name: 'app_service_deplacer',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function deplacer(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('deplacer_service', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $serviceId = $request->request->get('idservice');
        $messageId = $request->request->get('idmessage');

        $message = $this->entityManager
            ->getRepository('App\Entity\Message')
            ->find($messageId);

        $service = $this->entityManager
            ->getRepository(Service::class)
            ->find($serviceId);

        if ($message === null || $service === null) {
            $this->addFlash('error', 'service.not_found');
            return $this->redirectToRoute('app_message_liste', ['locale' => $locale]);
        }

        $message->setService($service);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_message_liste', ['locale' => $locale]);
    }

    #[Route(
        path: '/service/supprimer/{id}/{locale}/{type}',
        name: 'app_service_supprimer',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}',
            'type' => '\d+'
        ]
    )]
    public function supprimer(Request $request, int $id, string $locale, int $type): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('supprimer_service', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $service = $this->entityManager
            ->getRepository(Service::class)
            ->find($id);

        if ($service === null) {
            $this->addFlash('error', 'service.not_found');
            return $this->redirectToRoute('app_service_liste', ['locale' => $locale]);
        }

        $this->entityManager->remove($service);
        $this->entityManager->flush();

        $this->addFlash('success', 'notification.suppression');

        if ($type === 1) {
            return $this->redirectToRoute('app_message_liste', ['locale' => $locale]);
        }
        return $this->redirectToRoute('app_service_liste', ['locale' => $locale]);
    }

    #[Route(
        path: '/service/etat/{id}/{etat}/{locale}',
        name: 'app_service_etat',
        requirements: [
            'id' => '\d+',
            'etat' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function etat(Request $request, int $id, int $etat, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('gerer_etat_service', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $service = $this->entityManager
            ->getRepository(Service::class)
            ->find($id);

        if ($service === null) {
            $this->addFlash('error', 'service.not_found');
            return $this->redirectToRoute('app_service_liste', ['locale' => $locale]);
        }

        $service->setEtatService($etat);
        $this->entityManager->persist($service);
        $this->entityManager->flush();

        $this->addFlash('success', 'notification.modification');

        return $this->redirectToRoute('app_service_liste', ['locale' => $locale]);
    }

    #[Route(
        path: '/service/gerer/{locale}',
        name: 'app_service_gerer',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function gerer(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('gerer_service', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $services = $this->entityManager
            ->getRepository(Service::class)
            ->findAll();

        foreach ($services as $service) {
            $service->setEtatService(1);
            $this->entityManager->persist($service);
        }

        $this->entityManager->flush();
        $this->addFlash('success', 'notification.modification');

        return $this->redirectToRoute('app_service_liste', ['locale' => $locale]);
    }

    #[Route(
        path: '/service/modifier/{id}/{locale}',
        name: 'app_service_modifier',
        requirements: [
            'id' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function modifier(Request $request, int $id, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('modifier_service', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $service = $this->entityManager
            ->getRepository(Service::class)
            ->find($id);

        if ($service === null) {
            $this->addFlash('error', 'service.not_found');
            return $this->redirectToRoute('app_service_liste', ['locale' => $locale]);
        }

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($service);
            $this->entityManager->flush();

            $this->addFlash('success', 'notification.modification');
            return $this->redirectToRoute('app_service_liste', ['locale' => $locale]);
        }

        return $this->render('service/modifier.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route(
        path: '/service/corbeille/{locale}',
        name: 'app_service_corbeille',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function corbeille(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('corbeille_service', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $services = $this->entityManager
            ->getRepository(Service::class)
            ->findBy(['etatService' => 0]);

        return $this->render('service/corbeille.html.twig', [
            'services' => $services,
            'locale' => $locale
        ]);
    }
}