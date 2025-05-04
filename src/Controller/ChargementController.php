<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;
use App\Entity\Chargement;
use App\Entity\ChargementType;
use App\Entity\PrerequisChargementType;
use App\Entity\Abonne;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder;
use Doctrine\ORM\EntityManager;

/**
 * ChargementController pour les actions de chargement de fichiers
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 */
class ChargementController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private AccessControl $accessControl;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private string $uploadDir;

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
        $this->uploadDir = __DIR__ . '/../../../../web/upload/logsite';

        // Configuration des en-têtes de cache
        $response = new Response();
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('max-age', 0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);
    }

    public function saveFileAction(string $locale, string $type): Response
    {
        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager;

        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        if (!in_array('saveFileAction', $listeActions)) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $fileidtype = 0;
        $unchargement = new Chargement();
        $form = $this->createForm(ChargementType::class, $unchargement);
        $listeFile = $em->getRepository("utbClientBundle/Chargement")->findBy(['archive' => 0], ['id' => 'DESC']);

        $request = $this->requestStack->getCurrentRequest();
        $extensions = 'txt';
        $lesoper = [];

        if (!file_exists(__DIR__ . "/../../../../web/upload/chargement/uwebj.txt")) {
            $lesoper[0][0] = 0;
        }

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unchargement = $form->getData();

            // Traitement du fichier
            if ($unchargement->getFile() !== null) {
                if ($unchargement->getFile()->guessExtension() !== $extensions) {
                    $this->addFlash('notice', 'errortypfic');
                    return $this->redirect($this->generateUrl('utb_client_savefile', ['locale' => $locale]));
                }

                $unchargement->setDateChargement(new \DateTime());
                $unchargement->setUtilisateur($currentUtilID);
                $unchargement->setArchive(0);
                $unchargement->setEtatChargement(0);

                $em->persist($unchargement);
                $em->flush();

                return $this->redirect($this->generateUrl('utb_client_savefile', ['locale' => $locale]));
            }
        }

        return $this->render('utbClientBundle/Chargement/ajoutChargementFile.html.twig', [
            'form' => $form->createView(),
            'listeFile' => $listeFile,
            'locale' => $locale,
            'type' => $type,
        ]);
    }

    public function envoieFileAction(string $locale, string $type): Response
    {
        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager;

        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        if (!in_array('envoieFileAction', $listeActions)) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $fileidtype = $request->request->get('fileidtype');

        if ($fileidtype) {
            $unchargement = $em->getRepository("utbClientBundle:Chargement")->find($fileidtype);
            if (!$unchargement) {
                throw $this->createNotFoundException('Chargement non trouvé');
            }

            $unchargement->setEtatChargement(1);
            $em->persist($unchargement);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_client_savefile', ['locale' => $locale]));
        }

        return $this->redirect($this->generateUrl('utb_client_savefile', ['locale' => $locale]));
    }

    public function prerequisFileAction(string $locale, string $type): Response
    {
        $em = $this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $authManager = $this->Auth.Manager;

        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        if (!in_array('prerequisFileAction', $listeActions)) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unprerequisChargement = new PrerequisChargementType();
        $form = $this->createForm(PrerequisChargementType::class, $unprerequisChargement);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unprerequisChargement = $form->getData();

            $em->persist($unprerequisChargement);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_client_prerequisfile', ['locale' => $locale]));
        }

        return $this->render('utbClientBundle/Chargement/prerequisChargementFile.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
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