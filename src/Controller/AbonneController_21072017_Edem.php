<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Abonne;
use App\Entity\Compte;
use App\Entity\SousAbonneType;
use App\Entity\AbonneType;
use App\Entity\ModifPwdType;
use App\Entity\MessageClient;
use App\Entity\ModifFicheAbonneType;
use App\Entity\ModAbonneType;
use App\Entity\Parametrage;
use App\Entity\Type\RegistrationFormType;
use App\Entity\Fonds;
use App\Entity\HistoriqueConnexion;
use App\Entity\ProfilClient;
use App\Service\AccessControl;
use App\Service\AuthManager;
use App\Service\MessageManager;

class AbonneController_21072017_Edem extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private AuthManager $authManager;
    private MessageManager $messageManager;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        AuthManager $authManager,
        MessageManager $messageManager,
        RequestStack $requestStack,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->messageManager = $messageManager;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    #[Route("/{locale}/abonne/edit", name: "abonne_edit_old")]
    public function edit(string $locale): Response
    {
        $em = $this->entityManager;
        $authManager = $this->authManager;
        $messageManager = $this->messageManager;
        $request = $this->requestStack->getCurrentRequest();
        $request->setLocale($locale);

        // Vérification de la connexion
        $currentUtilID = $authManager->getCurrentId();
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        
        if (!$authManager->isLogged()) {
            return $this->redirectToRoute('utb_client_logout', ['locale' => $locale]);
        }
        
        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('EditAction', $listeActions)) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirectToRoute('utb_client_accueil', ['locale' => $locale]);
        }

        $unAbonne = new Abonne();
        $form = $this->createForm(AbonneType::class, $unAbonne);
        $listefonds = $authManager->getFonds();
            
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if ($form->isSubmitted()) {
                $unAbonne = $form->getData();
                
                // Validation du login
                if (strlen($unAbonne->getUsername()) < 5) {
                    $this->addFlash('notice', 'errorsmalllogin');
                    return $this->render('abonne/edit.html.twig', [
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'listefonds' => $listefonds
                    ]);
                }

                // Validation du format de login
                $pattern = '/[][(){}<>\/+"*%&=?`^\'!$:;,À�?ÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌ�?Î�?ìíîïÙÚÛÜùúûüÿÑñ]/';
                if (preg_match($pattern, $unAbonne->getUsername())) {
                    $this->addFlash('notice', 'errorlogincaracint');
                    return $this->render('abonne/edit.html.twig', [
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'listefonds' => $listefonds
                    ]);
                }

                // Validation du mot de passe
                $password = $form["password"]->getData();
                $cpassword = $form["cpassword"]->getData();

                if ($password !== $cpassword) {
                    $this->addFlash('notice', 'passworderror');
                    return $this->render('abonne/edit.html.twig', [
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'listefonds' => $listefonds
                    ]);
                }

                if (strlen($password) < 6) {
                    $this->addFlash('notice', 'smallpassworderror');
                    return $this->render('abonne/edit.html.twig', [
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'listefonds' => $listefonds
                    ]);
                }

                // Validation de l'email
                $regex = '#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,6}$#';
                if (!preg_match($regex, $unAbonne->getEmail())) {
                    $this->addFlash('notice', 'emailformaterror');
                    return $this->render('abonne/edit.html.twig', [
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'listefonds' => $listefonds
                    ]);
                }

                // Vérification de l'unicité de l'email et du login
                $email = $em->getRepository(Abonne::class)->findOneByEmail($unAbonne->getEmail());
                $unlogin = $em->getRepository(Abonne::class)->findOneByUsername($unAbonne->getUsername());
                $unloginutil = $em->getRepository('App:Utilisateur')->findOneByUsername($unAbonne->getUsername());

                if ($unlogin !== null || $unloginutil !== null) {
                    $this->addFlash('notice', 'loginerror');
                    return $this->render('abonne/edit.html.twig', [
                        'form' => $form->createView(),
                        'locale' => $locale,
                        'listefonds' => $listefonds
                    ]);
                }

                // Initialisation des valeurs par défaut
                $unAbonne->setEtatAbonne(1);
                $unAbonne->setGenPsswd('');
                $unAbonne->setAttempt(0);
                $unAbonne->setCodebase(0);
                $unAbonne->setCodeop(0);

                // Traitement des comptes
                if ($unAbonne->getComptes() !== null) {
                    foreach ($unAbonne->getComptes() as $compte) {
                        $em->persist($compte);
                    }
                }

                $em->persist($unAbonne);
                $em->flush();

                $this->addFlash('success', 'abonne.creation.success');
                return $this->redirectToRoute('abonne_list', ['locale' => $locale]);
            }
        }

        return $this->render('abonne/edit.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
            'listefonds' => $listefonds
        ]);
    }

    #[Route("/{locale}/abonne/liste", name: "abonne_list")]
    public function liste(string $locale, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $em = $this->entityManager;
        
        $abonnes = $em->getRepository(Abonne::class)->findBy(
            ['etatAbonne' => 1],
            ['dateCreation' => 'DESC']
        );

        return $this->render('abonne/liste.html.twig', [
            'abonnes' => $abonnes,
            'locale' => $locale,
            'page' => $page
        ]);
    }

    private function infoUtilisateur(
        EntityManagerInterface $em,
        AuthManager $authManager,
        array $currentConnete,
        string $user,
        string $locale
    ): void {
        // Implémentation de la méthode infoUtilisateur
    }
} 