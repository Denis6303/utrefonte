<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\ListeDiffusion;
use App\Entity\ListeDiffusionType;
use App\Entity\MessageReponse;
use App\Entity\MsgResponseNewsletterType;
use App\Entity\Emplacement;
use App\Entity\GAnalytics;
use App\Entity\EmplacementType;
use App\Entity\GAnalyticsType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

class AdminAutreController extends AbstractController
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

    #[Route("/{locale}/admin/liste-diffusion/edit/{id}", name: "admin_liste_diffusion_edit")]
    public function editionListeDiffusion(string $locale, int $id = 0): Response
    {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $request->setLocale($locale);

        $unelistediffusion = $id === 0 
            ? new ListeDiffusion()
            : $em->getRepository(ListeDiffusion::class)->find($id);

        if (!$unelistediffusion) {
            throw $this->createNotFoundException('Liste de diffusion non trouvée');
        }

        $form = $this->createForm(ListeDiffusionType::class, $unelistediffusion);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($unelistediffusion->getLesMails() === null) {
                    $unelistediffusion->setLesMails(serialize([]));
            }

            $em->persist($unelistediffusion);
            $em->flush();

                $this->addFlash('success', 'Liste de diffusion enregistrée avec succès');

                return $this->redirectToRoute('admin_liste_diffusion_details', [
                    'locale' => $locale,
                    'idliste' => $unelistediffusion->getId(),
                    'etat' => 1
                ]);
            }
        }

        return $this->render('admin/liste_diffusion/edit.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
            'id' => $id,
            'ajoutliste' => 1
        ]);
    }

    #[Route("/{locale}/admin/liste-diffusion", name: "admin_liste_diffusion_list")]
    public function listeListeDiffusion(string $locale): Response
    {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $request->setLocale($locale);

        $listeListeDiffusion = $em->getRepository(ListeDiffusion::class)->findAll();

        return $this->render('admin/liste_diffusion/list.html.twig', [
            'listediffusion' => $listeListeDiffusion,
            'locale' => $locale,
            'ajoutliste' => 0
        ]);
    }

    #[Route("/{locale}/admin/liste-diffusion/{idliste}/send", name: "admin_liste_diffusion_send")]
    public function sendMailToListeDiffusion(string $locale, int $idliste): Response
    {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $request->setLocale($locale);

        $unelistediffusion = $em->getRepository(ListeDiffusion::class)->find($idliste);

        if (!$unelistediffusion) {
            throw $this->createNotFoundException('Liste de diffusion non trouvée');
        }

            $listeinternaute = $unelistediffusion->getLesMails();
        if ($listeinternaute) {
            $listeinternaute = unserialize($listeinternaute);
            $email = implode(',', $listeinternaute);
        }

        $msgResponseNews = new MessageReponse();
        $msgResponseNews->setDestinatairesMsg($email ?? '');
        $msgResponseNews->setMessageLu(0);

        $form = $this->createForm(MsgResponseNewsletterType::class, $msgResponseNews);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($msgResponseNews);
            $em->flush();

                $this->addFlash('success', 'Message envoyé avec succès');
                return $this->redirectToRoute('admin_liste_diffusion_list', ['locale' => $locale]);
            }
        }

        return $this->render('admin/liste_diffusion/send.html.twig', [
                    'form' => $form->createView(),
                    'locale' => $locale,
            'idliste' => $idliste
        ]);
    }

    #[Route("/{locale}/admin/liste-diffusion/gerer", name: "admin_liste_diffusion_gerer", methods: ["POST"])]
    public function gererAllListeDif(Request $request): Response
    {
        $em = $this->entityManager;
        $listesIds = explode("|", $request->request->get('listesIds', ''));
        $etat = $request->request->get('etat');

        $result_traitement = 0;

        foreach ($listesIds as $value) {
            if (!empty($value)) {
                $unelisteDiffusion = $em->getRepository(ListeDiffusion::class)->find($value);

                if ($unelisteDiffusion && $unelisteDiffusion->getActif() != $etat) {
                    $unelisteDiffusion->setActif($etat);
                    $em->persist($unelisteDiffusion);
                } else {
                    $result_traitement = 1;
                }
            }
        }
        
        $em->flush();

        return $this->json([
            'result' => $result_traitement === 0 ? 'success' : 'erreur'
        ]);
    }

    #[Route("/{locale}/admin/liste-diffusion/supprimer", name: "admin_liste_diffusion_supprimer", methods: ["POST"])]
    public function supprAllListeDif(Request $request): Response
    {
        $em = $this->entityManager;
        $listesIds = explode("|", $request->request->get('listesIds', ''));

        $result_traitement = 0;

        foreach ($listesIds as $value) {
            if (!empty($value)) {
                $unelistediffusion = $em->getRepository(ListeDiffusion::class)->find($value);

                if ($unelistediffusion) {
                    $em->remove($unelistediffusion);
                } else {
                    $result_traitement = 1;
                }
            }
        }
        
        $em->flush();
        
        return $this->json([
            'result' => $result_traitement === 0 ? 'success' : 'erreur'
        ]);
    }

    #[Route("/{locale}/admin/liste-diffusion/all/{etat}", name: "admin_liste_diffusion_all")]
    public function listeAllListeDiffusion(string $locale, int $etat): Response
    {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $request->setLocale($locale);

        $listeListeDiffusion = $em->getRepository(ListeDiffusion::class)
            ->findBy(['actif' => $etat]);

        return $this->render('admin/liste_diffusion/list_all.html.twig', [
            'listediffusion' => $listeListeDiffusion,
            'locale' => $locale,
            'etat' => $etat
        ]);
    }

    #[Route("/{locale}/admin/liste-diffusion/{idliste}/details/{etat}", name: "admin_liste_diffusion_details")]
    public function detailsListeDiffusion(string $locale, int $idliste, int $etat): Response
    {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $request->setLocale($locale);

        $unelistediffusion = $em->getRepository(ListeDiffusion::class)->find($idliste);

        if (!$unelistediffusion) {
            throw $this->createNotFoundException('Liste de diffusion non trouvée');
        }

        return $this->render('admin/liste_diffusion/details.html.twig', [
            'liste' => $unelistediffusion,
            'locale' => $locale,
            'etat' => $etat
        ]);
    }

    #[Route("/{locale}/admin/liste-diffusion/save", name: "admin_liste_diffusion_save", methods: ["POST"])]
    public function saveListeDiffusion(Request $request): Response
    {
        $em = $this->entityManager;
        $email = $request->request->get('email');
        $idliste = $request->request->get('idliste');
        
        $unelistediffusion = $em->getRepository(ListeDiffusion::class)->find($idliste);
        
        if (!$unelistediffusion) {
            return $this->json(['result' => 'erreur']);
        }
        
        $lesMails = $unelistediffusion->getLesMails();
        $lesMails = $lesMails ? unserialize($lesMails) : [];
        
        if (!in_array($email, $lesMails)) {
            $lesMails[] = $email;
            $unelistediffusion->setLesMails(serialize($lesMails));
            $em->persist($unelistediffusion);
            $em->flush();

            return $this->json(['result' => 'success']);
        }
        
        return $this->json(['result' => 'existe']);
    }

    #[Route("/{locale}/admin/emplacement/ajouter", name: "admin_emplacement_ajouter")]
    public function ajoutEmplacement(string $locale, Request $request): Response
    {
        $em = $this->entityManager;
        $request->setLocale($locale);

        $emplacement = new Emplacement();
        $form = $this->createForm(EmplacementType::class, $emplacement);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($emplacement);
            $em->flush();
                
                $this->addFlash('success', 'Emplacement créé avec succès');
                return $this->redirectToRoute('admin_emplacement_list', ['locale' => $locale]);
            }
        }

        return $this->render('admin/emplacement/edit.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route("/{locale}/admin/emplacement", name: "admin_emplacement_list")]
    public function listeEmplacement(string $locale): Response
    {
        $em = $this->entityManager;
        $request = $this->requestStack->getCurrentRequest();
        $request->setLocale($locale);

        $emplacements = $em->getRepository(Emplacement::class)->findAll();

        return $this->render('admin/emplacement/list.html.twig', [
            'emplacements' => $emplacements,
            'locale' => $locale
        ]);
    }

    #[Route("/{locale}/admin/emplacement/{id}/modifier", name: "admin_emplacement_modifier")]
    public function modifierEmplacement(string $locale, int $id, Request $request): Response
    {
        $em = $this->entityManager;
        $request->setLocale($locale);

        $emplacement = $em->getRepository(Emplacement::class)->find($id);

        if (!$emplacement) {
            throw $this->createNotFoundException('Emplacement non trouvé');
        }

        $form = $this->createForm(EmplacementType::class, $emplacement);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
                
                $this->addFlash('success', 'Emplacement modifié avec succès');
                return $this->redirectToRoute('admin_emplacement_list', ['locale' => $locale]);
            }
        }

        return $this->render('admin/emplacement/edit.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
            'emplacement' => $emplacement
        ]);
    }

    #[Route("/admin/emplacement/supprimer", name: "admin_emplacement_supprimer", methods: ["POST"])]
    public function supprAllEmplacements(Request $request): Response
    {
        $em = $this->entityManager;
        $emplacementsIds = explode("|", $request->request->get('emplacementsIds', ''));
        
        $result_traitement = 0;
        
        foreach ($emplacementsIds as $value) {
            if (!empty($value)) {
                $emplacement = $em->getRepository(Emplacement::class)->find($value);
                
                if ($emplacement) {
                    $em->remove($emplacement);
                } else {
                    $result_traitement = 1;
                }
            }
        }
        
        $em->flush();
        
        return $this->json([
            'result' => $result_traitement === 0 ? 'success' : 'erreur'
        ]);
    }

    #[Route("/admin/emplacement/gerer", name: "admin_emplacement_gerer", methods: ["POST"])]
    public function gererAllEmplacement(Request $request): Response
    {
        $em = $this->entityManager;
        $emplacementsIds = explode("|", $request->request->get('emplacementsIds', ''));
        $etat = $request->request->get('etat');
        
        $result_traitement = 0;
        
        foreach ($emplacementsIds as $value) {
            if (!empty($value)) {
                $emplacement = $em->getRepository(Emplacement::class)->find($value);
                
                if ($emplacement && $emplacement->getActif() != $etat) {
                    $emplacement->setActif($etat);
                    $em->persist($emplacement);
                } else {
                    $result_traitement = 1;
                }
            }
        }
        
        $em->flush();
        
        return $this->json([
            'result' => $result_traitement === 0 ? 'success' : 'erreur'
        ]);
    }

    #[Route("/{locale}/admin/ganalytics", name: "admin_ganalytics")]
    public function ganalytics(string $locale, Request $request): Response
    {
        $em = $this->entityManager;
        $request->setLocale($locale);

        $ganalytics = $em->getRepository(GAnalytics::class)->findOneBy([]) ?? new GAnalytics();
        $form = $this->createForm(GAnalyticsType::class, $ganalytics);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($ganalytics);
                $em->flush();
                
                $this->addFlash('success', 'Configuration Google Analytics enregistrée avec succès');
                return $this->redirectToRoute('admin_ganalytics', ['locale' => $locale]);
            }
        }

        return $this->render('admin/ganalytics/edit.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }
} 