<?php

declare(strict_types=1);

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
use Symfony\Component\HttpFoundation\Response as ResponseInterface;
use App\Service\AccessControl;

use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessControl $accessControl,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(
        path: '/operation/telecharger/{locale}',
        name: 'app_operation_telecharger',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function telecharger(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('telecharger', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $type = $request->request->get('typefichier');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Liste des Opérations');
        $sheet->fromArray([
            ['Date Opération', 'Débit', 'Crédit', 'Date Valeur', 'Libelle', 'Numero mouvement']
        ], null, 'A1');

        $operations = $this->entityManager
            ->getRepository(Operation::class)
            ->findAll();

        $row = 2;
        foreach ($operations as $operation) {
            $sheet->fromArray([
                $operation->getDateOperation()->format('Y-m-d'),
                $operation->getSensOperation() === 0 ? $operation->getMontant() : 0,
                $operation->getSensOperation() === 1 ? $operation->getMontant() : 0,
                $operation->getDateValeur()->format('Y-m-d'),
                $operation->getLibOperation(),
                $operation->getId()
            ], null, 'A' . $row);
            $row++;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename=Operation-' . date("Y_m_d_His") . '.xlsx');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        return $response;
    }

    #[Route(
        path: '/operation/liste/{locale}',
        name: 'app_operation_liste',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function listeOperByCompte(Request $request, string $locale): ResponseInterface
    {
        if (!$this->accessControl->isLogged()) {
            return $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }

        $this->requestStack->getCurrentRequest()->setLocale($locale);
        $user = $this->getUser();
        
        if (!$this->accessControl->hasAccess('listeOperByCompte', $user)) {
            $this->addFlash('error', 'admin.layout.accesdenied');
            return $this->redirectToRoute('app_home', ['locale' => $locale]);
        }

        $compte = strtolower($request->request->get('compte', ''));
        
        $operations = $this->entityManager
            ->getRepository(Operation::class)
            ->findByCompte($compte);

        return $this->render('operation/liste.html.twig', [
            'operations' => $operations,
            'locale' => $locale
        ]);
    }

    private function infoUtilisateur(
        EntityManagerInterface $em,
        AccessControl $authManager,
        array $currentConnete,
        string $user,
        string $locale
    ): void {
        if (!isset($currentConnete["id_abonne"]) || $currentConnete["id_abonne"] === "") {
            return;
        }

        $idAbonne = $currentConnete["id_abonne"];
        $typeUser = $currentConnete["type_user"];
        $nomPrenom = $currentConnete["nomPrenom_abonne"];
        $profil = $currentConnete["profil_abonne"];
        $lastConnexion = $currentConnete["last_connexion"];
        $listeActions = $currentConnete["listeActions_abonne"];
        $subabonne = $currentConnete["sousAbonne"];

        $maxIdleTime = $this->getParameter('maxIdleTime');
        $session = $this->requestStack->getSession();
        
        if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {
            $this->addFlash('warning', 'session.expired');
            $this->redirectToRoute('app_logout', ['locale' => $locale]);
        }
    }
}