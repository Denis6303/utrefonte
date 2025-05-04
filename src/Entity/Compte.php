<?php

namespace App\Entity;

use App\Repository\CompteRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types; // Importer Types pour les types de colonnes
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Les imports pour TypeCompte et Abonne sont implicites s'ils sont dans le même namespace,
// mais il est bon de les ajouter si ce n'est pas le cas.
// use App\Entity\TypeCompte;
// use App\Entity\Abonne;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
#[ORM\Table(name: 'compte')]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idcompte', type: Types::INTEGER)] // Utilisation de Types::INTEGER
    private ?int $idCompte = null;

    #[ORM\Column(name: 'numerocompte', type: Types::STRING, length: 20, unique: true)] // Un numéro de compte devrait être unique
    #[Assert\NotBlank(message: "Le numéro de compte est obligatoire.")]
    #[Assert\Length(
        max: 20,
        maxMessage: "Le numéro de compte ne doit pas dépasser {{ limit }} caractères."
    )]
    // Ajoutez d'autres assertions si nécessaire (ex: Regex pour le format)
    private ?string $numeroCompte = null;

    #[ORM\Column(name: 'libellecompte', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé du compte est obligatoire.")]
    #[Assert\Length(
        max: 100,
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libelleCompte = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Relation vers le Type de Compte.
     * Supposons qu'un compte DOIT avoir un type.
     * 'comptes' est la propriété dans TypeCompte qui référence cette entité (inversedBy).
     */
    #[ORM\ManyToOne(targetEntity: TypeCompte::class, inversedBy: 'comptes')] // Ajout de inversedBy
    #[ORM\JoinColumn(name: 'idtypecompte', referencedColumnName: 'idtypecompte', nullable: false)] // Rendu non nullable
    #[Assert\NotNull(message: "Le type de compte est obligatoire.")] // Validation ajoutée
    private ?TypeCompte $typeCompte = null;

    /**
     * Relation vers l'Abonné propriétaire du compte.
     * Supposons qu'un compte DOIT avoir un abonné.
     * 'comptes' est la propriété dans Abonne qui référence cette entité (inversedBy).
     */
    #[ORM\ManyToOne(targetEntity: Abonne::class, inversedBy: 'comptes')] // Ajout de inversedBy
    #[ORM\JoinColumn(name: 'idabonne', referencedColumnName: 'idabonne', nullable: false)] // Rendu non nullable
    #[Assert\NotNull(message: "L'abonné est obligatoire.")] // Validation ajoutée
    private ?Abonne $abonne = null;

    // Pas de constructeur nécessaire si pas d'initialisation spécifique

    public function getIdCompte(): ?int
    {
        return $this->idCompte;
    }

    // Pas de setIdCompte car c'est un ID auto-généré

    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;
        return $this;
    }

    public function getLibelleCompte(): ?string
    {
        return $this->libelleCompte;
    }

    public function setLibelleCompte(string $libelleCompte): self
    {
        $this->libelleCompte = $libelleCompte;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getTypeCompte(): ?TypeCompte
    {
        return $this->typeCompte;
    }

    // Le paramètre ne peut plus être null si nullable=false
    public function setTypeCompte(TypeCompte $typeCompte): self
    {
        $this->typeCompte = $typeCompte;
        return $this;
    }

    public function getAbonne(): ?Abonne
    {
        return $this->abonne;
    }

     // Le paramètre ne peut plus être null si nullable=false
    public function setAbonne(Abonne $abonne): self
    {
        $this->abonne = $abonne;
        return $this;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        return $this->libelleCompte . ' (' . $this->numeroCompte . ')' ?? 'Compte #' . $this->idCompte;
    }
}