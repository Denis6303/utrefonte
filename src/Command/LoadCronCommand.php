<?php
namespace App\Command;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use App\Entity\Chargement;
use PDO;
use PDOException;
use DateTime;

class LoadCronCommand extends ContainerAwareCommand
{
	protected function configure(){
	$this
		->setName('utb:loadfile')
		->setDescription('Charger un fichier')
		->addArgument('path', InputArgument::OPTIONAL, 'Voulez vous Charger??');
	}

	protected function execute(InputInterface $input, OutputInterface $output){
            $argument = $input->getArgument('path');            
            $path = null; $path = substr($argument,0,strlen($argument)-1);// chemin passe en argument            
            $frequence = substr($argument, strlen($argument)-1,1);// frequence des traitements
             
            $datedujour = new Datetime(); $interval3mois = new \DateInterval('P6M');
            $datedebinter = new Datetime(); $datedebinter = $datedebinter->sub($interval3mois);
            $datedebut3mois = new Datetime(); $datedebut3mois->setDate((int)$datedebinter->format('Y'),(int) $datedebinter->format('m'),1 );
            
            if ( isset($path) && is_dir($path) ) {  

                $dir_iterator = new RecursiveDirectoryIterator($path);/* definition du dossier dans lequel s'effectuera les actions */                
                $iterator = new RecursiveIteratorIterator($dir_iterator);/* Initialisation de compteur de dossier/fichier  */               
                $entityManager = $this->getContainer()->get('doctrine')->getManager();/* entity manager defini */                

                $date = new DateTime();       $interval = new \DateInterval('P1D');
                $dateVeille = new DateTime(); $dateVeille =$dateVeille->sub($interval);
                
                $datetemoin = new DateTime(); $intervalmois = new \DateInterval('P1M');
                $datetemoin->setDate((int) $date->format('Y'), (int) $date->format('m'), 1);
                $datetemoin = $datetemoin->sub($intervalmois);
                $datetemoin = $datetemoin->sub($interval);
                
                $resultat = 0; $idfile =0; $typecompte = 0; $inter = ''; /* initialisation des variables */                  
                $chemin = $this->getContainer()->getParameter('cheminfichier');/* recuperation du chemin vers le dossier monte sur le serveur mysql */
                $Utils = $this->getContainer()->get('utb_admin.utils');/* recuperation du service en charge des traces sur l'eata d'avancement du traitement */       
                $alertmanager = $this->getContainer()->get('message.Manager'); /* recuperation du service en charge d'envoyer un message aux utilisateurs admin Fichier en cas d'une erreur */                                               
                $today = new DateTime(); /* Boucle sur l'iterateur des fichiers */
                
                foreach ($iterator as $file) {         
                    $resultat = 0; $idfile =0; $typecompte = 0; $extension =''; $nomfichier =''; $typechargement = 0; $inter =''; $allname ='';/* initialisation */                    
                    $idfileAncien = 0;
                    
                   if ( $file != null && $file->getExtension() != '') {                        
                        $allname = $file->getFilename();  //recuperration respective du nom du fichier - de son extension   
                                                          //uwebj - uweba - uwebs - afbw - afbw2
                        $extension = $file->getExtension();                      
                        $inter = substr($allname,0,4);                        
                        $typechargement = substr($allname,strpos($allname,$extension)-2,1);
                        if (  strtolower(trim($inter))=='uweb'  ){
                            $typecompte = 3;                            
                            $nomfichier = 'uweb';
                        }elseif (  strtolower(trim($allname))=='afbw.txt'  ){                            
                            $typecompte = 1;
                            $nomfichier = 'afbw'; // $typechargement = 'a';
                        }elseif (  strtolower(trim($allname))=='afbw2.txt'  ){
                            $typecompte = 2;
                            $nomfichier = 'afbw2'; // $typechargement = 'a';
                        }                        
                        $nomfichier = substr($allname,0,strpos($allname,$extension)-2);// recuperation du nom sans extension du fichier                         
                   }  
                  
                   if  ( strtolower($typechargement) == strtolower($frequence) )   {                           
                        /*try {                               
                              $sqlrech = 
                              ' SELECT idchargement, statut,filedateajout '
                            . ' FROM chargement '
                            . ' WHERE idtypecompte= :typecpte and (nomfile= :filename or urlfile= :url)  ';
                            $stmt = $entityManager->getConnection()->prepare($sqlrech);                              
                            $stmt->bindValue(':typecpte', $typecompte, PDO::PARAM_INT);
                            $stmt->bindValue(':filename', $allname, PDO::PARAM_STR);
                            $stmt->bindValue(':url', $allname, PDO::PARAM_STR);                                   
                            $stmt->execute(); 
                            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);  
                            unset($stmt);
                            if ($res != null) {
                                $idfile = $res[0]['idchargement'];
                                $ladatechargfile = substr($res[0]['filedateajout'],0,10);
                                if ($ladatechargfile != null && trim($ladatechargfile) != '' ){
                                    $ladatechgfile = new DateTime();
                                    $ladatechgfile->setDate( substr($ladatechargfile,0,4) , substr($ladatechargfile,5,2), substr($ladatechargfile,8,2));
                                    $dateVeillechg = new DateTime();
                                    $dateVeillechg->setDate( substr($ladatechargfile,0,4) , substr($ladatechargfile,5,2),1); 
                                    $dateVeillechg =$dateVeillechg->add($intervalmois);
                                    $dateVeillechg =$dateVeillechg->sub($interval);
                                }
                            } else $idfile=0 ;  // recuperation fichier                                 
                            $Utils->logload('Fichier trouve '); 
                        } catch (\Symfony\Component\Form\Exception\Exception $e) {
                            $Utils->logload('Fichier non trouve');
                            $idfile = 0;
                        } */   
                         
                        if ($idfile == null ) $idfile = 0;  
                        try {                                                             
                               $sqlrech = 
                               ' INSERT INTO chargement(idtypecompte, nomfile, urlfile, statut, filedateajout,
                                                        datedeb, datefin, typechargement, archive, natureChargement)  '
                             . ' VALUES (:typecpte,:nom,:url,:statut,:datajt,:deb,:fin,:typchrg,:arch,:nat) ';
                             $stmt = $entityManager->getConnection()->prepare($sqlrech);                              
                             $stmt->bindValue(':typecpte', $typecompte, PDO::PARAM_INT);
                             $stmt->bindValue(':nom', $allname, PDO::PARAM_STR);
                             $stmt->bindValue(':url', $allname, PDO::PARAM_STR); 
                             $stmt->bindValue(':statut', 1, PDO::PARAM_INT);
                             $stmt->bindValue(':datajt', $date->format('Y-m-d H:i:s'), PDO::PARAM_STR);
                             $stmt->bindValue(':deb', $datedebut3mois->format('Y-m-d'), PDO::PARAM_STR);
                             $stmt->bindValue(':fin', $date->format('Y-m-d'), PDO::PARAM_STR);
                             if ( strtolower(trim($typechargement)) == 'a'){
                                $stmt->bindValue(':typchrg', 0, PDO::PARAM_INT);
                             }elseif ( strtolower(trim($typechargement)) == 'j'){
                                $stmt->bindValue(':typchrg', 2, PDO::PARAM_INT); 
                             }elseif ( strtolower(trim($typechargement)) == 's'){
                                $stmt->bindValue(':typchrg', 1, PDO::PARAM_INT); 
                             }  
                             $stmt->bindValue(':arch', 0, PDO::PARAM_INT);
                             $stmt->bindValue(':nat', 0, PDO::PARAM_INT);
                             $stmt->execute();unset($stmt);                                
                             $Utils->logload('INSERTION NUMERO CHARGEMENT '); 
                          } catch (\Symfony\Component\Form\Exception\Exception $e) {
                             $Utils->logload('INSERTION FAILED');
                             $idfile = 0;
                          }
                        
                         // $output->writeln(' insertion '.$sqlrech. "\n"  ); 
                          
                        try {                                                             
                              $sqlrech = 
                              ' SELECT max(idchargement) as idchargement '
                            . ' FROM chargement '
                            . ' WHERE idtypecompte= :typecpte and nomfile= :filename or urlfile= :url ';
                            $stmt = $entityManager->getConnection()->prepare($sqlrech);                              
                            $stmt->bindValue(':typecpte', $typecompte, PDO::PARAM_INT);
                            $stmt->bindValue(':filename', $allname, PDO::PARAM_STR);
                            $stmt->bindValue(':url', $allname, PDO::PARAM_STR);                                   
                            $stmt->execute(); 
                            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);  unset($stmt);
                            if ($res != null) {
                                $idfile = $res[0]['idchargement'];
                            }    
                            else $idfile=0 ;  // recuperation fichier                                 
                         } catch (\Symfony\Component\Form\Exception\Exception $e) {
                            //$Utils->logload('Fichier non trouve');// $output->writeln('Failure insertion du fichier'. "\n"  );//$alertmanager->SendAvertissementMsg($entitymanager,136);
                            $idfile = 0;
                         }                
                     
                   if ( ( $idfile != 0 ) && ( strtolower($typechargement) == 'j' || strtolower($typechargement) == 'a' ) ){                        
                        /** Debut chargement en lot des donnees  * */
                        $sqltmp = " LOAD DATA INFILE :fichier INTO TABLE  ";
                        if ( trim($typecompte) == 1) {  
                            $sqltmp .= " afbwtmp ";  } 
                        elseif ( trim($typecompte) == 2) {
                            $sqltmp .= " afbw2tmp ";                             
                        }elseif ( trim($typecompte) == 3) { 
                            ( $typechargement == 'j')?  $sqltmp .= " uwebjtmp " : $sqltmp .= " uwebtmp ";   
                            
                        }
                        $sqltmp .= "  FIELDS TERMINATED BY '' ENCLOSED BY '' (  ";
                        /** *** determination des champs a  referencer suivant les type de fichiers a charger ***** */
                        if ( trim($typecompte) == 1){
                            $sqltmp .= " cdenr,cdbque,res21,cdgui,deviso,virgul,monori,nocpte,codop,datope,motrej,datval,libel,res22,noecri,exocom,indind,montan,refer";
                        } elseif ( trim($typecompte) == 2) {
                            $sqltmp .= " cdenr,cdbque,cdgui,nocpte,cdafb,cdcoib,datope,res13,datval,libel,cdexo,montan,res23,sens";
                        } elseif ( trim($typecompte) == 3) {
                            if( $typechargement == 'j'){
                                    $sqltmp .= " numcpt,intitu,datope,libel,datval,montan,sign,codope,even,nomvt,sldlig,signsldlign,fonds,lrdesc,rgribb,rgrib";
                            }
                            else{
                                    $sqltmp .= " numcpt,intitu,datope,datecomp,libel,datval,montan,sign,codope,even,nomvt,sldlig,signsldlign,fonds,lrdesc,rgribb,rgrib";
                            }
                        }                        
                        if ( trim($typecompte) == 1){/*             * *** Champs dont les valeurs seront fixees suivant les types de fichiers ***** */
                            $sqltmp .= " ) SET periode = '' , ";
                            $sqltmp .= " selected = 1 ";
                        } elseif ( trim($typecompte) == 2) {
                            $sqltmp .= "   ) SET periode = ''  ,";
                            $sqltmp .= " selected = 1 ";                            
                        } elseif ( trim($typecompte) == 3) {
                            $sqltmp .= "   ) SET periode = replace(datope,'/',' ') , ";
                            // ajout de la clause uniquement les lignes qui n'ont pas solde % different de
                            $sqltmp .= " codoper = case ";
                            $sqltmp .= " when ( trim(libel) = 'SOLDE DEBUT PERIODE') then '01' ";
                            $sqltmp .= " when ( trim(libel) = 'SOLDE FIN PERIODE') then '07' ";
                            $sqltmp .= " when ( (trim(libel) <> 'SOLDE FIN PERIODE')
                                                 and
                                                (trim(libel) <> 'SOLDE DEBUT PERIODE')
                                               ) then '04' ";
                            $sqltmp .= "  end ";
                        }
                        $sqltmp .= "   ,traite = 0,idfile = :idfile , selected =0 ";
                        $stmt = $entityManager->getConnection()->prepare($sqltmp,array(PDO::MYSQL_ATTR_LOCAL_INFILE => true));
                        $stmt->bindValue(':fichier',$chemin.$allname, PDO::PARAM_STR);
                        $stmt->bindValue(':idfile', $idfile, PDO::PARAM_STR);
                        try {
                            $stmt->execute();  unset($stmt); 
                            if($typechargement == 'j'){
                                $Utils->logload('ALIMENTATION TABLE TEMPORAIRE UWEBJ.');// $output->writeln('Success alimentation table temporaire'. "\n"  );
                                 
                                $sqltable = ' SELECT max(dateoperation) as dateoperation '
                                   . ' FROM operation '
                                   . ' WHERE chrgjr= 1 ';
                                   $stmt1 = $entityManager->getConnection()->prepare($sqltable);  
                                   //$stmt1->bindValue(':chrgjr',1, PDO::PARAM_INT); 
                                   $stmt1->execute();
                                   $res = $stmt1->fetchAll(PDO::FETCH_ASSOC);unset($stmt1);
                                   //var_dump($res);exit;
                                   $Utils->logload('DATE MAX CHARGEE :'.$res[0]['dateoperation']);
                                }
                            if($typechargement == 'a'){
                                $Utils->logload('ALIMENTATION TABLE TEMPORAIRE UWEBA.');// $output->writeln('Success alimentation table temporaire'. "\n"  );
                                   $sqltable = 
                                     ' SELECT max(dateoperation) as dateoperation '
                                   . ' FROM operation '
                                   . ' WHERE chrgjr= 0 ';
                                   $stmt = $entityManager->getConnection()->prepare($sqltable);
                                   $stmt->execute();
                                   $res = $stmt->fetchAll(PDO::FETCH_ASSOC);unset($stmt);
                                   $Utils->logload('DATE MAX CHARGEE :'.$res[0]['dateoperation']);
                                }
                            //requete statistique
                            
                        } catch (\PDOException  $e) {
                            $Utils->logload('ALIMENTATION TABLE TEMPORAIRE FAILED.');//$output->writeln('Failure alimentation table temporaire'. "\n"  );
                            $res = 1;
                        }
                   }elseif ( ( $idfile != 0) && strtolower($typechargement) == 's'  ) {                         
                        $sqltmp="";
                        $sqltmp = " LOAD DATA INFILE :fichier INTO TABLE soldecomptetmp
                                    FIELDS TERMINATED BY '' ENCLOSED BY '' 
                                    (
                                      numcpte,nom,datesolde,montan,sens
                                     ) SET idfile = :idfile  "; 
                        $stmt = $entityManager->getConnection()->prepare($sqltmp,array(PDO::MYSQL_ATTR_LOCAL_INFILE => true));
                        $stmt->bindValue(':fichier',$chemin.$allname, PDO::PARAM_STR);
                        $stmt->bindValue(':idfile', $idfile, PDO::PARAM_STR);
                        try {
                            $stmt->execute();  unset($stmt);                                     
                            $Utils->logload('ALIMENTATION TABLE SOLDE.');// $output->writeln('Success alimentation table temporaire'. "\n"  );
                        } catch (\PDOException  $e) {
                            $Utils->logload('ALIMENTATION TABLE SOLDE FAILED.');//$output->writeln('Failure alimentation table temporaire'. "\n"  );
                            $res = 1;
                        }
                   }   
                   
                   // $output->writeln(' sqltmp '.$sqltmp. "\n"  );
                   //exit();
                   if ( $idfile != 0 ) {        
                       if ( strtolower($typechargement) == 'a' || strtolower($typechargement) == 'j' ) {
                           $sqloperation = 'CALL traitementfiles (:idfile)';                 
                       }elseif (strtolower($typechargement) == 's')  {
                           $sqloperation = 'CALL traitementSolde (:idfile)';
                       }
                       //exit();
                       $stmt = $entityManager->getConnection()->prepare($sqloperation);                              
                       $stmt->bindValue(':idfile', $idfile, PDO::PARAM_INT);                               
                       try {
                            $stmt->execute();  unset($stmt);    
                            if ( strtolower($typechargement) == 'a' || strtolower($typechargement) == 'j') {
                                $Utils->logload('ALIMENTATION MOUVEMENTS.');// $output->writeln(' Success alimentation table operation. '. "\n"  );
                            } elseif ( strtolower($typechargement) == 's') {
                                $Utils->logload('ALIMENTATION SOLDE.');// $output->writeln(' Success alimentation table operation. '. "\n"  );
                            }    
                       } catch (\PDOException  $e) {
                            if ( strtolower($typechargement) == 'a' || strtolower($typechargement) == 'j') {
                                $Utils->logload('ALIMENTATION MOUVEMENTS FAILED.');
                            } elseif ( strtolower($typechargement) == 's') {
                                $Utils->logload('ALIMENTATION SOLDE FAILED.');// $output->writeln(' Success alimentation table operation. '. "\n"  );
                            }    
                       }
                    }
                    unlink($path.$allname); 
                  }
                  
                }  // fin foreach iterator 
                
            }
        }          
                              
}	
	
