<?php

namespace App\Entity;

use App\Repository\OrdreRepository; // Importer le Repository (nom de classe corrigé)
use Doctrine\DBAL\Types\Types;     // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité pour stocker l'ordre d'éléments pour une table/entité donnée.
 */
#[ORM\Entity(repositoryClass: OrdreRepository::class)] // Nom Repository corrigé
#[ORM\Table(name: 'ordreclient')] // Nom de table conservé
class Ordre
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idordre', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    /**
     * Nom de la table ou de l'entité pour laquelle l'ordre est défini.
     */
    #[ORM\Column(name: 'nomtable', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le nom de la table ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 100, // Correspond à la longueur ORM
        minMessage: "Le nom de la table doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de la table ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $nomTable = null; // Type hint ?string

    /**
     * Ordre des éléments, stocké en JSON.
     * Contient probablement un tableau d'IDs ou d'autres identifiants.
     * @var array|null
     */
    #[ORM\Column(name: 'ordre', type: Types::JSON)] // Changé en JSON pour stocker un tableau d'ordre
    #[Assert\NotNull(message: "La définition de l'ordre est requise.")] // Ne peut pas être null
    // Optionnel: #[Assert\Count(min: 1, minMessage: "L'ordre doit contenir au moins un élément.")]
    private ?array $ordre = []; // Initialisé à tableau vide, Type hint ?array

    // Constructeur vide supprimé

    /**
     * Get id
     *
     * @return integer|null
     */
    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    // Pas de setter pour l'ID

    /**
     * Set nomTable
     *
     * @param string $nomTable
     * @return Ordre
     */
    public function setNomTable(string $nomTable): self
    {
        $this->nomTable = $nomTable;
        return $this;
    }

    /**
     * Get nomTable
     *
     * @return string|null
     */
    public function getNomTable(): ?string
    {
        return $this->nomTable;
    }

    /**
     * Set ordre (tableau d'IDs ou d'identifiants).
     *
     * @param array $ordre
     * @return Ordre
     */
    public function setOrdre(array $ordre): self // Type paramètre changé en array
    {
        $this->ordre = $ordre;
        return $this;
    }

    /**
     * Get ordre
     *
     * @return array|null Le tableau représentant l'ordre
     */
    public function getOrdre(): ?array // Type retour changé en ?array
    {
        return $this->ordre;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return 'Ordre pour ' . ($this->nomTable ?? 'N/A') . ' (#' . $this->id . ')';
    }
}