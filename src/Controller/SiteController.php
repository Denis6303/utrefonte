<?php
/**
 * Description of siteController-----------
 *
 * @author ace3i ----
 */
namespace App\Controller;
use App\Entity\Message;
use App\Entity\Internaute;
use App\Entity\InternauteType;
use App\Entity\MessageInternauteType;
use App\Entity\NewsletterType;
use App\Entity\AdresseIp;
use Leg\GoogleChartsBundle\Charts\Gallery\BarChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Emplacement;
use App\Entity\Media;
use App\Entity\Cadre;

use App\Entity\InternauteAlerteType;
use App\Entity\InternauteReclamationType;

use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;


    /**
     * SiteController 
     * 
     * Le controleur qui gère la presentation du site public
     * 
     * 
     * Cette ligne de code permet de definir la langue Ã  travers la variable locale| fr pour le francais et en pour l'anglais
     * 
     * Presente dans la majorite des methodes
     * 
     * $this->requestStack->getCurrentRequest()->setLocale($locale);
     * 
     *
     * @author Ace3i <mail@utb.com>
     * @copyright 2013 Ace3i
     * @link      http://www.utb.tg
     * 
     * 
     */
class SiteController extends AbstractController
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
    }
{
    
    /**
     * Methode qui présente la page d'accueil
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listeBanniere: Variable qui affiche les bannieres de la page d'accueil
     * 
     * $listeactualite : Affiche les actualites et brefs sur la page d'accueil
     * 
     * $afficherrubaccueil: recupere la liste des rubriques Ã  afficher sur le site public
     * 
     * $afficherartaccueil: recupere la liste des articles  Ã  afficher sur le site public 
     * 
     * $sousrubaccueil: Tableau recuperant les sous rubriques des rubriques affichees sur le site public
     * 
     * $listecorrespondance: Affiche la liste de la zone << correspondant >> sur le site public  
     * 
     * $rubaccueil :  Tableau qui recupere les rubriques Ã  afficher dans les principaux zones reservees aux rubriques sur le site public en se basant sur $afficherrubaccueil
     * 
     * $artaccueil : Permet de recuperer les articles Ã  afficher dans les principaux zones reservees aux articles sur le site public en se basant sur $afficherartaccueil
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:index.html.twig 
     * 
     * 
     */
    
    public function indexAction(): Response(string $locale,$page): Response
    {        
        $em=$this->entityManager;        
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        $this->requestStack->getCurrentRequest()->attributes->set('typepage', $page);  

        /*$menu = $em->getRepository("utbAdminBundle/Menu")->findOneBy(array('libmenu'=>$page));
        $lien = $rubrique->getUrlExterneMenu(); 
        $lien = explode("|", $lien);*/  
        $this->infoSite($locale);        
        
        //var_dump($unEmplacement) ; exit;
                
        $listeBanniere = $em
                ->getRepository('utbAdminBundle/Menu')
                ->getAllMediasMenu(0,$locale);
        //var_dump($listeBanniere);
        $listeactualite =$em
                        ->getRepository('admin/Article')
                        ->afficherActuAccueil($locale, 4,10,2);
        $listeBreve =$em
                        ->getRepository('utbAdminBundle/Article')
                        ->afficherBreveAccueil($locale, 4,10,6);
        //var_dump($listeBreve);exit;
        $afficherrubaccueil = $em
                ->getRepository('App\Entity\Cadre')
                ->findAllCadreAccueil($locale,7); 
        
        $afficherartaccueil =$em
                ->getRepository('App\Entity\Cadre')
                ->findAllCadreArticleAccueil($locale,8);
        //$this->requestStack->getCurrentRequest()->attributes->set('lrubrique', $larubrique);        
            $i=0;
            $sousrubaccueil=array();
            $rubaccueil=array();
            $artaccueil=array();
           
          foreach($afficherrubaccueil as $idrub){               
              $rubaccueil[$i]= $em
                              ->getRepository("utbAdminBundle:Rubrique")
                              ->findOneByLocaleAccueil($idrub["rubPointer"],$locale);               
              //Tableau pour recupérer les sous menus 
            if(count($rubaccueil[$i])!=0){
                $sousrubaccueil[$i]= $em
                                ->getRepository("utbAdminBundle:Rubrique")
                                ->getRubSousRubPubSansFaq($rubaccueil[$i][0]["id"],$locale,0);                                             
             }
              $i++;
          } 
          
          //var_dump($sousrubaccueil);exit;
          //La liste des devises Ã  afficher sur le site  
          $autreDevise =$em
                ->getRepository('utbClientBundle:Devise')
                ->getTestDeviseLocale(0);
          $j=0;
          foreach($afficherartaccueil as $idrub){            
              $artaccueil[$j]= $em
                              ->getRepository("utbAdminBundle:Article")
                              ->findOneByAccueil($idrub["articlePointer"],$locale); 
              $j++;
          }
          $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere);             
          $listecorrespondance =$em
                         ->getRepository('App\Entity\Article')
                         ->getListeByParentRubriqueAccueilLocale(9, $locale,6);       
            if($page=='accueil'){
                return $this->render('utbSiteBundle/Site/index.html.twig',array('locale'=>$locale,'page'=>$page,'sousrubaccueil'=>$sousrubaccueil,
                                     'listecorrespondance'=>$listecorrespondance,'listeactualite'=>$listeactualite,'rubaccueil'=>$rubaccueil,'artaccueil'=>$artaccueil,'autreDevise'=>$autreDevise,'listeBreve'=>$listeBreve));           
            }else{
                return $this->render('utbSiteBundle:Site:'.$page.'.html.twig',array('locale'=>$locale,'page'=>$page,));       
            }
    }
    
    public function indexVideoAction(): Response(string $locale,$page): Response
    {        
        $em=$this->entityManager;        
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        $this->requestStack->getCurrentRequest()->attributes->set('typepage', $page);  

        /*$menu = $em->getRepository("utbAdminBundle/Menu")->findOneBy(array('libmenu'=>$page));
        $lien = $rubrique->getUrlExterneMenu(); 
        $lien = explode("|", $lien);*/  
        $this->infoSite($locale);        
        
        //var_dump($unEmplacement) ; exit;
                
        $listeBanniere = $em
                ->getRepository('admin/Menu')
                ->getAllMediasMenu(0,$locale);
        //var_dump($listeBanniere);
        $listeactualite =$em
                        ->getRepository('utbAdminBundle/Article')
                        ->afficherActuAccueil($locale, 4,10,2);
        $listeBreve =$em
                        ->getRepository('App\Entity\Article')
                        ->afficherBreveAccueil($locale, 4,10,6);
        //var_dump($listeBreve);exit;
        $afficherrubaccueil = $em
                ->getRepository('App\Entity\Cadre')
                ->findAllCadreAccueil($locale,7); 
        
        $afficherartaccueil =$em
                ->getRepository('App\Entity\Cadre')
                ->findAllCadreArticleAccueil($locale,8);
        //$this->requestStack->getCurrentRequest()->attributes->set('lrubrique', $larubrique);        
            $i=0;
            $sousrubaccueil=array();
            $rubaccueil=array();
            $artaccueil=array();
           
          foreach($afficherrubaccueil as $idrub){               
              $rubaccueil[$i]= $em
                              ->getRepository("utbAdminBundle:Rubrique")
                              ->findOneByLocaleAccueil($idrub["rubPointer"],$locale);               
              //Tableau pour recupérer les sous menus 
            if(count($rubaccueil[$i])!=0){
                $sousrubaccueil[$i]= $em
                                ->getRepository("utbAdminBundle:Rubrique")
                                ->getRubSousRubPubSansFaq($rubaccueil[$i][0]["id"],$locale,0);                                             
             }
              $i++;
          } 
          
          //var_dump($sousrubaccueil);exit;
          //La liste des devises Ã  afficher sur le site  
          $autreDevise =$em
                ->getRepository('utbClientBundle:Devise')
                ->getTestDeviseLocale(0);
          $j=0;
          foreach($afficherartaccueil as $idrub){            
              $artaccueil[$j]= $em
                              ->getRepository("utbAdminBundle:Article")
                              ->findOneByAccueil($idrub["articlePointer"],$locale); 
              $j++;
          }
          $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere);             
          $listecorrespondance =$em
                         ->getRepository('App\Entity\Article')
                         ->getListeByParentRubriqueAccueilLocale(9, $locale,6);       
            if($page=='accueil'){
                return $this->render('utbSiteBundle/Site/indexVideo.html.twig',array('locale'=>$locale,'page'=>$page,'sousrubaccueil'=>$sousrubaccueil,
                                     'listecorrespondance'=>$listecorrespondance,'listeactualite'=>$listeactualite,'rubaccueil'=>$rubaccueil,'artaccueil'=>$artaccueil,'autreDevise'=>$autreDevise,'listeBreve'=>$listeBreve));           
            }else{
                return $this->render('utbSiteBundle:Site:'.$page.'.html.twig',array('locale'=>$locale,'page'=>$page,));       
            }
    }
    
    /**
     * Methode qui gere l'affichage des principaux menus sur le site
     * 
     * @var
     * 
     * Les Variables
     * 
     * $menuParents: Donne la liste des principaux menux parents(le menu du premier niveau) sur le site public
     * 
     * $listeLiens : Tableau qui recupere la liste de lien (menu) a afficher sur le site
     * 
     * $thisOrdre: récupere l'ordre defini pour les menus dans la table ordre
     * 
     * $ordre: Tableau recuperant les ordres apres l'unserialize de $thisOrdre
     * 
     * $t: Texte illustrant les types menus niveau menu parent
     * 
     * $im: Image de presentation des types menus niveau menu parent
     * 
     * $type: Texte illustrant les types menus niveau des sous menus
     * 
     * $image: Image de presentation des types menus niveau des sous menus
     * 
     * $menuFilsIDs :  recupere les sous menus ordonnes d'un menu
     * 
     * $groupeMenus : Donne la liste des groupes de menu enregistres sur le site 
     * 
     * $listeLiensTemp :  Tout comme $listeLiens recupere la liste de lien (menu) utilise pour recuperer les menus quand le groupe = 1
     * 
     * $artaccueil : Permet de recuper les articles Ã  afficher dans les principaux zones reservees aux articles sur le site public
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <integer> $groupe permet de differencier les menus 1 pour les menus principaux | un autre pour les menus du pied de page
     * 
     * @param <integer> $idrub : identifiant de la rubrique parent recuper lors d'un click sur un lien, in permet de montrer le menu qui est actif dans le groupe des menus principaux.
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:index.html.twig 
     * 
     */
    
    public function menuAction(): Response(string $locale, $groupe,$idrub): Response{
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        $em = $this->entityManager;        
                                     
        $menuParents = $em->getRepository("utbAdminBundle:Menu")->findMenusParent($groupe,$locale);
        $listeLiens = array();                
       //var_dump($menuParents);exit;
        foreach ($menuParents as $mp) {
            $mp->setTranslatableLocale($locale);
            $em->refresh($mp);
            $t = $em->getRepository("utbAdminBundle:Menu")->getTextTypeMenu($mp->getTypeMenu());
            $im = $em->getRepository("utbAdminBundle:Menu")->getImageTypeMenu($mp->getTypeMenu());      
         if($groupe != 1){                                
               $thisOrdre = $em->getRepository("utbAdminBundle:Ordre")->findOneBy(array("nomTable"=>"MenuFils"));
               $ordre = unserialize($thisOrdre->getOrdre());
                if(array_key_exists($mp->getId(), $ordre)){
                   $menuFilsIDs = $ordre[$mp->getId()];
                   $listeLiens[$mp->getLibMenu() ."|" .$mp->getUrlExterneMenu(). "|" . $mp->getId() . "|" . $t . "|" . $im. "|".$groupe] = array();
                  // print_r($menuFilsIDs);
                   if(!empty($menuFilsIDs)){
                        foreach($menuFilsIDs as $mID){
                            $mFils = $em->getRepository("utbAdminBundle:Menu")->findOneMenuByLocale($mID, $locale); //find($mID);
                           
                            if ($mFils !=null){
                             $type = $em->getRepository("utbAdminBundle:Menu")->getTextTypeMenu($mFils[0]['typeMenu']);  //($mFils->getTypeMenu());
                             $image = $em->getRepository("admin/Menu")->getImageTypeMenu($mFils[0]['typeMenu']);
                             $listeLiens[$mp->getLibMenu() . "|".$mp->getUrlExterneMenu(). "|" . $mp->getId() . "|" . $t . "|" . $im. "|".$groupe][] = array(
                                 "id" => $mFils[0]['id'],
                                 "libelle" => $mFils[0]['libMenu'],
                                 "url" => $mFils[0]['urlExterneMenu'],
                                 "typeMenu" => $type,
                                 "imageMenu" => $image,
                                );
                            }
                        }
                   }
                }
        }else {
                     $listeLiens[] = array(
                         "id" => $mp->getId(),
                         "libelle" => $mp->getLibMenu(),
                         "url" => $mp->getUrlExterneMenu(),
                         "typeMenu" => $t,
                         "imageMenu" => $im,
                     );   
             }           
        }
         
        if($groupe == 1){
               $thisOrdre = $em->getRepository("utbAdminBundle/Ordre")->findOneBy(array("nomTable"=>"MenuParent"));
               //
               $ordre = unserialize($thisOrdre->getOrdre());     			   
               $listeLiensTemp = array();
               if(count($ordre)==count($listeLiens)){                  
                   ////ce controle est juste pour au cas ou l'ordre n'est pas encore definit ou
                   //un nouveau menu ï¿½ ï¿½tï¿½ ajoutï¿½ mais n'existe pas dans le rang. C'est juste pour etre sur
                   //pour ï¿½viter cela, il faudrait s'assurer que quand un menu est crï¿½e, on l'ajoute dans l'ordre aussitot.                    
                   foreach($ordre as $pos=>$id){
                        foreach($listeLiens as $p=>$d){                          
                             if( $id == $d['id']){
                                    $listeLiensTemp[] =  array(
                                                             "id" => $d['id'],
                                                             "libelle" => $d['libelle'],
                                                             "url" => $d['url'],
                                                             "typeMenu" => $d['typeMenu'],
                                                             "imageMenu" => $d['imageMenu'],
                                                         );   
                             }
                        }
                    }                      
               }else{//Tant qu'un menu n'existe pas dans l'ordre on zape l'ordre et on prends l'ajustement par defaut ds la table menu.
                   $listeLiensTemp = $listeLiens;
               }
        }        
       //Récuperons la liste des Groupes menu
        $groupeMenus = $em->getRepository("utbAdminBundle/GroupeMenu")->findAll();
        //recuperons la liste des menu parents 
        $listePmenu = $em->getRepository("utbAdminBundle/Menu")->findMenuFils( 0,$locale);//findBy(array('idParentMenu' => 0));
        //récupérons la liste des rubrique  
                if($groupe==1){
                    return $this->render('utbSiteBundle/Site/menu.html.twig', array("listeLiens" => $listeLiensTemp, 'GroupeMenu' => $groupeMenus, "listePmenu" => $listePmenu,'locale'=>$locale,'idrub'=>$idrub,));
                }elseif($groupe==2){                    
                    return $this->render('utbSiteBundle/Site/menubas.html.twig', array("listeLiens" => $listeLiens, 'GroupeMenu' => $groupeMenus, "listePmenu" => $listePmenu,'locale'=>$locale,'idrub'=>$idrub,));            
                }
    }    

    /**
     * Methode qui présente la page d'accueil
     * 
     * @var
     * 
     * Les Variables
     * 
     * $larubrique: Recupere les informations de la rubrique
     * 
     * $listesousrub : Recupere la liste des sous rubriques d'une rubrique
     * 
     * $image : Permet d'avoir l'image de presentation d'une rubrique donnee
     * 
     * $listecategorie: Pour avoir la liste des categories c'est Ã  dire rubrique de troisieme niveau
     * 
     * $articlecategorie : Tableau qui recupere la liste des articles d'une categorie donnee
     * 
     * $listearticlefirst: Recupere le premier article pour presenter une rubrique, illustre sur le twig rubriqueart.html.twig
     * 
     * $listepublication : Recupere la liste des publications de la rubrique presentation 
     * 
     * $listecorrespondance: Recupere la liste des correspondants de la rubrique presentation
     * 
     * $solutiongenerale : Recupere la liste des sous rubriques 
     * 
     * $listeactualite: Pour avoir la liste des actualites et brefs sur le site 
     * 
     * $listearticle: Pour avoir la liste des articles d'une rubrique 
     * 
     * $listecateg :  recupere la liste des sous rubriques de la rubrique "Grand parent" d'une categorie
     * 
     * $articles_per_page : la liste des Faq par page
     * 
     * $articlereseau : Tableau a deux niveaux recuperant la liste des reseaux(reseau GAB, TPE, ... ) de l'utb
     * 
     * $listeBanniere: Variable qui prend la liste des bannieres d'une rubrique
     * 
     * $natureDoc: Pour remplir le select box du type de document
     * 
     * $votrecategorie : recupere l'objet id et permet de test voir si la rubrique est un sous rubrique ou categorie 
     * 
     * $articlesousrubrique :  Tout comme la $listeLiens recupere la liste de lien (menu) utilise pour recuperer les menus quand le groupe = 1
     * 
     * @param <string> $locale : Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <integer> $type : represente le type de presentation vers lequel pointe le lien(il en existe plusieurs)
     * 
     * @param <integer> $id : identifiant de la rubrique d lien sur lequel on clique
     * 
     * @param <integer> $date : 
     * 
     * @param <integer> $typecate : type categorie des documents de la rubrique pulication on a accès Ã  travers le formulaire de recherche de la page qui liste les publications
     * 
     * @return <string>  retourne plusieurs twigs   suivants  la presentation choisie   
     * 
     */    
    
    public function rubriqueAction(): Response(int $id,string $locale,string $type,$date,$typecate): Response{        

        $this->requestStack->getCurrentRequest()->setLocale($locale); 
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        $em = $this->entityManager;  
        
        // Pour avoir la rubrique 
        $listecategorie=array();
        
        $articlecategorie=null; $larubrique = null; $image = null;$natureDoc = null;
        $listesousrub = null;$listearticlefirst = null;$listecorrespondance =null;
        $solutiongenerale =null;$listeactualite =null;$listeactualite = null;$listearticle =null;
        $listecateg = null;$listepublication = null;$articlesousrubrique=null;$listefaq=null;
        
        $request = $this->requestStack->getCurrentRequest();
        $typecate = $request->request->get('typecate');
        
        if ($typecate == null) {
            $typecate = 0;
        }

            $listeBreve =$this->getDoctrine()
                            ->getEntityManager()
                            ->getRepository('admin/Article')
                            ->findAllByStatutLimitDesc(4,$locale,1,1,6); 		
		
		
        $larubrique = $this->entityManager
              ->getRepository("utbAdminBundle/Rubrique")
              ->findOneByLocale($id,$locale);         
        // dans les presentation 1 - 9 - 10 
        if ( ($type == 1) || ($type == 12) || ($type == 16) || ($type == 13) ||($type == 9) || ($type == 10) || ($type == 11) || ($type==5) || ($type ==6) || ($type ==8) || ($type==15) || ($type==17)|| ($type == 20)) {                
             
            $listesousrub = $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getRubSousRubPubSansFaq($id,$locale,0);    
        
            $listecorrespondanceEtranger =$em
               ->getRepository('App\Entity\Article')
               ->getListeByParentRubriqueAccueilLocale(9, $locale,6);            
        }      
        // dans les presentation 1 
        if (($type == 1) || ($type == 12)|| ($type == 13)  || ($type==17))  {
            $image = $this->entityManager
                    ->getRepository('App\Entity\Rubrique')
                    ->getListeImageOrIcone($id,1,1,$locale);

            $articlepub =$this->getDoctrine()
                            ->getEntityManager()
                            ->getRepository('App\Entity\Article')
                            ->findAllByStatutLimitDesc(4,$locale,1,1,$id); 

        }
        
        $this->requestStack->getCurrentRequest()->attributes->set('lrubrique', $larubrique); 
        
        if( $type==2 || $type==14 || ($type == 16)   ) {           
                 $listesousrub = $this->entityManager
                    ->getRepository('App\Entity\Rubrique')
                    ->getRubSousRubPubSansFaq($larubrique[0]['idParent'],$locale,0);
                 
        }
        
        if( $type == 2 || $type == 11 || $type==14 || ($type == 16) ){
             $listecategorie = $this->entityManager
                ->getRepository('App\Entity\Rubrique')
                ->getRubSousRubPubSansFaq($id,$locale,0);
             //Pour afficher les article d'une catégorie
             foreach($listecategorie as $categorie){
                        $n=0;
                        if(count($categorie)!=0){                                            
                                  $articlecategorie[$categorie['id']]=$this->getDoctrine()
                                          ->getEntityManager()
                                          ->getRepository('App\Entity\Article')
                                          ->findAllByLocaleType($locale, 4,10,$categorie['id']);//var_dump($articlereseau[$sousrubr['id']]);                                                   
                        }                             
                        $n++;
             }               
            // Pour afficher les article d'une sous rubrique
             foreach($listesousrub as $categorie){
                    $n=0;
                    if(count($categorie)!=0){                                            
                              $articlesousrubrique[$categorie['id']]=$this->getDoctrine()
                                      ->getEntityManager()
                                      ->getRepository('utbAdminBundle/Article')
                                      ->findAllByLocaleType($locale, 4,10,$categorie['id']);//var_dump($articlereseau[$sousrubr['id']]);                                                   
                     }                             
                     $n++;
            }
       }       
       // Recupere le premier article pour presenter une rubrique illustre sur le twig rubriqueart.html.twig
       if($type == 2 || $type==14 || ($type == 16)){
            $listearticlefirst =$this->getDoctrine()
                            ->getEntityManager()
                            ->getRepository('utbAdminBundle/Article')
                            ->findAllByStatutLimitDesc(4,$locale,1,20,$larubrique[0]['idParent']);            
       }          
        
       // recuperer la liste des publication sur le site 
       if($type == 3 || $type== 4 ){  
            
            $listepublication =$this->getDoctrine()
                             ->getEntityManager()
                             ->getRepository('admin/Article')
                             ->findAllByLocalePublication($locale, 4, 10,4,$date,$typecate);            
        } 
       // var_dump($listepublication);exit;    
        $articlereseau=array();
        $listecateg=array();
        $sousrubr=null;
        
       // Pour avoir la liste des reseaux utb nous utilisons un tableau a deux dimensions pour structurer les reseaux par sous rubrique(categorie si possible) 
        if($type == 6  || $type==17){
         $k=0;
            foreach($listesousrub as $sousrubr){
                $articlereseau[$sousrubr['id']][]=$this->getDoctrine()
                         ->getEntityManager()
                         ->getRepository('utbAdminBundle/Article')
                         ->findAllByLocaleType($locale, 4,100,$sousrubr['id']);//var_dump($articlereseau[$sousrubr['id']]);                           
              $k++;
            } 
         }
        
       //recupere la liste de sous rubrique de la rubrique d'une categorie
       if($type == 2 || $type ==7 || $type ==5 || $type ==9 || $type ==10 || $type ==11 || $type==14 || ($type == 16)|| ($type == 12)){
            $votrecategorie = $em->getRepository("utbAdminBundle:Rubrique")->find($id);

            if ($votrecategorie!=null && $votrecategorie->getIdgrandparent()!=0){
                 $listecateg = $this->entityManager
                      ->getRepository('App\Entity\Rubrique')
                      ->getRubSousRubPubSansFaq($votrecategorie->getIdgrandparent(),$locale,0); 
            }
       }  
       // Avoir la liste des articles d'une rubrique 
       if($type == 3 || $type ==5 || $type == 7 || $type ==9|| ($type == 12) ){
            $listearticle =$this->getDoctrine()
                             ->getEntityManager()
                             ->getRepository('App\Entity\Article')
                             ->findAllByStatutLimitDesc(4,$locale,1,20,$id);
       } 
       
       /****************   **********/
       if($type == 6  || $type==17){  
            $articles_per_page = $this->container->get->getParameter('max_articles_on_faq'); // Le liste de faq par page
            $listefaq =$this->getDoctrine()
                             ->getEntityManager()
                             ->getRepository('App\Entity\Rubrique')
                             ->getArticlesFaqRub($id,$locale,$page=1,$articles_per_page);       
       }
       /********  **************/ 
       
       
       if(($type == 1) || ($type == 6) || ($type == 12)|| ($type == 13) || ($type==17)){       
            $listeactualite =$this->getDoctrine()
                             ->getEntityManager()
                             ->getRepository('App\Entity\Article')
                            // ->findAllByLocaleType($locale, 4,10,2); 
                            ->afficherActuAccueil($locale, 4,10,2);
                    ///var_dump($listeactualite);exit;
       }
       
      // Pour avoir les solution générale 
         if($type == 1 ||  $type == 11 || $type==12 || $type == 13){
           if($type == 12 || $type == 1 || $type == 13 || $type == 5 ){
               
               $id1=29;
               $listecorrespondance =$this->entityManager
                             ->getRepository('App\Entity\Article')
                             ->findAllByStatutLimitDesc(4,$locale,1,20,$id1);            
            }else{
               
            }
            $solutiongenerale =$this->entityManager
                             ->getRepository('App\Entity\Rubrique')
                             ->getRubSousRubPub(8,$locale);        
       }
       
       if($type == 8 ){

            $listecorrespondance =$this->getDoctrine()
                             ->getEntityManager()
                             ->getRepository('App\Entity\Article')
                             ->getListeByParentRubriqueLocale($id, $locale,0);       
       }

       if($type == 4){
            $natureDoc= $this->entityManager
             ->getRepository('App\Entity\NatureDoc')
             ->findBy(array("statutNatureDoc"=>1));        
       }
       
       if($type==1){
           
           $idarticle=205;
           $notrearticle = $em->getRepository("utbAdminBundle:Article")->findOneByLocale2($idarticle, $locale);
           
           
       }

       $listeBanniere =null; 
       $listeBanniere = $this->entityManager
                        ->getRepository('App\Entity\Menu')
                        ->getAllMediasMenu($id,$locale);    

        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', ''); 
        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere); 
        
       //var_dump($listeBanniere);exit;
        
        ($larubrique == null)? 
        $this->requestStack->getCurrentRequest()->attributes->set('idrubrik', 0)
                :
        $this->requestStack->getCurrentRequest()->attributes->set('idrubrik', $id); 
      
        ($larubrique == null)? 
        $this->requestStack->getCurrentRequest()->attributes->set('idgrdprub', 0)
                :
        $this->requestStack->getCurrentRequest()->attributes->set('idgrdprub', $larubrique[0]['idGrandParent']);         
        
        ($larubrique == null)? 
        $this->requestStack->getCurrentRequest()->attributes->set('idparentrub', 0)
                :
        $this->requestStack->getCurrentRequest()->attributes->set('idparentrub', $larubrique[0]['idParent']);    
        
        
        if($type==1){
                return $this->render('utbSiteBundle/Site/rubrique.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'image' => $image,'listecateg'=>$listecateg,'listecorrespondance'=>$listecorrespondance,'notrearticle'=>$notrearticle,));               
        }elseif($type==2){
               return $this->render('utbSiteBundle/Site/rubriqueart.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listepublication'=>$listepublication,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,'articlesousrubrique'=>$articlesousrubrique,'listearticlefirst'=>$listearticlefirst,));            
        }elseif($type==3){
                return $this->render('utbSiteBundle/Site/rubriquearb.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listepublication'=>$listepublication,'listefaq'=>$listefaq,'image' => $image,'listecateg'=>$listecateg,));
        }elseif($type==4){           
                 return $this->render('utbSiteBundle/Site/alldocuments.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listepublication'=>$listepublication,'natureDoc'=>$natureDoc,'image' => $image,'listecateg'=>$listecateg,));           
        }elseif($type==5){
                  return $this->render('utbSiteBundle/Site/particulier.html.twig',
                              array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'image' => $image,'listecateg'=>$listecateg,'listecorrespondance'=>$listecorrespondance,'listecorrespondanceEtranger'=>$listecorrespondanceEtranger,));                     
        }elseif($type==6){
                  return $this->render('utbSiteBundle/Site/reseau.html.twig',
                              array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'articlereseau'=>$articlereseau,'image' => $image,'listecateg'=>$listecateg,));                     
        }elseif($type==7){
                  return $this->render('utbSiteBundle/Site/rubriquearticle.html.twig',
                              array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'articlereseau'=>$articlereseau,'image' => $image,'listecateg'=>$listecateg,));                     
        }elseif($type==8){
                  return $this->render('utbSiteBundle/Site/correspondant.html.twig',
                  array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'listesousrub'=>$listesousrub,'solutiongenerale'=>$solutiongenerale,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'articlereseau'=>$articlereseau,'listecorrespondance'=>$listecorrespondance,'image' => $image,));                     
        }elseif($type==9){
                  return $this->render('utbSiteBundle/Site/presentationCarte.html.twig',
                  array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'listesousrub'=>$listesousrub,'solutiongenerale'=>$solutiongenerale,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'articlereseau'=>$articlereseau,'listecorrespondance'=>$listecorrespondance,'image' => $image,'listecateg'=>$listecateg,));                     
        }elseif($type==10){
                  return $this->render('utbSiteBundle/Site/presentationCarte1.html.twig',
                  array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'listesousrub'=>$listesousrub,'solutiongenerale'=>$solutiongenerale,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'articlereseau'=>$articlereseau,'listecorrespondance'=>$listecorrespondance,'image' => $image,'listecateg'=>$listecateg,));                     
        }elseif($type==11){           
                  return $this->render('utbSiteBundle/Site/rubriqueEpargne.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,));
        }  elseif($type==12){           
                  return $this->render('utbSiteBundle/Site/particulier_cover.html.twig',array('listeBreve'=>$listeBreve,'listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,'listecorrespondance'=>$listecorrespondance,'listecorrespondanceEtranger'=>$listecorrespondanceEtranger,'articlepub'=>$articlepub,));
        } elseif($type==13){           
                  return $this->render('utbSiteBundle/Site/rubriqueEntreprise.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,'listecorrespondance'=>$listecorrespondance,'listecorrespondanceEtranger'=>$listecorrespondanceEtranger,'articlepub'=>$articlepub,));
        } elseif($type==14){           
                  return $this->render('utbSiteBundle/Site/ebox.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listepublication'=>$listepublication,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,'articlesousrubrique'=>$articlesousrubrique,));
        } elseif($type==15){           
                  return $this->render('utbSiteBundle/Site/service.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listepublication'=>$listepublication,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,'articlesousrubrique'=>$articlesousrubrique,));
        } elseif($type==16){           
                  return $this->render('utbSiteBundle/Site/presentationCarteOnglet.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listepublication'=>$listepublication,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,'articlesousrubrique'=>$articlesousrubrique,'listearticlefirst'=>$listearticlefirst,'listecorrespondanceEtranger'=>$listecorrespondanceEtranger,));            
        }elseif($type==17){
                  return $this->render('utbSiteBundle/Site/reseau2.html.twig',
                              array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'articlereseau'=>$articlereseau,'image' => $image,'listecateg'=>$listecateg,));                     
        } elseif($type==18){           
                  return $this->render('utbSiteBundle/Site/service2.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listepublication'=>$listepublication,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,'articlesousrubrique'=>$articlesousrubrique,));
        } elseif($type==19){           
                  return $this->render('utbSiteBundle/Site/service2.html.twig',array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listepublication'=>$listepublication,'listefaq'=>$listefaq,'image' => $image,'listecategorie'=>$listecategorie,'articlecategorie'=>$articlecategorie,'listecateg'=>$listecateg,'articlesousrubrique'=>$articlesousrubrique,));
        } elseif($type==20){           
                   return $this->render('utbSiteBundle/Site/particulier2.html.twig',
                              array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'image' => $image,'listecateg'=>$listecateg,));                     
        }  elseif($type==21){           
                  return $this->render('utbSiteBundle/Site/conditions.html.twig',
                             array('listeBreve'=>$listeBreve,'larubrique'=>$larubrique,'listearticle'=>$listearticle,'solutiongenerale'=>$solutiongenerale,'listesousrub'=>$listesousrub,'locale'=>$locale,'listeBanniere'=>$listeBanniere,'listeactualite'=>$listeactualite,'listefaq'=>$listefaq,'image' => $image,'listecateg'=>$listecateg,));                     
       }
        
    }
    
    /**
     * Methode qui gere l'affichage d'un article sur le site
     * 
     * @var
     * 
     * Les Variables
     * 
     * $larubrique: Permet d'avoir la rubrique dans laquelle appartient l'article 
     * 
     * $listecateg :  recupere la liste des sous rubriques de la rubrique d'une categorie (permet d'afficher les sous menu juste en dessous du banniere)
     * 
     * $larticleFrancais : Pour avoir la liste des actualistes et brefs sur le site
     * 
     * $listeactualite: Pour avoir la liste des actualites et brefs sur le site 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <integer> $groupe permet de differencier les menus 1 pour les menus principaux | un autre pour les menus du pied de page
     * 
     * @param <integer> $idrub : identifiant de la rubrique parent recuper lors d'un click sur un lien, in permet de montrer le menu qui est actif dans le groupe des menus principaux.
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:articles.html.twig 
     * 
     */    
    
    public function articleAction(): Response(int $id,string $locale,string $type): Response
    {
        $em = $this->entityManager; 
        $notrearticle = $em->getRepository("utbAdminBundle:Article")->find($id);
        
        $larubrique = $this->entityManager
                ->getRepository("utbAdminBundle:Rubrique")
                ->findOneByLocale($notrearticle->getRubrique()->getId(),$locale);
    
        
        ($larubrique == null)? 
        $this->requestStack->getCurrentRequest()->attributes->set('idrubrik', 0)
                :
        $this->requestStack->getCurrentRequest()->attributes->set('idrubrik', $notrearticle->getRubrique()->getId()); 
        
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        
        $votrecategorie = $em->getRepository("utbAdminBundle:Rubrique")->find($notrearticle->getRubrique()->getId());        
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);        
        //la liste des articles
        $larticleFrancais = $this->entityManager
                ->getRepository("utbAdminBundle:Article")
                ->findOneByLocale($id,$locale);
        
        $listeBanniere =null; 
        $listeBanniere = $this->entityManager
                ->getRepository('App\Entity\Menu')
                ->getAllMediasMenu($votrecategorie->getIdparent(),$locale);         
        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere); 
       
        $idrub= $notrearticle->getRubrique()->getId();
        
        $memeRubrique=$this->getDoctrine()
                        ->getEntityManager()
                        ->getRepository('App\Entity\Article')
                        ->listeMemeRubriqueLocale($idrub, $locale, $id);
       $articlecategorie=null;
        if($type==2 || $type==3){
         //liste des sous rubrique de la rubrique de l'article           
               $listecategorie = $this->entityManager
                       ->getRepository('admin/Rubrique')
                       ->getRubSousRubPubSansFaq($notrearticle->getRubrique()->getIdparent(),$locale,0);
               
        

                foreach($listecategorie as $categorie){
                           $n=0;
                           if(count($categorie)!=0){                                            
                                     $articlecategorie[$categorie['id']]=$this->getDoctrine()
                                             ->getEntityManager()
                                             ->getRepository('utbAdminBundle/Article')
                                             ->findAllByLocaleType($locale, 4,10,$categorie['id']);//var_dump($articlereseau[$sousrubr['id']]);                                                   
                           }                             
                           $n++;
                }  
                //var_dump($listecategorie);exit;
        }
        
      // fin traitement des sous rubriques  
        
     // pour afficher si posssible d'autre images illustratives ...
        $image = $this->entityManager
                ->getRepository('utbAdminBundle/Article')
                ->getListeMedia($id, 1, $locale);        
     //  fin affichage des  autres images de l'article
             
        $listeactualite =$this->getDoctrine()
                        ->getEntityManager()
                        ->getRepository('utbAdminBundle/Article')
                        ->findAllByLocaleType($locale, 4,10,2); 
        $listeuser = $this->entityManager
                        ->getRepository('App\Entity\User')
                        ->findAll();  
        
    // permet d'afficher les sous menu permettant d'aller plus rapidement dans les autres presentation de rubrique
          $listecorrespondance =$em
                         ->getRepository('App\Entity\Article')
                         ->getListeByParentRubriqueAccueilLocale(9, $locale,6); 
          
        if ($votrecategorie!=null && $votrecategorie->getIdgrandparent()!=0){
             $listecateg = $this->entityManager
                  ->getRepository('App\Entity\Rubrique')
                  ->getRubSousRubPubSansFaq($votrecategorie->getIdgrandparent(),$locale,0); 
        } else{
            $listecateg=null;
        } 
        $this->requestStack->getCurrentRequest()->attributes->set('lrubrique', $larubrique);
        ($larubrique == null)? 
        $this->requestStack->getCurrentRequest()->attributes->set('idrubrik', 0)
                :
        $this->requestStack->getCurrentRequest()->attributes->set('idrubrik', $larubrique[0]['id']); 
      
        ($larubrique == null)? 
        $this->requestStack->getCurrentRequest()->attributes->set('idgrdprub', 0)
                :
        $this->requestStack->getCurrentRequest()->attributes->set('idgrdprub', $larubrique[0]['idGrandParent']);         
        
        ($larubrique == null)? 
        $this->requestStack->getCurrentRequest()->attributes->set('idparentrub', 0)
                :
        $this->requestStack->getCurrentRequest()->attributes->set('idparentrub', $larubrique[0]['idParent']);
        
        if($type==2){
            
            return $this->render('utbSiteBundle/Site/article3.html.twig',array('larticleFrancais'=>$larticleFrancais,
                                                                                'locale'=>$locale,
                                                                                'listeuser'=>$listeuser,
                                                                                'listeactualite'=>$listeactualite,
                                                                                'memeRubrique'=>$memeRubrique,
                                                                                'listecateg'=>$listecateg,
                                                                                'larubrique'=>$larubrique,
                                                                                'listeBanniere'=>$listeBanniere,
                                                                                'articlecategorie'=>$articlecategorie,
                                                                                'listecategorie'=>$listecategorie,
                                                                                'image'=>$image,
                                                                                'listecorrespondance'=>$listecorrespondance,
                                                                                      ));
       }elseif($type==3){
            
            return $this->render('utbSiteBundle/Site/article4.html.twig',array('larticleFrancais'=>$larticleFrancais,
                                                                                'locale'=>$locale,
                                                                                'listeuser'=>$listeuser,
                                                                                'listeactualite'=>$listeactualite,
                                                                                'memeRubrique'=>$memeRubrique,
                                                                                'listecateg'=>$listecateg,
                                                                                'larubrique'=>$larubrique,
                                                                                'listeBanniere'=>$listeBanniere,
                                                                                'articlecategorie'=>$articlecategorie,
                                                                                'listecategorie'=>$listecategorie,
                                                                                'image'=>$image,
                                                                                'listecorrespondance'=>$listecorrespondance,
                                                                                      ));
       }else{
           
            return $this->render('utbSiteBundle/Site/article2.html.twig',array('larticleFrancais'=>$larticleFrancais,
                                                                                'locale'=>$locale,
                                                                                'listeuser'=>$listeuser,
                                                                                'listeactualite'=>$listeactualite,
                                                                                'memeRubrique'=>$memeRubrique,
                                                                                'listecateg'=>$listecateg,
                                                                                'larubrique'=>$larubrique,
                                                                                'listeBanniere'=>$listeBanniere,
                                                                                'listecorrespondance'=>$listecorrespondance,
                ));
        }
    } 
            
    /**
     * Gere le formulaire de contact du site public et la newsletters
     * 
     * @var
     * 
     * Les Variables
     * 
     * $uncontact: Instance d'objet de la classe Contact
     * 
     * $message :  Instance d'objet de classe Message
     * 
     * $email : recupere l'email du service auquel on envoie le message
     * 
     * $listedifNewsletter : Permet de recueillir tout les mail de la liste de diffusion
     * 
     * $mailmessage : Permet de tester l'existence d'un mail
     * 
     * $idliste :recupere l'id de la liste de diffution
     * 
     * $envoimail : sevice qui nous permet d'envoyer les emails la  fonction  se trouvent src/utb/ 
     * 
     * $laliste :
     * 
     * $mailsListe : Tableau recuperant les emails de la liste de diffusion
     * 
     * $tablo : unserialize unserialise le tableau $mailsListe
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @param <integer> $type  permet de distinguer le le contact et la newsletters
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:contact.html.twig 
     * 
     */     
    
    
    
    public function contactAction(): Response(string $locale,string $type): Response 
    {
 
            $this->requestStack->getCurrentRequest()->setLocale($locale);    
            $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
            $em = $this->entityManager;     
            $message=new Message(); 
            $reponse="";$idliste =null;
            $uncontact = new Internaute();
            
            $defaultservice = $em->getRepository("utbAdminBundle:Service")->find(1) ; 
            
        $listeBanniere = $em
                ->getRepository('App\Entity\Menu')
                ->getAllMediasMenu(0,$locale);
        
        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere);
            if ($type == 0) {                
                $message->setCorbeilleMessage(0);
                $message->setMessageLu(0);
                $message->setService($defaultservice);
                $message->setDateEnvoi(new \Datetime);
                $uncontact->addMessage($message);            
            }
            
               $listeAgence =$this->entityManager
                             ->getRepository('App\Entity\Article')
                             ->findAllByStatutLimitDesc(4,$locale,1,5,29); 
               
             $listetheme = $em->getRepository("utbAdminBundle:Objet")->findAllObjet($locale);
            
            if ($type == 0) {
                $form = $this->createForm(new InternauteType($locale), $uncontact);
            } else {
                $form = $this->createForm($this->createForm(NewsletterType::class), $uncontact);                
            }    
            
            $listedifNewsletter = $em->getRepository("utbAdminBundle:ListeDiffusion")->findby(array('typeListeDiffusion' => 1)) ; 
            /* On ne traite que les données passées en méthode POST */
               $request = $request;           
                if ($request->isMethod('POST')) {
                    
//                    $recaptcha = $request->request->get('g-recaptcha-response');
//                    $ip = $_SERVER["REMOTE_ADDR"];
////                    var_dump($this->isRecaptchaValid($recaptcha,$ip));exit;
//                    if($this->isRecaptchaValid($recaptcha,$ip)) {
                    
                    $form->handleRequest($request);
                    $uncontact = $form->getData();                    
                    if ($type == 0) {
                        $email=$message->getService()->getEmailService();
                    }
                    $request = $this->requestStack->getCurrentRequest(); 
                    $recevoir= $request->request->get('recevoir');
//                    $idtheme= $request->request->get('idtheme');
                    //Modification apport�e pour g�rer la prise en compte des r�clamations et alertes ce 09.06.2017 Par Edem
                    $idtheme= $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("ID_THEME_CONTACT")->getValeur();//Le theme suggestion est pris par d�faut
//                    var_dump($idtheme);exit;
                    $mailmessage = $this->entityManager
                                        ->getRepository('App\Entity\Message')
                                        ->getMailMessage2($uncontact->getMailInternaute()); 
                    $siExiste = count($mailmessage) ;
                               
                    $uncontact->setVille(0);                    
                    $uncontact->setPays(null);                    
                    if($siExiste!=0){  
                        //var_dump();exit;
                        
                        //$uncontact=$em->getRepository("utbAdminBundle:Internaute")->find($mailmessage['mailInternaute']);
                        $uncontact=$em->getRepository("utbAdminBundle:Internaute")->find($mailmessage[0][1]);
                        $untheme=$em->getRepository("utbAdminBundle:objet")->find($idtheme);                        
                        $message->setInternaute($uncontact);
                        $message->setObjet($untheme);
                        $em->persist($message);
                        
                    }else{                        
                        $message->setInternaute($uncontact);                                                
                        if ($type == 0) {                            
                            $em->persist($message);                            
                        } else {
                            
                            if ($em->getRepository("utbAdminBundle:Internaute")->find($form->get('mailInternaute')->getData()) ==null ){ 
                                $uncontact->setTypeInternaute(1);
                                $uncontact->setNomPrenom('Newsletter');
                                $uncontact->setVille('Newsletter');
                            }
                            $mailsListe = array();
                            $idliste = $request->get('listediffusion');                             
                            $laliste = $em->getRepository("utbAdminBundle:ListeDiffusion")->find($idliste);                            
                            $mailsListe = $laliste->getLesMails();                            
                            $tablo = unserialize($mailsListe);
                                                        
                            if  ( !in_array( trim($form->get('mailInternaute')->getData()) , $tablo)){
                                $tablo[] =$form->get('mailInternaute')->getData();                                 
                                $laliste->setLesMails(serialize($tablo));                                
                                $em->persist($laliste);                                                       
                            }
                            if ($em->getRepository("admin/Internaute")->find($form->get('mailInternaute')->getData()) ==null ){
                                
                                    $em->persist($uncontact);
                            }        
                            $em->flush();
                        }
                    }
                                        
                    if ($type == 0) {
                        $em->persist($uncontact); 
                        $em->flush($uncontact);
                    }   
                    $emailEmetteur=$this->container->get->getParameter('mailer_user');
                    $mailinternaute=$uncontact->getMailInternaute();
                    if ($type == 0){
                        if($recevoir=="oui"){
//                              var_dump($mailinternaute);
//                            var_dump($recevoir);exit;
                            //var_dump('testeur de projet');exit;
                                
                               
//                                $envoimail =  $this->utb_admin.Mailer->sendMessage($emailEmetteur,$mailinternaute, $message->getTitreMessage(),$message->getContenuMessage());                                       
                                $envoimail =  $this->utb_admin.Mailer->sendMessage($email,$mailinternaute, $message->getTitreMessage(),$message->getContenuMessage());                                       
                                //var_dump('testeur de projet');exit;
                                }                
                    
//                        $envoimail =  $this->utb_admin.Mailer->sendMessage($emailEmetteur,$email, $message->getTitreMessage(),$message->getContenuMessage());                     
                        $envoimail =  $this->utb_admin.Mailer->sendMessage($mailinternaute,$email, $message->getTitreMessage(),$message->getContenuMessage());                     
                        
                    }
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'success');
					
					return $this->redirect($this->generateUrl('utb_site_contact', ['locale' => $locale]));
					
                   
