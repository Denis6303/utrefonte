<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Abonne;
use App\Entity\Operation;
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
use \HTML2PDF;
use \utb\ClientBundle\Types\TypeParametre;

class RechercheController extends AbstractController
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
     * Methode permettant de rechercher sur le site - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $motcle: Mot cle saisie
     * 
     * @param <string>  $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return sur le twig resultatRecherche.html.twig
     *  
     */
    public function rechercheAbonneAction(): Response(string $locale): Response {

        $em = $this->entityManager;
        /** $authManager = $this->Auth.Manager;//on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
          $this->requestStack->getCurrentRequest()->setLocale($locale);
          //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
          if(!$authManager->isLogged())
          return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

          $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
          $currentConnete = $authManager->getFlash("utb_client_data");
          //$this->infoUtilisateur($em,$authManager,$currentConnete,'abonne',$locale);
          $listeActions = $currentConnete["listeActions_abonne"];
          //var_dump($currentConnete["listeActions_abonne"]);exit;

          if ( !in_array('rechercheAbonneAction', $listeActions) ){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */
        $idprofilgestionnaire = $this->container->get->getParameter('idgestionnaire');

        $listeGestionnaire = $this->entityManager
                ->getRepository("utbClientBundle:Utilisateur")
                ->findAllGestionnaireByLocale($idprofilgestionnaire, $locale);


        return $this->render('utbClientBundle/Recherche/rechercheAbonne.html.twig', array(
                    'locale' => $locale, 'listeGestionnaire' => $listeGestionnaire
        ), $this->response);
    }

    public function rechercheOperationAction(): Response($idCompte, $post, $page, $idAbonne, string $locale): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        //$this->infoUtilisateur($em,$authManager,$currentConnete,'abonne',$locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('rechercheOperationAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $listeCompte = $this->entityManager
                ->getRepository("utbClientBundle/Compte")
                ->findAllCompteAbonne($idAbonne);

        return $this->render('utbClientBundle/Recherche/rechercheOperation.html.twig', array('page' => $page,
                    'locale' => $locale, 'idCompte' => $idCompte, 'idAbonne' => $idAbonne, 'listeCompte' => $listeCompte,'post' => $post,
        ), $this->response);
    }

    public function apercuRechercheAction(): Response(): Response {

        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification
        //on verifie si la personne est connectée. si elle ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));

        // $this->requestStack->getCurrentRequest()->setLocale($locale);

        return $this->render('utbClientBundle/Recherche/apercuRecherche.html.twig', $this->response);
    }

    public function rechercheOperationCompteAction(): Response($idCompte, $idAbonne, string $locale): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        //$this->infoUtilisateur($em,$authManager,$currentConnete,'abonne',$locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('rechercheOperationCompteAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        return $this->render('utbClientBundle/Recherche/rechercheOperationCompte.html.twig', array(
                    'locale' => $locale, 'idCompte' => $idCompte, 'idAbonne' => $idAbonne,
        ), $this->response);
    }

    public function rechercheOperationAdminAction(): Response($imprimer, $afficher,$post,$page,string $locale,$cpte,$deb,$fin,$mttde,$mtta,$sens): Response {
        
        //$session = $request->getSession();
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;
        global $listeOperation, $articles_per_page, $last_page, $previous_page, $next_page, $total ;
        
        /* if ( !in_array('rechercheOperationAdminAction', $listeActions) ){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */
        
        $request = $request;
        $excelService = new PHPExcel();
        //$excelService = $this->xls.service_xls2007;
        $excelService->getProperties()
                ->setTitle("Liste des Abonnes")
                ->setDescription("Liste des Abonnes");

        $excelService->getActiveSheet()->setTitle('Reunion Commercial');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->setActiveSheetIndex(0);

        //create the response

        $excelService->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Date Opération')
                ->setCellValue('B1', 'Numero mouvement')
                ->setCellValue('C1', 'Libélle')
                ->setCellValue('D1', 'Date Valeur')
                ->setCellValue('E1', 'Débit')
                ->setCellValue('F1', 'Crédit');

        $typefichier = $request->request->get('typefichier');

        //$request->request->get('numCompte')    
        
        if ($request->getMethod()=='POST'){            
            
            $cpte = strtolower($request->request->get('numCompte'));
            $this->requestStack->getCurrentRequest()->attributes->set('compte', $cpte);
            // $session->set('s_cpte', $cpte);
            //$s_cpte = $session->get('s_cpte');

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
            
        }// else $imprimer == 1;
        /* total des resultats */ 
        
        /*$articles_per_page=0;
        $last_page=0;
        $previous_page=0;
        $next_page=0;$total=0; */
        //var_dump($cpte);exit;
       if($imprimer == 1 || $typefichier == 1 || $typefichier == 2 || $typefichier == 3 || $typefichier == 4 || $sens!=''  || $mtta!='' || $mttde!='' || $cpte!='' || $fin!='' || $deb!='' ){
            $total = $em->getRepository("utbClientBundle:Compte")->getTotalOperationsAdmin(1,$deb, $fin, $cpte, $mttde, $mtta, $sens);

            //$articles_per_page = $this->container->get->getParameter('max_operations_on_listepage');
            $articles_per_page = $this->container->get->getParameter('max_operations_on_listepage');
            $last_page = ceil($total / $articles_per_page);
            $previous_page = $page > 1 ? $page - 1 : 1;
            $next_page = $page < $last_page ? $page + 1 : $last_page;

            $listeOperation = $this->entityManager
                    ->getRepository("utbClientBundle:Compte")
                    ->getListeOperationsAdmin($post,$deb, $fin, $cpte, $mttde, $mtta, $sens, $total, $page, $articles_per_page);
        }

        //var_dump($total);var_dump(count($listeOperation));exit;

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
            // $lien = __DIR__ . '/../../../../web/';
            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle:ParamSysteme")->find(4);

            if ($path != null) $lien = $path->getValeur();
            return $this->render('utbClientBundle/Recherche/apercuRecherche.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation,'lien'=>$lien), $this->response);
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
          
                $separateur = ";";
                foreach ($listeOperation as $operation) {
                     $donneperation[$i] = "" . $operation['dateOperation']->format("d/m/Y") . "" . $separateur . "" . $operation['numeroMvt']  . "" . $separateur . "" . $operation['libOperation']  . "" . $separateur . "" . $operation['dateValeur']->format("d/m/Y") . "" . $separateur . "" . $operation['montant'] . "\r\n";
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
            ));*/
            $response = $this->render('utbClientBundle/Recherche/export_txt.html.twig', array('donneperation' => $donneperation), $this->response);
            $filename = "export_".date("Y_m_d_His").".txt";
            $response->headers->set('Content-Type', 'text/txt');
            $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);


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
            $donneperation.="</table>";*/
            
            //$lien = __DIR__ . '/../../../../web/';
            
            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle/ParamSysteme")->find(4);

            if ($path != null) $lien = $path->getValeur();
            
            $donneperation = $this->templating->render('utbClientBundle:Compte:operation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation,'lien'=>$lien,'numeroCompte'=>$idCompte), $this->response);

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
            return $this->render('utbClientBundle/Recherche/rechercheOperation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation,
                        'post' => $post,
                        'last_page' => $last_page,
                        'previous_page' => $previous_page,
                        'current_page' => $page,
                        'next_page' => $next_page,
                        'total' => $total,
                        'afficher' => $afficher,
                        'cpte'=>$cpte,'datedeb'=>$deb,'datefin'=>$fin,'mttde'=>$mttde,'mttap'=>$mtta,'sens'=>$sens
            ), $this->response);
        }
    }

    public function exporterOperationAdminAction(): Response(string $locale, $imprimer, $cpte, $deb, $fin, $mttde, $mtta, $sens): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];

        /*   if ( !in_array('exporterOperationAdminAction', $listeActions) ){
          $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
          return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
          } */
        $request = $request;
        $excelService = new PHPExcel();
        //$excelService = $this->xls.service_xls2007;
        $excelService->getProperties()
                ->setTitle("Liste des Abonne")
                ->setDescription("Liste des Abonnes");

        $excelService->getActiveSheet()->setTitle('Reunión Comercial');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->setActiveSheetIndex(0);
        $typefichier = $request->request->get('typefichier');
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

            if ($request->getMethod()=='POST'){
                
                  $this->requestStack->getCurrentRequest()->attributes->set('compte', $cpte);

                  $this->requestStack->getCurrentRequest()->attributes->set('datedebut', $deb);

                  $this->requestStack->getCurrentRequest()->attributes->set('datefin', $fin);

                  $this->requestStack->getCurrentRequest()->attributes->set('mttde', $mttde);

                  $this->requestStack->getCurrentRequest()->attributes->set('mtta', $mtta);

                  $this->requestStack->getCurrentRequest()->attributes->set('sens', $sens);
            }
        //var_dump($sens);var_dump($mttde);var_dump($fin);var_dump($deb);var_dump($cpte);var_dump($mtta);
        //exit;

        /* total des resultats */
        $total = $em->getRepository("utbClientBundle:Compte")->getTotalOperationsAdmin(1,$deb, $fin, $cpte, $mttde, $mtta, $sens);
        $page = 1;
        $articles_per_page = 10000;
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;
        //var_dump($deb);
        
       $sidetailcpte= $request->request->get('sidetailcpte');
        if ($sidetailcpte != 1){
            $listeOperation = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getListeOperationsAdmin(1,$deb, $fin, $cpte, $mttde, $mtta, $sens, $total, $page, $articles_per_page);
        }
       $soldeDeb = null;$soldeFin = null; $dateFin =null; $dateDeb =null;
        
            if ($deb !=0 && strlen($deb)>9 ){
                $dateDeb = new \Datetime(); 
                $dateDeb->setDate(substr($deb,6,4),substr($deb,3,2),substr($deb,0,2)); 
            }
            $test = 0;

            if ($fin !=0 && strlen($fin)>9 ){
                $dateFin = new \Datetime(); 
                $dateFin->setDate(substr($fin,6,4),substr($fin,3,2),substr($fin,0,2));
                $test =  $em->getRepository("utbClientBundle:Compte")->getSiExisteOp($cpte,$dateFin->format('Y-m-d'));
            }         

            if ($dateDeb == null ){
                $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,4);
            }else{
                $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,$dateDeb->format('Y'),$dateDeb->format('m'),$dateDeb->format('d'),2);    
            }   

            $lecpte = $em->getRepository("utbClientBundle:Compte")->find($cpte);
        
            if ( $lecpte != null && ($lecpte->getTypeCompte()->getId() == 1 || $lecpte->getTypeCompte()->getId() == 2) ){
                 $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldeAfbw2($lecpte->getNumeroCompte());
            }
            
            if ($dateFin == null ){
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,5);
            }else{
                if ($test == 0){
                    $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,5);//$cpte,$dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'),2);
                } else{
                    $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,$dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'),3);
                }    
            }        
        $unAbonne = $this->entityManager
                ->getRepository("utbClientBundle/Abonne")
                ->findOneByLocale($currentUtilID, $locale);
        $aux = 3;

        if ($soldeDeb !=null){
            //$debit =0; $credit =0;
            $datedSolde =null; $valeur =0;
            $datedSolde = $soldeDeb[0]['dateSolde'];
            $datedSolde = $datedSolde->format('d/m/Y');
            if ($soldeDeb[0]['solde']<0) {
                $valeur =$soldeDeb[0]['solde']; 
            }else{
               $valeur=$soldeDeb[0]['solde']; 
            }
            $excelService->setActiveSheetIndex(0)
                            ->setCellValue('A' . $aux, $datedSolde)    
                            ->setCellValue('B' . $aux, 'SOLDE DEBUT PERIODE')
                            ->setCellValue('C' . $aux, '')
                            ->setCellValue('D' . $aux, '')
                            ->setCellValue('E' . $aux, '')                                                      
                            ->setCellValue('F' . $aux, '')                            
                            ->setCellValue('G' . $aux, $valeur); 
        }
        
        $dateFictive = new \DateTime(null);
        if ($typefichier == 1 || $typefichier == 2) {
            foreach ($listeOperation as $row) {
              if ($row['codOperation'] =='04' ){
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
                            ->setCellValue('B' . $aux, $row['libOperation'])
                            ->setCellValue('C' . $aux, $datval)
                            ->setCellValue('D' . $aux, 0)
                            ->setCellValue('E' . $aux, $row['montant'])
                            ->setCellValue('F' . $aux, $row['numeroMvt'])                      
                            ->setCellValue('G' . $aux, $row['soldeEnLigne']);
                } else {
                    $excelService->setActiveSheetIndex(0)
                            ->setCellValue('A' . $aux, $datop)//$row['dateOperation']
                            ->setCellValue('B' . $aux, $row['libOperation'])
                            ->setCellValue('C' . $aux, $datval)//$row['dateValeur'] 
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
                        
            if ($soldeFin !=null){
                //$debit =0; $credit =0; 
                $valeur =0; $datefSolde = null;
                $datefSolde =$soldeFin[0]['dateSolde'];
                $datefSolde = $datefSolde->format('d/m/Y');
                if ($soldeFin[0]['solde']<0) {
                   $valeur =$soldeFin[0]['solde'];// $credit =0;
                }else{
                   $valeur=$soldeFin[0]['solde']; // $debit =0; 
                }
                $excelService->setActiveSheetIndex(0)
                                ->setCellValue('A' . 2, $datefSolde)  
                                ->setCellValue('B' . 2, 'SOLDE FIN PERIODE')
                                ->setCellValue('C' . 2, '')
                                ->setCellValue('D' . 2, '')
                                ->setCellValue('E' . 2, '')  
                                ->setCellValue('F' . 2, '')                            
                                ->setCellValue('G' . 2, $valeur); 
            }
            
            $response = new Response();
        }
        // Quand le telechargement doit etre en Excel
        elseif ($imprimer == 1) {
            //$lien = __DIR__ . '/../../../../web/';
            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle/ParamSysteme")->find(4);

            if ($path != null) $lien = $path->getValeur();
            return $this->render('utbClientBundle/Recherche/apercuRecherche.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation,'lien'=>$lien,'soldedeb'=>$soldeDeb,'soldefin'=>$soldeFin), $this->response);
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
            $donneperation = null;
            $donneperation = array();
            $i = 2;
            $separateur = chr(9);
 
           $donneperation[$aux]=""."DATE"."" .$separateur . "" ."SIGNE"."" .$separateur . "" ."MONTANT". "" .$separateur . "" . "DATE VALEUR". "" . $separateur ."          ".$separateur.""."LIBELLE "."" .$separateur . "NUMERO MVT" ."\r\n"; ; 
            
           if ($soldeDeb !=null){
                $debit =0; $credit =0;$datedSolde =null;$valeur ="";
                $datedSolde = $soldeDeb[0]['dateSolde'];
                $datedSolde = $datedSolde->format('dmy');
               
                if (strtolower($soldeDeb[0]['sens'])=='d') {
                    $valeur ="(-)".(string)$soldeFin[0]['solde'];
                }elseif (strtolower($soldeDeb[0]['sens'])=='c'){
                    $valeur ="(+)".(string)$soldeFin[0]['solde']; 
                }
                
                //$donneperation[$i]="".$datedSolde. "" .$separateur . "" . $valeur. "" . $separateur ."          ".$separateur.""."SOLDE DEBUT PERIODE "."" .$separateur . $soldeDeb[0]['numMvt'] . "\r\n"; ;
                $donneperation[1]= $datedSolde ."". $separateur ."". $valeur . "" . $separateur . "" .  " " . "" . $separateur ."SOLDE DEBUT PERIODE".$separateur."" . "" . "\r\n"; 
            }
            
            foreach ($listeOperation as $operation) {
                if ($operation ['codOperation']=='04') {
                    $valeur =0;                    
                    if (strtolower($operation['sensOperation'])=='d') {
                        $valeur ="(-)".(string)$operation['montant'];
                    }elseif (strtolower($operation['sensOperation'])=='c'){
                        $valeur ="(+)".(string)$operation['montant']; 
                    }
                    
                    //$donneperation[$i] = "" . $operation['dateOperation']->format("dmy") . $separateur . $operation['dateValeur']->format("d/m/Y") . $separateur . "" . $operation['libOperation'] . "" . $separateur . "" . $valeur . "" . $separateur . $operation['numeroMvt'] . "" . "\r\n";
                    $donneperation[$i] = "" . $operation['dateOperation']->format("dmy") . "" .  $separateur . "". $valeur . "" . $separateur . "" . $operation['dateValeur']->format("d/m/Y") . "".$separateur . "" . $operation['libOperation'] . "" . $separateur . ""  .$operation['numeroMvt']. "\r\n";
                    fputcsv($handle, $donneperation);
                    $i++;
                }
            }

            if ($soldeFin !=null){
                $debit =0; $credit =0;$datefSolde =null;$valeur ="";
                $datefSolde = $soldeFin[0]['dateSolde'];
                $datefSolde = $datefSolde->format('dmy');
                    
                if (strtolower($soldeDeb[0]['sens'])=='d') {
                    $valeur ="(-)".(string)$soldeFin[0]['solde'];
                }elseif (strtolower($soldeDeb[0]['sens'])=='c'){
                    $valeur ="(+)".(string)$soldeFin[0]['solde']; 
                }
                
                $donneperation[$i]= $datedSolde ."". $separateur ."". $valeur . "" . $separateur . "" . " " ."" . $separateur .""."SOLDE FIN PERIODE"."".$separateur."" . "" . "\r\n";   
                        //"".$datefSolde. "" .$separateur .""."SOLDE FIN PERIODE ". "" . $valeur. "" . $separateur ."          ".$separateur. "\r\n"; ;
            }            
            
            
           /* rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return new Response($content, 200, array(
                'Content-Type' => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="export.txt"'
            ));*/
            $response = $this->render('utbClientBundle/Recherche/export_txt.html.twig', array('donneperation' => $donneperation), $this->response);
            $filename = "export_".date("Y_m_d_His").".txt";
            $response->headers->set('Content-Type', 'text/txt');
            $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);

            return $response; 
        }
        // Quand le telechargement doit etre en fichier pdf
        elseif ($typefichier == 4) {
            ob_start();
           /* $donneperation = "<div align=\"center\" style=\"padding: 10px;\">LISTE DES OPERATIONS DU COMPTE " . $cpte . "</div>";
            $i = 0;
            $separateur = "";
            /* $donneperation.="<div class=\"marginBottom10p nomClient\">
              ".$unAbonne['nomPrenom']."<br />
              ".$unAbonne['adresseAbonne']."
              08 BP 81531 - LOME - TOGO</div>"; 
            $donneperation.="<table width=\"100%\" style=\"border: solid 1px #ccc;\" align=\"center\">";
            $donneperation.= "<tr style=\"width:90px; background: #ccc;\">
                                           <th style=\"width:90px\">Date</th>
                                           <th style=\"width:90px\">Libellé</th>
                                           <th style=\"width:90px\">Valeur</th>
                                           <th style=\"width:90px;\">Débit</th>
                                           <th style=\"width:90px;\">Crédit</th>
                                           <th style=\"width:90px\"> N°</th>
                                      </tr>";
            //echo "<tbody>";
            foreach ($listeOperation as $operation) {

                $donneperation.="<tr>
                                                 <td>" . $operation['dateOperation']->format("d/m/Y") . "</td>
                                                 <td >" . $operation['libOperation'] . "</td>
                                                 <td >" . $operation['dateValeur']->format("d/m/Y") . "</td>";
                if ($operation['sensOperation'] == 'C') {
                    $donneperation.="<td style=\"text-align:right;\">0</td>";
                    $donneperation.="<td style=\"text-align:right;\">" . $operation['montant'] . "</td>";
                } else {
                    $donneperation.="<td style=\"text-align:right;\">" . $operation['montant'] . "</td>";
                    $donneperation.="<td style=\"text-align/right;\">0</td>";
                }
                $donneperation.= "<td >" . $operation['numeroMvt'] . "</td>
                                            </tr>";
                //var_dump($donneperation[$i]);exit;                           
                $i++;
            }
            //echo "</tbody>";
            $donneperation.="</table>";*/
            
            /*$lien = __DIR__ . '/../../../../web/';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle/ParamSysteme")->find(4);

            $pos = strpos($lien, 'src');
            $lien = substr($lien, 0, $pos - 1);
            $pos = strpos($lien, $path->getValeur());
            $lien = substr($lien, 0, $pos - 1);*/
            $lien = '';
            $path = $this->entityManager
                            ->getRepository("utbClientBundle:ParamSysteme")->find(4);

            if ($path != null) $lien = $path->getValeur();
            
            $listeOperation = array_chunk($listeOperation,43,True);
            
           //var_dump($lien);exit;
            $donneperation = $this->templating->render('utbClientBundle:Compte:operation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation,'lien'=>$lien,'numeroCompte'=>$cpte,'soldedeb'=>$soldeDeb,'soldefin'=>$soldeFin), $this->response);
            // var_dump($donneperation);
            /*
            $html2pdf = new HTML2PDF('P', 'A4', 'fr');
            $html2pdf->WriteHTML($donneperation);
            $fichier = $html2pdf->Output('Operation.pdf');
            $response = new Response();
            $response->clearHttpHeaders();
            $response->setContent(file_get_contents($fichier));
            $response->headers->set('Content-Type', 'application/force-download');
            $response->headers->set('Content-disposition', 'filename=' . $fichier);

            return $response;*/
            
            
            $donneperation = utf8_decode($donneperation);
           
           $piedpage = $this->translator->trans('compte.texte',array(),'compte');
           $piedpage = utf8_decode($piedpage);     
           
           $footer=' <div style="border-top: 2px solid #555;width:755px;margin-top: 20px;">
                                        <h5 style="text-align: center;margin: 5px 0px;padding: 0px;font-size: 8px;font-weight: normal;"> '
                                           .$piedpage.
                                    '   </h5>                   
                                        <span style="clear:both;"></span>
                                    </div> ';
            
           return new Response(
                    $this->knp_snappy.pdf->getOutputFromHtml($donneperation,
                            array(                                
                                /*'ignore-load-errors'           => null, // old v0.9
                                'lowquality'                   => false,
                                'collate'                      => null,
                                'no-collate'                   => null,
                                'cookie-jar'                   => null,
                                'copies'                       => null,
                                'dpi'                          => null,
                                'extended-help'                => null,*/
                                'grayscale'                    => false,
                                'help'                         => null,
                                'htmldoc'                      => null,
                                'image-dpi'                    => null,
                                'image-quality'                => null,
                                'manpage'                      => null,
                                'margin-bottom'                => 12,
                                'margin-left'                  => 5,
                                'margin-right'                 => 5,
                                'margin-top'                   => 6,
                                'orientation'                  => null,
                                'output-format'                => null,
                                'page-height'                  => null,
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
                                'window-status'                => null,*/
                                'zoom'                         => 1.04,
                                'footer-center'                => null,
                                'footer-font-name'             => null,
                                'footer-font-size'             => 8,
                                'footer-html'                  => $footer ,
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
                                'redirect-delay'               => null,  */                                
                            )), 200, array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename=OPERATIONS_'.$cpte.'.pdf '
                    )
            );
            
            
            
        } else {
            //var_dump($listeOperation);   
            return $this->render('utbClientBundle/Recherche/rechercheOperation.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation,
                        'last_page' => $last_page,
                        'previous_page' => $previous_page,
                        'current_page' => $page,
                        'next_page' => $next_page,
                        'total' => $total
            ), $this->response);
        }
    }

    public function rechercheOperationAbonneAction(): Response($idCompte, $idAbonne, $page, string $locale, $cpte, $deb, $fin, $mttde, $mtta, $sens): Response {

        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $currentUtilID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'abonne', $locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $listeActions = $currentConnete["listeActions_abonne"];
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('rechercheOperationAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        $unAbonne = $this->entityManager
                ->getRepository("utbClientBundle/Abonne")
                ->findOneByLocale($idAbonne, $locale);

        $listeCompte = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->findAllCompteAbonne($idAbonne);

        $request = $request;

  if ($request->getMethod()=='POST'){
        $cpte = strtolower($request->request->get('numCompte'));
        $this->requestStack->getCurrentRequest()->attributes->set('compte', $cpte);


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
        if ($deb ==null){
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,4);
        }else{
            $soldeDeb= $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,substr($deb,1,4),substr($deb,7,2),substr($deb,9,2),2);    
        }
        
        $lecpte = $em->getRepository("utbClientBundle:Compte")->find($cpte);
        
        if ( $lecpte != null && ($lecpte->getTypeCompte()->getId() == 1 || $lecpte->getTypeCompte()->getId() == 2) ){
             $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldeAfbw2($lecpte->getNumeroCompte());
        }
        
        if ($fin ==null){
            $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,5);
        }else{
            $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,substr($fin,1,4),substr($fin,7,2),substr($fin,9,2),3);
        }
  
        $listeOperation = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getListeOperationsAbonne(0, 100, $deb, $fin, $cpte, $idAbonne, $mttde, $mtta, $sens);


        return $this->render('utbClientBundle/Recherche/rechercheOperationAbonne.html.twig', array('unAbonne' => $unAbonne,
            'idAbonne' => $idAbonne, 'idCompte' => $idCompte, 'locale' => $locale, 'listeOperation' => $listeOperation,
            'listeCompte' => $listeCompte,'soldedeb'=>$soldeDeb,'soldefin'=>$soldeFin), $this->response);
    }

    public function rechercheOperationEspaceAbonneAction(): Response($page, $imprimer, $afficher,$post, string $locale,$cpte,$deb,$fin,$mttde,$mtta,$sens): Response {

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
        //var_dump($currentConnete["listeActions_abonne"]);exit;

        if (!in_array('rechercheOperationEspaceAbonneAction', $listeActions)) {
            $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_client_accueil', ['locale' => $locale]));
        }

        //Ajout Gautier
        $a = $this->entityManager->getRepository("utbClientBundle:Abonne") ->find($currentUtilID);
        
       $id_temporaire = 0;
        $comptesFils = array();
        if ($a instanceof Abonne && $a->getProfil()->getId() === TypeParametre::PROFIL_SOUS_ABONNE) {
            $id_temporaire = $a->getIdAbonneParent()->getId();
            $comptesFil = $a->getCompteParents();
            $comptesFils = explode("|", $comptesFil);
        } else {
            $id_temporaire = $a->getId();
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
        
        
        $listeCompte = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->findAllCompteAbonne($id_temporaire,$ids);

        $request = $request;

       if ($request->getMethod()=='POST'){            
            
            $cpte = strtolower($request->request->get('numCompte'));
            $this->requestStack->getCurrentRequest()->attributes->set('compte', $cpte);
            // $session->set('s_cpte', $cpte);
            //$s_cpte = $session->get('s_cpte');

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
        $total = $em->getRepository("utbClientBundle:Compte")->getTotalOperationsAbonne($deb, $fin, $cpte, $id_temporaire, $mttde, $mtta, $sens);

        //$articles_per_page = $this->container->get->getParameter('max_operations_on_listepage');
        $articles_per_page = $this->container->get->getParameter('max_operations_on_listepage');
        $last_page = ceil($total / $articles_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $listeOperation = $this->entityManager
                ->getRepository("utbClientBundle:Compte")
                ->getListeOperationsAbonne(0, 100, $deb, $fin, $cpte, $id_temporaire, $mttde, $mtta, $sens, $total, $page, $articles_per_page);
        
        /*$soldeDeb = null;$soldeFin = null;
        if ( $listeOperation !=null){
            $dateFin = $listeOperation[0]['dateOperation'] ; $dateDeb = $listeOperation[count($listeOperation)-1]['dateOperation'] ;
            //var_dump($dateDeb);var_dump($dateFin);//exit;
            if ($dateDeb ==null){
                $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,4);
            }else{
                $soldeDeb= $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,$dateDeb->format('Y'),$dateDeb->format('m'),$dateDeb->format('d'),2);    
            }
            var_dump($soldeDeb);var_dump($dateFin);
            if ($dateFin ==null){
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,5);
            }else{
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,$dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'),3);
            }
        }*/
		
        $soldeDeb = null;$soldeFin = null; $dateFin =null; $dateDeb =null;
        
		//var_dump($deb); var_dump(substr($deb,7,4));var_dump(substr($deb,1,2));var_dump(substr($deb,4,2));
		
        if ($deb !=0 && strlen($deb)>9 ){
            $dateDeb = new \Datetime(); 
            $dateDeb->setDate(substr($deb,6,4),substr($deb,3,2),substr($deb,0,2)); 
        }
        
        $test = 0;
        
        if ($fin !=0 && strlen($fin)>9 ){
            $dateFin = new \Datetime(); 
            $dateFin->setDate(substr($fin,6,4),substr($fin,3,2),substr($fin,0,2));
            $test =  $em->getRepository("utbClientBundle:Compte")->getSiExisteOp($cpte,$dateFin->format('Y-m-d'));
        }         
        
        if ($dateDeb == null ){
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,4);
        }else{
            $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,$dateDeb->format('Y'),$dateDeb->format('m'),$dateDeb->format('d'),2);    
        }   
        
        $lecpte = $em->getRepository("utbClientBundle:Compte")->find($cpte);
        
        if ( $lecpte != null && ($lecpte->getTypeCompte()->getId() == 1 || $lecpte->getTypeCompte()->getId() == 2) ){
             $soldeDeb = $em->getRepository("utbClientBundle:Compte")->getSoldeAfbw2($lecpte->getNumeroCompte());
        }
        
        if ($dateFin == null ){
            $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,5);
        }else{
            if ($test == 0){
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,0,0,0,5);//$cpte,$dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'),2);
            } else{
                $soldeFin = $em->getRepository("utbClientBundle:Compte")->getSoldDebFinPeriode($cpte,$dateFin->format('Y'),$dateFin->format('m'),$dateFin->format('d'),3);
            }    
        } 	
      
		//var_dump($dateDeb);var_dump($dateFin);var_dump($soldeDeb);var_dump($soldeFin);
        
        if ($a instanceof Abonne && $a->getProfil()->getId()===TypeParametre::PROFIL_SOUS_ABONNE) {
            $tab = array();
            
            (isset($deb)  && trim($deb) !='0')? $tab['Date-Debut'] = $deb : 1 ;
            (isset($fin) && trim($fin) !='0')? $tab['Date-Fin'] = $fin : 1 ;
            (isset($mttde) && trim($mttde) !='0')? $tab['Deb-Solde'] = $mttde : 1 ;
            (isset($mtta) && trim($mtta) !='0')? $tab['Fin-Solde'] = $mtta : 1 ;
            (isset($sens) && trim($sens) !='0')? $tab['Sens'] = $sens : 1 ;
            (isset($cpte) && trim($cpte) !='0')? $tab['Compte'] = $cpte : 1 ;
            
            (count($tab)>0)? $message='RECHERCHE/ EFFECTIVE AVEC CRITERES '.json_encode($tab) / 
                              $message='CONSULTATION :ACCES EFFECTIF AU FORMULAIRE DE RECHERCHE D\'OPERATIONS';
            
            $authManager->writeLogMessage($message,$authManager->getLogin() ,$code='105');
        }
        
        
        
        
        return $this->render('utbClientBundle/Recherche/rechercheOperationAbonne.html.twig', array('locale' => $locale, 'listeOperation' => $listeOperation, 'listeCompte' => $listeCompte, 'afficher' => $afficher,
                        'post' => $post,
                        'last_page' => $last_page,
                        'previous_page' => $previous_page,
                        'current_page' => $page,
                        'next_page' => $next_page,
                        'total' => $total,'soldedeb'=>$soldeDeb,'soldefin'=>$soldeFin,                 
                        'cpte'=>$cpte,'datedeb'=>$deb,'datefin'=>$fin,'mttde'=>$mttde,'mttap'=>$mtta,'sens'=>$sens            
            ), $this->response);
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

    
    public function rechercheHistorikAction(): Response($page, $afficher, string $locale,$connecte,$deb,$fin,$typecon,$mois,$an): Response {
        
        //$session = $request->getSession();
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        //on verifie si l'abonnee est connecté. sil ne l'est pas on le dirige à la page de connexion
        if (!$authManager->isLogged())
            return $this->redirect($this->generateUrl('utb_client_logout', ['locale' => $locale]));

        $currentID = $authManager->getCurrentId(); //comment récupérer L'id de l'abonne courrant
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $listeActions = $currentConnete["listeActions_abonne"];

        //global $listeOperation, $articles_per_page, $last_page, $previous_page, $next_page, $total ;

        $request = $request; $excelService = new PHPExcel();
 
        $excelService->getProperties()->setTitle("Liste des Abonnes")->setDescription("Liste des Abonnes");
        $excelService->getActiveSheet()->setTitle('Reunion Commercial');
        $excelService->setActiveSheetIndex(0);

        //create the response
        $nbjr = 0;
        if ($mois==0){
            $ladatedeb = new \DateTime();
            $interval = new \DateInterval('P1M'); 
            $intervalajustement = new \DateInterval('P1D'); //1 jour   
            $ladatedeb->add($interval);
            $ladatedeb->sub($intervalajustement);
            $nbjr = $ladatedeb->format('d');

            $thedeb =  new \DateTime();
            $thedeb->setDate($an, $mois, 1);
            $thedeb->add($interval);
            $thedeb->sub($intervalajustement);
            $nbjr = $thedeb->format('d');
            
        }
        
        $excelService->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Date Opération')
                ->setCellValue('B1', 'Numero mouvement')
                ->setCellValue('C1', 'Libélle')
                ->setCellValue('D1', 'Date Valeur')
                ->setCellValue('E1', 'Débit')
                ->setCellValue('F1', 'Crédit');

        $typefichier = $request->request->get('typefichier');

        if ($request->getMethod()=='POST'){            
            
            $connecte = strtolower($request->request->get('connecte'));
            $this->requestStack->getCurrentRequest()->attributes->set('connecte', $connecte);

            $deb = strtolower($request->request->get('datedebut'));
            $this->requestStack->getCurrentRequest()->attributes->set('datedebut', $deb);

            $fin = strtolower($request->request->get('datefin'));
            $this->requestStack->getCurrentRequest()->attributes->set('datefin', $fin);

            $typecon = strtolower($request->request->get('typecon'));
            $this->requestStack->getCurrentRequest()->attributes->set('typecon', $typecon);
            
            $mois = strtolower($request->request->get('mois'));
            $this->requestStack->getCurrentRequest()->attributes->set('mois', $mois);
            
            $an = strtolower($request->request->get('an'));
            $this->requestStack->getCurrentRequest()->attributes->set('an', $an);
            
			
        }
        
       /* 
        $interval = new \DateInterval('P1M'); //30 jours
        $intervalajustement = new \DateInterval('P1D'); //1 jour        
        $ladeb = new \DateTime(); $ladeb->setDate($an, $mois, 1);
        $ladeb = $ladeb->format('d-m-Y');
        $lafin = new \DateTime(); $lafin->setDate($an, $mois, 1);
        $lafin->add($interval);$lafin->sub($intervalajustement);
        $lafin = $lafin->format('d-m-Y');
        */
       /*$unedatedebut =new \DateTime();
        $deb=$unedatedebut->setDate($an, $mois, $deb);   
        $unedatefin =new \DateTime();
        $fin=$unedatefin->setDate($an, $mois, $fin);  */       
        //var_dump($connecte);var_dump($deb);var_dump($fin);var_dump($typecon);var_dump($an);var_dump($mois);
        
       if(($deb =="")){ 
          
          $deb=0;
         //var_dump($deb);exit;
       }
       if($fin==""){ 
          $fin=0;
       }
	$listehistor=null;	
        //if(  $typecon!='' ||  $connecte!='' || $fin!='' || $deb!='' ){
                // $total =10000;// $em->getRepository("utbClientBundle/Compte")->getListeHistorik($deb, $fin, $$connecte, $typecon);        
                //$articles_per_page = $this->container->get->getParameter('max_operations_on_listepage');
                $listehistor[$mois]['liste'] = $this->entityManager
                        ->getRepository("utbClientBundle:HistoriqueConnexion")
                        ->getListeHistorikTotal($connecte, $deb, $fin, $typecon,$mois,$an);
                $total=count($listehistor[$mois]['liste']);
                 
                $articles_per_page = 10000;//$this->container->get->getParameter('max_articles_on_listepage');
                $last_page = ceil($total / $articles_per_page);
                $previous_page = $page > 1 ? $page - 1 : 1;
                $next_page = $page < $last_page ? $page + 1 : $last_page;
   
                $listehistor[$mois]['liste'] = $this->entityManager
                        ->getRepository("utbClientBundle:HistoriqueConnexion")
                        ->getListeHistorik($connecte, $deb, $fin, $typecon,$mois,$an,$page,$articles_per_page);
        //}
              

            //var_dump($listeOperation);   
            return $this->render('utbClientBundle/Historique/rechercheHistorik.html.twig', array('locale' => $locale, 'listehistor' => $listehistor,
                        'last_page' => $last_page,
                        'mois'=>$mois,'nbjr'=>$nbjr,
                        'previous_page' => $previous_page,
                        'current_page' => $page, 'an'=>$an,
                        'next_page' => $next_page,'mois'=>$mois,
                        'total' => $total,
                        // 'ladeb'=>$ladeb,
                        'afficher' => $afficher,
                        // 'lafin'=>$lafin,
                        'connecte'=>$connecte,'datedeb'=>$deb,'datefin'=>$fin,'typecon'=>$typecon
            ), $this->response);
       // }
    }    
    
}

