<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Compte;
use App\Entity\AbonneCompteType;
use App\Entity\CompteType;
use App\Entity\DossierType;
use App\Entity\AdresseIp;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;

use PHPExcel;
use PHPExcel_IOFactory;

/**
 * 
 * CompteController pour la gestion des Comptes
 * 
 * Cette methode permet de verifier le droit de l'utilisateur avant d'effectuer une action dans une methode
 * 
 * 
 * $em = $this->entityManager;
 * $AccessControl = $this->utb_admin.AccessControl;
 * $checkAcces = $AccessControl->verifAcces($em, 'ajoutCompteAction', $this->container->get);
 * 
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
class OperationController extends AbstractController
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


    private $response ;
    public function __construct() {
        $this->response = new Response;
        $this->response->headers->addCacheControlDirective('no-cache', true);
        $this->response->headers->addCacheControlDirective('max-age', 0);
        $this->response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response->headers->addCacheControlDirective('no-store', true);
    } 

    /**
     *  Methode qui s'occupe de l'ajout d'un  Compte
     * 
     * $unCompte : Un objet de la classe Compte
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un Compte (ajoutCompte.html.twig)
     * 
     */
    public function telechargerAction(): Response(): Response {
       // $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        //$this->infoUtilisateur($em,$authManager,$currentConnete,'abonne',$locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('telechargerAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $request;
        $type = $request->request->get('typefichier');

        $excelService = new PHPExcel();
        //$excelService = $this->xls.service_xls2007;
        $excelService->getProperties()
                ->setTitle("Liste des Abonne")
                ->setDescription("Liste des Abonne");

        $excelService->getActiveSheet()->setTitle('Reunión Comercial');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->setActiveSheetIndex(0);

        //create the response

        $excelService->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Date Opération')
                ->setCellValue('B1', 'Débit')
                ->setCellValue('C1', 'Crédit')
                ->setCellValue('D1', 'Date Valeur')
                ->setCellValue('E1', 'Libelle')
                ->setCellValue('F1', 'Numero mouvement');


        // $em = $this->entityManager;

        $listeOperation = $this->entityManager
                ->getRepository("utbClientBundle:Operation")
                ->getListeOperations($type, $limit, $deb, $fin, $cpte, $idabonne, $mttde, $mtta, $libop, $sens);

        $aux = 2;
        foreach ($listeOperation as $row) {




            if ($row['sensOperation'] == 0) {
                $excelService->setActiveSheetIndex(0)
                        ->setCellValue('A' . $aux, $row['dateOperation'])
                        ->setCellValue('B' . $aux, $row['montant'])
                        ->setCellValue('C' . $aux, 0)
                        ->setCellValue('D' . $aux, $row['dateValeur'])
                        ->setCellValue('E' . $aux, $row['libOperation'])
                        ->setCellValue('F' . $aux, $row['id']);
            } else {
                $excelService->setActiveSheetIndex(0)
                        ->setCellValue('A' . $aux, $row['dateOperation'])
                        ->setCellValue('B' . $aux, 0)
                        ->setCellValue('C' . $aux, $row['montant'])
                        ->setCellValue('D' . $aux, $row['dateValeur'])
                        ->setCellValue('E' . $aux, $row['libOperation'])
                        ->setCellValue('F' . $aux, $row['id']);
            }


            ;


            // Set active sheet index to the first sheet
            $excelService->setActiveSheetIndex(0);
            $aux++;
        };
        $response = new Response();
        if ($type == 1) {

            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.csv');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } elseif ($type == 2) {
            //$response = $excelService->getResponse();
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.csv');
            //$response->prepare();
            $response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($excelService, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } elseif ($type == 3) {
            
        }
    }

    public function listeOperByCompteAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        //$this->infoUtilisateur($em,$authManager,$currentConnete,'abonne',$locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged()) {
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));
        }

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('listeOperByCompteAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $request = $request;

        //pr la recherche

        $compte = strtolower($request->request->get('compte'));

        /* $nomprenom=strtolower($request->request->get('nomprenom'));

          $username=strtolower($request->request->get('username'));

          $gestionnaire=strtolower($request->request->get('gestionnaire')); */



        //var_dump($gestionnaire);
        $listeAbonne = $this->entityManager
                ->getRepository("utbClientBundle/Compte")
                ->getListeOperations(0, 0, null, null, $compte, $idabonne, $mttde, $mtta, $libop, $sens);

        return $this->render('utbClientBundle/Abonne/listeAbonne.html.twig', array('listeAbonne' => $listeAbonne, 'locale' => $locale, 'abonneid' => $abonneid,), $this->response);
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
                /*         * *******  Maj historique ********** */
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

}