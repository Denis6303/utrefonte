<?php

/**
 * Description of AuthController
 *
 * @author fomathi
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Abonne;
use App\Entity\Utilisateur;
use App\Entity\HistoriqueConnexion;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

use utb\ClientBundle\Types\TypeParametre;

class AuthController extends AbstractController
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

    public function loginAction(string $locale): Response
    {
        $authManager = $this->Auth.Manager;
        if ($authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getSession()->set('_locale', $locale);
        
        return $this->render('utbClientBundle/Client:Login.html.twig', [
            'locale' => $locale,
        ]);
    }

    public function loginProcessAction(string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getSession()->set('_locale', $locale);
        
        $request = $this->requestStack->getCurrentRequest();
        $login = $request->request->get('login');
        $pwd = $request->request->get('passwd');
        $last_connexion = null;

        if (empty($login) || empty($pwd)) {
            if ($locale == 'fr') {
                $this->addFlash('emptyData', "Les champs Login et Mot de passe sont obligatoires");
            } elseif ($locale == 'en') {
                $this->addFlash('emptyData', "Login et password inputs are required");
            }
            return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
        }

        $authManager = $this->Auth.Manager;
        $abonnAuth = $authManager->login($login, md5($pwd));
        $em = $this->entityManager;

        $thisLogin = $em->getRepository('utbClientBundle:Abonne')->findAbonneByLogin($login);

        if (count($abonnAuth) == 0) {
            $abonneLogin = $em->getRepository('utbClientBundle:Abonne')
                ->findOneBy(["username" => $login]);

            if ($abonneLogin) {
                $attempt = $abonneLogin->getAttempt();
                if ($attempt == 2) {
                    $abonneLogin->setAttempt(3);
                    $abonneLogin->setEtatAbonne(2);
                    $em->persist($abonneLogin);
                    $em->flush();
                    $authManager->logout();

                    if ($locale == 'fr') {
                        $this->addFlash('emptyData', "Votre compte est bloqué apres trois (03) tentatives sans succes. Veuillez contacter votre gestionnaire de compte. Merci");
                    } elseif ($locale == 'en') {
                        $this->addFlash('emptyData', "Your account is blocked after three (03) unsuccessful attempts. Please contact your account manager. Thanks");
                    }
                    return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                }

                $abonneLogin->setAttempt($attempt + 1);
                $em->persist($abonneLogin);
                $em->flush();
                $authManager->logout();

                if ($locale == 'fr') {
                    $this->addFlash('emptyData', "Utilisateur inconnu. Avez vous oublié votre mot de passe? veuillez contacter votre Gestionnaire. Merci");
                } elseif ($locale == 'en') {
                    $this->addFlash('emptyData', "Unknown user. Have you forget your password? Please contact your account manager. Thanks");
                }
                return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
            }

            $userAuth = $authManager->loginUser($login, md5($pwd));
            $utilisateurLogin = $em->getRepository('utbClientBundle:Utilisateur')
                ->findOneBy(["username" => $login]);

            if ($utilisateurLogin !== null) {
                $etatprofil = $utilisateurLogin->getProfil()->getEtatProfil();
                $supprprofil = $utilisateurLogin->getProfil()->getSuppr();

                if ($etatprofil == 0 || $supprprofil == 1) {
                    if ($locale == 'fr') {
                        $this->addFlash('emptyData', "Utilisateur inconnu. Avez vous oublié votre mot de passe? veuillez contacter L'administrateur géneral SVP. Merci");
                    } elseif ($locale == 'en') {
                        $this->addFlash('emptyData', "Unknown user. Have you forget your password? Please contact the administrator. Thanks");
                    }
                    return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
                }
            }

            if (count($userAuth) == 0) {
                if ($utilisateurLogin) {
                    $etatuser = $utilisateurLogin->getEtatUtilisateur();
                    if ($etatuser == 2) {
                        $authManager->logout();

                        if ($locale == 'fr') {
                            $this->addFlash('emptyData', "Votre Compte a été bloqué pour des raisons sécuritaires. Veuillez contacter l'administrateur.");
                        } elseif ($locale == 'en') {
                            $this->addFlash('emptyData', "Your account was blocked for security reasons. Please contact the administrator.");
                        }
                        return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                    }

                    $utili_attempt = $utilisateurLogin->getAttempt();
                    if ($utili_attempt == 2) {
                        $utilisateurLogin->setAttempt(3);
                        $utilisateurLogin->setEtatUtilisateur(2);
                        $em->persist($utilisateurLogin);
                        $em->flush();
                        $authManager->logout();

                        if ($locale == 'fr') {
                            $this->addFlash('emptyData', "Votre compte est bloqué apres trois (03) tentatives sans succes. Veuillez contacter l'administrateur. Merci");
                        } elseif ($locale == 'en') {
                            $this->addFlash('emptyData', "Your account is blocked after three (03) unsuccessful attempts. Please contact the administrator. Thanks");
                        }
                        return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                    }

                    $utilisateurLogin->setAttempt($utili_attempt + 1);
                    $em->persist($utilisateurLogin);
                    $em->flush();
                    $authManager->logout();

                    if ($locale == 'fr') {
                        $this->addFlash('emptyData', "Utilisateur inconnu. Avez vous oublié votre mot de passe? veuillez contacter l'administrateur. Merci");
                    } elseif ($locale == 'en') {
                        $this->addFlash('emptyData', "Unknown user. Have you forget your password? Please contact the administrator. Thanks");
                    }
                    return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                }

                if ($locale == 'fr') {
                    $this->addFlash('emptyData', "Utilisateur inconnu. Avez vous oublié votre mot de passe? veuillez contacter l'administrateur. Merci");
                } elseif ($locale == 'en') {
                    $this->addFlash('emptyData', "Unknown user. Have you forget your password? Please contact the administrator. Thanks");
                }
                return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
            }

            $utilisateurLogin->setAttempt(0);
            $em->persist($utilisateurLogin);
            $em->flush();

            $historique = new HistoriqueConnexion();
            $historique->setDateConnexion(new \DateTime());
            $historique->setUtilisateur($utilisateurLogin);
            $em->persist($historique);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $abonneLogin = $em->getRepository('utbClientBundle:Abonne')
            ->findOneBy(["username" => $login]);
        $abonneLogin->setAttempt(0);
        $em->persist($abonneLogin);
        $em->flush();

        return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
    }

    public function logoutAction(string $locale): Response
    {
        $authManager = $this->Auth.Manager;
        $authManager->logout();
        return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
    }
}

?>
