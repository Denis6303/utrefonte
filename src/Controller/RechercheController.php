<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Abonne;
use App\Entity\Compte;
use App\Entity\Operation;
use App\Entity\Utilisateur;
use App\Service\AccessControl;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, RequestStack, Response};
use Symfony\Component\HttpFoundation\Response as ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RechercheController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(
        path: '/recherche/abonne/{locale}',
        name: 'app_recherche_abonne',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function rechercheAbonne(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('recherche_abonne', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $idProfilGestionnaire = $this->getParameter('idgestionnaire');
        $gestionnaires = $this->entityManager
            ->getRepository(Utilisateur::class)
            ->findAllGestionnaireByLocale($idProfilGestionnaire, $locale);

        return $this->render('recherche/abonne.html.twig', [
            'locale' => $locale,
            'gestionnaires' => $gestionnaires
        ]);
    }

    #[Route(
        path: '/recherche/operation/{idCompte}/{idAbonne}/{locale}',
        name: 'app_recherche_operation',
        requirements: [
            'idCompte' => '\d+',
            'idAbonne' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function rechercheOperation(
        Request $request,
        int $idCompte,
        int $idAbonne,
        string $locale
    ): ResponseInterface {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('recherche_operation', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $page = $request->query->getInt('page', 1);
        $comptes = $this->entityManager
            ->getRepository(Compte::class)
            ->findAllCompteAbonne($idAbonne);

        return $this->render('recherche/operation.html.twig', [
            'page' => $page,
            'locale' => $locale,
            'idCompte' => $idCompte,
            'idAbonne' => $idAbonne,
            'comptes' => $comptes
        ]);
    }

    #[Route(
        path: '/recherche/apercu/{locale}',
        name: 'app_recherche_apercu',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function apercuRecherche(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        return $this->render('recherche/apercu.html.twig');
    }

    #[Route(
        path: '/recherche/operation-compte/{idCompte}/{idAbonne}/{locale}',
        name: 'app_recherche_operation_compte',
        requirements: [
            'idCompte' => '\d+',
            'idAbonne' => '\d+',
            'locale' => '[a-z]{2}'
        ]
    )]
    public function rechercheOperationCompte(
        Request $request,
        int $idCompte,
        int $idAbonne,
        string $locale
    ): ResponseInterface {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('recherche_operation_compte', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        return $this->render('recherche/operation-compte.html.twig', [
            'locale' => $locale,
            'idCompte' => $idCompte,
            'idAbonne' => $idAbonne
        ]);
    }

    #[Route(
        path: '/recherche/operation-admin/{locale}',
        name: 'app_recherche_operation_admin',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function rechercheOperationAdmin(
        Request $request,
        string $locale,
        ?string $compte = null,
        ?string $debut = null,
        ?string $fin = null,
        ?float $montantMin = null,
        ?float $montantMax = null,
        ?int $sens = null
    ): ResponseInterface {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('recherche_operation_admin', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        if ($request->isMethod('POST')) {
            $compte = $request->request->get('numCompte');
            $debut = $request->request->get('datedebut');
            $fin = $request->request->get('datefin');
            $montantMin = $request->request->get('mttde');
            $montantMax = $request->request->get('mtta');
            $sens = $request->request->get('sens');
        }

        $operations = $this->entityManager
            ->getRepository(Operation::class)
            ->findByCriteria($compte, $debut, $fin, $montantMin, $montantMax, $sens);

        if ($request->request->get('typefichier') === 'excel') {
            return $this->generateExcel($operations);
        }

        return $this->render('recherche/operation-admin.html.twig', [
            'operations' => $operations,
            'locale' => $locale,
            'compte' => $compte,
            'debut' => $debut,
            'fin' => $fin,
            'montantMin' => $montantMin,
            'montantMax' => $montantMax,
            'sens' => $sens
        ]);
    }

    private function generateExcel(array $operations): ResponseInterface
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Liste des Opérations');
        $sheet->fromArray([
            ['Date Opération', 'Numéro mouvement', 'Libellé', 'Date Valeur', 'Débit', 'Crédit']
        ], null, 'A1');

        $row = 2;
        foreach ($operations as $operation) {
            $sheet->fromArray([
                $operation->getDateOperation()->format('Y-m-d'),
                $operation->getId(),
                $operation->getLibOperation(),
                $operation->getDateValeur()->format('Y-m-d'),
                $operation->getSensOperation() === 0 ? $operation->getMontant() : 0,
                $operation->getSensOperation() === 1 ? $operation->getMontant() : 0
            ], null, 'A' . $row);
            $row++;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename=Operations-' . date("Y_m_d_His") . '.xlsx');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        return $response;
    }
}