//                    }else{
//                        
//                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'isrobot');
//                        return $this->render('utbSiteBundle/Site/contact.html.twig', array(
//                            'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme , 'annuler'=>1, 
//                        ));
//                    }
                }
                
                
            if ($type == 0){    
                
                return $this->render('utbSiteBundle/Site/contact.html.twig', array(
                        'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme , 'annuler'=>0, 
                    ));
            }  else {                              
                
               return $this->render('utbSiteBundle/Site/newsletter.html.twig', array(
                        'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listedif'=>$listedifNewsletter,
                   ));
            }  
    }  
    
    
    


    /**
     * Gere le formulaire de contact du site public et la newsletters
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listedifNewsletter/ Renvoi la liste des mails de la liste de diffusion
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:newsletter.html.twig 
     * 
     */ 
    
    public function newsletterAction(): Response(string $locale): Response
    {
        
        $em = $this->entityManager; 
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);

        $listedifNewsletter = $em->getRepository("admin/ListeDiffusion")->findby(array('typeListeDiffusion' => 1)) ;     
        
        return $this->render('utbSiteBundle/Site/newsletter.html.twig',array('locale'=>$locale,'listedif'=>$listedifNewsletter));
    }
    

    /**
     * Gere le formulaire de contact du site public et la newsletters
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unsondage / Renvoi la liste des mails de la liste de diffusion
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:sondage.html.twig 
     * 
     */ 
    
    public function sondageAction(): Response(string $locale): Response           
    {

        $em = $this->entityManager; 
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);

        $unsondage = $em->getRepository("utbAdminBundle/Sondage")->findby(array("actif"=>1));
        //var_dump($unsondage);
        $listeopinion = $em->getRepository("utbAdminBundle/SondageOpinion")->findby(array("sondage"=>$unsondage));

        return $this->render('utbSiteBundle/Site/sondage.html.twig',array('locale'=>$locale,'sondage'=>$unsondage,'listeopinion'=>$listeopinion));
    }     

    /**
     * Gere le vote de l'internaute en incrementant les resultats de l'opinion choisie 
     * 
     * @var
     * 
     * Les Variables
     * 
     * $uneopinion : Recupere l'instance de l'objet de l'opinions choisi par l'internaute 
     * 
     * $totalreponse : Incremente le nombre de vote de l'opinions choisi par l'internaute  
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <objet>  return new Response( json_encode(array("result"=>"success")));
     * 
     */     
    
    function voteAction(): Response
    {
                           
        $em =$this->entityManager;	
        $request = $this->requestStack->getCurrentRequest();
        $opinionIds  = $request->request->get('opinionIds');         
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);        
                //$unip=new AdresseIp();                 
                $uneopinion = $em->getRepository("admin/SondageOpinion")->find($opinionIds);
               // var_dump($uneopinion);
                //$uneopinion->addAdresseIp($unip);
                
               // $lip=$em->getRepository("utbAdminBundle/AdresseIp")->findBy(array('ip'=>$_SERVER["REMOTE_ADDR"]));
                if (isset($_COOKIE['Sondage']))   
                {    
                    return new Response( json_encode(array("result"=>"failure")));
                }  
                else   
                {  
                        
                        $totalreponse=$uneopinion->getNbReponse()+1;
                        $uneopinion->setNbReponse($totalreponse);
                        
                        $em->persist($uneopinion);
                        $em->flush();  
                        setcookie('Sondage','Sondage' , time() + 30*24*3600);
                        return new Response( json_encode(array("result"=>"success")));
                        
                }                                    
                        //$unip->setIp($_SERVER["REMOTE_ADDR"]);                    
                        //if ($uneopinion !=null) {
                           // $unip->setSondageopinion($uneopinion);
                       // } 
                        //$em->persist($unip);
                       // $totalreponse=$uneopinion->getNbReponse()+1;
                        //$uneopinion->setNbReponse($totalreponse);       
    }        
 
    /**
     * Gere les recherches sur le site public
     * 
     * @var
     * 
     * Les Variables
     *  
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:recherche.html.twig 
     * 
     */
    public function rechercheAction(): Response(string $locale): Response
    {
           $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale); 
           return $this->render('utbSiteBundle/Site/recherche.html.twig',array('locale'=>$locale,));
    } 
  
    
    /**
     * Gere les recherche sur le site public
     * 
     * @var
     * 
     * Les Variables
     * 
     * $unsondage : Recupere le sondage (Theme active)
     * 
     * $nombreVote : Recupere le nombre total de vote effectue sur un theme
     * 
     * $listeopinion : Recupere les opinions a choisir par l'internaute 
     * 
     * $nbre : Tableau qui recupere le nombre de vote qu'a une opinion
     * 
     * $nbre : Tableau qui recupere le nombre de vote qu'a une opinion
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:index.html.twig 
     * 
     */    
    function resultatAction(): Response
    {    
        
        $em = $this->entityManager;          
        $unsondage = $em->getRepository("admin/Sondage")->findby(array("actif"=>1));
        $nombreVote = $em->getRepository("utbAdminBundle/SondageOpinion")
                         ->getTotalLOpinions($unsondage[0]->getId(),'en') ;
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
            //var_dump($unsondage);
        $listeopinion = $em->getRepository("utbAdminBundle/SondageOpinion")->findby(array("sondage"=>$unsondage));        
        $nbre=array();
        $i=0;
        foreach($listeopinion as $unopinion){            
           $nbre[$i]= $unopinion->getNbReponse();
           $label[$i]= $unopinion->getReponse();
           $deno=(int)$nombreVote[0]['nombreTotal'];
           if( $deno !=0){
              $pourcentage[$i] = ($unopinion->getNbReponse()/$deno)*100;
           }
           $i++; 
        }
        
        /*  $chart = new BarChart();
          $chart  ->setWidth(500)
                  ->setHeight(500)
                  ->setDatas($nbre)
                  ->setlabels($label);
          $url = $this->leg_google_charts->build($chart); */   
        
        return $this->render('utbSiteBundle/Site/resultat.html.twig',array('sondage'=>$unsondage,'listeopinion'=>$listeopinion,'pourcentage'=>$pourcentage,));//,'chart' => $url
         
    }    
        
    public function connexionAction(): Response(string $locale): Response
    {
           return $this->render('utbSiteBundle/Site/connexion.html.twig',array('locale'=>$locale,));
    }  
    
    /**
     * 
     * Gere le formulaire de recherche au niveau du FAQ
     * 
     * @var
     * 
     * Les Variables
     * 
     * $listeRubrikfaq : Rucupere les thematiques de FAQ
     * 
     * $total :Permet d'avoir la liste de tous les abonnes(affiché au niveau de la pagination)
     * 
     * $articles_per_page :    Le nombre de FAQ  selectionne par page
     *
     * $last_page :   le id de page la dernierre page 
     * 
     * $next_page : le id de page la dernierre suivante
     * 
     * $previous_page : le id de page precedent
     * 
     * $listeArticlesfaq : Recupere la liste de FAQ présent sur le site
     * 
     * $listeopinion : Recupere les opinions a choisir par l'internaute 
     * 
     * $nbre : Tableau qui recupere le nombre de vote qu'a une opinion
     * 
     * $nbre : Tableau qui recupere le nombre de vote qu'a une opinion
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site 
     * 
     * @return <string>  retourne le twig utbSiteBundle:Site:index.html.twig 
     * 
     */    
    
    public function rechFAQAction(): Response(string $locale,$page): Response
    {        
        //$listefaq = $em->getRepository("admin/Rubrique")->findby(array("actif"=>1));
        $em =$this->entityManager;
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        $request =$request;
        $id = $request->request->get('theme');
        $listeBanniere = $em
                ->getRepository('utbAdminBundle/Menu')
                ->getAllMediasMenu(0,$locale);
        
        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere);
        
        if ($id == null) $id =0;
        
        
        $listeRubrikfaq = $this->entityManager
                    ->getRepository("utbAdminBundle/Rubrique")
                    ->getThemeFaq($locale);   
        
        $total = $this->entityManager
                    ->getRepository("utbAdminBundle/Rubrique")
                    ->getCountArticlesFaq($id,$locale);  
        
        $articles_per_page = $this->container->get->getParameter('max_articles_on_faq');
        $last_page         = ceil($total / $articles_per_page);
        $previous_page     = $page > 1 ? $page - 1 : 1;
        $next_page         = $page < $last_page ? $page + 1 : $last_page;         
        
        //tous les articles
        $listeArticlesfaq = $this->entityManager
                    ->getRepository("utbAdminBundle:Rubrique")
                    ->getArticlesFaqRub($id,$locale,$page,$articles_per_page);       
        
        return $this->render('utbSiteBundle/Site/allfaqs.html.twig',array('locale'=>$locale,
                                                                          'listearticlefaq'=>$listeArticlesfaq,
                                                                          'listerubriquefaq'=>$listeRubrikfaq, 
                                                                          'articles_per_page'=>$articles_per_page, 
                                                                          'last_page'=>$last_page, 
                                                                          'previous_page'=>$previous_page, 
                                                                          'next_page'=>$next_page, 
                                                                          'total'=>$total, 
                                                                          'id'=>$id,
                                                                          'current_page'=>$page, ));
    }      

    public function pageAction(): Response($page): Response
    {
        if ($page ==0){
           return $this->render('utbSiteBundle/Site/reseau.html.twig',array('page'=>$page,));
        } elseif ($page ==1){   
           return $this->render('utbSiteBundle/Site/presentation.html.twig',array('page'=>$page,)); 
        }elseif ($page ==2){   
           return $this->render('utbSiteBundle/Site/assuranceparticulier.html.twig',array('page'=>$page,)); 
        }elseif ($page ==3){   
           return $this->render('utbSiteBundle/Site/article.html.twig',array('page'=>$page,)); 
        }elseif ($page ==4){   
           return $this->render('utbSiteBundle/Site/reseau.html.twig',array('page'=>$page,)); 
        }
    }     
    
    public function souscriptionAction(): Response(string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        //$em = $this->entityManager;
        $listeBanniere = $this->entityManager
                ->getRepository('admin/Menu')
                ->getAllMediasMenu(0,$locale);
        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere);
        
        return $this->render('utbSiteBundle/Site/souscription.html.twig',array('locale'=>$locale,));
    }
    
    public function videohdAction(): Response(string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        //$this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        $this->infoSite($locale);
        
        return $this->render('utbSiteBundle/Site/videohd.html.twig',array('locale'=>$locale));           
            
    }
    
    
    public function infoSite(): Response(string $locale): Response {       
        
        $unmedia=""; $libCadre=""; $contenuCadre=""; $untypecadre="";$statutEmplacement="";
       
       //Recuperation de l'emplacement - findOneByLocale
        $idboxpub = $this->container->get->getParameter('idboxpub');
        $unemplacement = $this->entityManager
                ->getRepository('utbAdminBundle/Emplacement')
                ->findOneByLocale($idboxpub,$locale);
        //$unemplacement = $em->getRepository("utbAdminBundle:Emplacement")->find(3);
        //$unemplacement = $em->getRepository("utbClientBundle:Emplacement")->getEmplacement(3,$locale);
        //var_dump($unemplacement); exit;
        $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
        if($unemplacement == null){
            
        }
        else{
            $idcadre = $unemplacement['0']->getCadre()->getId();  
            //$statutEmplacement = $unemplacement['0']->getStatutEmplacement();  
            //$uncadre = $em->getRepository("utbAdminBundle:Cadre")->find($idcadre);
            $uncadre = $this->entityManager
                    ->getRepository('App\Entity\Cadre')
                    ->findOneCadre($idcadre);
            //$uncadre->setTranslatableLocale($locale); 
            $uncadre = $uncadre['0'];            
           if ($uncadre != null){
                $unmedia = $uncadre->getMedias();
                $libCadre = $uncadre->getLibCadre();
                $contenuCadre = $uncadre->getContenuCadre();
                $untypecadre = $uncadre->getTypeCadre();//type du cadre pr organiser les champs Ã  afficher sur le twig de modif
                $statutEmplacement = $unemplacement['0']->getStatutEmplacement();//statut de lemplacement
            } 
        }
        
       
            
        //$this->requestStack->getCurrentRequest()->attributes->set('org', $org);
        $this->requestStack->getCurrentRequest()->attributes->set('statutEmplacement', $statutEmplacement);
        $this->requestStack->getCurrentRequest()->attributes->set('unmedia', $unmedia);
        $this->requestStack->getCurrentRequest()->attributes->set('libCadre', $libCadre);
        $this->requestStack->getCurrentRequest()->attributes->set('contenuCadre', $contenuCadre);
        $this->requestStack->getCurrentRequest()->attributes->set('untypecadre', $untypecadre);
   }
   
   
   public function standardAction(): Response($page): Response
    {
        if ($page == 100){
           return $this->render('utbSiteBundle/Standard/rendezvous.html.twig',array('page'=>$page,));
        }elseif($page == 101){
             return $this->render('utbSiteBundle/Standard/simulateur_convertisseur.html.twig',array('page'=>$page,));
        }
        elseif($page == 102){
             return $this->render('utbSiteBundle/Standard/services.html.twig',array('page'=>$page,));
        }
    }
	
	/**
	 * Action de redirection vers le site du partenaire Bpi
	 * @param type $locale
	 * @return RedirectResponse
	 */
	 public function partenaireBpiAction(): Response(): Response
	{	 
		 return $this->redirect('http://www.bpifrance-universite.fr/');
	}

    
    /**
     * Page de gestion des reclamations ce 09.06.2017
     * @author Edem AHOLOUVI <edem.aholouvi@ce3i.com>
     * @param type $locale
     * @param type $type
     * @return type
     */
    public function ReclamationsAction(): Response(string $locale,string $type): Response 
    {
            $this->requestStack->getCurrentRequest()->setLocale($locale);    
            $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
            $em = $this->entityManager;     
            $message = new Message(); 
            $reponse="";$idliste =null;
            $uncontact = new Internaute();
            
            //ID du service reclamations
            $id_service_reclamation = $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("ID_SERVICE_RECLAMATION")->getValeur();
            $defaultservice = $em->getRepository("utbAdminBundle:Service")->find($id_service_reclamation) ; 
			
            
        $listeBanniere = $em
                ->getRepository('App\Entity\Menu')
                ->getAllMediasMenu(0,$locale);
        
        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere);
            if ($type == 0) {                
                $message->setCorbeilleMessage(0);
                $message->setMessageLu(0);
                $message->setService($defaultservice);
                $message->setDateEnvoi(new \Datetime);
                $uncontact->addMessage($message);            
            }
            
               $listeAgence =$this->entityManager
                             ->getRepository('App\Entity\Article')
                             ->findAllByStatutLimitDesc(4,$locale,1,5,29); 
               
             $listetheme = $em->getRepository("utbAdminBundle:Objet")->findAllObjet($locale);
            
            if ($type == 0) {
                $form = $this->createForm(new InternauteReclamationType($locale), $uncontact);
				
            } else {
                $form = $this->createForm($this->createForm(NewsletterType::class), $uncontact);                
            }  

            $listedifNewsletter = $em->getRepository("utbAdminBundle:ListeDiffusion")->findby(array('typeListeDiffusion' => 1)) ; 
            /* On ne traite que les données passées en méthode POST */
               $request = $request;
			   
                if ($request->isMethod('POST')) {
					
				//var_dump($form->getErrors(true)); 
                    
//                    $recaptcha = $request->request->get('g-recaptcha-response');
//                    $ip = $_SERVER["REMOTE_ADDR"];
////                    var_dump($this->isRecaptchaValid($recaptcha,$ip));exit;
//                    if($this->isRecaptchaValid($recaptcha,$ip)) {
                    
                    $form->handleRequest($request);
                    if($form->isValid()){
						$uncontact = $form->getData();                    
                    if ($type == 0) {
                        $email=$message->getService()->getEmailService();
                    }
                    $request = $this->requestStack->getCurrentRequest(); 
                    $recevoir= $request->request->get('recevoir');
//                    $idtheme= $request->request->get('idtheme'); 
                    //Modification apport�e pour g�rer la prise en compte des r�clamations et alertes ce 09.06.2017 Par Edem
                    $idtheme= $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("ID_THEME_CONTACT")->getValeur();//Le theme suggestion est pris par d�faut
                    $mailmessage = $this->entityManager
                                        ->getRepository('App\Entity\Message')
                                        ->getMailMessage2($uncontact->getMailInternaute()); 
                    $siExiste = count($mailmessage) ;
                               
                    $uncontact->setVille(0);                    
                    $uncontact->setPays(null);                    
                    if($siExiste!=0){  
                        //var_dump();exit;
                        
                        //$uncontact=$em->getRepository("utbAdminBundle:Internaute")->find($mailmessage['mailInternaute']);
                        $uncontact=$em->getRepository("utbAdminBundle:Internaute")->find($mailmessage[0][1]);
                        $untheme=$em->getRepository("utbAdminBundle:objet")->find($idtheme);                        
                        $message->setInternaute($uncontact);
                        $message->setObjet($untheme);
                        $em->persist($message);
                        
                    }else{                        
                        $message->setInternaute($uncontact);                                                
                        if ($type == 0) {                            
                            $em->persist($message);                            
                        } else {
                            
                            if ($em->getRepository("utbAdminBundle:Internaute")->find($form->get('mailInternaute')->getData()) ==null ){ 
                                $uncontact->setTypeInternaute(1);
                                $uncontact->setNomPrenom('Newsletter');
                                $uncontact->setVille('Newsletter');
                            }
                            $mailsListe = array();
                            $idliste = $request->get('listediffusion');                             
                            $laliste = $em->getRepository("utbAdminBundle:ListeDiffusion")->find($idliste);                            
                            $mailsListe = $laliste->getLesMails();                            
                            $tablo = unserialize($mailsListe);
                                                        
                            if  ( !in_array( trim($form->get('mailInternaute')->getData()) , $tablo)){
                                $tablo[] =$form->get('mailInternaute')->getData();                                 
                                $laliste->setLesMails(serialize($tablo));                                
                                $em->persist($laliste);                                                       
                            }
                            if ($em->getRepository("utbAdminBundle:Internaute")->find($form->get('mailInternaute')->getData()) ==null ){
                                
                                    $em->persist($uncontact);
                            }        
                            $em->flush();
                        }
                    }
                                        
                    if ($type == 0) {
                        $em->persist($uncontact); 
                        $em->flush($uncontact);
                    }   
                    $emailEmetteur=$this->container->get->getParameter('mailer_user');
                    if ($type == 0){
                        $corpsMessage = "";
//                        var_dump($form->get('numeroCompte')->getData());exit;
                        if($form->get('numeroCompte')->getData() != ""){
                            $corpsMessage = "Numero de compte : " . $form->get('numeroCompte')->getData() . 
                                    
                                    
                                    "\r\n" . 
                                    
                                    $message->getContenuMessage();
                        }else{
                            $corpsMessage = $message->getContenuMessage();
                        }
                        $mailinternaute=$uncontact->getMailInternaute();
                        if($recevoir=="oui"){
//                              var_dump($mailinternaute);
//                            var_dump($recevoir);exit;
                            //var_dump('testeur de projet');exit;
                                
                                  
//                                $envoimail =  $this->utb_admin.Mailer->sendMessage($emailEmetteur,$mailinternaute, $message->getTitreMessage(),$corpsMessage);                                       
                                $envoimail =  $this->utb_admin.Mailer->sendMessage($email,$mailinternaute, $message->getTitreMessage(),$corpsMessage);                                       
                                
                                //var_dump('testeur de projet');exit;
                                } 
                        
//                        $envoimail =  $this->utb_admin.Mailer->sendMessage($emailEmetteur,$email, $message->getTitreMessage(), nl2br($corpsMessage)); 
                        $envoimail =  $this->utb_admin.Mailer->sendMessage($mailinternaute,$email, $message->getTitreMessage(), nl2br($corpsMessage)); 
                        
                        
                    }
                   
				   $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'success');
				   
				   return $this->redirect($this->generateUrl('utb_site_reclamations', ['locale' => $locale]));
                    
                    
//                    }else{
//                        
//                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'isrobot');
//                        return $this->render('utbSiteBundle/Site/reclamations.html.twig', array(
//                            'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme , 'annuler'=>1, 
//                        ));
//                    }
					}
                    
                }
				
            if ($type == 0){    
                
                return $this->render('utbSiteBundle/Site/reclamations.html.twig', array(
                        'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme  , 'annuler'=>0, 
                    ));
            }  else {                              
                
               return $this->render('utbSiteBundle/Site/newsletter.html.twig', array(
                        'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listedif'=>$listedifNewsletter,
                   ));
            }  
    }
    
    /**
     * Page de gestion des alertes ce 09.06.2017
     * @author Edem AHOLOUVI <edem.aholouvi@ce3i.com>
     * @param type $locale
     * @param type $type
     * @return type
     */
    public function AlertesAction(): Response(string $locale,string $type): Response 
    {
 
            $this->requestStack->getCurrentRequest()->setLocale($locale);    
            $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
            $em = $this->entityManager;     
            $message=new Message(); 
            $reponse="";$idliste =null;
            $uncontact = new Internaute();
            
            //ID du service alerte
            $id_service_alerte = $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("ID_SERVICE_ALERTE")->getValeur();
            $defaultservice = $em->getRepository("utbAdminBundle:Service")->find($id_service_alerte) ;
            
        $listeBanniere = $em
                ->getRepository('App\Entity\Menu')
                ->getAllMediasMenu(0,$locale);
        
        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere);
            if ($type == 0) {                
                $message->setCorbeilleMessage(0);
                $message->setMessageLu(0);
                $message->setService($defaultservice);
                $message->setDateEnvoi(new \Datetime);
                $uncontact->addMessage($message);            
            }
            
               $listeAgence =$this->entityManager
                             ->getRepository('App\Entity\Article')
                             ->findAllByStatutLimitDesc(4,$locale,1,5,29); 
               
             $listetheme = $em->getRepository("utbAdminBundle:Objet")->findAllObjet($locale);
            
            if ($type == 0) {
                $form = $this->createForm(new InternauteAlerteType($locale), $uncontact);
            } else {
                $form = $this->createForm($this->createForm(NewsletterType::class), $uncontact);                
            } 
            
            $listedifNewsletter = $em->getRepository("utbAdminBundle:ListeDiffusion")->findby(array('typeListeDiffusion' => 1)) ; 
            /* On ne traite que les données passées en méthode POST */
               $request = $request;           
                if ($request->isMethod('POST')) { 
                    
//                    $recaptcha = $request->request->get('g-recaptcha-response');
//                    $ip = $_SERVER["REMOTE_ADDR"];
////                    var_dump($this->isRecaptchaValid($recaptcha,$ip));exit;
//                    if($this->isRecaptchaValid($recaptcha,$ip)) {
                        
                    $form->handleRequest($request);
                    $uncontact = $form->getData(); 
                    
                    //if(($uncontact->getMailInternaute() == "") || ($uncontact->getMailInternaute() == null) ){
                        $mailAnonyme= $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("MAIL_ANONYME")->getValeur();
                        $nomAnonyme= $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("NOM_ANONYME")->getValeur();
                        $uncontact->setMailInternaute($mailAnonyme);
                        $uncontact->setNomPrenom($nomAnonyme);
                    //}
                    
                    if ($type == 0) {
                        $email=$message->getService()->getEmailService();
                    }
                    $request = $this->requestStack->getCurrentRequest(); 
//                    $idtheme= $request->request->get('idtheme');
                    //Modification apport�e pour g�rer la prise en compte des r�clamations et alertes ce 09.06.2017 Par Edem
                    $idtheme= $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("ID_THEME_CONTACT")->getValeur();//Le theme suggestion est pris par d�faut
                    
                    $mailmessage = $this->entityManager
                                        ->getRepository('App\Entity\Message')
                                        ->getMailMessage2($uncontact->getMailInternaute()); 
                    $siExiste = count($mailmessage) ;
                               
                    $uncontact->setVille(0);                    
                    $uncontact->setPays(null);                    
                    if($siExiste!=0){  
                        //var_dump();exit;
                        
                        //$uncontact=$em->getRepository("utbAdminBundle:Internaute")->find($mailmessage['mailInternaute']);
                        $uncontact=$em->getRepository("utbAdminBundle:Internaute")->find($mailmessage[0][1]);
                        $untheme=$em->getRepository("utbAdminBundle:objet")->find($idtheme);                        
                        $message->setInternaute($uncontact);
                        $message->setObjet($untheme);
                        $em->persist($message);
                        
                    }else{                        
                        $message->setInternaute($uncontact);                                                
                        if ($type == 0) {                            
                            $em->persist($message);                            
                        } else {
                            
                            if ($em->getRepository("utbAdminBundle:Internaute")->find($form->get('mailInternaute')->getData()) ==null ){ 
                                $uncontact->setTypeInternaute(1);
                                $uncontact->setNomPrenom('Newsletter');
                                $uncontact->setVille('Newsletter');
                            }
                            $mailsListe = array();
                            $idliste = $request->get('listediffusion');                             
                            $laliste = $em->getRepository("utbAdminBundle:ListeDiffusion")->find($idliste);                            
                            $mailsListe = $laliste->getLesMails();                            
                            $tablo = unserialize($mailsListe);
                                                        
                            if  ( !in_array( trim($form->get('mailInternaute')->getData()) , $tablo)){
                                $tablo[] =$form->get('mailInternaute')->getData();                                 
                                $laliste->setLesMails(serialize($tablo));                                
                                $em->persist($laliste);                                                       
                            }
                            if ($em->getRepository("utbAdminBundle:Internaute")->find($form->get('mailInternaute')->getData()) ==null ){
                                
                                    $em->persist($uncontact);
                            }        
                            $em->flush();
                        }
                    }
                                        
                    if ($type == 0) {
                        $em->persist($uncontact); 
                        $em->flush($uncontact);
                    }   
                    $emailEmetteur=$this->container->get->getParameter('mailer_user');
                    $mailinternaute=$uncontact->getMailInternaute();
                    if ($type == 0){
                                     
                    
//                        $envoimail =  $this->utb_admin.Mailer->sendMessage($emailEmetteur,$email, $message->getTitreMessage(),$message->getContenuMessage()); 
                        $envoimail =  $this->utb_admin.Mailer->sendMessage($mailinternaute,$email, $message->getTitreMessage(),$message->getContenuMessage()); 
                        
                    }
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'success');
                    
					return $this->redirect($this->generateUrl('utb_site_alertes', ['locale' => $locale]));
					
                    
