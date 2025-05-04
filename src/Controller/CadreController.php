<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Media;
use App\Entity\Dimension;
use App\Entity\Cadre;
use App\Entity\CadreType;
use App\Entity\MediaCadreType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\AccessControl;

/**
 * 
 * CadreController pour la gestion des cadres
 * 
 * Cette methode permet de verifier le droit de l'utilisateur avant d'effectuer une action dans une methode
 * 
 * $em = $this->entityManager;
 * $AccessControl = $this->utb_admin.AccessControl;
 * $checkAcces = $AccessControl->verifAcces($em, 'ajoutprofilAction', $this->container->get);
 * 
 *
 * @author Ace3i <mail@ace3i.com>
 * @copyright 2013 Ace3i
 * @link      http://www.utb.tg
 * 
 * 
 */
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

    /**
     *  Methode qui s'occupe de l'ajout d'un cadre
     * 
     * $listestat : Pour afficher les statistiques generales sur le site 
     * 
     * 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le formulaire d'ajout d'un cadre(ajoutCadre.html.twig)
     * 
     */
    public function ajoutCadreAction(string $locale): Response
    {
        // Code qui vérifie si l'utilisateur courant a accès à cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'ajoutCadreAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
            ->getRepository('App\Entity\Statistique')
            ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $uncadre = new Cadre();
        $uncadre->setTranslatableLocale($locale);

        // Récupération de l'id
        $user = $this->security->getToken()->getUser()->getId();

        // Données de base et non nulles à renseigner
        $uncadre->setCadreAjoutPar($user);

        // Gestion de l'image
        $uneimage = new Media();
        $extensions = ['jpg', 'png', 'jpeg', 'gif'];
        $uneimage->extensions = $extensions;

        if ($uneimage->getUrlMedia() === null) {
            $uneimage->setTypeMedia(3);
            $uneimage->setIllustreImgMedia(1);
            $uneimage->setNomMedia("---");
            $uneimage->setUrlMedia("default_.png");
            $uneimage->setUrlFistMedia("default_.png");
            $uneimage->setMediaAjoutPar($user);
            $uncadre->addMedia($uneimage);
        } else {
            $uneimage->setTypeMedia(3);
            $uneimage->setIllustreImgMedia(1);
            $uneimage->setMediaAjoutPar($user);
            $uncadre->addMedia($uneimage);
        }

        $uncadre->setCadreDateAjout(new \DateTime());
        $form = $this->createForm(CadreType::class, $uncadre);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $uncadre = $form->getData();
            if ($uncadre->getContenuCadre() === null) {
                $uncadre->setContenuCadre("---");
            }
            $uneimage->setCadre($uncadre);
            $em->persist($uneimage);
            $em->persist($uncadre);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_admin_listecadre', ['locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Cadre/ajoutCadre.html.twig', [
            'form' => $form->createView(),
            'listestat' => $listestat,
            'locale' => $locale,
        ]);
    }

    /**
     *  Methode qui liste les cadres
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * 
     * @return <string> return le twig (listeCadre.html.twig)
     * 
     */
    public function listeCadreAction(string $locale): Response
    {
        // Code qui vérifie si l'utilisateur courant a accès à cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'listeCadreAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
            ->getRepository('App\Entity\Statistique')
            ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $listecadre = $this->entityManager
            ->getRepository('utbAdminBundle/Cadre')
            ->findAllCadreByLocale($locale);

        return $this->render('utbAdminBundle/Cadre/listeCadre.html.twig', [
            'listecadre' => $listecadre,
            'listestat' => $listestat,
            'locale' => $locale,
        ]);
    }

    /**
     *  Methode qui s'occupe de la modification d'un cadre
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du cadre
     * 
     * @return <string> return le twig (modifCadre.html.twig)
     * 
     */
    public function modifierCadreAction(int $id, string $locale): Response
    {
        // Code qui vérifie si l'utilisateur courant a accès à cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierCadreAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
            ->getRepository('App\Entity\Statistique')
            ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $uncadre = $em->getRepository('utbAdminBundle:Cadre')->find($id);
        if (!$uncadre) {
            throw $this->createNotFoundException('Cadre non trouvé');
        }

        $uncadre->setTranslatableLocale($locale);
        $form = $this->createForm(CadreType::class, $uncadre);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $uncadre = $form->getData();
            $em->persist($uncadre);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_admin_listecadre', ['locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Cadre/modifierCadre.html.twig', [
            'form' => $form->createView(),
            'listestat' => $listestat,
            'locale' => $locale,
            'uncadre' => $uncadre,
        ]);
    }

    /**
     *  Methode qui s'occupe de la modification d'un cadre
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du cadre
     * 
     * @return <string> return le twig (modifCadre.html.twig)
     * 
     */
    public function modifierCadreRubriqueBanniereAction(int $id, string $locale): Response
    {
        // Code qui vérifie si l'utilisateur courant a accès à cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierCadreRubriqueBanniereAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
            ->getRepository('App\Entity\Statistique')
            ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $uncadre = $em->getRepository('utbAdminBundle:Cadre')->find($id);
        if (!$uncadre) {
            throw $this->createNotFoundException('Cadre non trouvé');
        }

        $uncadre->setTranslatableLocale($locale);
        $form = $this->createForm(CadreType::class, $uncadre);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $uncadre = $form->getData();
            $em->persist($uncadre);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_admin_listerubrique', ['locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Cadre/modifierCadreRubriqueBanniere.html.twig', [
            'form' => $form->createView(),
            'listestat' => $listestat,
            'locale' => $locale,
            'uncadre' => $uncadre,
        ]);
    }

    /**
     *  Methode qui s'occupe de la modification d'un cadre
     * 
     *  
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant du cadre
     * 
     * @return <string> return le twig (modifCadre.html.twig)
     * 
     */
    public function modifierCadreArticleAction(int $id, string $locale): Response
    {
        // Code qui vérifie si l'utilisateur courant a accès à cette action
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifierCadreArticleAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
            ->getRepository('App\Entity\Statistique')
            ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $uncadre = $em->getRepository('utbAdminBundle:Cadre')->find($id);
        if (!$uncadre) {
            throw $this->createNotFoundException('Cadre non trouvé');
        }

        $uncadre->setTranslatableLocale($locale);
        $form = $this->createForm(CadreType::class, $uncadre);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $uncadre = $form->getData();
            $em->persist($uncadre);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_admin_listearticle', ['locale' => $locale]));
        }

        return $this->render('utbAdminBundle/Cadre/modifierCadreArticle.html.twig', [
            'form' => $form->createView(),
            'listestat' => $listestat,
            'locale' => $locale,
            'uncadre' => $uncadre,
        ]);
    }

    /**
     * Methode permettant de supprimer definitivement des cadres selectionnes - Backoffice
     * 
     * @var
     * 
     * Les Variables
     * 
     * $usersIds: Tableau regoupants les Ids des instances de la classe Cadre selectionnes
     * 
     * $unuser: Instance de la classe Cadre a supprimer definitivement
     * 
     * @return <json> return etat du traitement effectue
     *  
     */
    public function supprAllCadresAction(): Response
    {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'supprAllCadresAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil'));
        }

        $request = $this->requestStack->getCurrentRequest();
        $locale = $request->getLocale();

        $listecadre = $em->getRepository('utbAdminBundle:Cadre')->findAll();
        foreach ($listecadre as $cadre) {
            $em->remove($cadre);
        }
        $em->flush();

        return $this->redirect($this->generateUrl('utb_admin_listecadre', ['locale' => $locale]));
    }

    public function gererAllCadresAction(): Response
    {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'gererAllCadresAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil'));
        }

        $request = $this->requestStack->getCurrentRequest();
        $locale = $request->getLocale();

        $listecadre = $em->getRepository('utbAdminBundle:Cadre')->findAll();
        foreach ($listecadre as $cadre) {
            $cadre->setEtatCadre(1);
            $em->persist($cadre);
        }
        $em->flush();

        return $this->redirect($this->generateUrl('utb_admin_listecadre', ['locale' => $locale]));
    }

    /**
     * Methode gerant la modification  de l'image à une cadre 
     *  
     * Les variables
     *
     * $unecadre :   Array d'objet pour avoir la cadre
     * 
     * $unmedia :       Objet de la classe Media
     * 
     * $extensions :     Tableau des extensions acceptées
     * 
     * $listestat :     Liste de statistiques type 3 comme explique dans StatistiqueRepository 
     * 
     * @param <string> $locale Variable passee pour gerer le multilingue sur le site
     * @param <integer> $id identifiant de la cadre
     * 
     * 
     * @return <string> return le twig (modifMediaCadre.html.twig , oneCadre.html.twig)
     * 
     */
    public function modifMediaCadreAction(int $id, int $idmedia, string $locale): Response
    {
        $em = $this->entityManager;
        $AccessControl = $this->utb_admin.AccessControl;
        $checkAcces = $AccessControl->verifAcces($em, 'modifMediaCadreAction', $this->container->get);

        if (!$checkAcces) {
            $this->addFlash('accesdenied', "admin.layout.accesdenied");
            return $this->redirect($this->generateUrl('utb_admin_accueil', ['locale' => $locale]));
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $listestat = $this->entityManager
            ->getRepository('App\Entity\Statistique')
            ->getInfoOrStat($typeStat = 4, $locale, $valeur = 0, $article = null);

        $uncadre = $em->getRepository('utbAdminBundle:Cadre')->find($id);
        if (!$uncadre) {
            throw $this->createNotFoundException('Cadre non trouvé');
        }

        $unmedia = $em->getRepository('utbAdminBundle:Media')->find($idmedia);
        if (!$unmedia) {
            throw $this->createNotFoundException('Media non trouvé');
        }

        $form = $this->createForm(MediaCadreType::class, $unmedia);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $unmedia = $form->getData();
            $em->persist($unmedia);
            $em->flush();

            return $this->redirect($this->generateUrl('utb_admin_modifiercadre', [
                'id' => $id,
                'locale' => $locale,
            ]));
        }

        return $this->render('utbAdminBundle/Cadre/modifMediaCadre.html.twig', [
            'form' => $form->createView(),
            'listestat' => $listestat,
            'locale' => $locale,
            'uncadre' => $uncadre,
            'unmedia' => $unmedia,
        ]);
    }
}