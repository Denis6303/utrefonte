<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccessControl
{
    private $entityManager;
    private $security;
    private $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $security,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function hasAccess($resource): bool
    {
        // Implémentation basique - à personnaliser selon vos besoins
        return true;
    }
} 