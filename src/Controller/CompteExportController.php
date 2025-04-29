<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\CompteExport;
use App\Form\CompteExportType;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AccessControl;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CompteExport controller.
 *
 * #[Route("/efacture_export_compte")]
 */
class CompteExportController extends AbstractController
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

    /**
     * Lists all CompteExport entities.
     *
     * #[Route("/", name="efacture_export_compte")]
     * @Method("GET")
     * @Template()
     */
    public function indexAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $entities = $em->getRepository('utbParamsCompteBundle:CompteExport')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new CompteExport entity.
     *
     * #[Route("/", name="efacture_export_compte_create")]
     * @Method("POST")
     * @Template("utbParamsCompteBundle:CompteExport:new.html.twig")
     */
    public function createAction(): Response(Request $request, string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $entity = new CompteExport();
        $form = $this->createForm($this->createForm(CompteExportType::class), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->entityManager;
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('efacture_export_compte_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new CompteExport entity.
     *
     * #[Route("/new", name="efacture_export_compte_new")]
     * @Method("GET")
     * @Template()
     */
    public function newAction(): Response(string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $entity = new CompteExport();
        $form = $this->createForm($this->createForm(CompteExportType::class), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a CompteExport entity.
     *
     * #[Route("/{id}", name="efacture_export_compte_show")]
     * @Method("GET")
     * @Template()
     */
    public function showAction(): Response(int $id, string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $entity = $em->getRepository('utbParamsCompteBundle:CompteExport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Cet compte n\'existe pas.');
        }

        $deleteForm = $this->createDeleteForm($id, $locale);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CompteExport entity.
     *
     * #[Route("/{id}/edit", name="efacture_export_compte_edit")]
     * @Method("GET")
     * @Template()
     */
    public function editAction(): Response(int $id, string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $entity = $em->getRepository('utbParamsCompteBundle:CompteExport')->find($id);



        if (!$entity) {
            throw $this->createNotFoundException('Cet compte n\'existe pas.');
        }

        $editForm = $this->createForm($this->createForm(CompteExportType::class), $entity);
        $deleteForm = $this->createDeleteForm($id, $locale);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing CompteExport entity.
     *
     * #[Route("/{id}", name="efacture_export_compte_update")]
     * @Method("PUT")
     * @Template("utbParamsCompteBundle:CompteExport:edit.html.twig")
     */
    public function updateAction(): Response(Request $request, int $id, string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $entity = $em->getRepository('utbParamsCompteBundle:CompteExport')->find($id);


        $entity = $em->getRepository('utbParamsCompteBundle:CompteExport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Cet compte n\'existe pas.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm($this->createForm(CompteExportType::class), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('efacture_export_compte'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a CompteExport entity.
     *
     * #[Route("/{id}", name="efacture_export_compte_delete")]
     * @Method("DELETE")
     */
    public function deleteAction(): Response(Request $request, int $id, string $locale): Response {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $entity = $em->getRepository('utbParamsCompteBundle:CompteExport')->find($id);

        $form = $this->createDeleteForm($id, $locale);

        $form->bind($request);

        if ($form->isValid() or ( $entity instanceof CompteExport)) {

            $em = $this->entityManager;
            $entity = $em->getRepository('utbParamsCompteBundle:CompteExport')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Cet compte n\'existe pas.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('efacture_export_compte'));
    }

    /**
     * Creates a form to delete a CompteExport entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(int $id, string $locale) {
        $em = $this->entityManager;
        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $currentConnete = $authManager->getFlash("utb_client_data");
        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);
        $entity = $em->getRepository('utbParamsCompteBundle:CompteExport')->find($id);
        // var_dump($entity);
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

  

    /**
     * 
     * @return \utb\ParamsCompteBundle\Controller\Response
     */
    public function compteEfactureAction(): Response($date): Response {
     
        $date = new \DateTime($date);
        
        $em = $this->entityManager;
//        $authManager = $this->Auth.Manager; //on recupere le service qui gère l'authentification = $this->Auth.Manager;//on recupere le service qui gère l'authentification
//        $this->requestStack->getCurrentRequest()->setLocale($locale);
//        $currentConnete = $authManager->getFlash("utb_client_data");
//        $this->infoUtilisateur($em, $authManager, $currentConnete, 'utilisateur', $locale);

//        $entities = $em->getRepository('utbParamsCompteBundle:Operationcfonb')->findBy(array('dateOperation'=>$date));
//        $resultat = array();
//        $i = 0;
//        foreach ($entities as $ent) {
//            $i++;
//            $resultat[$i]['DateValeur'] = $ent->getDateValeur()->format("Y-m-d h:i:s");
//            $resultat[$i]['DateOperation'] = $ent->getDateOperation()->format("Y-m-d h:i:s");
//            $resultat[$i]['DateCompta'] = $ent->getDateCompta()->format("Y-m-d h:i:s");
//            $resultat[$i]['montant'] = $ent->getMontant();
//            $resultat[$i]['compte'] = $ent->getCompte()->getNumeroCompte();
//            $resultat[$i]['sensOperation'] = $ent->getSensOperation();
//            $resultat[$i]['coef'] = $ent->getCoef();
//            $resultat[$i]['numeroMvt'] = $ent->getNumeroMvt();
//            $resultat[$i]['devise'] = $ent->getDevise();
//            $resultat[$i]['codOperation'] = $ent->getCodOperation();
//            $resultat[$i]['periode'] = $ent->getPeriode();
//            $resultat[$i]['codeGui'] = $ent->getCodeGui();
//            $resultat[$i]['codeBnq'] = $ent->getCodeBnq();
//            $resultat[$i]['libOperation'] = $ent->getLibOperation();
//        }
       
        return new Response(json_encode("test"));
    }

}
