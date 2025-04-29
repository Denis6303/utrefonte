<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Compte;
use App\Entity\CompteRepository;
use App\Entity\AbonneCompteType;
use App\Entity\CompteType;
use App\Entity\DossierType;
use App\Entity\AdresseIp;
use App\Entity\Operation;
use App\Entity\Abonne;
use PHPExcel;
use PHPExcel_IOFactory;
use \HTML2PDF;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;

use \utb\ClientBundle\Types\TypeParametre;

/**
 * 
 * CompteController pour la gestion des Comptes
 * 
 *
 *
 *
 * Cette methode permet de verifier le droit de l'utilisateur avant d'effectuer une action dans une methode
 * 
 * Fonction utilise pour afficher les menus auquels l'utilisateurs à droit
 *
 * $em = $this-> getDoctrine()->getEntityManager();
 * $authManager = $this->Auth.Manager;
 * $this->requestStack->getCurrentRequest()->setLocale($locale);
 * 
 * if(!$authManager->isLogged())
 *     return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
 * 
 * $currentConnete = $authManager->getFlash("utb_client_data"); //avoir les information sur la personne connecté
 * $this->infoUtilisateur($em,$authManager,$currentConnete,'utilisateur',$locale);
 * $listeActions = $currentConnete["listeActions_abonne"]; //avoir la liste des actions auquelles l'utilisateur connecté à droit
 *  
 *   if ( !in_array('EditAction', $listeActions) ){ // test si l'action courant appartient à la liste des actions affectées à l'utilisateur 
 *       $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
 *       return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));// redirectionnement vers l'accueil
 *   }
 * 
 * Cette ligne de code permet de definir la langue à travers la variable locale| fr pour le francais et en pour l'anglais
 * 
 * Presente dans la majorite des methodes
 * 
 * $this->requestStack->getCurrentRequest()->setLocale($locale);
 * 
 * Declaration de formulaire relatif à une instance d'objet : Presente dans la majorite des methodes
 * 
 * $form: Instance de la classe Form
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class CompteController extends AbstractController
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

    private $response;

    public function __construct() {
        $this->response = new Response;
        $this->response->headers->addCacheControlDirective('no-cache', true);
        $this->response->headers->addCacheControlDirective('max-age', 0);
        $this->response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response->headers->addCacheControlDirective('no-store', true);
    }

    /**
     * Methode permettant d'ajouter un abonnné
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unabonne: Variable contenant l'objet abonné créée
     * 
     * $compte: un tableau pour recuperer le compte de l'abonne
     * 
     * $champ: tableau qui recupère les chiffres saisie pour former le numero de compte
     * 
     * $NUMCPT: recupère les chiffres concatené pour former le mot de passe c'est à le mot de passe final former à l'aide des inputs
     * 
     * $typecompte : rucupere le type du compte 1 uweb | 2 afwb | 3 afwb2
     * 
     * $namefond : Pour former le nom de l'input de fonds
     * 
     * $lefond : créér l'objet Fonds par rapport à l'id de l'objet fonds 
     * 
     * $cat: par rapport on numero de compte saisi on coupe pour chercher le catégorie du (Compte epargne, ...) on coupe 3 caratere à partir du 6 pour le numéro de compte à 11 caratère et de 9 celui de 11
     * 
     * $categorie:
     * 
     * $vpassword: recupère le password de l'abonne qu'on a saisi c'est a cette variable qu'on passe la fonction de cryptage  MD5
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <string> $idabonne Variable passee pour recuperer l'id de l'abonne connecte
     * 
     * @return <string>  retourne le twig utbClientBundle:Abonne:ajoutCompte.html.twig 
     * 
     */
    public function ajoutCompteAction(): Response(string $locale, $idabonne = 0): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        /*   if ( !in_array('ajoutCompteAction', $listeActions) ){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */
        //transformation de $type_user en variable globale
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', 'utilisateur');

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unabonne = $em->getRepository("utbClientBundle:Abonne")->find($idabonne);

        $form = $this->createForm($this->createForm(AbonneCompteType::class), $unabonne);

        //$compte  =array(); 

        $unfonds = $em->getRepository("utbClientBundle:Abonne")->findLeFondsAbonne($idabonne);

        $request = $request;

        if ($request->isMethod('POST')) {

            //$form->handleRequest($request);           
            $unabonne = $form->getData();
            $compte = $unabonne->getComptes();
            $champ = array();

            $champ[] = $request->request->get('Champ1', array());
            $champ[] = $request->request->get('Champ2', array());
            $champ[] = $request->request->get('Champ3', array());

            $champ[] = $request->request->get('Champ4', array());
            $champ[] = $request->request->get('Champ5', array());
            $champ[] = $request->request->get('Champ6', array());
            $champ[] = $request->request->get('Champ7', array());
            $champ[] = $request->request->get('Champ8', array());
            $champ[] = $request->request->get('Champ9', array());
            $champ[] = $request->request->get('Champ10', array());
            $champ[] = $request->request->get('Champ11', array());
            $champ[] = $request->request->get('Champ12', array());
            $champ[] = $request->request->get('Champ13', array());

            $typecompte[] = $request->request->get('type_compte', array());

            $champ[] = $request->request->get('Champ14', array());
            $champ[] = $request->request->get('Champ15', array());


            $NUMCPT = array();

            for ($i = 0; $i < count($request->request->get('Champ3', array())); $i++) {
                $NUMCPT[] = null;
            }
            $i = 0;
            $k = 0;
            $n = 0;
            // var_dump($champ);   
            //var_dump(count($request->request->get('Champ1',array())));exit;

            for ($i = 0; $i < count($request->request->get('Champ3', array())); $i++) {
                $uncompte = new Compte();
                foreach ($champ as $t => $j) {

                    if (isset($j[$n]) && $j[$n] != "") {
                        $NUMCPT[$n] .= $j[$n];
                    }
                }
                $k++;


                /**
                 * Transformation depuis le background
                 */
                if ($typecompte[0][$i] == 4) {//afbwn
                    // Code Agence . Code Radical . Type de compte .  Numero Ordre
                    $NUMCPT[$i] = substr($NUMCPT[$i], 0, 2) . substr($NUMCPT[$i], 2, 6) . substr($NUMCPT[$i], 9, 3) . substr($NUMCPT[$i], 12, 1);
                }




                //var_dump($NUMCPT);exit; 
                // créaction de l'objet Fonds
                $namefond = 'comptes_' . $k . '_fonds';
                // $lefond = $em->getRepository("utbClientBundle:Fonds")->find($request->request->get($namefond));
                $lefond = $em->getRepository("utbClientBundle:Fonds")->find($unfonds[0][1]);
                // créaction de l'objet TypeCompte
                //$nametypecompte='comptes_'.$k.'_typecompte';
                // var_dump($typecompte);var_dump($typecompte[0][$n]);
                $letypecompte = $em->getRepository("utbClientBundle:TypeCompte")->find($typecompte[0][$n]);
                //var_dump($letypecompte);
                $lenumerocompte = $em->getRepository("utbClientBundle:Compte")->findOneBy(array('numeroCompte' => $NUMCPT[$n]));
                $unnumcompte2 = "";
                //Controle si le numero de compte existe deja
                $unnumcompte = $this->entityManager
                        ->getRepository('utbClientBundle:Compte')
                        ->findOneCompte($NUMCPT[$n]);
                //var_dump($NUMCPT[$n]); var_dump($unnumcompte); exit;
                if (($unnumcompte != null && $unnumcompte != 0)) {
                    //Controle si le numero de cpte existe mais a ete supprimer
                    $unnumcompte2 = $this->entityManager
                            ->getRepository('utbClientBundle:Compte')
                            ->findByCompteDeleted($NUMCPT[$n]);


                    if (($unnumcompte2 != null && $unnumcompte2 != 0)) {

                        $lenumerocompte->setDateCreation(new \Datetime());
                        $lenumerocompte->setFacturation(0);
                        $lenumerocompte->setEtatCompte(0);
                        $lenumerocompte->setFonds($lefond);
                        $lenumerocompte->setTypeCompte($letypecompte);
                        $lenumerocompte->setAbonne($unabonne);
                    } else {
                        $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'compteerror');
                        return $this->redirect($this->generateUrl('utb_client_detail_abonneadmin', ['locale' => $locale, 'id' => $idabonne,]));
                    }
                } else {
                    $uncompte->setDateCreation(new \Datetime());
                    $uncompte->setFacturation(0);
                    $uncompte->setEtatCompte(0);
                    $uncompte->setFonds($lefond);
                    $uncompte->setTypeCompte($letypecompte);
                    $uncompte->setAbonne($unabonne);
                    $uncompte->setNumeroCompte($NUMCPT[$n]);
                }

//                var_dump($NUMCPT[$n]) ;
//                var_dump($typecompte[0][$i]) ; 
//                var_dump($NUMCPT) ; 
//                exit ;

                $cat = '';
                $couper2 = 0;
                $categorie = null;

                if ($typecompte[0][$n] == 3) {//uweb
                    if (strlen($NUMCPT[$n]) == 15) {
                        $couper2 = 9;
                        $cat = substr($NUMCPT[$n], $couper2, 3);
                    } else {
                        $couper2 = 6;
                        $cat = substr($NUMCPT[$n], $couper2, 3);
                    }
                } elseif ($typecompte[0][$i] == 1) {//afbw
                    $cat = "AFB0";
                    $couper2 = 9;
                    $couper3 = 10;
                    $catcontrole = substr($NUMCPT[$i], $couper3, 2);
                } elseif ($typecompte[0][$i] == 2) {//afbw2
                    $cat = "AFB0";
                    $couper2 = 9;
                    $couper3 = 10;
                    $catcontrole = substr($NUMCPT[$i], $couper3, 2);
                } elseif ($typecompte[0][$i] == 4) {//afbwn
                    $cat = "AFB2";
                    $couper2 = 9;
                    $couper3 = 10;
                    $catcontrole = substr($NUMCPT[$i], $couper3, 2);
                }

//                                var_dump($catcontrole) ; 
//                                var_dump(strlen($NUMCPT[$i])) ; 
//                                exit ;

                if ($cat != null) {
                    $categorie = $em->getRepository("utbClientBundle:CategorieCompte")->find($cat);
                    if (($unnumcompte2 != null && $unnumcompte2 != 0)) {
                        $lenumerocompte->setCategorieCompte($categorie);
                    } else {
                        $uncompte->setCategorieCompte($categorie);
                    }
                }
                //
                $n++;
                /*
                 * uweb :  1
                 * afwb :  2
                 * afwb2 : 3
                 * 
                 */

                // var_dump($typecompte[0][$i]);var_dump(strlen($NUMCPT[$i]));var_dump($typecompte[0][$i]);var_dump(strlen($NUMCPT[$i]));var_dump(strlen($typecompte[0][$i]));var_dump(strlen($NUMCPT[$i]));exit;
                //if($lenumerocompte!=null){
                if (($typecompte[0][$i] == 3 && strlen($NUMCPT[$i]) == 15) || ($typecompte[0][$i] == 1 && strlen($NUMCPT[$i]) == 11) || ($typecompte[0][$i] == 2 && strlen($NUMCPT[$i]) == 11) || ($typecompte[0][$i] == 4 && strlen($NUMCPT[$i]) == 12)) {
                    //var_dump($NUMCPT[0]);exit;
                    //  var_dump('test');exit;
                    if ($lenumerocompte == NULL) {

                        $em->persist($uncompte);
                    }
                    if (($unnumcompte2 != null && $unnumcompte2 != 0)) {

                        $em->persist($lenumerocompte);
                    }
                    $em->flush();
                } else {
                    $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'errorcompte');
                    return $this->redirect($this->generateUrl('utb_client_detail_abonneadmin', ['locale' => $locale, 'id' => $idabonne,]));
                }
            }
            $msgnotification = $this->translator->trans('notification.ajout');
            return $this->redirect($this->generateUrl('utb_client_detail_abonneadmin', ['locale' => $locale, 'id' => $idabonne,]));
        }

        return $this->render('utbClientBundle/Compte/ajoutCompte.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'idabonne' => $idabonne, 'unfonds' => $unfonds,
                        ), $this->response);
    }

    /**
     *  Methode qui liste les Comptes
     * 
     * @var
     * 
     * $listeCompte : Un objet de la classe Compte
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCompte.html.twig)
     * 
     */
    public function listeCompteAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('listeCompteAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeCompte = $em->getRepository("utbClientBundle/Compte")->findAll();
        foreach ($listeCompte as $unCompte) {
            $unCompte->setTranslatableLocale($locale);
            $em->refresh($unCompte);
        }


        $listestat = $this->entityManager
                ->getRepository('utbClientBundle/Statistique')
                ->getInfoOrStat($typeStat = 4, $locale, 0, null);

        return $this->render('utbClientBundle/Compte/listeCompte.html.twig', array('listeCompte' => $listeCompte, 'locale' => $locale, 'listestat' => $listestat,), $this->response);
    }

    /**
     *  Methode qui permet de faire oposition
     * 
     * @var
     * 
     * $listeCompte : Un objet de la classe Compte
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCompte.html.twig)
     * 
     */
    public function faireOppositionAction(): Response(string $locale, $numeroCompte): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('faireOppositionAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $this->requestStack->getCurrentRequest();
        $testCompte = $em->getRepository("utbClientBundle:Compte")->testCompteAbonne($numeroCompte, $currentUtilID);
        if ($testCompte == null) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        if ($request->isMethod('POST')) {

            $unAbonne = $em->getRepository("utbClientBundle:Abonne")->find($currentUtilID);
            $ungest = $em->getRepository("utbClientBundle:Compte")->getGestionnaireCompte($numeroCompte);
            $lanotification = $em->getRepository("utbClientBundle:MessageClient")->find(96);
            $notice = $this->message.Manager;
            $objet = $notice->traiteContenu("#nomPrenom", $currentConnete['nomPrenom_abonne'], $lanotification->getObjetMessageClient());
            $contenu1 = $notice->traiteContenu("#nomPrenom", $currentConnete['nomPrenom_abonne'], $lanotification->getContenuMessageClient());
            $contenu = $notice->traiteContenu("#compte", $numeroCompte, $contenu1);
            $notice->envoyerNoticeUtil($em, $objet, $contenu, null, $currentUtilID, $ungest[0]['idgest'], 0);

            return $this->redirect($this->generateUrl("utb_client_detail_compte_abonne", array('locale' => $locale, 'idAbonne' => $unAbonne->getId(), 'idCompte' => $numeroCompte)));
        }

        return $this->render('utbClientBundle/Compte/faireOpposition.html.twig', array('locale' => $locale, 'numeroCompte' => $numeroCompte,), $this->response);
    }

    /**
     *  Methode qui liste les Comptes
     * 
     * @var
     * 
     * $listeCompte : Un objet de la classe Compte
     * 
     * $listestat :     Liste de statistiques type 4 comme explique dans StatistiqueRepository
     * 
     * @param integer $page
     * @param integer $imprimer
     * @param integer $idCompte
     * @param integer $idAbonne
     * @param integer $locale
     * 
     * @return 
     */
    public function detailCompteAction(): Response($page, $imprimer, $idCompte, $idAbonne, $post, string $locale, $deb, $fin, $mttde, $mtta, $sens): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('detailCompteAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $testCompte = $em->getRepository("utbClientBundle:Compte")->testCompteAbonne($idCompte, $idAbonne);
        if ($testCompte == null) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $request;
        $excelService = new PHPExcel();
        //$excelService = $this->xls.service_xls2007;
        $excelService->getProperties()
                ->setTitle("Liste des Operations du compte")
                ->setDescription("Liste des Operations du compte");

        $excelService->getActiveSheet()->setTitle('Liste des Operations du compte');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->setActiveSheetIndex(0);

        //create the response

        $excelService->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Date Opération')
                ->setCellValue('B1', 'Numero mouvement')
                ->setCellValue('C1', 'Libelle')
                ->setCellValue('D1', 'Date Valeur')
                ->setCellValue('E1', 'Débit')
                ->setCellValue('F1', 'Crédit')
        ;
        $typefichier = $request->request->get('typefichier');


        /*
         * recuperation des params et leurs transformations en var globales 
         * pr telechargement ou impression
         */
        if ($request->getMethod() == 'POST') {
            $this->requestStack->getCurrentRequest()->attributes->set('compte', $idCompte);


            $deb = strtolower($request->request->get('datedebut'));
            $this->requestStack->getCurrentRequest()->attributes->set('datedebut', $deb);


            $fin = strtolower($request->request->get('datefin'));
            $this->requestStack->getCurrentRequest()->attributes->set('datefin', $fin);


            $mttde = strtolower($request->request->get('mttde'));
            $this->requestStack->getCurrentRequest()->attributes->set('mttde', $mttde);


            $mtta = strtolower($request->request->get('mtta'));
            $this->requestStack->getCurrentRequest()->attributes->set('mtta', $mtta);


            $sens = strtolower($request->request->get('sens'));
            $this->requestStack->getCurrentRequest()->attributes->set('sens', $sens);
        }


        /* total des resultats */
        $total = $em->getRepository("utbClientBundle:Compte")->getTotalOperationsCompte($post, 0, $deb, $fin, $idCompte, $idAbonne, $mttde, $mtta, $sens);

        $articles_per_page = $this->container->get->getParameter('max_operations_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;



        $unAbonne = $this->entityManager
                ->getRepository("utbClientBundle:Abonne")
                ->findOneByLocale($idAbonne, $locale);


        $listeOperation = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getListeOperationsCompte($post, 100, $deb, $fin, $idCompte, $idAbonne, $mttde, $mtta, $sens, $total, $page, $articles_per_page);

        /* $abonne = $this->security->getToken()->getAbonne()->getId(); */

        $aux = 2;

        $dateFictive = new \DateTime(null);
        if ($typefichier == 1 || $typefichier == 2) {
            foreach ($listeOperation as $row) {
                $datop = null;
                $daval = null;
                $datop = $row['dateOperation']->format("d/m/Y");
                $datval = $row['dateValeur']->format("d/m/Y");
                if ($datval == '30/11/-0001') {
                    $datval = null;
                }
                if ($row['sensOperation'] == 'C') {
                    $excelService->setActiveSheetIndex(0)
                            ->setCellValue('A' . $aux, $datop) //$row['dateOperation']
                            ->setCellValue('B' . $aux, $row['numeroMvt'])
                            ->setCellValue('C' . $aux, $row['libOperation'])
                            ->setCellValue('D' . $aux, $datval)//$row['dateValeur']
                            ->setCellValue('E' . $aux, 0)
                            ->setCellValue('F' . $aux, $row['montant']);
                } else {
                    $excelService->setActiveSheetIndex(0)
                            ->setCellValue('A' . $aux, $datop)//$row['dateOperation']
                            ->setCellValue('B' . $aux, $row['numeroMvt'])
                            ->setCellValue('C' . $aux, $row['libOperation'])
                            ->setCellValue('D' . $aux, $datval)//$row['dateValeur']
                            ->setCellValue('E' . $aux, $row['montant'])
                            ->setCellValue('F' . $aux, 0);
                }
                // Set active sheet index to the first sheet
                $excelService->setActiveSheetIndex(0);
                $aux++;
            }
            $response = new Response();
        }
        // Quand le telechargement doit etre en Excel
        elseif ($imprimer == 1) {

            //$lien = __DIR__ . '/../../../../web/';

            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle:ParamSysteme")->find(4);

            if ($path != null)
                $lien = $path->getValeur();
            return $this->render('utbClientBundle/Recherche/apercuRecherche.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation, 'lien' => $lien), $this->response);
        }

        if ($typefichier == 1) {

            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.xls');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }
        // Quand le telechargement doit etre en csv
        elseif ($typefichier == 2) {
            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.csv');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }
        // Quand le telechargement doit etre en fichier txt
        elseif ($typefichier == 3) {
            $aux = 0;
            $handle = fopen('php///memory', 'r+');
            $header = array();
            $donneperation = null;
            $donneperation = array();
            $i = 0;
            $separateur = chr(9);
            foreach ($listeOperation as $operation) {
                $donneperation[$i] = "" . $operation['dateOperation']->format("d/m/Y") . "" . $separateur . "" . $operation['numeroMvt'] . "" . $separateur . "" . $operation['libOperation'] . "" . $separateur . "" . $operation['dateValeur']->format("d/m/Y") . "" . $separateur . "" . $operation['montant'] . "\r\n";
                //var_dump($donneperation[$i]);exit;
                fputcsv($handle, $donneperation);

                $i++;
            }

            /* rewind($handle);
              $content = stream_get_contents($handle);
              fclose($handle);

              return new Response($content, 200, array(
              'Content-Type' => 'application/force-download',
              'Content-Disposition' => 'attachment; filename="export.txt"'
              )); */
            $response = $this->render('utbClientBundle/Recherche/export_txt.html.twig', array('donneperation' => $donneperation), $this->response);
            $filename = "export_" . date("Y_m_d_His") . ".txt";
            $response->headers->set('Content-Type', 'text/txt');
            $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);

            return $response;
        }
        // Quand le telechargement doit etre en fichier pdf
        elseif ($typefichier == 4) {
            ob_start();
            /* $donneperation = "";
              $i = 0;
              $separateur = "";
              $donneperation = "<table align=\"center\" border=\"1\">";
              $donneperation.= "<tr>
              <td>Date</td>
              <td>Libellé</td>
              <td>Valeur</td>
              <td>Montant</td>
              <td> N°</td>
              </tr>";
              foreach ($listeOperation as $operation) {
              $donneperation.="<tr>
              <td>" . $operation['dateOperation']->format("d/m/Y") . "</td>
              <td>" . $operation['libOperation'] . "</td>
              <td>" . $operation['dateValeur']->format("d/m/Y") . "</td>
              <td>" . $operation['montant'] . "</td>
              <td>" . $operation['numeroMvt'] . "</td>
              </tr>";
              //var_dump($donneperation[$i]);exit;
              $i++;
              }
              $donneperation.="</table>"; */

            //$lien = __DIR__ . '/../../../../web/';

            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle/ParamSysteme")->find(4);

            if ($path != null)
                $lien = $path->getValeur();

            $donneperation = $this->templating->render('utbClientBundle:Compte:operation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation, 'lien' => $lien, 'numeroCompte' => $idCompte), $this->response);

            $html2pdf = new HTML2PDF('P', 'A4', 'fr');
            $html2pdf->WriteHTML($donneperation);
            $fichier = $html2pdf->Output('exemple.pdf');
            $response = new Response();
            $response->clearHttpHeaders();
            $response->setContent(file_get_contents($fichier));
            $response->headers->set('Content-Type', 'application/force-download');
            $response->headers->set('Content-disposition', 'filename=' . $fichier);

            return $response;
        } else {
            //var_dump($listeOperation);   
            return $this->render('utbClientBundle/Compte/detailCompte.html.twig', array('unAbonne' => $unAbonne, 'idAbonne' => $idAbonne,
                        'idCompte' => $idCompte, 'locale' => $locale,
                        'post' => $post,
                        'listeOperation' => $listeOperation,
                        'last_page' => $last_page,
                        'previous_page' => $previous_page,
                        'current_page' => $page,
                        'next_page' => $next_page,
                        'total' => $total,
                        'datedeb' => $deb, 'datefin' => $fin, 'mttde' => $mttde, 'mttap' => $mtta, 'sens' => $sens,
                            ), $this->response);
        }
    }

    /**
     * 
     * @param type $page
     * @param type $imprimer
     * @param type $idCompte
     * @param type $idAbonne
     * @param type $locale
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailCompteAbonneAction(): Response($page, $imprimer, $idCompte, $post, $idAbonne, string $locale, $deb, $fin, $mttde, $mtta, $sens): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        $objetCompte = $em->getRepository("utbClientBundle:Compte")->find($idCompte);

        if (!in_array('detailCompteAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        //Ajout Gautier
        $a = $em->getRepository("utbClientBundle:Abonne")->find($currentID);

        $id_temporaire = 0;

        if ($a instanceof Abonne && $a->getProfil()->getId() == TypeParametre::PROFIL_SOUS_ABONNE) {

            $id_temporaire = $a->getIdAbonneParent()->getId();
        } else {
            $id_temporaire = $a->getId();
        }

        // Controle de l'id qui se trouve dans l'URL
        if (!($a instanceof Abonne) || ($idAbonne != $currentID && $idAbonne != $a->getIdAbonneParent()->getId() )) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }


        $testCompte = $em->getRepository("utbClientBundle:Compte")->testCompteAbonne($idCompte, $id_temporaire);
//        var_dump($testCompte);exit;
        if ($testCompte == null) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->attributes->set('sidetailcpte', 1);

        $request = $request;
        $excelService = new PHPExcel();
        //$excelService = $this->xls.service_xls2007;
        $excelService->getProperties()
                ->setTitle("Liste des Operations du compte")
                ->setDescription("Liste des Operations du compte");

        $excelService->getActiveSheet()->setTitle('Liste des Operations du compte');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->setActiveSheetIndex(0);

        //create the response

        $excelService->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Date Opération')
                ->setCellValue('B1', 'Numero mouvement')
                ->setCellValue('C1', 'Libelle')
                ->setCellValue('D1', 'Date Valeur')
                ->setCellValue('E1', 'Débit')
                ->setCellValue('F1', 'Crédit');

        $typefichier = $request->request->get('typefichier');
        $tab = null;
        $cpte = $em->getRepository("utbClientBundle:Compte")->find($idCompte);
        if ($cpte != null/* instanceof App\Entity\Compte */) {
            $today = new \Datetime();
            $tablosolde = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($idCompte, (int) $today->format('Y'), (int) $today->format('m'), 0, 0);
            // $tab['dat'] = $today;
        }

        //


        /*
         * recuperation des params et leurs transformations en var globales 
         * pr telechargement ou impression
         */
        if ($request->getMethod() == 'POST') {
            $this->requestStack->getCurrentRequest()->attributes->set('compte', $idCompte);


            $deb = strtolower($request->request->get('datedebut'));
            $this->requestStack->getCurrentRequest()->attributes->set('datedebut', $deb);


            $fin = strtolower($request->request->get('datefin'));
            $this->requestStack->getCurrentRequest()->attributes->set('datefin', $fin);


            $mttde = strtolower($request->request->get('mttde'));
            $this->requestStack->getCurrentRequest()->attributes->set('mttde', $mttde);


            $mtta = strtolower($request->request->get('mtta'));
            $this->requestStack->getCurrentRequest()->attributes->set('mtta', $mtta);


            $sens = strtolower($request->request->get('sens'));
            $this->requestStack->getCurrentRequest()->attributes->set('sens', $sens);
        }


        /* total des resultats */

        $soldeDeb = null;
        $soldeFin = null;
        $dateFin = null;
        $dateDeb = null;
        $dateDeb = new \Datetime();
        $interval30jr = new \DateInterval('P1M');
        //$intervalajustement = new \DateInterval('P1D');
        $dateDeb->sub($interval30jr);

        $test = 0;
        $dateFin = new \Datetime();
        $test = $em->getRepository("utbClientBundle:Compte")->getSiExisteOp($cpte, $dateFin->format('Y-m-d'));

//        $total = $em->getRepository("utbClientBundle:Compte")->getTotalOperationsCompte(0, 0, $deb, $fin, $idCompte, $currentID, $mttde, $mtta, $sens);
        $total = $em->getRepository("utbClientBundle:Compte")->getTotalOperationsCompte(0, 0, $deb, $fin, $idCompte, $id_temporaire, $mttde, $mtta, $sens);

        $articles_per_page = $this->container->get->getParameter('max_operations_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $unAbonne = $this->entityManager
                ->getRepository("utbClientBundle:Abonne")
                ->findOneByLocale($idAbonne, $locale);

//        $listeOperation = $this->getDoctrine()
//                ->getManager()
//                ->getRepository("utbClientBundle:Compte")
//                ->getListeOperationsCompte(0, 100, $deb, $fin, $idCompte, $currentID, $mttde, $mtta, $sens, $total, $page, $articles_per_page);
        $listeOperation = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getListeOperationsCompte(0, 100, $deb, $fin, $idCompte, $id_temporaire, $mttde, $mtta, $sens, $total, $page, $articles_per_page);
//        var_dump($listeOperation);      exit;  
        // }         

        if ($dateDeb == null) {
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, 0, 0, 0, 4);
        } else {
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, $dateDeb->format('Y'), $dateDeb->format('m'), $dateDeb->format('d'), 2);
        }

        if ($cpte->getTypeCompte()->getId() == 1 || $cpte->getTypeCompte()->getId() == 2) {
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldeAfbw2($cpte->getNumeroCompte());
        }

        if ($dateFin == null) {
            $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, 0, 0, 0, 5);
        } else {
            //$em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($idCompte, (int) $today->format('Y'), (int) $today->format('m'),0, 0);
            if ($test == 0) {
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, 0, 0, 0, 5); //$cpte,$dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'),2);
            } else {
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, $dateFin->format('Y'), $dateFin->format('m'), $dateFin->format('d'), 5);
            }
        }

        $opsolde = null;
        $opsolde = $this->entityManager
                ->getRepository("utbClientBundle/Compte")
                ->getSoldeCompte($idCompte);

        $aux = 2;

        $dateFictive = new \DateTime(null);
        if ($typefichier == 1 || $typefichier == 2) {
            foreach ($listeOperation as $row) {
                $datop = null;
                $daval = null;
                $datop = $row['dateOperation']->format("d/m/Y");
                $datval = $row['dateValeur']->format("d/m/Y");
                if ($datval == '30/11/-0001') {
                    $datval = null;
                }
                if ($row['sensOperation'] == 'C') {
                    $excelService->setActiveSheetIndex(0)
                            ->setCellValue('A' . $aux, $datop) //$row['dateOperation']
                            ->setCellValue('B' . $aux, $row['numeroMvt'])
                            ->setCellValue('C' . $aux, $row['libOperation'])
                            ->setCellValue('D' . $aux, $datval)//$row['dateValeur']
                            ->setCellValue('E' . $aux, 0)
                            ->setCellValue('F' . $aux, $row['montant']);
                } else {
                    $excelService->setActiveSheetIndex(0)
                            ->setCellValue('A' . $aux, $datop) //$row['dateOperation']
                            ->setCellValue('B' . $aux, $row['numeroMvt'])
                            ->setCellValue('C' . $aux, $row['libOperation'])
                            ->setCellValue('D' . $aux, $datval)//$row['dateValeur']
                            ->setCellValue('E' . $aux, $row['montant'])
                            ->setCellValue('F' . $aux, 0);
                }


                // Set active sheet index to the first sheet
                $excelService->setActiveSheetIndex(0);
                $aux++;
            }
            $response = new Response();
        }
        // Quand le telechargement doit etre en Excel
        elseif ($imprimer == 1) {

            //$lien = __DIR__ . '/../../../../web/';
            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle/ParamSysteme")->find(4);

            if ($path != null)
                $lien = $path->getValeur();
            return $this->render('utbClientBundle/Recherche/apercuRecherche.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation, 'lien' => $lien), $this->response);
        }

        if ($typefichier == 1) {
            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.xls');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }
        // Quand le telechargement doit etre en csv
        elseif ($typefichier == 2) {
            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.csv');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }
        // Quand le telechargement doit etre en fichier txt
        elseif ($typefichier == 3) {
            $aux = 0;
            $handle = fopen('php///memory', 'r+');
            $header = array();
            $donneperation = null;
            $donneperation = array();
            $i = 0;
            $separateur = chr(9);
            foreach ($listeOperation as $operation) {
                $donneperation[$i] = "" . $operation['dateOperation']->format("d/m/Y") . "" . $separateur . "" . $operation['numeroMvt'] . "" . $separateur . "" . $operation['libOperation'] . "" . $separateur . "" . $operation['dateValeur']->format("d/m/Y") . "" . $separateur . "" . $operation['montant'] . "\r\n";
                //var_dump($donneperation[$i]);exit;
                fputcsv($handle, $donneperation);

                $i++;
            }

            $response = $this->render('utbClientBundle/Recherche/export_txt.html.twig', array('donneperation' => $donneperation), $this->response);
            $filename = "export_" . date("Y_m_d_His") . ".txt";
            $response->headers->set('Content-Type', 'text/txt');
            $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);

            return $response;
        }
        // Quand le telechargement doit etre en fichier pdf
        elseif ($typefichier == 4) {
            ob_start();

            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle/ParamSysteme")->find(4);

            if ($path != null)
                $lien = $path->getValeur();

            $listeOperation = array_chunk($listeOperation, 48, True);


            //$donneperation = $this->templating->render('utbClientBundle:Compte:operation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation,'lien'=>$lien,'numeroCompte'=>$cpte,'soldedeb'=>$soldeDeb,'soldefin'=>$soldeFin), $this->response);
            $donneperation = $this->templating->render('utbClientBundle/Compte/operation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation, 'lien' => $lien, 'numeroCompte' => $idCompte), $this->response);

            $donneperation = utf8_decode($donneperation);

            $piedpage = $this->translator->trans('compte.texte', array(), 'compte');
            $piedpage = utf8_decode($piedpage);

            $footer = ' <div style="border-top: 2px solid #555;width:755px;margin-top: 20px;">
                                        <h5 style="text-align: center;margin: 5px 0px;padding: 0px;font-size: 8px;font-weight: normal;"> '
                    . $piedpage .
                    '   </h5>                   
                                        <span style="clear:both;"></span>
                                    </div> ';

            return new Response(
                    $this->knp_snappy.pdf->getOutputFromHtml($donneperation, array(
                        /* 'ignore-load-errors'           => null, // old v0.9
                          'lowquality'                   => false,
                          'collate'                      => null,
                          'no-collate'                   => null,
                          'cookie-jar'                   => null,
                          'copies'                       => null,
                          'dpi'                          => null,
                          'extended-help'                => null, */
                        'grayscale' => false,
                        'help' => null,
                        'htmldoc' => null,
                        'image-dpi' => null,
                        'image-quality' => null,
                        'manpage' => null,
                        'margin-bottom' => 12,
                        'margin-left' => 5,
                        'margin-right' => 5,
                        'margin-top' => 6,
                        'orientation' => null,
                        'output-format' => null,
                        'page-height' => null,
                        /* 'page-size'                    => "A4",
                          'page-width'                   => null,
                          'no-pdf-compression'           => null,
                          'quiet'                        => null,
                          'read-args-from-stdin'         => null,
                          'title'                        => null,
                          'use-xserver'                  => null,
                          'version'                      => null,
                          'dump-default-toc-xsl'         => null,
                          'dump-outline'                 => null,
                          'outline'                      => null,
                          'no-outline'                   => null,
                          'outline-depth'                => null,
                          'allow'                        => null,
                          'background'                   => null,
                          'no-background'                => null,
                          'checkbox-checked-svg'         => null,
                          'checkbox-svg'                 => null,
                          'cookie'                       => null,
                          'custom-header'                => null,
                          'custom-header-propagation'    => null,
                          'no-custom-header-propagation' => null,
                          'debug-javascript'             => null,
                          'no-debug-javascript'          => null,
                          'default-header'               => null,
                          'encoding'                     => null,
                          'disable-external-links'       => null,
                          'enable-external-links'        => null,
                          'disable-forms'                => null,
                          'enable-forms'                 => null,
                          'images'                       => true,
                          'no-images'                    => null,
                          'disable-internal-links'       => null,
                          'enable-internal-links'        => null,
                          'disable-javascript'           => null,
                          'enable-javascript'            => null,
                          'javascript-delay'             => null,
                          'load-error-handling'          => null,
                          'disable-local-file-access'    => null,
                          'enable-local-file-access'     => null,
                          'minimum-font-size'            => null,
                          'exclude-from-outline'         => null,
                          'include-in-outline'           => null,
                          'page-offset'                  => null,
                          'password'                     => null,
                          'disable-plugins'              => null,
                          'enable-plugins'               => null,
                          'post'                         => null,
                          'post-file'                    => null,
                          'print-media-type'             => null,
                          'no-print-media-type'          => null,
                          'proxy'                        => null,
                          'radiobutton-checked-svg'      => null,
                          'radiobutton-svg'              => null,
                          'run-script'                   => null,
                          'disable-smart-shrinking'      => true,
                          'enable-smart-shrinking'       => null,
                          'stop-slow-scripts'            => null,
                          'no-stop-slow-scripts'         => null,
                          'disable-toc-back-links'       => null,
                          'enable-toc-back-links'        => null,
                          'user-style-sheet'             => null,
                          'username'                     => null,
                          'window-status'                => null, */
                        'zoom' => 1.04,
                        'footer-center' => null,
                        'footer-font-name' => null,
                        'footer-font-size' => 8,
                        'footer-html' => $footer,
                            /* 'footer-left'                  => null,
                              'footer-line'                  => null,
                              'no-footer-line'               => null ,
                              'footer-right'                 => null,
                              'footer-spacing'               => null,
                              'header-center'                => null,
                              'header-font-name'             => null,
                              'header-font-size'             => 8,
                              'header-html'                  => null,
                              'header-left'                  => null,
                              'header-line'                  => null,
                              'no-header-line'               => null,
                              'header-right'                 => null,
                              'header-spacing'               => null,
                              'replace'                      => null,
                              'disable-dotted-lines'         => null,
                              'cover'                        => null,
                              'toc'                          => null,
                              'toc-depth'                    => null,
                              'toc-font-name'                => null,
                              'toc-l1-font-size'             => null,
                              'toc-header-text'              => null,
                              'toc-header-font-name'         => null,
                              'toc-header-font-size'         => null,
                              'toc-level-indentation'        => null,
                              'disable-toc-links'            => null,
                              'toc-text-size-shrink'         => null,
                              'xsl-style-sheet'              => null,
                              'redirect-delay'               => null, */
                    )), 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=OPERATIONS_' . $cpte . '.pdf '
                    )
            );

            /* $html2pdf = new HTML2PDF('P', 'A4', 'fr');
              $html2pdf->pdf->SetDisplayMode('real');
              $html2pdf->WriteHTML($donneperation);
              $fichier = $html2pdf->Output('exemple.pdf');
              $response = new Response();
              $response->clearHttpHeaders();
              $response->setContent(file_get_contents($fichier));
              $response->headers->set('Content-Type', 'application/force-download');
              $response->headers->set('Content-disposition', 'filename=' . $fichier);
              return $response; */
        } else {
            //var_dump($listeOperation);   
            return $this->render('utbClientBundle/Compte/detailCompteAbonne.html.twig', array('unAbonne' => $unAbonne, 'idAbonne' => $idAbonne,
                        'idCompte' => $idCompte, 'locale' => $locale,
                        'listeOperation' => $listeOperation,
                        'post' => $post,
                        'last_page' => $last_page,
                        'previous_page' => $previous_page,
                        'current_page' => $page,
                        'next_page' => $next_page,
                        //'tablo'=> $tablosolde,
                        'soldedeb' => $soldeDeb, 'soldefin' => $soldeFin,
                        'total' => $total,
                        'objetCpte' => $objetCompte,
                        'opsolde' => $opsolde,
                        'datedeb' => $deb, 'datefin' => $fin, 'mttde' => $mttde, 'mttap' => $mtta, 'sens' => $sens,
                            ), $this->response);
        }
    }

    public function exporterOperationCompteAction(): Response(string $locale, $imprimer, $cpte, $deb, $fin, $mttde, $mtta, $sens, string $type): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        //$this->infoUtilisateur($em,$authManager,$currentConnete,'utilisateur',$locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion

        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('exporterOperationCompteAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $soldeDeb = null;
        $soldeFin = null;
        $dateFin = null;
        $dateDeb = null;

        $dateDeb = new \Datetime();
        $dateDebut = new \Datetime();
        $interval30jr = new \DateInterval('P1M');
        $intervalajust = new \DateInterval('P1D');
        $dateDeb->sub($interval30jr);
        $dateDebut->sub($interval30jr); //$dateDebut->add($intervalajust); 
        $test = 0;

        $dateFin = new \Datetime();
        //$dateFin->setDate($dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'));
        $test = $em->getRepository("utbClientBundle:Compte")->getSiExisteOp($cpte, $dateFin->format('Y-m-d'));

        if ($dateDeb == null) {
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, 0, 0, 0, 4);
        } else {
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, $dateDeb->format('Y'), $dateDeb->format('m'), $dateDeb->format('d'), 2);
        }

        $lecpte = $em->getRepository("utbClientBundle:Compte")->find($cpte);

        if ($lecpte != null && ($lecpte->getTypeCompte()->getId() == 1 || $lecpte->getTypeCompte()->getId() == 2)) {
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldeAfbw2($lecpte->getNumeroCompte());
        }

        if ($dateFin == null) {
            $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, 0, 0, 0, 5);
        } else {
            // $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,$dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'),3);
            if ($test == 0) {
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, 0, 0, 0, 5); //$cpte,$dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'),2);
            } else {
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, $dateFin->format('Y'), $dateFin->format('m'), $dateFin->format('d'), 5);
            }
        }


        //var_dump($soldeDeb);
        //var_dump($soldeFin);exit;

        $request = $request;
        $excelService = new PHPExcel();
        //$excelService = $this->xls.service_xls2007;
        $excelService->getProperties()
                ->setTitle("Liste des Abonne")
                ->setDescription("Liste des Abonnes");

        $excelService->getActiveSheet()->setTitle('LISTE DES OPERATIONS');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->setActiveSheetIndex(0);
        $typefichier = $request->request->get('typefichier');
        $sidetailcpte = $request->request->get('sidetailcpte');
        //var_dump($sidetailcpte);
        //create the response
        $excelService->setActiveSheetIndex(0)
                ->setCellValue('A1', 'DATE OPERATION')
                ->setCellValue('B1', 'LIBELLE')
                ->setCellValue('C1', 'DATE VALEUR')
                ->setCellValue('D1', 'DEBIT')
                ->setCellValue('E1', 'CREDIT')
                ->setCellValue('F1', 'NUMERO MOUVEMENT')
                ->setCellValue('G1', 'SOLDE EN LIGNE')
        ;

        $this->requestStack->getCurrentRequest()->attributes->set('compte', $cpte);
        $this->requestStack->getCurrentRequest()->attributes->set('datedebut', $deb);
        $this->requestStack->getCurrentRequest()->attributes->set('datefin', $fin);
        $this->requestStack->getCurrentRequest()->attributes->set('mttde', $mttde);
        $this->requestStack->getCurrentRequest()->attributes->set('mtta', $mtta);
        $this->requestStack->getCurrentRequest()->attributes->set('sens', $sens);

        $total = $em->getRepository("utbClientBundle:Compte")->getTotalOperationsAdmin($type, $deb, $fin, $cpte, $mttde, $mtta, $sens);
        $page = 1;
        $articles_per_page = 10000;
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $listeOperation = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getListeOperationsAdmin(0, $deb, $fin, $cpte, $mttde, $mtta, $sens, $total, $page, $articles_per_page);

        $aux = 3;

        $dateFictive = new \DateTime(null);
        if ($typefichier == 1 || $typefichier == 2) {
            $valeur = '0';
            if ($soldeDeb != null) {

                if (strtolower($soldeDeb[0]['sens']) == 'd') {
                    $valeur = - $soldeDeb[0]['solde'];
                } elseif (strtolower($soldeDeb[0]['sens']) == 'c') {
                    $valeur = $soldeDeb[0]['solde'];
                }

                $datedSolde = $soldeDeb[0]['dateSolde'];
                $datedSolde = $datedSolde->format('d/m/Y');
                $excelService->setActiveSheetIndex(0)
                        ->setCellValue('A2', $datedSolde)
                        ->setCellValue('B2', 'SOLDE DEBUT PERIODE')
                        ->setCellValue('C2', '')
                        ->setCellValue('D2', '')
                        ->setCellValue('E2', '')
                        ->setCellValue('F2', ' ')
                        ->setCellValue('G2', $valeur);
            }

            foreach ($listeOperation as $row) {
                if ($row['codOperation'] == '04') {
                    $datop = null;
                    $daval = null;
                    $datop = $row['dateOperation']->format("d/m/Y");
                    $datval = $row['dateValeur']->format("d/m/Y");
                    if ($datval == '30/11/-0001') {
                        $datval = null;
                    }
                    if ($row['sensOperation'] == 'C') {
                        $excelService->setActiveSheetIndex(0)
                                ->setCellValue('A' . $aux, $datop)
                                ->setCellValue('B' . $aux, $row['libOperation'])
                                ->setCellValue('C' . $aux, $datval)
                                ->setCellValue('D' . $aux, 0)
                                ->setCellValue('E' . $aux, $row['montant'])
                                ->setCellValue('F' . $aux, $row['numeroMvt'])
                                ->setCellValue('G' . $aux, $row['soldeEnLigne']);
                    } else {
                        $excelService->setActiveSheetIndex(0)
                                ->setCellValue('A' . $aux, $datop)
                                ->setCellValue('B' . $aux, $row['libOperation'])
                                ->setCellValue('C' . $aux, $datval)
                                ->setCellValue('D' . $aux, $row['montant'])
                                ->setCellValue('E' . $aux, 0)
                                ->setCellValue('F' . $aux, $row['numeroMvt'])
                                ->setCellValue('G' . $aux, $row['soldeEnLigne']);
                    }
                    // Set active sheet index to the first sheet
                    $excelService->setActiveSheetIndex(0);
                    $aux++;
                }
            }

            if ($soldeFin != null) {

                if (strtolower($soldeFin[0]['sens']) == 'd') {
                    $valeur = - $soldeFin[0]['solde'];
                } elseif (strtolower($soldeFin[0]['sens']) == 'c') {
                    $valeur = $soldeFin[0]['solde'];
                }

                $datefSolde = null;
                $datefSolde = $soldeFin[0]['dateSolde'];
                $datefSolde = $datefSolde->format('d/m/Y');
                $excelService->setActiveSheetIndex(0)
                        ->setCellValue('A' . $aux, $datefSolde)
                        ->setCellValue('B' . $aux, 'SOLDE FIN PERIODE')
                        ->setCellValue('C' . $aux, '')
                        ->setCellValue('D' . $aux, '')
                        ->setCellValue('E' . $aux, '')
                        ->setCellValue('F' . $aux, '')
                        ->setCellValue('G' . $aux, $valeur);
            }

            $response = new Response();
        }
        // Quand le telechargement doit etre en Excel
        elseif ($imprimer == 1) {

            //$lien = __DIR__ . '/../../../../web/';

            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle:ParamSysteme")->find(4);

            if ($path != null)
                $lien = $path->getValeur();
            return $this->render('utbClientBundle/Recherche/apercuRecherche.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation, 'lien' => $lien, 'soldedeb' => $soldeDeb, 'soldefin' => $soldeFin), $this->response);
        }

        if ($typefichier == 1) {

            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.xls');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }
        // Quand le telechargement doit etre en csv
        elseif ($typefichier == 2) {
            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.csv');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }
        // Quand le telechargement doit etre en fichier txt
        elseif ($typefichier == 3) {
            $aux = 0;
            $handle = fopen('php://memory', 'r+');
            $header = array();

            $donneperation = array();
            $donneperation[0] = "";

            /**
             * Modifie ce 23.03.2016
             * evolution eINFO
             */
            if ($lecpte != null && ($lecpte->getTypeCompte()->getId() == 3)) {
                $i = 1;
            } elseif ($lecpte != null && ($lecpte->getTypeCompte()->getId() == 1 || $lecpte->getTypeCompte()->getId() == 2 || $lecpte->getTypeCompte()->getId() == 4 )) {
// comment� 29/11/2016
// 				$donneperation[0] .= preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#","$4$3$2","DATE")."\t"."SIGNE "."MONTANT"."\t\t"."DATE VALEUR"."\t"."LIBELLE"."\t\t"."NUMERO MVT"."\r\n";
                $i = 1;
            }

            $separateur = chr(9);

            if ($lecpte != null && $lecpte->getTypeCompte()->getId() == 3) {
                // comment� 29
                //   $donneperation[0] .= preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#","$4$3$2","DATE")."\t"."SIGNE "."MONTANT"."\t\t"."DATE VALEUR"."\t"."LIBELLE"."\t\t"."NUMERO MVT"."\r\n";
            }

            if ($soldeDeb != null) {
                $debit = 0;
                $credit = 0;
                $datedSolde = null;
                $valeur = "";
                $datedSolde = $soldeDeb[0]['dateSolde'];
                $datedSolde = $datedSolde->format('dmy');

                if (strtolower($soldeDeb[0]['sens']) == 'd') {
                    $valeur = "(-)" . (string) $soldeDeb[0]['solde'];
                } elseif (strtolower($soldeDeb[0]['sens']) == 'c') {
                    $valeur = "(+)" . (string) $soldeDeb[0]['solde'];
                }

                if ($lecpte != null && $lecpte->getTypeCompte()->getId() == 3) {
                    $donneperation[0] .= $datedSolde . "" . $separateur . "" . $valeur . "" . $separateur . "" . " " . "" . $separateur . "SOLDE DEBUT PERIODE" . $separateur . "" . "" . "\r\n";
                }
            }

            foreach ($listeOperation as $operation) {
//                var_dump($operation['codOperation']);
//                var_dump($lecpte->getTypeCompte()->getId());exit;
                
                if ($operation['codOperation'] == '01' && $lecpte != null && ($lecpte->getTypeCompte()->getId() == 1 || $lecpte->getTypeCompte()->getId() == 4 )) {
                    $separateur = '     ';
                    $termine = '                ';
                    $firstdate = '      ';
                    if ($operation['dateValeur']->format('dmy') != '3011-1') {
                        $firstdate = $operation['dateValeur']->format('dmy');
                    }

                    if ($operation['codOperation'] == '01') {


                        $donneperation[0].=str_replace("\n", '', $operation['codOperation']) . $operation['codeBnq'] . $operation['res21'] . $operation['codeGui'] . $operation['codeDevise'] .
                                $operation['virgul'] . $operation['monori'] . $operation['numeroCompte'] . '  ' .
                                preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#", "$4$3$2", $operation['dateOperation']->format('dmy')) .
                                $operation['motrej'] . $firstdate . $operation['libOperation'] . $operation['res22'] .
                                $operation['noecri'] . $operation['exocom'] . $operation['ind'] . $operation['mttafbw'] . $termine . "\r\n";
                    } else {
                        $donneperation[0] .= $operation['codOperation'] . $operation['codeBnq'] . $operation['res21'] . $operation['codeGui'] . $operation['codeDevise'] .
                                $operation['virgul'] . $operation['monori'] . $operation['numeroCompte'] . '  ' .
                                preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#", "$4$3$2", $operation['dateOperation']->format('dmy')) .
                                $operation['motrej'] . $firstdate . $operation['libOperation'] . $operation['res22'] .
                                $operation['noecri'] . $operation['exocom'] . $operation['ind'] . $operation['mttafbw'] . $separateur . $operation['numeroMvt'] . "\r\n";
                    }


                } elseif ($operation['codOperation'] == '01' && $lecpte != null && ($lecpte->getTypeCompte()->getId() == 2 )) {

                    $firstdate = '      ';
                    if ($operation['dateValeur']->format('dmy') != '3011-1') {
                        $firstdate = $operation['dateValeur']->format('dmy');
                    }


                    $donneperation[0] = $operation['codOperation'] . $operation['codeBnq'] . $operation['codeGui'] . $operation['numeroCompte'] .
                            $operation['cdafb'] . $operation['cdcoib'] . preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#", "$4$3$2", $operation['dateOperation']->format('dmy')) .
                            $operation['res13'] . $firstdate . $operation['libOperation'] . $operation['cdexo'] . $operation['mttafbw'] . $operation['res23'] . $operation['sign'] . "\r\n";
                }


                if ($operation['codOperation'] == '04') {
                    if ($lecpte != null && $lecpte->getTypeCompte()->getId() == 3) {
                        $valeur = 0;
                        if (strtolower($operation['sensOperation']) == 'd') {
                            $valeur = "(-)" . (string) $operation['montant'];
                        } elseif (strtolower($operation['sensOperation']) == 'c') {
                            $valeur = "(+)" . (string) $operation['montant'];
                        }
                        $donneperation[0] .= $operation['dateOperation']->format("dmy") . "" . $separateur . "" . $valeur . "" . $separateur . "" . $operation['dateValeur']->format("d/m/Y") . "" . $separateur . "" . $operation['libOperation'] . "" . $separateur . "" . $operation['numeroMvt'] . "\r\n";
                    } elseif ($lecpte != null && ($lecpte->getTypeCompte()->getId() == 1 || $lecpte->getTypeCompte()->getId() == 4 )) {
                        $separateur = '     ';
                        /* $donneperation[0] .= $operation['codOperation'].$operation['codeBnq'].$operation['res21'].$operation['codeGui'] .$operation['codeDevise'] .$operation['virgul'] .$operation['monori'] .$operation['numeroCompte']. 
                          $operation['codOperation'].$operation['dateOperation']->format('dmy').$separateur.$operation['dateValeur']->format('dmy').$operation['libOperation'].$operation['exocom'].$operation['ind'].
                          $operation['mttafbw'].$operation['numeroMvt']."\r\n"; */


                        $donneperation[0].=$operation['codOperation'] . $operation['codeBnq'] . $operation['res21'] . $operation['codeGui'] . $operation['codeDevise'] . $operation['virgul'] . $operation['monori'] . $operation['numeroCompte'] . $operation['codOperation'] .
                                preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#", "$4$3$2", $operation['dateOperation']->format('dmy')) . $operation['motrej'] . $operation['dateValeur']->format('dmy') . $operation['libOperation'] .
                                $operation['res22'] . $operation['noecri'] . $operation['exocom'] . $operation['ind'] . $operation['mttafbw'] . $separateur .
                                $operation['numeroMvt'] . "\r\n";
                    } elseif ($lecpte != null && ($lecpte->getTypeCompte()->getId() == 2)) {
                        $lastdate = '      ';
                        if ($operation['dateValeur']->format('dmy') != '3011-1') {
                            $lastdate = $operation['dateValeur']->format('dmy');
                        }
                        $donneperation[0].= $operation['codOperation'] . $operation['codeBnq'] . $operation['codeGui'] . $operation['numeroCompte'] .
                                $operation['cdafb'] . $operation['cdcoib'] . preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#", "$4$3$2", $operation['dateOperation']->format('dmy')) .
                                $operation['res13'] . $lastdate . $operation['libOperation'] . $operation['cdexo'] . $operation['mttafbw'] . $operation['res23'] . $operation['sign'] . "\r\n";
                    }
                    fputcsv($handle, $donneperation);
                    $i++;
                }

                if ($operation['codOperation'] == '07' && $lecpte != null && ($lecpte->getTypeCompte()->getId() == 1 || $lecpte->getTypeCompte()->getId() == 4)) {

                    $lastdate = '      ';
                    if ($operation['dateValeur']->format('dmy') != '3011-1') {
                        $lastdate = $operation['dateValeur']->format('dmy');
                    }
                    $separateur = '     ';
                    $termine = '                ';
                    if ($operation['codOperation'] == '07') { // Le cas 7 o� on a pas de num�ro de mouvement il faut completer par vide pour atteind 120
                        $donneperation[0] .= $operation['codOperation'] . $operation['codeBnq'] . $operation['res21'] . $operation['codeGui'] . $operation['codeDevise'] . $operation['virgul'] . $operation['monori'] . $operation['numeroCompte'] . '  ' .
                                preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#", "$4$3$2", $operation['dateOperation']->format('dmy')) . $operation['motrej'] . $lastdate . $operation['libOperation'] . $operation['res22'] . $operation['noecri'] . $operation['exocom'] . $operation['ind'] . $operation['mttafbw'] . $termine;
                    } else {
                        $donneperation[0] .= $operation['codOperation'] . $operation['codeBnq'] . $operation['res21'] . $operation['codeGui'] . $operation['codeDevise'] . $operation['virgul'] . $operation['monori'] . $operation['numeroCompte'] . '  ' .
                                preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#", "$4$3$2", $operation['dateOperation']->format('dmy')) . $operation['motrej'] . $lastdate . $operation['libOperation'] . $operation['res22'] . $operation['noecri'] . $operation['exocom'] . $operation['ind'] . $operation['mttafbw'] . $separateur . $operation['numeroMvt'];
                    }
                } elseif ($operation['codOperation'] == '07' && $lecpte != null && ($lecpte->getTypeCompte()->getId() == 2)) {



                    $donneperation[0].= $operation['codOperation'] . $operation['codeBnq'] . $operation['codeGui'] . $operation['numeroCompte'] .
                            $operation['cdafb'] . $operation['cdcoib'] . preg_replace("#([[:digit:]]{2})([[:digit:]]{2})-([[:digit:]]{2})-([[:digit:]]{2})#", "$4$3$2", $operation['dateOperation']->format('dmy')) .
                            $operation['res13'] . $operation['dateValeur']->format('dmy') . $operation['libOperation'] . $operation['cdexo'] . $operation['mttafbw'] . $operation['res23'] . $operation['sign'] . "\r\n";
                }
            }

            if ($soldeFin != null) {
                $debit = 0;
                $credit = 0;
                $datefSolde = null;
                $valeur = "";
                $datefSolde = $soldeFin[0]['dateSolde'];
                $datefSolde = $datefSolde->format('d/m/Y');
                if (strtolower($soldeFin[0]['sens']) == 'd') {
                    $valeur = "(-)" . (string) $soldeFin[0]['solde'];
                } elseif (strtolower($soldeFin[0]['sens']) == 'c') {
                    $valeur = "(+)" . (string) $soldeFin[0]['solde'];
                }
                if ($lecpte != null && $lecpte->getTypeCompte()->getId() == 3) {
                    $donneperation[0].= //"".$datefSolde."".$separateur.""."SOLDE FIN PERIODE " .$separateur . "" ."          ".$separateur. "" . $valeur. "" . $separateur . "\r\n";
                            $datedSolde . "" . $separateur . "" . $valeur . "" . $separateur . "SOLDE FIN PERIODE" . $separateur . "" . "" . "\r\n";
                }
            }


            $response = $this->render('utbClientBundle/Recherche/export_txt.html.twig', array('donneperation' => $donneperation), $this->response);
            $filename = "export_" . date("Y_m_d_His") . ".txt";
            $response->headers->set('Content-Type', 'text/txt');
            $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);


            return $response;
        }
        // Quand le telechargement doit etre en fichier pdf
        elseif ($typefichier == 4) {
            ob_start();
            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle/ParamSysteme")->find(4);

            if ($path != null) {
                $lien = $path->getValeur();
            } else {
                $path = '';
            }

            $t_array = array(0 => array(
                    'numeroCompte' => null, 'coef' => null, 'numeroMvt' => null, 'id' => null, 'idfile' => null,
                    'soldeEnLigne' => null, 'libOperation' => null, 'codOperation' => null, 'dateValeur' => null,
                    'dateOperation' => null, 'montant' => null, 'sensOperation' => null,
                )
                    );

            $listeOperation = array_merge($t_array, $listeOperation);

            $listeOperation[] = $t_array;
            $listeOperation[] = $t_array;
            $listeOperation = array_chunk($listeOperation, 48, True);


            $donneperation = $this->templating->render('utbClientBundle/Compte:operation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation, 'lien' => $lien, 'numeroCompte' => $cpte, 'soldedeb' => $soldeDeb, 'soldefin' => $soldeFin), $this->response);
            $donneperation = utf8_decode($donneperation);

            $piedpage = $this->translator->trans('compte.texte', array(), 'compte');
            $piedpage = utf8_decode($piedpage);

            $footer = ' <div style="border-top: 2px solid #555;width:755px;margin-top: 20px;">
                                        <h5 style="text-align: center;margin: 5px 0px;padding: 0px;font-size: 8px;font-weight: normal;"> '
                    . $piedpage .
                    '   </h5>                   
                                        <span style="clear:both;"></span>
                                    </div> ';

            return new Response(
                    $this->knp_snappy.pdf->getOutputFromHtml($donneperation, array(
                        /* 'ignore-load-errors'           => null, // old v0.9
                          'lowquality'                   => false,
                          'collate'                      => null,
                          'no-collate'                   => null,
                          'cookie-jar'                   => null,
                          'copies'                       => null,
                          'dpi'                          => null,
                          'extended-help'                => null, */
                        'grayscale' => false,
                        'help' => null,
                        'htmldoc' => null,
                        'image-dpi' => null,
                        'image-quality' => null,
                        'manpage' => null,
                        'margin-bottom' => 12,
                        'margin-left' => 5,
                        'margin-right' => 5,
                        'margin-top' => 6,
                        'orientation' => null,
                        'output-format' => null,
                        'page-height' => null,
                        /* 'page-size'                    => "A4",
                          'page-width'                   => null,
                          'no-pdf-compression'           => null,
                          'quiet'                        => null,
                          'read-args-from-stdin'         => null,
                          'title'                        => null,
                          'use-xserver'                  => null,
                          'version'                      => null,
                          'dump-default-toc-xsl'         => null,
                          'dump-outline'                 => null,
                          'outline'                      => null,
                          'no-outline'                   => null,
                          'outline-depth'                => null,
                          'allow'                        => null,
                          'background'                   => null,
                          'no-background'                => null,
                          'checkbox-checked-svg'         => null,
                          'checkbox-svg'                 => null,
                          'cookie'                       => null,
                          'custom-header'                => null,
                          'custom-header-propagation'    => null,
                          'no-custom-header-propagation' => null,
                          'debug-javascript'             => null,
                          'no-debug-javascript'          => null,
                          'default-header'               => null,
                          'encoding'                     => null,
                          'disable-external-links'       => null,
                          'enable-external-links'        => null,
                          'disable-forms'                => null,
                          'enable-forms'                 => null,
                          'images'                       => true,
                          'no-images'                    => null,
                          'disable-internal-links'       => null,
                          'enable-internal-links'        => null,
                          'disable-javascript'           => null,
                          'enable-javascript'            => null,
                          'javascript-delay'             => null,
                          'load-error-handling'          => null,
                          'disable-local-file-access'    => null,
                          'enable-local-file-access'     => null,
                          'minimum-font-size'            => null,
                          'exclude-from-outline'         => null,
                          'include-in-outline'           => null,
                          'page-offset'                  => null,
                          'password'                     => null,
                          'disable-plugins'              => null,
                          'enable-plugins'               => null,
                          'post'                         => null,
                          'post-file'                    => null,
                          'print-media-type'             => null,
                          'no-print-media-type'          => null,
                          'proxy'                        => null,
                          'radiobutton-checked-svg'      => null,
                          'radiobutton-svg'              => null,
                          'run-script'                   => null,
                          'disable-smart-shrinking'      => true,
                          'enable-smart-shrinking'       => null,
                          'stop-slow-scripts'            => null,
                          'no-stop-slow-scripts'         => null,
                          'disable-toc-back-links'       => null,
                          'enable-toc-back-links'        => null,
                          'user-style-sheet'             => null,
                          'username'                     => null,
                          'window-status'                => null, */
                        'zoom' => 1.04,
                        'footer-center' => null,
                        'footer-font-name' => null,
                        'footer-font-size' => 8,
                        'footer-html' => $footer,
                            /* 'footer-left'                  => null,
                              'footer-line'                  => null,
                              'no-footer-line'               => null ,
                              'footer-right'                 => null,
                              'footer-spacing'               => null,
                              'header-center'                => null,
                              'header-font-name'             => null,
                              'header-font-size'             => 8,
                              'header-html'                  => null,
                              'header-left'                  => null,
                              'header-line'                  => null,
                              'no-header-line'               => null,
                              'header-right'                 => null,
                              'header-spacing'               => null,
                              'replace'                      => null,
                              'disable-dotted-lines'         => null,
                              'cover'                        => null,
                              'toc'                          => null,
                              'toc-depth'                    => null,
                              'toc-font-name'                => null,
                              'toc-l1-font-size'             => null,
                              'toc-header-text'              => null,
                              'toc-header-font-name'         => null,
                              'toc-header-font-size'         => null,
                              'toc-level-indentation'        => null,
                              'disable-toc-links'            => null,
                              'toc-text-size-shrink'         => null,
                              'xsl-style-sheet'              => null,
                              'redirect-delay'               => null, */
                    )), 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=OPERATIONS_' . $cpte . '.pdf '
                    )
            );
        } else {
            //var_dump($listeOperation);   
            return $this->render('utbClientBundle/Recherche/rechercheOperation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation,
                        'last_page' => $last_page,
                        'previous_page' => $previous_page,
                        'current_page' => $page,
                        'next_page' => $next_page,
                        'total' => $total, 'soldedeb' => $soldeDeb, 'soldefin' => $soldeFin,
                            ), $this->response);
        }
    }

    function SupprimerCompteAction(string $locale, $idCompte, $idAbonne) {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('SupprimerCompteAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unCompte = $em->getRepository("utbClientBundle:Compte")->find($idCompte);
        $unCompte->setEtatCompte(1);
        $em->persist($unCompte);
        $em->flush();

        $this->requestStack->getCurrentRequest()Stack->getSession()->setFlash('notice', 'desactivesuccess');
        return $this->redirect($this->generateUrl('utb_client_detail_abonneadmin', array('locale' => $locale, 'id' => $idAbonne,)));
    }

    public function releveAbonneAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('releveAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        // $request = $request;
        //envoi du numéro de compte
        //$this->requestStack->getCurrentRequest()->attributes->set('', );
        $unAbonne = $em->getRepository("utbClientBundle:Abonne")->find($currentUtilID);

        //Ajout Gautier       
        $id_temporaire = 0;
        $comptesFils = array();
        if ($unAbonne instanceof Abonne && $unAbonne->getProfil()->getId() === TypeParametre::PROFIL_SOUS_ABONNE) {
            $a = $unAbonne->getIdAbonneParent();
            $comptesFil = $unAbonne->getCompteParents();
            if ($a instanceof Abonne) {
                $unAbonne = $a;
            }

            $id_temporaire = $unAbonne->getId();

            $comptesFils = explode("|", $comptesFil);
        } else {
            $id_temporaire = $unAbonne->getId();
        }

        $ids = "";
        $compteFils = array();
        if (is_array($comptesFils) && count($comptesFils) > 0) {
            foreach ($comptesFils as $id) {
                if ($id != "") {
                    $compteFils[] = "'" . $id . "'";
                }
            }
        }
        $ids = implode(',', $compteFils);
//            var_dump($ids);exit;
        // liste des comptes de l'abonné
        if ($unAbonne != null) {

//                $listeCompte = $this->getDoctrine()
//                        ->getManager()
//                        ->getRepository("utbClientBundle/Compte")
//                        ->findAllCompteAbonne($unAbonne->getId());
            $listeCompte = $this->entityManager
                    ->getRepository("utbClientBundle/Compte")
                    ->findAllCompteAbonne($id_temporaire, $ids);
        }

        $listeAnnee = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getAnneeAbonne($id_temporaire);

        return $this->render('utbClientBundle/Compte/releveCompteAbonne.html.twig', array(
                    'unAbonne' => $unAbonne,
                    'locale' => $locale,
                    'listeCompte' => $listeCompte,
                    'listeAnnee' => $listeAnnee,
                        ), $this->response);
    }

    /*
     * L'action releveAbonneAction suffit à imprimer le relevé
     */

    public function imprimeReleveAbonneAction(): Response(string $locale): Response {

        // initilaisation
        $unAbonne = null;
        $listeOperation = null;

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        //$currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];

        if (!in_array('releveAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }
        $request = $request;

        //recuppération du numéro de compte
        $cpte = $request->request->get('numCompte');
        $mois = $request->request->get('mois');
        $an = $request->request->get('annee');
        $tabMois = Array('fr' => array('01' => "Janvier", '02' => 'Février', '03' => 'Mars', '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
                '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'),
            'en' => array('01' => "January", '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'Mai', '06' => 'June',
                '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December')
        );
        $per = $mois . ' ' . $an;
        $laperiode = $tabMois[$locale][$mois] . ' ' . $an;
        $uncompte = $em->getRepository("utbClientBundle:Compte")->find($cpte);
        $gestTel = '';
        $agence = null;
        $numrib = '';

        $soldeDeb = null;
        $soldeFin = null;

        if ($uncompte != null) {
            $unAbonne = $uncompte->getAbonne();
            $listeOperation = $this->entityManager->getRepository("utbClientBundle:Compte")->getOperationsReleve($cpte, $per, $locale);
            $numrib = $uncompte->getNumRib();

            $ladeb = new \DateTime();
            $ladeb->setDate($an, $mois, 1);
            $lafin = $listeOperation[count($listeOperation) - 1]['dateOperation'];

            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, $ladeb->format('Y'), $ladeb->format('m'), 1, 0);
            $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte, $lafin->format('Y'), $lafin->format('m'), $lafin->format('d'), 3);

            $gestTel = $uncompte->getFonds()->getUtilisateur()->getNomPrenom();
            $agence = $em->getRepository("utbClientBundle:Agence")->find(substr($cpte, 0, 2));
            $fin = $lafin->format('Y-m-d');
        }

        if ($listeOperation == null) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'errornoeoperation');
            return $this->redirect($this->generateUrl('utb_client_releveabonne', ['locale' => $locale]));
        }

        if ($listeOperation[0]['idfile'] != null) {
            $chargement = $em->getRepository("utbClientBundle:Chargement")->find($listeOperation[0]['idfile']);
        }
        $periodeentete = '';
        $periodeentete = '01' . ' ' . $tabMois[$locale][$mois] . ' ' . $an . ' ' . ' - ' . ' ' . substr($fin, 8, 2) . ' ' . $tabMois[$locale][$mois] . ' ' . $an;

        $lien = '';
        $path = $this->entityManager->getRepository("utbClientBundle/ParamSysteme")->find(4);

        if ($path != null) {
            $lien = $path->getValeur();
        } else {
            $path = '';
        }

        $nbre_operation_first_page = $this->container->get->getParameter('max_operation_first_page');
        $operations_per_page = $this->container->get->getParameter('max_operation_on_page');
        $page = 1;
        $total = count($listeOperation);
        $nb_page = ceil(($total - $nbre_operation_first_page) / $operations_per_page) + 1;

        ob_start();
        $donneperation = "";

        // Gestion des formats Excellet csv
        $excelService = new PHPExcel();
        //$excelService = $this->xls.service_xls2007;
        $excelService->getProperties()
                ->setTitle("Liste des Abonne")
                ->setDescription("Liste des Abonnes");

        $excelService->getActiveSheet()->setTitle('Reunión Comercial');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->setActiveSheetIndex(0);
        // var_dump($listeOperation);exit;
        $type = $request->request->get('typefichier');
        $aux = 3;
        if ($type == 2 || $type == 3) {
            //var_dump($soldeDeb);exit;
            $excelService->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'DATE OPERATION')
                    ->setCellValue('B1', 'DEBIT')
                    ->setCellValue('C1', 'CREDIT')
                    ->setCellValue('D1', 'DATE VALEUR')
                    ->setCellValue('E1', 'LIBELLE')
                    ->setCellValue('F1', 'NUMERO MOUVEMENT')
                    ->setCellValue('G1', 'SOLDE EN LIGNE');

            if ($soldeDeb != null) {

                if (strtolower($soldeDeb[0]['sens']) == 'd') {
                    $valeur = -$soldeDeb[0]['solde'];
                } elseif (strtolower($soldeDeb[0]['sens']) == 'c') {
                    $valeur = $soldeDeb[0]['solde'];
                }

                $datedSolde = $soldeDeb[0]['dateSolde'];
                $datedSolde = $datedSolde->format('d/m/Y');
                $excelService->setActiveSheetIndex(0)
                        ->setCellValue('A2', $datedSolde)
                        ->setCellValue('B2', '')
                        ->setCellValue('C2', '')
                        ->setCellValue('D2', '')
                        ->setCellValue('E2', 'SOLDE DEBUT PERIODE')
                        ->setCellValue('F2', ' ')
                        ->setCellValue('G2', $valeur);
            }

            foreach ($listeOperation as $row) {
                if ($row['codOperation'] == '04') {
                    $datop = null;
                    $daval = null;
                    $datop = $row['dateOperation']->format("d/m/Y");
                    $datval = $row['dateValeur']->format("d/m/Y");
                    if ($datval == '30/11/-0001') {
                        $datval = null;
                    }
                    if ($row['sensOperation'] == 'C') {
                        $excelService->setActiveSheetIndex(0)
                                ->setCellValue('A' . $aux, $datop)
                                ->setCellValue('B' . $aux, 0)
                                ->setCellValue('C' . $aux, $row['montant'])
                                ->setCellValue('D' . $aux, '')       //$datval                     
                                ->setCellValue('E' . $aux, $row['libOperation'])
                                ->setCellValue('F' . $aux, $row['numeroMvt'])
                                ->setCellValue('G' . $aux, $row['soldeEnLigne']);
                    } else {
                        $excelService->setActiveSheetIndex(0)
                                ->setCellValue('A' . $aux, $datop)
                                ->setCellValue('B' . $aux, $row['montant'])
                                ->setCellValue('C' . $aux, 0)
                                ->setCellValue('D' . $aux, '')//$row['dateValeur']                            
                                ->setCellValue('E' . $aux, $row['libOperation'])
                                ->setCellValue('F' . $aux, $row['numeroMvt'])
                                ->setCellValue('G' . $aux, $row['soldeEnLigne']);
                    }
                    $excelService->setActiveSheetIndex(0);
                    $aux++;
                }
            }
        }

        if ($soldeFin != null) {

            if (strtolower($soldeFin[0]['sens']) == 'd') {
                $valeur = -$soldeFin[0]['solde'];
            } elseif (strtolower($soldeFin[0]['sens']) == 'c') {
                $valeur = $soldeFin[0]['solde'];
            }

            $datefSolde = null;
            $datefSolde = $soldeFin[0]['dateSolde'];
            $datefSolde = $datefSolde->format('d/m/Y');
            $excelService->setActiveSheetIndex(0)
                    ->setCellValue('A' . $aux, $datefSolde)
                    ->setCellValue('B' . $aux, '')
                    ->setCellValue('C' . $aux, '')
                    ->setCellValue('D' . $aux, '')
                    ->setCellValue('E' . $aux, 'SOLDE FIN PERIODE')
                    ->setCellValue('F' . $aux, '')
                    ->setCellValue('G' . $aux, $valeur);
        }
        $response = new Response();

        if ($type == 1) {

            $t_array = array(0 => array(
                    'numeroCompte' => null, 'coef' => null, 'numeroMvt' => null, 'id' => null, 'idfile' => null,
                    'soldeEnLigne' => null, 'libOperation' => null, 'codOperation' => null, 'dateValeur' => null,
                    'dateOperation' => null, 'montant' => null, 'sensOperation' => null
                )
                    );

            $listeOperation = array_merge($t_array, $listeOperation);

            $listeOperation[] = $t_array;
            $listeOperation[] = $t_array;
            //var_dump($lien);exit;
            $listeOperation = array_chunk($listeOperation, 42, True);
            //var_dump($listeOperation[0]);exit;
            //$b = $listeOperation[1];
            //$listeOperation = array('0'=>$listeOperation[0],'1'=>$b );

            $donneperation = null;
            $donneperation = $this->renderView('utbClientBundle/Compte:apercuReleve.html.twig', array('telGest' => $gestTel, 'agence' => $agence,
                'unAbonne' => $unAbonne, 'lien' => $lien, 'locale' => $locale, 'listeOperation' => $listeOperation,
                'periodentete' => $periodeentete, 'operations_per_page' => $operations_per_page, 'page' => $page,
                'nb_page' => $nb_page, 'nbre_operation_first_page' => $nbre_operation_first_page, 'numrib' => $numrib,
                'laperiode' => $laperiode, 'chargement' => $chargement, 'debsolde' => $soldeDeb, 'finsolde' => $soldeFin), $this->response);

            $donneperation = utf8_decode($donneperation);

            $piedpage = $this->translator->trans('compte.texte', array(), 'compte');
            $piedpage = utf8_decode($piedpage);

            $footer = ' <div style="border-top: 2px solid #555;width:755px;margin-top: 20px;">
                                        <h5 style="text-align: center;margin: 5px 0px;padding: 0px;font-size: 8px;font-weight: normal;"> '
                    . $piedpage .
                    '   </h5>                   
                                        <span style="clear:both;"></span>
                                    </div> ';
            return new Response(
                    $this->knp_snappy.pdf->getOutputFromHtml($donneperation, array(
                        /* 'ignore-load-errors'           => null, // old v0.9
                          'lowquality'                   => false,
                          'collate'                      => null,
                          'no-collate'                   => null,
                          'cookie-jar'                   => null,
                          'copies'                       => null,
                          'dpi'                          => null,
                          'extended-help'                => null, */
                        'grayscale' => false,
                        'help' => null,
                        'htmldoc' => null,
                        'image-dpi' => null,
                        'image-quality' => null,
                        'manpage' => null,
                        'margin-bottom' => 12,
                        'margin-left' => 5,
                        'margin-right' => 5,
                        'margin-top' => 6,
                        'orientation' => null,
                        'output-format' => null,
                        'page-height' => null,
                        /* 'page-size'                    => "A4",
                          'page-width'                   => null,
                          'no-pdf-compression'           => null,
                          'quiet'                        => null,
                          'read-args-from-stdin'         => null,
                          'title'                        => null,
                          'use-xserver'                  => null,
                          'version'                      => null,
                          'dump-default-toc-xsl'         => null,
                          'dump-outline'                 => null,
                          'outline'                      => null,
                          'no-outline'                   => null,
                          'outline-depth'                => null,
                          'allow'                        => null,
                          'background'                   => null,
                          'no-background'                => null,
                          'checkbox-checked-svg'         => null,
                          'checkbox-svg'                 => null,
                          'cookie'                       => null,
                          'custom-header'                => null,
                          'custom-header-propagation'    => null,
                          'no-custom-header-propagation' => null,
                          'debug-javascript'             => null,
                          'no-debug-javascript'          => null,
                          'default-header'               => null,
                          'encoding'                     => null,
                          'disable-external-links'       => null,
                          'enable-external-links'        => null,
                          'disable-forms'                => null,
                          'enable-forms'                 => null,
                          'images'                       => true,
                          'no-images'                    => null,
                          'disable-internal-links'       => null,
                          'enable-internal-links'        => null,
                          'disable-javascript'           => null,
                          'enable-javascript'            => null,
                          'javascript-delay'             => null,
                          'load-error-handling'          => null,
                          'disable-local-file-access'    => null,
                          'enable-local-file-access'     => null,
                          'minimum-font-size'            => null,
                          'exclude-from-outline'         => null,
                          'include-in-outline'           => null,
                          'page-offset'                  => null,
                          'password'                     => null,
                          'disable-plugins'              => null,
                          'enable-plugins'               => null,
                          'post'                         => null,
                          'post-file'                    => null,
                          'print-media-type'             => null,
                          'no-print-media-type'          => null,
                          'proxy'                        => null,
                          'radiobutton-checked-svg'      => null,
                          'radiobutton-svg'              => null,
                          'run-script'                   => null,
                          'disable-smart-shrinking'      => true,
                          'enable-smart-shrinking'       => null,
                          'stop-slow-scripts'            => null,
                          'no-stop-slow-scripts'         => null,
                          'disable-toc-back-links'       => null,
                          'enable-toc-back-links'        => null,
                          'user-style-sheet'             => null,
                          'username'                     => null,
                          'window-status'                => null, */
                        'zoom' => 1.04,
                        'footer-center' => null,
                        'footer-font-name' => null,
                        'footer-font-size' => 8,
                        'footer-html' => $footer,
                            /* 'footer-left'                  => null,
                              'footer-line'                  => null,
                              'no-footer-line'               => null ,
                              'footer-right'                 => null,
                              'footer-spacing'               => null,
                              'header-center'                => null,
                              'header-font-name'             => null,
                              'header-font-size'             => 8,
                              'header-html'                  => null,
                              'header-left'                  => null,
                              'header-line'                  => null,
                              'no-header-line'               => null,
                              'header-right'                 => null,
                              'header-spacing'               => null,
                              'replace'                      => null,
                              'disable-dotted-lines'         => null,
                              'cover'                        => null,
                              'toc'                          => null,
                              'toc-depth'                    => null,
                              'toc-font-name'                => null,
                              'toc-l1-font-size'             => null,
                              'toc-header-text'              => null,
                              'toc-header-font-name'         => null,
                              'toc-header-font-size'         => null,
                              'toc-level-indentation'        => null,
                              'disable-toc-links'            => null,
                              'toc-text-size-shrink'         => null,
                              'xsl-style-sheet'              => null,
                              'redirect-delay'               => null, */
                    )), 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=' . "Releve_" . $cpte . "_" . $mois . $an . ".pdf"
                    )
            );
        } elseif ($type == 2) {
            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.xls');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } elseif ($type == 3) {
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.csv');
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } elseif ($type == 4) {
            $aux = 0;
            $handle = fopen('php://memory', 'r+');
            $header = array();
            $donneperation = array();
            $separateur = chr(9);
            $i = 1;
            $donneperation = null;

            if ($soldeFin != null) {
                $debit = 0;
                $credit = 0;
                $datefSolde = null;
                $valeur = "";
                $datefSolde = $soldeFin[0]['dateSolde'];
                $datefSolde = $datefSolde->format('d/m/Y');
                if (strtolower($soldeFin[0]['sens']) == 'd') {
                    $valeur = "(-)" . (string) $soldeFin[0]['solde'];
                } elseif (strtolower($soldeFin[0]['sens']) == 'c') {
                    $valeur = "(+)" . (string) $soldeFin[0]['solde'];
                }
                $donneperation[0] = "" . $datefSolde . "" . $separateur . "" . $valeur . "" . $separateur . "          " . $separateur . "" . "SOLDE FIN PERIODE " . "\r\n";
                ;
            }

            foreach ($listeOperation as $operation) {
                if ($operation['codOperation'] == '04') {

                    $valeur = 0;
                    if (strtolower($operation['sensOperation']) == 'd') {
                        $valeur = "(-)" . (string) $operation['montant'];
                    } elseif (strtolower($operation['sensOperation']) == 'c') {
                        $valeur = "(+)" . (string) $operation['montant'];
                    }

                    $donneperation[$i] = "" . $operation['dateOperation']->format("d/m/Y") . "" . $separateur . "" . $valeur . "" . $separateur . "" . $operation['dateValeur']->format("d/m/Y") . "" . $separateur . "" . $operation['libOperation'] . "" . $separateur . "\r\n";
                    $i++;
                }
            }

            if ($soldeDeb != null) {
                $datedSolde = null;
                $valeur = "";
                $datedSolde = $soldeDeb[0]['dateSolde'];
                $datedSolde = $datedSolde->format('d/m/Y');

                if (strtolower($soldeDeb[0]['sens']) == 'd') {
                    $valeur = "(-)" . (string) $soldeFin[0]['solde'];
                } elseif (strtolower($soldeDeb[0]['sens']) == 'c') {
                    $valeur = "(+)" . (string) $soldeFin[0]['solde'];
                }

                $donneperation[$i] = "" . $datedSolde . "" . $separateur . "" . $valeur . "" . $separateur . "          " . $separateur . "" . "SOLDE DEBUT PERIODE " . "\r\n";
                ;
            }

            $response = $this->render('utbClientBundle/Recherche/export_txt.html.twig', array('donneperation' => $donneperation), $this->response);
            $filename = "export_" . date("Y_m_d_His") . ".txt";
            $response->headers->set('Content-Type', 'text/txt');
            $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
            return $response;
        }
    }

    public function infoUtilisateur(): Response($em, $authManager, $currentConnete, $user, string $locale): Response {
        //$currentConnete = $authManager->getFlash("utb_client_data");

        if (isset($currentConnete["id_abonne"]) && $currentConnete["id_abonne"] != "") {
            $id_abonne = $currentConnete["id_abonne"];
            $type_user = $currentConnete["type_user"];
            $nomPrenom = $currentConnete["nomPrenom_abonne"];
            $profil = $currentConnete["profil_abonne"];
            $last_connexion = $currentConnete["last_connexion"];
            $listeActions = $currentConnete["listeActions_abonne"];
            $subabonne = $currentConnete["sousAbonne"];

            $maxIdleTime = $this->container->get->getParameter('maxIdleTime');
            $session = $this->requestStack->getCurrentRequest()Stack->getSession();
            if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {
                /*                 * *******  Maj historique ********** */
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

                $_SESSION["utb_client_data"] = array();
            }
        } else {
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        //transformation de $type_user en variable globale
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale); // gautier 404
        $this->requestStack->getCurrentRequest()->attributes->set('id_abonne', $id_abonne);
        $this->requestStack->getCurrentRequest()->attributes->set('type_user', $type_user);
        $this->requestStack->getCurrentRequest()->attributes->set('nomPrenom', $nomPrenom);
        $this->requestStack->getCurrentRequest()->attributes->set('profil', $profil);
        $this->requestStack->getCurrentRequest()->attributes->set('last_connexion', $last_connexion);
        $this->requestStack->getCurrentRequest()->attributes->set('listeActions', $listeActions);
        $this->requestStack->getCurrentRequest()->attributes->set('sousAbonne', $subabonne);



        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuUtil($em, $id_abonne);
        $this->requestStack->getCurrentRequest()->attributes->set('nbreluutil', $nbrelu);
        //Info non lu abonne
        $listeMessageNonLu = $this->message.Manager;
        $nbrelu = $listeMessageNonLu->getNombreMsgNonLuAbonne($em, $id_abonne);

        $this->requestStack->getCurrentRequest()->attributes->set('nbrelu', $nbrelu);

        $this->requestStack->getCurrentRequest()->attributes->set('type_user', $user);
    }

    public function remplirMoisAnneeAction(): Response(): Response {
        $em = $this->entityManager;
        $request = $request;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        //$currentConnete = $authManager->getFlash("utb_client_data");
        //$listeActions = $currentConnete["listeActions_abonne"];

        $unAbonne = $em->getRepository("utbClientBundle:Abonne")->find($currentUtilID);

        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax
            $annee = $request->request->get('annee');
            $compte = $request->request->get('compte');


            //Ajout Gautier / Parfaire par Jérémie      
            $id_temporaire = 0;
            if ($unAbonne instanceof Abonne && $unAbonne->getProfil()->getId() === TypeParametre::PROFIL_SOUS_ABONNE) {
                $a = $unAbonne->getIdAbonneParent();

                if ($a instanceof Abonne) {
                    $unAbonne = $a;
                }

                $id_temporaire = $unAbonne->getId();
            } else {
                $id_temporaire = $unAbonne->getId();
            }

            if ($annee != null) {
//                $listemois = $em->getRepository('utbClientBundle:Operation')->getMoisdeAnnee($annee,$compte,$currentUtilID);
                $listemois = $em->getRepository('utbClientBundle:Operation')->getMoisdeAnnee($annee, $compte, $id_temporaire);

                $response = new Response();
                $listemois = json_encode(array('mois' => $listemois));
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent($listemois);
                return $response;
            }
        }

        return new Response("Nonnn ....");
    }

    public function remplirAnneeAction(): Response(): Response {

        $em = $this->entityManager;
        $request = $request;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification

        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant

        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax
            //$annee = $request->request->get('annee');
            $compte = $request->request->get('compte');


            $unAbonne = $em->getRepository("utbClientBundle:Abonne")->find($currentUtilID);

            //Ajout Gautier / Parfaire par Jérémie      
            $id_temporaire = 0;
            if ($unAbonne instanceof Abonne && $unAbonne->getProfil()->getId() === TypeParametre::PROFIL_SOUS_ABONNE) {
                $a = $unAbonne->getIdAbonneParent();

                if ($a instanceof Abonne) {
                    $unAbonne = $a;
                }

                $id_temporaire = $unAbonne->getId();
            } else {
                $id_temporaire = $unAbonne->getId();
            }


//                $listeannee = $em->getRepository('utbClientBundle:Compte')->getAnneePresentAbonne($currentUtilID,$compte);                
            $listeannee = $em->getRepository('utbClientBundle/Compte')->getAnneePresentAbonne($id_temporaire, $compte);
            $response = new Response();
            $listeannee = json_encode(array('annee' => $listeannee));
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($listeannee);
            return $response;
        }

        return new Response("Nonnn ....");
    }

    public function enteteAction(): Response(string $locale): Response {

        return $this->render('utbClientBundle/Compte/ApercuReleveHeader.html.twig', array(
                        ), $this->response);
    }

}
