<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Media;
use App\Entity\Dimension;
use App\Entity\Cadre;
use App\Entity\CadreType;
use App\Entity\MediaCadreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

class CadreController extends AbstractController
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

    #[Route('/cadre/ajouter/{locale}', name: 'app_cadre_ajouter')]
    public function ajouter(Request $request, string $locale): Response
    {
        if (!$this->accessControl->verifAcces($this->entityManager, 'ajoutCadreAction')) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_accueil', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $cadre = new Cadre();
        $cadre->setTranslatableLocale($locale);
        $cadre->setCadreAjoutPar($this->getUser()->getId());
        $cadre->setCadreDateAjout(new \DateTime());

        $media = new Media();
        $media->setTypeMedia(3);
        $media->setIllustreImgMedia(1);
        $media->setMediaAjoutPar($this->getUser()->getId());
        
        if (!$media->getUrlMedia()) {
            $media->setNomMedia('---');
            $media->setUrlMedia('default_.png');
            $media->setUrlFistMedia('default_.png');
        }

        $cadre->addMedia($media);

        $form = $this->createForm(CadreType::class, $cadre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cadre = $form->getData();
            
            if (!$cadre->getContenuCadre()) {
                $cadre->setContenuCadre('---');
            }

            $media->setCadre($cadre);
            $this->entityManager->persist($media);
            $this->entityManager->persist($cadre);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_cadre_liste', ['locale' => $locale]);
        }

        return $this->render('cadre/ajouter.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale
        ]);
    }

    #[Route('/cadre/liste/{locale}', name: 'app_cadre_liste')]
    public function liste(string $locale): Response
    {
        if (!$this->accessControl->verifAcces($this->entityManager, 'listeCadreAction')) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_accueil', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $cadres = $this->entityManager->getRepository(Cadre::class)->findAllByLocale($locale);

        return $this->render('cadre/liste.html.twig', [
            'cadres' => $cadres,
            'locale' => $locale
        ]);
    }

    #[Route('/cadre/modifier/{id}/{locale}', name: 'app_cadre_modifier')]
    public function modifier(Request $request, int $id, string $locale): Response
    {
        if (!$this->accessControl->verifAcces($this->entityManager, 'modifierCadreAction')) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_accueil', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $cadre = $this->entityManager->getRepository(Cadre::class)->find($id);
        
        if (!$cadre) {
            throw $this->createNotFoundException('cadre.not_found');
        }

        $form = $this->createForm(CadreType::class, $cadre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'cadre.modif_success');
            return $this->redirectToRoute('app_cadre_liste', ['locale' => $locale]);
        }

        return $this->render('cadre/modifier.html.twig', [
            'form' => $form->createView(),
            'cadre' => $cadre,
            'locale' => $locale
        ]);
    }

    #[Route('/cadre/supprimer/{id}/{locale}', name: 'app_cadre_supprimer')]
    public function supprimer(int $id, string $locale): Response
    {
        if (!$this->accessControl->verifAcces($this->entityManager, 'supprimerCadreAction')) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_accueil', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $cadre = $this->entityManager->getRepository(Cadre::class)->find($id);
        
        if (!$cadre) {
            throw $this->createNotFoundException('cadre.not_found');
        }

        $cadre->setSuppr(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'cadre.suppr_success');
        return $this->redirectToRoute('app_cadre_liste', ['locale' => $locale]);
    }
} 