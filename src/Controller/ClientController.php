<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\ProfilClient;
use App\Entity\Facturation;
use App\Entity\Module;
use App\Entity\Controleur;
use App\Entity\Action;
use App\Entity\Abonne;
use App\Entity\ActionClient;
use App\Entity\ComptePrRib;
use App\Entity\HistoriqueConnexion;
use App\Form\ProfilType;
use App\Form\FacturationType;
use App\Form\ModuleType;
use App\Form\ControleurType;
use App\Form\ActionType;
use App\Form\ActionClientType;
use App\Form\ComptePrRibType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

class ClientController extends AbstractController
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

    #[Route('/client/accueil/{locale}', name: 'app_client_accueil')]
    public function accueil(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login', ['locale' => $locale]);
        }

        $abonne = null;
        $type = 'utilisateur';
        if ($user instanceof Abonne) {
            $abonne = $user;
            $type = 'abonne';
        }

        $historiques = $this->entityManager->getRepository(HistoriqueConnexion::class)
            ->findBy(['utilisateur' => $user], ['dateConnexion' => 'DESC'], 5);

        return $this->render('client/accueil.html.twig', [
            'user' => $user,
            'abonne' => $abonne,
            'type' => $type,
            'historiques' => $historiques,
            'locale' => $locale
        ]);
    }

    #[Route('/client/module/liste/{locale}', name: 'app_client_module_liste')]
    public function listeModule(string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $modules = $this->entityManager->getRepository(Module::class)->findAll();

        return $this->render('client/module/liste.html.twig', [
            'modules' => $modules,
            'locale' => $locale
        ]);
    }

    #[Route('/client/module/ajouter/{locale}', name: 'app_client_module_ajouter')]
    public function ajouterModule(Request $request, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($module);
            $this->entityManager->flush();

            $this->addFlash('success', 'module.ajout_success');
            return $this->redirectToRoute('app_client_module_liste', ['locale' => $locale]);
        }

        return $this->render('client/module/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route('/client/module/modifier/{id}/{locale}', name: 'app_client_module_modifier')]
    public function modifierModule(Request $request, int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $module = $this->entityManager->getRepository(Module::class)->find($id);

        if (!$module) {
            throw $this->createNotFoundException('module.not_found');
        }

        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'module.modif_success');
            return $this->redirectToRoute('app_client_module_liste', ['locale' => $locale]);
        }

        return $this->render('client/module/modifier.html.twig', [
            'form' => $form->createView(),
            'module' => $module,
            'locale' => $locale
        ]);
    }

    #[Route('/client/module/supprimer/{id}/{locale}', name: 'app_client_module_supprimer')]
    public function supprimerModule(int $id, string $locale): Response
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $module = $this->entityManager->getRepository(Module::class)->find($id);

        if (!$module) {
            throw $this->createNotFoundException('module.not_found');
        }

        $this->entityManager->remove($module);
        $this->entityManager->flush();

        $this->addFlash('success', 'module.suppr_success');
        return $this->redirectToRoute('app_client_module_liste', ['locale' => $locale]);
    }
} 