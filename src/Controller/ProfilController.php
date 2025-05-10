<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\ProfilClient;
use App\Entity\ProfilType;
use App\Entity\DroitClient;
use App\Entity\Ordre;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response as ResponseInterface;
use App\Service\AccessControl;

class ProfilController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(
        path: '/profil/ajouter/{locale}',
        name: 'app_profil_ajouter',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function ajouter(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('ajouter', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $profil = new ProfilClient();
        $default = serialize([]);
        $droits = new DroitClient();
        $droits->setProfil($profil);
        $droits->setDroits($default);

        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profil = $form->getData();

            $existe = $this->entityManager
                ->getRepository(ProfilClient::class)
                ->findOneBy(['libProfil' => $profil->getLibProfil()]);

            if ($existe) {
                $this->addFlash('error', 'profil.existe');
                return $this->redirectToRoute('app_profil_liste', ['locale' => $locale]);
            }

            $supprime = $this->entityManager
                ->getRepository(ProfilClient::class)
                ->findOneBy(['libProfil' => $profil->getLibProfil(), 'suppr' => true]);

            if ($supprime) {
                $supprime->setSuppr(false);
                $this->entityManager->persist($supprime);
            } else {
                $this->entityManager->persist($profil);
                $this->entityManager->persist($droits);
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'profil.ajoute');

            return $this->redirectToRoute('app_profil_liste', ['locale' => $locale]);
        }

        return $this->render('profil/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route(
        path: '/profil/liste/{locale}',
        name: 'app_profil_liste',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function liste(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('liste', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $profils = $this->entityManager
            ->getRepository(ProfilClient::class)
            ->findBy(['locale' => $locale, 'suppr' => false]);

        return $this->render('profil/liste.html.twig', [
            'profils' => $profils,
            'locale' => $locale
        ]);
    }

    #[Route(
        path: '/profil/supprimer/{id}/{locale}',
        name: 'app_profil_supprimer',
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
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('supprimer', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $profil = $this->entityManager
            ->getRepository(ProfilClient::class)
            ->find($id);

        if (!$profil) {
            throw $this->createNotFoundException('profil.not_found');
        }

        $utilisateur = $this->entityManager
            ->getRepository(Utilisateur::class)
            ->findOneBy(['profil' => $profil]);

        if ($utilisateur) {
            $this->addFlash('error', 'profil.utilise');
            return $this->redirectToRoute('app_profil_liste', ['locale' => $locale]);
        }

        $profil->setSuppr(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'profil.supprime');
        return $this->redirectToRoute('app_profil_liste', ['locale' => $locale]);
    }

    private function infoUtilisateur(
        EntityManagerInterface $em,
        AccessControl $authManager,
        array $currentConnete,
        string $user,
        string $locale
    ): void {
        if (!isset($currentConnete["id_abonne"]) || $currentConnete["id_abonne"] === "") {
            return;
        }

        $idAbonne = $currentConnete["id_abonne"];
        $typeUser = $currentConnete["type_user"];
        $nomPrenom = $currentConnete["nomPrenom_abonne"];
        $profil = $currentConnete["profil_abonne"];
        $lastConnexion = $currentConnete["last_connexion"];
        $listeActions = $currentConnete["listeActions_abonne"];
        $subabonne = $currentConnete["sousAbonne"];

        $maxIdleTime = $this->getParameter('maxIdleTime');
        $session = $this->requestStack->getSession();
        
        if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {
            $this->addFlash('warning', 'session.expired');
            $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }
    }
}