//                    }else{
//                        
//                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'isrobot');
//                        return $this->render('utbSiteBundle/Site/alertes.html.twig', array(
//                            'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme , 'annuler'=>1, 
//                        ));
//                    }                    
                    
                } 
            if ($type == 0){    
                
                return $this->render('utbSiteBundle/Site/alertes.html.twig', array(
                        'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme , 'annuler'=>0 , 
                    ));
            }  else {                              
                
               return $this->render('utbSiteBundle/Site/newsletter.html.twig', array(
                        'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listedif'=>$listedifNewsletter,
                   ));
            }  
    }
    
    public function recaptchaTestAction(): Response(string $locale,string $type): Response 
    {
 
            $this->requestStack->getCurrentRequest()->setLocale($locale);    
            $this->requestStack->getCurrentRequest()Stack->getSession()->set('_locale', $locale);
            $em = $this->entityManager;     
            $message=new Message(); 
            $reponse="";$idliste =null;
            $uncontact = new Internaute();
            
            //ID du service alerte
            $id_service_alerte = $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("ID_SERVICE_RECAPTCHA")->getValeur();
            $defaultservice = $em->getRepository("utbAdminBundle:Service")->find($id_service_alerte) ;
            
        $listeBanniere = $em
                ->getRepository('App\Entity\Menu')
                ->getAllMediasMenu(0,$locale);
        
        $this->requestStack->getCurrentRequest()->attributes->set('listebanniere', $listeBanniere);
            if ($type == 0) {                
                $message->setCorbeilleMessage(0);
                $message->setMessageLu(0);
                $message->setService($defaultservice);
                $message->setDateEnvoi(new \Datetime);
                $uncontact->addMessage($message);            
            }
            
               $listeAgence =$this->entityManager
                             ->getRepository('App\Entity\Article')
                             ->findAllByStatutLimitDesc(4,$locale,1,5,29); 
               
             $listetheme = $em->getRepository("utbAdminBundle:Objet")->findAllObjet($locale);
            
            if ($type == 0) {
                $form = $this->createForm(new InternauteAlerteType($locale), $uncontact);
            } else {
                $form = $this->createForm($this->createForm(NewsletterType::class), $uncontact);                
            } 
            
            $listedifNewsletter = $em->getRepository("utbAdminBundle:ListeDiffusion")->findby(array('typeListeDiffusion' => 1)) ; 
            /* On ne traite que les données passées en méthode POST */
               $request = $request;           
                if ($request->isMethod('POST')) { 
                    
                    $recaptcha = $request->request->get('g-recaptcha-response');
                    $ip = $_SERVER["REMOTE_ADDR"];
                    if($this->isRecaptchaValid($recaptcha,$ip)) {
                        
                    $form->handleRequest($request);
                    $uncontact = $form->getData(); 
                    
                    //if(($uncontact->getMailInternaute() == "") || ($uncontact->getMailInternaute() == null) ){
                        $mailAnonyme= $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("MAIL_ANONYME")->getValeur();
                        $nomAnonyme= $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("NOM_ANONYME")->getValeur();
                        $uncontact->setMailInternaute($mailAnonyme);
                        $uncontact->setNomPrenom($nomAnonyme);
                    //}
                    
                    if ($type == 0) {
                        $email=$message->getService()->getEmailService();
                    }
                    $request = $this->requestStack->getCurrentRequest(); 
//                    $idtheme= $request->request->get('idtheme');
                    //Modification apport�e pour g�rer la prise en compte des r�clamations et alertes ce 09.06.2017 Par Edem
                    $idtheme= $em->getRepository("utbClientBundle:ParamSysteme")->findOneByCle("ID_THEME_CONTACT")->getValeur();//Le theme suggestion est pris par d�faut
                    
                    $mailmessage = $this->entityManager
                                        ->getRepository('App\Entity\Message')
                                        ->getMailMessage2($uncontact->getMailInternaute()); 
                    $siExiste = count($mailmessage) ;
                               
                    $uncontact->setVille(0);                    
                    $uncontact->setPays(null);                    
                    if($siExiste!=0){  
                        //var_dump();exit;
                        
                        //$uncontact=$em->getRepository("utbAdminBundle:Internaute")->find($mailmessage['mailInternaute']);
                        $uncontact=$em->getRepository("utbAdminBundle:Internaute")->find($mailmessage[0][1]);
                        $untheme=$em->getRepository("utbAdminBundle:objet")->find($idtheme);                        
                        $message->setInternaute($uncontact);
                        $message->setObjet($untheme);
                        $em->persist($message);
                        
                    }else{                        
                        $message->setInternaute($uncontact);                                                
                        if ($type == 0) {                            
                            $em->persist($message);                            
                        } else {
                            
                            if ($em->getRepository("utbAdminBundle:Internaute")->find($form->get('mailInternaute')->getData()) ==null ){ 
                                $uncontact->setTypeInternaute(1);
                                $uncontact->setNomPrenom('Newsletter');
                                $uncontact->setVille('Newsletter');
                            }
                            $mailsListe = array();
                            $idliste = $request->get('listediffusion');                             
                            $laliste = $em->getRepository("utbAdminBundle:ListeDiffusion")->find($idliste);                            
                            $mailsListe = $laliste->getLesMails();                            
                            $tablo = unserialize($mailsListe);
                                                        
                            if  ( !in_array( trim($form->get('mailInternaute')->getData()) , $tablo)){
                                $tablo[] =$form->get('mailInternaute')->getData();                                 
                                $laliste->setLesMails(serialize($tablo));                                
                                $em->persist($laliste);                                                       
                            }
                            if ($em->getRepository("utbAdminBundle:Internaute")->find($form->get('mailInternaute')->getData()) ==null ){
                                
                                    $em->persist($uncontact);
                            }        
                            $em->flush();
                        }
                    }
                                        
                    if ($type == 0) {
                        $em->persist($uncontact); 
                        $em->flush($uncontact);
                    }   
                    $emailEmetteur=$this->container->get->getParameter('mailer_user');
                     $mailinternaute=$uncontact->getMailInternaute();
                    
                    if ($type == 0){
                                     
                    
//                        $envoimail =  $this->utb_admin.Mailer->sendMessage($emailEmetteur,$email, $message->getTitreMessage(),$message->getContenuMessage()); 
                        $envoimail =  $this->utb_admin.Mailer->sendMessage($mailinternaute,$email, $message->getTitreMessage(),$message->getContenuMessage()); 
                        
                    }
                    $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'success');
                    return $this->render('utbSiteBundle/Site/recaptchatest.html.twig', array(
                            'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme , 'annuler'=>1 , 
                        ));
                    
                    }else{
                        
                        $this->requestStack->getCurrentRequest()Stack->getSession()->getFlashBag()->add('notice', 'isrobot');
                        return $this->render('utbSiteBundle/Site/recaptchatest.html.twig', array(
                            'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme , 'annuler'=>1, 
                        ));
                    }                    
                    
                } 
            if ($type == 0){    
                
                return $this->render('utbSiteBundle/Site/recaptchatest.html.twig', array(
                        'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listeAgence'=>$listeAgence,'listetheme'=>$listetheme , 'annuler'=>0 , 
                    ));
            }  else {                              
                
               return $this->render('utbSiteBundle/Site/newsletter.html.twig', array(
                        'form' => $form->createView(), 'reponse'=>$reponse, 'locale'=>$locale,'listedif'=>$listedifNewsletter,
                   ));
            }  
    }
    
    function isRecaptchaValid($code, $ip){
	
        $secret= '6LdPHiUUAAAAAB2131J-q4pFZmhKT4dArn2pPLrd';        
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $params = '?secret=' . $secret . '&response=' . $code . '&remoteip=' . $ip;
        
//        $url = $this->url . 'granted/generation/code?celAb=' . $celAbonne . '&id=' . $id;
  
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Recaptcha UTB');
        $reponse = curl_exec($curl_handle);
//        if(curl_errno($curl_handle))
//        {
//            echo 'Erreur Curl : ' . curl_error($curl_handle);
//        }
        curl_close($curl_handle);
        return $reponse;
        
//        var_dump($reCaptcha); exit;
		
    }
        
  }

