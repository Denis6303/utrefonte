<?php

namespace App\Entity;

use App\Repository\CompteInexistantRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types; // Importer Types pour les types de colonnes
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables pour les dates

#[ORM\Entity(repositoryClass: CompteInexistantRepository::class)]
#[ORM\Table(name: 'compteinexistant')]
class CompteInexistant
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idcompteinexistant', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    #[ORM\Column(name: 'numerocompte', type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: "Le numéro de compte est obligatoire.")]
    #[Assert\Length(
        max: 20,
        maxMessage: "Le numéro de compte ne doit pas dépasser {{ limit }} caractères."
    )]
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

    #[ORM\Column(name: 'datecreation', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull] // Assurer que la date est toujours définie
    private ?DateTimeImmutable $dateCreation = null; // Changé en DateTimeImmutable

    #[ORM\ManyToOne(targetEntity: TypeCompte::class)] // inversedBy n'est probablement pas nécessaire ici
    #[ORM\JoinColumn(name: 'idtypecompte', referencedColumnName: 'idtypecompte', nullable: true)] // Gardé nullable
    private ?TypeCompte $typeCompte = null;

    public function __construct()
    {
        // Initialiser avec DateTimeImmutable
        $this->dateCreation = new DateTimeImmutable();
    }

    public function getId(): ?int // Nom et type retour standardisés
    {
        return $this->id;
    }

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

    public function getDateCreation(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateCreation;
    }

    /**
     * Normalement, la date de création ne devrait pas être modifiable après l'instanciation.
     * Si elle doit l'être, la méthode est correcte, sinon, elle peut être supprimée.
     */
    public function setDateCreation(DateTimeImmutable $dateCreation): self // Type paramètre corrigé
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getTypeCompte(): ?TypeCompte
    {
        return $this->typeCompte;
    }

    public function setTypeCompte(?TypeCompte $typeCompte): self
    {
        $this->typeCompte = $typeCompte;
        return $this;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return 'Compte Inexistant: ' . $this->numeroCompte ?? 'CompteInexistant #' . $this->id;
    }
}