<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\ModifPwdType;
use App\Entity\PhotoType;
use App\Entity\ModifFicheUserType;
use App\Entity\ModUserType;
use App\Entity\Parametrage;
use App\Entity\Type\RegistrationFormType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

class UserController extends AbstractController
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
    }

    #[Route("/{locale}/user/edit", name: "user_edit")]
    public function edit(): Response(string $locale, Request $request): Response
    {
        $em = $this->entityManager;
        $AccessControl = $this->accessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'EditAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_dashboard', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unUser = new User();
        $form = $this->createForm(UserType::class, $unUser);

        $listestat = $this->entityManager->getRepository('Statistique::class')
            ->getStatProfilLocale($locale, $type = 1);

        $extensions = array('jpg', 'png', 'jpeg', 'gif');

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if (strlen($unUser->getUsername()) < 5) {
                $this->addFlash('notice', 'errorsmalllogin');
                return $this->render('user/Edit.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat
                ));
            }

            $mail = $unUser->getEmail();
            $login = $unUser->getUsername();
            $password = $form["password"]->getData();
            $cpassword = $form["cpassword"]->getData();

            $atom = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';
            $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';

            $regex = '/^' . $atom . '+' .
                '(\.' . $atom . '+)*' .
                '@' .
                '(' . $domain . '{1,63}\.)+' .
                $domain . '{2,63}$/i';

            if (!preg_match($regex, $mail)) {
                $this->addFlash('notice', 'emailformaterror');
                return $this->render('user/Edit.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat
                ));
            }

            if ($password != $cpassword) {
                $this->addFlash('notice', 'passworderror');
                return $this->render('user/Edit.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat
                ));
            }

            if (strlen($password) <= 4) {
                $this->addFlash('notice', 'smallpassworderror');
                return $this->render('user/Edit.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
                ));
            }

            $email = $this->entityManager->getRepository('User::class')
                ->findByEmail($mail);

            $unlogin = $this->entityManager->getRepository('User::class')
                ->findByLogin($login);

            if ($unlogin != null && $unlogin != 0) {
                $this->addFlash('notice', 'loginerror');
                return $this->render('user/Edit.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
                ));
            }

            if ($unUser->photo !== null && !in_array($unUser->photo->guessExtension(), $extensions)) {
                $this->addFlash('notice', 'errortypficart');
                return $this->render('user/Edit.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
                ));
            }

            if ($unUser->getUrlPhoto() == "") {
                $unUser->setUrlPhoto('default_photo$' . rand(0, 100000) . ".png");
            }

            if ($email != null && $email == 0) {
                $unUser->setPlainPassword($unUser->getPassword());
                $unUser->setEnabled(1);

                $em->persist($unUser);

                $this->addFlash('notice', 'success');
            } else {
                $this->addFlash('notice', 'emailerror');
                return $this->render('user/Edit.html.twig', array(
                    'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
                ));
            }
        }

        return $this->render('user/Edit.html.twig', array(
            'form' => $form->createView(), 'locale' => $locale, 'listestat' => $listestat,
        ));
    }

    #[Route("/{locale}/user/{id}/edit/{genre}", name: "user_modifier")]
    public function modifier(): Response(int $id,string $locale,string $genre): Response
    { 
        $em =$this->entityManager;	
        $AccessControl =  $this->accessControl;
        $checkAcces =  $AccessControl->verifAcces($em,'modifierAction', $this->container->get );
        
         if(!$checkAcces){
              $this->addFlash('accesdenied', "admin.layout.accesdenied");
              return $this->redirect ( $this->generateUrl('admin_dashboard', ['locale' =>$locale]));
         }        
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $unUser = $em->getRepository(User::class)->find($id);
        $ancienpwd = $unUser->getPassword();
        $extensions = array('jpg','png','jpeg','gif');
        
        if($genre==1){
            $form = $this->createForm($this->createForm(PhotoType::class),$unUser);
        }else{
            $form = $this->createForm(UserType::class, $unUser );                        
        }
  
        $listestat = $this->entityManager->getRepository('Statistique::class')
                ->getStatProfilLocale($locale,$type=1);          
        
       $request = $this->requestStack->getCurrentRequest();
       
       if ($request->isMethod('POST')) { 
                   
           $form->handleRequest($request);
               
           $unUser->setEnabled(0);

           if($genre==1){   
               $unUser->setPassword($ancienpwd);
               $unUser->photo->move($unUser->getUploadRootDir(), $unUser->getUrlPhoto());               
           }else{ 
                $unUser->setPassword($ancienpwd);
                $em->persist($unUser);             
                $em->flush();
            }  
          

             $unUser->photo = null;
           
           
           if($genre==1){
               $this->addFlash('notice', 'successmodifmediaart');
              return $this->redirect($this->generateUrl("detail_utilisateur",array('id'=>$id,'locale' =>$locale,))); 
           }else{
               $this->addFlash('notice', 'modifsuccess');
              return $this->redirect($this->generateUrl("liste_utilisateur",array('locale' =>$locale,)));                
           }           
       }
    if($genre==1){       
        return $this->render('user/ajoutPhoto.html.twig',array('form' =>$form->createView(),'id'=>$id,'locale'=>$locale, 'genre'=>$genre));       
    }else{
       return $this->render('user/modifUser.html.twig',array('form' =>$form->createView(),'id'=>$id,'locale'=>$locale, 'listestat'=>$listestat));               
    }         
    }
    
    #[Route("/{locale}/user/{id}/etat/{etat}", name: "user_gerer_etat")]
    public function gererEtat(): Response(int $id,int $etat,string $locale): Response
    {
        $em =$this->entityManager;	
        $AccessControl =  $this->accessControl;
        $checkAcces =  $AccessControl->verifAcces($em,'gererEtatAction', $this->container->get );
        
         if(!$checkAcces){
              $this->addFlash('accesdenied', "admin.layout.accesdenied");
              return $this->redirect ( $this->generateUrl('admin_dashboard', ['locale' =>$locale]));
         }        
        
        $this->requestStack->getCurrentRequest()->setLocale($locale);

       
       $unUser = $em->getRepository(User::class)->find($id);        
       $unUser->setEnabled($etat)     ;
       
       $em->persist($unUser);           
       $em->flush();
           
       return $this->redirect($this->generateUrl("liste_utilisateur",array('locale' =>$locale,)));       
         
    }    
    
    #[Route("/{locale}/users", name: "liste_utilisateur")]
    public function index(): Response(string $locale): Response
    {   
        $em =$this->entityManager;	
        $AccessControl =  $this->accessControl;
        $checkAcces =  $AccessControl->verifAcces($em,'listeAction', $this->container->get );
        
         if(!$checkAcces){
              $this->addFlash('accesdenied', "admin.layout.accesdenied");
              return $this->redirect ( $this->generateUrl('admin_dashboard', ['locale' =>$locale]));
         }        
  //     $repertoire = $this-> getDoctrine()->getManager()->getRepository();
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
    	$listeUser = $this->entityManager->getRepository(User::class)
                ->findAllByLocale($locale);
        
        $listestat = $this->entityManager->getRepository('Statistique::class')
                ->getStatProfilLocale($locale,$type=1);          
        
        
    	$boxinfos = $this->entityManager->getRepository("utbAdminBundle:Parametrage")
                ->getTexteBoxInfos($locale,$type=9);
        
        $user = $this->security->getToken()->getUser()->getId();

        return $this->render('user/listeUser.html.twig', array('listeUser' => $listeUser, 'locale'=>$locale,'infos'=>$boxinfos,'listestat'=>$listestat, 'userid'=>$user ));
              
    } 
    
    #[Route("/{locale}/user/{id}", name: "detail_utilisateur")]
    public function show(): Response(int $id,string $locale): Response
    {   
        $em =$this->entityManager;	
        $AccessControl =  $this->accessControl;
        $checkAcces =  $AccessControl->verifAcces($em,'detailUtilisateurAction', $this->container->get );
        
         if(!$checkAcces){
              $this->addFlash('accesdenied', "admin.layout.accesdenied");
              return $this->redirect ( $this->generateUrl('admin_dashboard', ['locale' =>$locale]));
         }        
  //     $repertoire = $this-> getDoctrine()->getManager()->getRepository();
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        
        $listestat = $this->entityManager->getRepository('Statistique::class')
                ->getStatProfilLocale($locale,$type=1);          
        
    	$unUser = $this->entityManager->getRepository(User::class)
                ->findOneByLocale($id,$locale);
        
        $user = $this->security->getToken()->getUser()->getId();
        

        return $this->render('user/detailUser.html.twig', array('unUser' => $unUser, 'locale'=>$locale,'listestat'=>$listestat,'userid'=>$user, ));
              
    } 
    
    //supprimer définitivement une sélection d'utilisateurs
    public function deleteAll(): Response(): Response{
        $em =$this->entityManager;	
        $request = $this->requestStack->getCurrentRequest();
        $usersIds  = $request->request->get('usersIds'); 
        $usersIds = explode("|",$usersIds);
        
        $letest = null;
        $letest = true;  
        
        $user =  null;
        $user = $this->security->getToken()->getUser()->getId();
                
        
        foreach($usersIds as $key=>$value){
            
             if(!empty($value)){
                $unuser = $em->getRepository(User::class)->find($value);
                
                
                $actionuser= null;
                $actionuser = $this->entityManager->getRepository('User::class')
                        ->findUserAction($value); 

                if ($actionuser !=0){

                    return new Response( json_encode(array("result"=>"operationerror")));

                }    
                
                
                if ( $unuser->getId() != $user  ) {
                    $em->remove($unuser);
                    $em->flush();   
                } else {
                   $letest = false;  
                }
             }/* else {
                   $letest = false;  
             }*/
         }
         
         if ($letest == true) {
            return new Response( json_encode(array("result"=>"success"))); 
         } else{         
            return new Response( json_encode(array("result"=>"error")));
         }
    }  
    
    public function activate(): Response(string $locale,int $id): Response{
        $em =$this->entityManager;	
        $request = $this->requestStack->getCurrentRequest();
        $userId  = $id;         
        $user =  null;
        $user = $this->security->getToken()->getUser()->getId(); 
         
        /*$listestat = $this->entityManager->getRepository('Statistique::class')
                ->getInfoOrStat($typeStat = 4, $locale , $valeur = 0, $article = null); */ 
        
        $listestat = $this->entityManager->getRepository('Statistique::class')
                ->getStatProfilLocale($locale,$type=1);          
        
             if(!empty($userId)){
                $unuser = $em->getRepository(User::class)->find($userId);                
                $unuser->setEnabled(1);  
                $em->persist($unuser);                
                $em->flush();                 
             }
             $this->addFlash('notice', 'activesuccess');
          /* ... et on redirige vers la page d'administration des users */
        return $this->redirect($this->generateUrl('liste_utilisateur',array('locale' =>$locale,)));
    }
    
    public function deactivate(): Response(string $locale,int $id): Response{
        $em =$this->entityManager;	
        $request = $this->requestStack->getCurrentRequest();
        $userId  = $id;         
        $user =  null;
        $user = $this->security->getToken()->getUser()->getId(); 
         
        /*$listestat = $this->entityManager->getRepository('Statistique::class')
                ->getInfoOrStat($typeStat = 4, $locale , $valeur = 0, $article = null);  */
        
        $listestat = $this->entityManager->getRepository('Statistique::class')
                ->getStatProfilLocale($locale,$type=1);          
        
             if(!empty($userId)){
                $unuser = $em->getRepository(User::class)->find($userId);                
                $unuser->setEnabled(0);  
                $em->persist($unuser);                
                $em->flush();                 
             }
             $this->addFlash('notice', 'desactivesuccess');
           /* ... et on redirige vers la page d'administration des users */
        return $this->redirect($this->generateUrl('liste_utilisateur',array('locale' =>$locale,)));
    }
    
    public function activateAll(): Response(): Response {

        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $usersIds = $request->request->get('usersIds');
        $usersIds = explode("|", $usersIds);
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les id users
        foreach ($usersIds as $key => $value) {
            if (!empty($value)) {
                $unuser = $em->getRepository(User::class)->find($value);
                //Activation 
                
                if($unuser->getProfil()->getEtatProfil()== 0){
                  return new Response(json_encode(array("result" => "profildesactive")));    
                }else{              
                    $unuser->setEnabled(1); 
                }
                $em->persist($unuser);
                $em->flush();
            }            
        }
        //$em->flush();   
        return new Response(json_encode(array("result" => "success")));
    }
    
    public function deactivateAll(): Response(): Response {

        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $usersIds = $request->request->get('usersIds');
        $usersIds = explode("|", $usersIds);
         
        $user = $this->security->getToken()->getUser()->getId();
        //boucle sur les id users
        foreach ($usersIds as $key => $value) {
            if (!empty($value)) {                
                $unuser = $em->getRepository(User::class)->find($value);
                //Activation 
                $unuser->setEnabled(0); 
                $em->persist($unuser);
                $em->flush();
            }            
        }
        //$em->flush();   
      return new Response(json_encode(array("result" => "success"))); 
    }
    
    public function listProfiles(): Response(string $locale): Response{
        
        
        $letexte = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('utbAdminBundle/Parametrage')
                ->getTitreDescription($locale,$type=10);        
        
        $listeuser = $this->entityManager->getRepository('User/:class')
                ->findAll();
        
        $listearticlerecent = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('admin/Article')
                ->findAllByLocaleType($locale,4,5,0); 

        //Texte à afficcher dans l'accueil    
        $listearticleattente = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('utbAdminBundle/Article')
                ->findAllByLocaleAttente5($locale);
        
        $listearticlesoumis = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('App\Entity\Article')
                ->findAllByLocaleType($locale,2,5,0);          
      $this->requestStack->getCurrentRequest()->setLocale($locale);  
        return $this->render('user/homeUserProfil.html.twig',
        array(
            'locale'=>$locale, 
            'listearticlerecent'=>$listearticlerecent,            
            'letexte'=>$letexte,'listeuser' => $listeuser,
            'listearticleattente'=>$listearticleattente,
            'listearticlesoumis'=>$listearticlesoumis,
            )        
                );  
    }
    
    
    
    #[Route("/{locale}/user/{id}/type/{type}", name: "modif_suivant_type")]
    public function editByType(): Response(int $id,string $type,string $locale): Response {

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        //code qui verifie si l'utilisateur courant a acces a cette action
        $em = $this->entityManager;
        /*$AccessControl = $this->accessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifSuivantTypeAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('admin_dashboard', ['locale' => $locale]));
        }*/

        // récuperation de l'user en question
	$unUser = $em->getRepository(User::class)->find($id);

        //recuperation de l'ancien pwd
        $ancienpwd = $unUser->getPassword();
        
        // Si l'user en question existe: creation des formulaire suivant le type de modification
        if ($unUser != null)  {               
            if ($type == 2)  {                 
                $form = $this->createForm(ModifFicheUserType::class, $unUser );                
            } 
            elseif ($type == 1) {
                $form = $this->createForm(ModifPwdType()::class, $unUser );							
            }    
        }    
    
        // chargement du type de statistiques
        $listestat = $this->entityManager->getRepository('Statistique::class')
                ->getStatProfilLocale($locale,1);          
        
        
       
        
        
        if ($request->isMethod('POST')) {  
            
            //application des donnees au formulaire 
            $form->handleRequest($request); 
            
            if ($type == 2)  {
                
                //recuperation du mail saisi ou actuel pour test
                $mail = $unUser->getEmail();

                /*** Controle du format de l'email **/
                $atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
                $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)

                $regex = '/^' . $atom . '+' .    // Une ou plusieurs fois les caractères autorisés avant l'arobase
                '(\.' . $atom . '+)*' .          // Suivis par zéro point ou plus
                                                 // séparés par des caractères autorisés avant l'arobase
                '@' .                            // Suivis d'un arobase
                '(' . $domain . '{1,63}\.)+' .   // Suivis par 1 à 63 caractères autorisés pour le nom de domaine
                                                 // séparés par des points
                $domain . '{2,63}$/i';           // Suivi de 2 à 63 caractères autorisés pour le nom de domaine

                // test de l'adresse e-mail
                if (!preg_match($regex, $mail)) {
                     $this->addFlash('notice', 'emailformaterror');
                     return $this->render('user/ModifFicheUser.html.twig',array(
                     'form' =>$form->createView(),'locale' =>$locale,'listestat'=>$listestat,'unuser'=>$unUser,
                     ));
                }     
               
                // controle de verif si le champ email existe deja
                $email = $this->entityManager->getRepository('User::class')
                 ->findByEmail($mail); 
                
           }	           
                      
           // cas ou on modifie juste le mot de passe 
           if ($type == 1)  {               
                // ancien password saisi
                $vpassword = $request->request->get('vpassword');
              
                /************************/                 
                $pass = $vpassword;
                $salt = $unUser->getSalt();
                $iterations = 5000; 

                $salted = $pass.'{'.$salt.'}';
                $digest = hash('sha512', $salted, true);

                for ($i = 1; $i < $iterations; $i++) {
                    $digest = hash('sha512', $digest.$salted, true);
                }
                $cryptedPass = base64_encode($digest);   
                
                /*************************/                
                
                // test pour vérifier si le pwd saisi et crypte = pwd de la bd
                if ( $ancienpwd != $cryptedPass ) {

                    $this->addFlash('notice', 'errancienpwd');
                    /*return $this->render('user/modifPwdUser.html.twig',array(
                                         'form' =>$form->createView(),'locale' =>$locale,'listestat'=>$listestat,
                                         'unuser'=>$unUser,
                    ));   */    
                    return $this->redirect($this->generateUrl("detail_utilisateur",array('id'=>$id,'locale' =>$locale,)));
                }

                // test si pwd new = pwd new confirmation
                if( $form["password"]->getData() != $form["cpassword"]->getData() ){
                     $this->addFlash('notice', 'passworderror');
                     /*
                     return $this->render('user/modifPwdUser.html.twig',array(
                     'form' =>$form->createView(),'locale' =>$locale,'listestat'=>$listestat,'unuser'=>$unUser,                     
                     ));*/
                     return $this->redirect($this->generateUrl("detail_utilisateur",array('id'=>$id,'locale' =>$locale,)));
                } 
           }
        
        if ( ( ($type == 2) && ($email !=null) && ( $email !=0 ) ) or ($type == 1) ) {           
           
            $unUser->setPlainPassword($unUser->getPassword());
            $unUser->setEnabled(1);
            $em->persist($unUser);           
            $em->flush();   
            
            if ($type == 2) {
                //return $this->redirect($this->generateUrl("detail_utilisateur",array('id'=>$id,'locale' =>$locale,)));
                return $this->redirect($this->generateUrl("liste_utilisateur",array('locale' =>$locale))); 
            } elseif ($type==1){
                //return $this->forward('FOSUserBundle:Security:logout');
                //return $this->redirect($this->generateUrl("liste_utilisateur",array('locale' =>$locale)));
                return $this->redirect($this->generateUrl("fos_user_security_logout"));
            }
            
        }           
            
           
        }
        $user = $this->security->getToken()->getUser()->getId(); 
        
        if ($type == 2) {
        
            return $this->render('user/modifFicheUser.html.twig',array(
                                             'form' =>$form->createView(),'locale' =>$locale,'listestat'=>$listestat
                                             ,'unuser'=>$unUser,
                             ));
        } elseif ($type == 1) {
            
            return $this->render('user/modifPwdUser.html.twig',array(
                                             'form' =>$form->createView(),'locale' =>$locale,'listestat'=>$listestat
                                             ,'unuser'=>$unUser, ));   
        }
    
    } 
    
}
