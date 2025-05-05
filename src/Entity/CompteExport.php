<?php

namespace App\Entity;

use App\Repository\CompteExportRepository; // Assurez-vous que ce repository existe ou créez-le
use Doctrine\DBAL\Types\Types; // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // Importer Assert

/**
 * CompteExport
 * Entité potentiellement utilisée pour l'export de données simplifiées de comptes.
 */
// Ajouter le nom de la table, par ex: 'compte_export'
#[ORM\Entity(repositoryClass: CompteExportRepository::class)]
#[ORM\Table(name: 'compte_export')] // Spécifiez le nom de la table si différent de 'compte_export'
class CompteExport
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(type: Types::INTEGER)] // name="id" est la valeur par défaut si la propriété s'appelle id
    private ?int $id = null;

    /**
     * @var string|null Le numéro de compte (ou identifiant)
     */
    #[ORM\Column(type: Types::STRING, length: 32)] // name="compte" est la valeur par défaut
    #[Assert\NotBlank(message: "Le champ 'compte' ne peut pas être vide.")]
    #[Assert\Length(
        max: 32,
        maxMessage: "Le champ 'compte' ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $compte = null;

    /**
     * @var string|null Le libellé associé au compte
     */
    #[ORM\Column(type: Types::STRING, length: 255)] // name="lib" est la valeur par défaut
    #[Assert\NotBlank(message: "Le libellé ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $lib = null;


    /**
     * Get id
     *
     * @return integer|null
     */
    public function getId(): ?int // Type de retour corrigé en ?int
    {
        return $this->id;
    }

    // Pas de setter pour l'ID auto-généré

    /**
     * Set compte
     *
     * @param string $compte
     * @return CompteExport
     */
    public function setCompte(string $compte): self
    {
        $this->compte = $compte;
        return $this;
    }

    /**
     * Get compte
     *
     * @return string|null
     */
    public function getCompte(): ?string
    {
        return $this->compte;
    }

    /**
     * Set lib
     *
     * @param string $lib
     * @return CompteExport
     */
    public function setLib(string $lib): self
    {
        $this->lib = $lib;
        return $this;
    }

    /**
     * Get lib
     *
     * @return string|null
     */
    public function getLib(): ?string
    {
        return $this->lib;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->lib . ' (' . $this->compte . ')' ?? 'CompteExport #' . $this->id;
    }
}