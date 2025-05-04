<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\ProfilClient;
use App\Entity\ProfilType;
use App\Entity\Facturation;
use App\Entity\FacturationType;
use App\Entity\Module;
use App\Entity\ModuleType;
use App\Entity\Controleur;
use App\Entity\ControleurType;
use App\Entity\Action;
use App\Entity\Abonne;
use App\Entity\ActionType;
use App\Entity\ActionClientType;
use App\Entity\ComptePrRibType;
use App\Entity\HistoriqueConnexion;
use utb\ClientBundle\Controller\SessionExpired;
use utb\ClientBundle\Types\TypeParametre;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

/**
 * ClientController 
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 */
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

        // Configuration des en-têtes de cache
        $response = new Response();
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('max-age', 0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);
    }

    public function indexAction(string $locale, $typePre): Response
    {
        $em = $this->entityManager;
        $type_user = "";
        $nomPrenom = "";
        $profil = "";
        $last_connexion = "";

        $authManager = $this->Auth.Manager;
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
        }

        $currentID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        if (isset($currentConnete["id_abonne"]) && $currentConnete["id_abonne"] != "") {
            $id_abonne = $currentConnete["id_abonne"];
            $type_user = $currentConnete["type_user"];
            $nomPrenom = $currentConnete["nomPrenom_abonne"];
            $profil = $currentConnete["profil_abonne"];
            $last_connexion = $currentConnete["last_connexion"];
            $listeActions = $currentConnete["listeActions_abonne"];
        }

        $this->requestStack->getCurrentRequest()->attributes->set('id_abonne', $id_abonne);
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', $type_user);
        $this->requestStack->getCurrentRequest()->attributes->set('nomPrenom', $nomPrenom);
        $this->requestStack->getCurrentRequest()->attributes->set('profil', $profil);
        $this->requestStack->getCurrentRequest()->attributes->set('last_connexion', $last_connexion);
        $this->requestStack->getCurrentRequest()->attributes->set('listeActions', $listeActions);

        return $this->render('utbClientBundle/Client/index.html.twig', [
            'type_user' => $type_user,
            'nomPrenom' => $nomPrenom,
            'profil' => $profil,
            'last_connexion' => $last_connexion,
            'locale' => $locale,
            'typePre' => $typePre,
        ]);
    }

    private function infoUtilisateur(EntityManagerInterface $em, $authManager, array $currentConnete, string $user, string $locale): void
    {
        if ($user == 'utilisateur') {
            $utilisateur = $em->getRepository("utbClientBundle:Utilisateur")->find($currentConnete["id_abonne"]);
            if (!$utilisateur) {
                $authManager->logout();
                throw $this->createNotFoundException('Utilisateur non trouvé');
            }
        } else {
            $abonne = $em->getRepository("utbClientBundle:Abonne")->find($currentConnete["id_abonne"]);
            if (!$abonne) {
                $authManager->logout();
                throw $this->createNotFoundException('Abonné non trouvé');
            }
        }
    }
} 