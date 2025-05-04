<?php

namespace App\Entity;

use App\Repository\ObjetRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;     // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables

/**
 * Entité représentant un Objet (potentiellement un objet de message, un type d'élément...).
 */
#[ORM\Entity(repositoryClass: ObjetRepository::class)]
#[ORM\Table(name: 'objet')]
#[ORM\HasLifecycleCallbacks] // Conserver pour les dates
class Objet
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idobjet', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    /**
     * Libellé de l'objet (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'libobjet', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé de l'objet ne peut être vide.", groups: ['translatable_validation'])] // Valider si besoin par langue
    #[Assert\Length(
        min: 2,
        max: 100, // Correspond à la longueur ORM
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libObjet = null; // Type hint ?string

    /**
     * Description de l'objet.
     */
    #[ORM\Column(name: 'descriptionobjet', type: Types::TEXT, nullable: true)] // Changé en TEXT, nullable
    // Pas d'Assert\NotBlank car nullable, pas de MinLength par défaut sur Text
    private ?string $descriptionObjet = null; // Type hint ?string

    /**
     * État de l'objet (ex: actif/inactif).
     */
    #[ORM\Column(name: 'etatobjet', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull] // Un booléen doit être défini
    private ?bool $etatObjet = true; // Initialisé à true (actif) par défaut, type hint ?bool

    // --- Champs de suivi (Utilisateurs) ---
    // !! Recommandation : Remplacer par des relations ManyToOne vers User/Utilisateur !!
    #[ORM\Column(name: 'objetajoutpar', type: Types::INTEGER, nullable: true)] // Rendu nullable
    private ?int $objetAjoutPar = null;

    #[ORM\Column(name: 'objetmodifpar', type: Types::INTEGER, nullable: true)]
    private ?int $objetModifPar = null;

    // --- Champs de suivi (Dates) ---
    #[ORM\Column(name: 'objetdateajout', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Géré par PrePersist, rendu nullable
    private ?DateTimeImmutable $objetDateAjout = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'objetdatemodif', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Géré par PreUpdate
    private ?DateTimeImmutable $objetDateModif = null; // Type hint DateTimeImmutable


    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;


    public function __construct()
    {
        $this->etatObjet = true; // Actif par défaut
        // Les dates sont initialisées via PrePersist/PreUpdate
    }

    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        if ($this->objetDateAjout === null) {
            $this->objetDateAjout = new DateTimeImmutable();
        }
        // On pourrait définir objetAjoutPar ici
    }

    #[ORM\PreUpdate]
    public function setTimestampsOnUpdate(): void
    {
        $this->objetDateModif = new DateTimeImmutable();
        // On pourrait définir objetModifPar ici
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    // Pas de setter pour l'ID

    public function getLibObjet(): ?string
    {
        return $this->libObjet;
    }

    public function setLibObjet(string $libObjet): self
    {
        $this->libObjet = $libObjet;
        return $this;
    }

    public function getDescriptionObjet(): ?string
    {
        return $this->descriptionObjet;
    }

    public function setDescriptionObjet(?string $descriptionObjet): self // Accepte null
    {
        $this->descriptionObjet = $descriptionObjet;
        return $this;
    }

    /**
     * Retourne true si l'objet est actif.
     */
    public function isEtatObjet(): ?bool // Getter booléen
    {
        return $this->etatObjet;
    }

    /**
     * Définit l'état de l'objet.
     */
    public function setEtatObjet(bool $etatObjet): self // Setter booléen
    {
        $this->etatObjet = $etatObjet;
        return $this;
    }

     /**
      * Get etatObjet (moins sémantique que isEtatObjet)
      * @return boolean|null
      */
     public function getEtatObjet(): ?bool // Type retour corrigé
     {
         return $this->etatObjet;
     }


    // Getters/Setters pour les champs *Par (types corrigés)
    public function getObjetAjoutPar(): ?int { return $this->objetAjoutPar; }
    public function setObjetAjoutPar(?int $id): self { $this->objetAjoutPar = $id; return $this; } // Accepte null
    public function getObjetModifPar(): ?int { return $this->objetModifPar; }
    public function setObjetModifPar(?int $id): self { $this->objetModifPar = $id; return $this; } // Accepte null

    // Getters pour les champs Date* (types corrigés)
    // Setters retirés car gérés par callbacks
    public function getObjetDateAjout(): ?DateTimeImmutable { return $this->objetDateAjout; }
    public function getObjetDateModif(): ?DateTimeImmutable { return $this->objetDateModif; }


    // --- Gestionnaire de locale Gedmo ---
    public function setTranslatableLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->libObjet ?? 'Objet #' . $this->id;
    }
}