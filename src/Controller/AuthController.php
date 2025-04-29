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
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;

use utb\ClientBundle\Types\TypeParametre;

class AuthController extends AbstractController
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
        $this->requestStack->getCurrentRequest()Stack = $requestStack;
        $this->translator = $translator;
    }

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
        $this->requestStack->getCurrentRequest()Stack = $requestStack;
        $this->translator = $translator;
    } {
    /*
     * Cette methode va afficher la page d'authentification
     * 
     */

    private $response ;
    public function __construct() {
        $this->response = new Response;
        $this->response->headers->addCacheControlDirective('no-cache', true);
        $this->response->headers->addCacheControlDirective('max-age', 0);
        $this->response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response->headers->addCacheControlDirective('no-store', true);
    } 
    public function loginAction(): Response(string $locale): Response {
        $authManager = $this->Auth.Manager;
        if ($authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale); // gautier 404
        return $this->render('utbClientBundle/Client:Login.html.twig'
                        , array(
                    'locale' => $locale,
        ),$this->response);
    }

    /*
     * Cette methode s'occupe de verifier si le login et le mot de pass envoyer par 
     * l'utlisateur est correct et lui permet d'essayer trois fois
     */

    public function loginProcessAction(): Response(string $locale): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale); // gautier 404
        $request = $this->requestStack->getCurrentRequest();
        $login = $request->request->get('login');
        $pwd = $request->request->get('passwd');
        $last_connexion = null;
        //Si le login ou le mot de passe est vide, on fait une redirection avec un msg d'erreur. ----
        if (empty($login) || empty($pwd)) {
            if ($locale == 'fr') {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Les champs Login et Mot de passe sont obligatoires");
            } elseif ($locale == 'en') {
                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Login et password inputs are required");
            }
            return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
        } else {// nous allons ici procéder aux vérification
            $authManager = $this->Auth.Manager;
            
            $abonnAuth = $authManager->login($login, md5($pwd)); //on essaie de logger comme abonné
            $em = $this->entityManager;

            $thisLogin = $em->getRepository('utbClientBundle:Abonne')->findAbonneByLogin($login);

            if (count($abonnAuth) == 0) { //si la connexion en tant que abonné echoue :
                $abonneLogin = $em->getRepository('utbClientBundle:Abonne')
                        ->findOneBy(array("username" => $login));
                if (isset($abonneLogin)) {//on verifie au moin si un abonne avec ce login existe
                    $attempt = $abonneLogin->getAttempt();
                    if ($attempt == 2) {

                        $abonneLogin->setAttempt(3);
                        $abonneLogin->setEtatAbonne(2);
                        $em->persist($abonneLogin);
                        $em->flush();
                        $authManager->logout();

                        if ($locale == 'fr') {
                            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Votre compte est bloqué apres trois (03) tentatives sans succes. Veuillez contacter votre gestionnaire de compte.Merci");
                        } elseif ($locale == 'en') {
                            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Your account is blocked after three (03) unsuccessful attempts. Please contact your account manager.Thanks ");
                        }
                        //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Votre compte est bloqué apres trois (03) tentatives sans succes. Veuillez contacter votre gestionnaire de compte.Merci");
                        return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                    } else {

                        $abonneLogin->setAttempt($attempt + 1);
                        $em->persist($abonneLogin);
                        $em->flush();
                        $authManager->logout();

                        // $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Utilisateur inconnu.Avez vous oublié votre mot de passe? veuillez contacter votre Gestionnaire. Merci");                                
                        if ($locale == 'fr') {
                            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Utilisateur inconnu.Avez vous oublié votre mot de passe? veuillez contacter votre Gestionnaire. Merci");
                        } elseif ($locale == 'en') {
                            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Unknown user.Have you forget your password? Please contact your account manager.Thanks ");
                        }

                        return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                    }
                } else {
                    //sinon on essaie de logger comme utilisateur
                    $userAuth = $authManager->loginUser($login, md5($pwd));
                    //on va verifier si un utilisateur avec ce login existe
                    $utilisateurLogin = $em->getRepository('utbClientBundle:Utilisateur')
                            ->findOneBy(array("username" => $login));
						if($utilisateurLogin !=null){		
							$etatprofil=$utilisateurLogin->getProfil()->getEtatProfil();
							$supprprofil=$utilisateurLogin->getProfil()->getSuppr();
							// verifier si le profil a ete deseactive
							//var_dump($supprprofil);var_dump($etatprofil);exit;
						 
							if($etatprofil==0){
								if ($locale == 'fr') {
									$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Utilisateur inconnu.Avez vous oublié votre mot de passe? veuillez contacter L'administrateur géneral SVP. Merci");
								} elseif ($locale == 'en') {
									$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Unknown user.Have you forget your password? Please contact the administrator.Thanks ");
								}
								return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
							}
							if($supprprofil==1){
								if ($locale == 'fr') {
									$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Utilisateur inconnu.Avez vous oublié votre mot de passe? veuillez contacter L'administrateur géneral SVP. Merci");
								} elseif ($locale == 'en') {
									$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Unknown user.Have you forget your password? Please contact the administrator.Thanks ");
								}
								return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
							}
						}	
                    if (count($userAuth) == 0) {

                        if (isset($utilisateurLogin)) {
                            $etatuser = $utilisateurLogin->getEtatUtilisateur();
                            if ($etatuser == 2) {//Utilisateur bloquee
                                $authManager->logout();

                                if ($locale == 'fr') {
                                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Votre Compte a été bloqué pour des raisons sécuritaires. Veuillez contacter l'administrateur.");
                                } elseif ($locale == 'en') {
                                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Your account was blocked for security reasons . Please contact the administrator. ");
                                }

                                return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                            } else {
                                $utili_attempt = $utilisateurLogin->getAttempt();
                                if ($utili_attempt == 2) {//si l'utilisateur avait deja fait 02 tentatives, on le bloc si le 3ieme echoue
                                    $utilisateurLogin->setAttempt(3);
                                    $utilisateurLogin->setEtatUtilisateur(2);
                                    $em->persist($utilisateurLogin);
                                    $em->flush();
                                    $authManager->logout();

                                    if ($locale == 'fr') {
                                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Votre compte est bloqué apres trois (03) tentatives sans succes. Veuillez contacter L'administrateur géneral.Merci");
                                    } elseif ($locale == 'en') {
                                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Your account is blocked after three (03) unsuccessful attempts . Please contact the administrator.Thanks ");
                                    }

                                    //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Votre compte est bloqué apres trois (03) tentatives sans succes. Veuillez contacter L'administrateur géneral.Merci");
                                    return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                                } else {
                                    $utilisateurLogin->setAttempt($utili_attempt + 1);
                                    $em->persist($utilisateurLogin);
                                    $em->flush();
                                    $authManager->logout();

                                    if ($locale == 'fr') {
                                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Utilisateur inconnu.Avez vous oublié votre mot de passe? veuillez contacter L'administrateur géneral SVP. Merci");
                                    } elseif ($locale == 'en') {
                                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Unknown user.Have you forget your password?  Please contact the administrator.Thanks ");
                                    }

                                    //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Utilisateur inconnu.Avez vous oublié votre mot de passe? veuillez contacter L'administrateur géneral SVP. Merci");
                                    return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                                }
                            }
                        } else {
                            $authManager->logout();

                            if ($locale == 'fr') {
                                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Utilisateur inconnu. Vérifiez votre mot de passe ou contacter votre gestionnaire.");
                            } elseif ($locale == 'en') {
                                $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Unknown user. Please recheck your password or contact the administrator.Thanks ");
                            }


                            //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Utilisateur inconnu. Vérifiez votre mot de passe ou contacter votre gestionnaire.");

                            return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                        }
                    } else {
                        //Date dernière connexion
                        $id_last_connexion = $em->getRepository("utbClientBundle:HistoriqueConnexion")->getMaxLastHistorique($userAuth[0]['id'], 1);
                        if (isset($id_last_connexion)) {
                            $last_connexion = $em->getRepository("utbClientBundle:HistoriqueConnexion")->find($id_last_connexion);
                        }
                        //Fin date dernière connexion
                        //nous allons créer la session et y mettre les informations de l'utilisateur
                        $utilisateurData = array();
                        $utilisateurData["id_abonne"] = $userAuth[0]['id'];
                        $utilisateurData["username_abonne"] = $userAuth[0]['username'];
                        $utilisateurData["nomPrenom_abonne"] = $userAuth[0]['nomPrenom'];
                        $utilisateurData["email_abonne"] = $userAuth[0]['email'];
                        $utilisateurData["profil_abonne"] = $userAuth[0]['libProfil'];
                        $utilisateurData["idprofil_abonne"] = $userAuth[0]['idprofil'];
                        $utilisateurData["type_user"] = "utilisateur";
                        $utilisateurData["sousAbonne"] = 0;

                        $listeModules = $em->getRepository("utbAdminBundle:Module")->findBy(array("client" => 1));
                        $thisDroits = $em->getRepository("utbClientBundle:droitClient")->findBy(array("profil" => $userAuth[0]['idprofil']));
                        if (!empty($thisDroits)) {
                            $thisDroits = unserialize($thisDroits[0]->getDroits());
                        }
                        $actionsByProfil = array();
                        foreach ($listeModules as $modules) {
                            //nous allons cherchons les action pour chaque modules 
                            $listeActions = $em->getRepository("utbAdminBundle:Action")
                                    ->getActionsByModule($modules->getId());
                            foreach ($listeActions as $action) {
                                if (isset($thisDroits[$modules->getId()]) && in_array($action->getId(), $thisDroits[$modules->getId()])) {
                                    $actionsByProfil[] = $action->getLibAction();
                                }
                            }
                        }

                        $utilisateurData["listeActions_abonne"] = $actionsByProfil;
                        
                        // 
                        ($last_connexion != null) ?
                                        $utilisateurData["last_connexion"] = $last_connexion->getDateDeb() :
                                        $utilisateurData["last_connexion"] = null;

                        /** Ajout gautier * */
                        $historique = new HistoriqueConnexion();
                        $user = $em->getRepository("utbClientBundle:Utilisateur")->find($userAuth[0]['id']);
                        $user->setAttempt(0);
                        $historique->setUtilisateur($user);
                        $historique->setAdresseIp( $request->getClientIp() );
                        $user->setGenPsswd('');
                        $em->persist($historique);
                        $em->flush();
                        /** Fin Ajout gautier * */
                        //Nous créons la session avec les donnée de l'abonnée
                        $authManager->setFlash("utb_client_data", $utilisateurData);
                        return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
                    }
                }
            } else {
                $etatabonne = $thisLogin[0]['etatAbonne'];
                if ($etatabonne == 2) {
                    $authManager->logout();
                    //$this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Votre Compte a été bloqué pour des raisons sécuritaires. Veuillez contacter votre gestionnaire.");

                    if ($locale == 'fr') {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Votre Compte a été bloqué pour des raisons sécuritaires. Veuillez contacter votre gestionnaire.");
                    } elseif ($locale == 'en') {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('emptyData', "Your account was blocked for security reasons . Please contact your account manager. ");
                    }

                    return $this->redirect($this->generateUrl('utb_client_login', ['locale' => $locale]));
                } else {
                    $abonneLogin = $em->getRepository('utbClientBundle:Abonne')
                            ->findOneBy(array("username" => $login));
                    $abonneLogin->setAttempt(0);

                    //Date dernière connexion
                    $id_last_connexion = $em->getRepository("utbClientBundle:HistoriqueConnexion")->getMaxLastHistorique($abonneLogin->getId(), 0);
                    if (isset($id_last_connexion)) {
                        $last_connexion = $em->getRepository("utbClientBundle:HistoriqueConnexion")->find($id_last_connexion);
                    }
                    //Fin date dernière connexion

                    
                    /** Ajout gautier * */
                    $historique = new HistoriqueConnexion();
                    $historique->setAbonne($abonneLogin);
                    $historique->setAdresseIp($request->getClientIp());
                    $abonneLogin->setGenPsswd('');
                    $em->persist($historique);


                    //var_dump($historique);
                    /** Fin Ajout gautier * */
                    $em->flush();
                    //nous allons créer la session et y mettre les informations de l'abonne
                    $abonneData = array();
                    $abonneData["id_abonne"] = $abonnAuth[0]['id'];
                    $abonneData["username_abonne"] = $abonnAuth[0]['username'];
                    $abonneData["nomPrenom_abonne"] = $abonnAuth[0]['nomPrenom'];
                    $abonneData["email_abonne"] = $abonnAuth[0]['email'];
                    $abonneData["idprofil_abonne"] = $abonnAuth[0]['idprofil'];
                    $abonneData["profil_abonne"] = $abonnAuth[0]['libProfil'];
                    $abonneData["type_user"] ='abonne';
                    
                    $a_SubAb = $em->getRepository('utbClientBundle:Abonne')->findBy(array("idAbonneParent" => $abonneLogin->getId())); 
                    //var_dump($a_SubAb);exit;
                    if (count($a_SubAb)>0) {
                        $abonneData["sousAbonne"] = 1;
                    } else $abonneData["sousAbonne"] = 0;

                    $listeModules = $em->getRepository("utbAdminBundle:Module")->findBy(array("client" => 1));
                    
                    
                    
                    $thisDroits = $em->getRepository("utbClientBundle:droitClient")->findBy(array("profil" => $abonnAuth[0]['idprofil']));
                    
                    if (!empty($thisDroits) && ($thisDroits != null )) {
                        $thisDroits = unserialize($thisDroits[0]->getDroits());
                    }
                    $actionsByProfil = array();
                    foreach ($listeModules as $modules) {
                        //nous allons cherchons les action pour chaque modules 
                        $listeActions = $em->getRepository("utbAdminBundle:Action")
                                ->getActionsByModule($modules->getId());
                        foreach ($listeActions as $action) {
                            if (isset($thisDroits[$modules->getId()]) && in_array($action->getId(), $thisDroits[$modules->getId()])) {
                                $actionsByProfil[] = $action->getLibAction();
                            }
                        }
                    }

                    $abonneData["listeActions_abonne"] = $actionsByProfil;
                    ($last_connexion != null) ? $abonneData["last_connexion"] = $last_connexion->getDateDeb() : $abonneData["last_connexion"] = null;

                    //var_dump($last_connexion);exit;                    
                    //Nous créons la session avec les donnée de l'abonné

                    $authManager->setFlash("utb_client_data", $abonneData);
                    //$currentAbonne = $authManager->getFlash("utb_client_data");
                    return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
                }
            }          
        }
    }

    public function logoutAction(): Response(string $locale): Response {
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale); // gautier 404
        $authManager = $this->Auth.Manager;

        /*         * *******  Ajout gautier ********** */
        $histo = null;
        $unuser = null;
        $unabonne = null;
        $idhisto = 0;
        $currentConnete = $authManager->getFlash("utb_client_data");
        //var_dump();exit;
        if (isset($currentConnete["id_abonne"])) {
            $em = $this->entityManager;

            if ($currentConnete["type_user"] == "abonne") {
                $unabonne = $em->getRepository('utbClientBundle:Abonne')->find($currentConnete["id_abonne"]);
                $idhisto = $em->getRepository('utbClientBundle:HistoriqueConnexion')->getMaxHistorique($unabonne->getId(), 0);
                if (isset($idhisto) && ($idhisto != 0)) {
                    $histo = $em->getRepository("utbClientBundle:HistoriqueConnexion")->find($idhisto);
                }
            }

            if ($currentConnete["type_user"] == "utilisateur") {
                $unuser = $em->getRepository("utbClientBundle:Utilisateur")->find($currentConnete['id_abonne']);
                $idhisto = $em->getRepository('utbClientBundle:HistoriqueConnexion')->getMaxHistorique($unuser->getId(), 1);
                if (isset($idhisto) && ($idhisto != 0)) {
                    $histo = $em->getRepository("utbClientBundle:HistoriqueConnexion")->find($idhisto);
                }
            }

            if ($histo != null) {

                $lafin = new \Datetime();
                $ledebut = $histo->getDateDeb();
                $laduree = $lafin->diff($ledebut);
                $laduree->format('%h heures %i minutes %s secondes');
                $histo->setDateFin($lafin);
                $histo->setDuree($laduree->format('%h h %i min %s sec'));
                $em->persist($histo);
                $em->flush();
            }
        }

        //$user = $em->getRepository("utbClientBundle:Utilisateur")->find($userAuth[0]['id']); 
        //$historique->setUtilisateur($user);              
        /*         * *******  Fin Ajout gautier ********** */

        $authManager->logout();
        return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
    }

}

?>
