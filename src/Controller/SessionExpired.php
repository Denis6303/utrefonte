<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpFoundation\Response as ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Gestion de l'expiration de session
 * 
 * @author edem
 */
class SessionExpired extends AbstractController
{
    #[Route(
        path: '/session/expired/{locale}',
        name: 'app_session_expired',
        requirements: ['locale' => '[a-z]{2}']
    )]
    public function expired(Request $request, string $locale): ResponseInterface
    {
        return $this->render('client/session_expired.html.twig', [
            'locale' => $locale
        ]);
    }
}
