<?php

namespace App\Entity;

use App\Repository\NatureDocRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer Media si nécessaire
// use App\Entity\Media;

/**
 * Entité représentant la nature ou le type d'un document/média.
 */
#[ORM\Entity(repositoryClass: NatureDocRepository::class)]
#[ORM\Table(name: 'naturedoc')]
#[ORM\HasLifecycleCallbacks] // Conserver pour les callbacks de date
class NatureDoc
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idnaturedoc', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    #[ORM\Column(name: 'libnaturedoc', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé est requis.")]
    #[Assert\Length(
        min: 2,
        max: 100, // Correspond à la longueur ORM
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libNatureDoc = null; // Type hint ?string

    /**
     * Statut de la nature du document (0-6?).
     */
    #[ORM\Column(name: 'statutnaturedoc', type: Types::INTEGER, nullable: true)] // Gardé nullable
    #[Assert\Range(min: 0, max: 6, notInRangeMessage: "Le statut doit être compris entre {{ min }} et {{ max }}.")] // Plus approprié que Regex
    private ?int $statutNatureDoc = 0; // Initialisé dans PrePersist, type hint ?int

    // --- Champs de suivi (Utilisateurs) ---
    // !! Recommandation : Remplacer par des relations ManyToOne vers User/Utilisateur !!
    #[ORM\Column(name: 'naturedocajoutpar', type: Types::INTEGER, nullable: true)]
    private ?int $natureDocAjoutPar = null;

    #[ORM\Column(name: 'naturedocmodifpar', type: Types::INTEGER, nullable: true)]
    private ?int $natureDocModifPar = null;

    #[ORM\Column(name: 'naturedocactivepar', type: Types::INTEGER, nullable: true)]
    private ?int $natureDocActivePar = null;

    #[ORM\Column(name: 'naturedocdesactivepar', type: Types::INTEGER, nullable: true)]
    private ?int $natureDocDesactivePar = null;

    // --- Champs de suivi (Dates) ---
    #[ORM\Column(name: 'naturedocdateajout', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Géré par PrePersist, rendu nullable pour éviter erreur avant flush
    private ?DateTimeImmutable $natureDocDateAjout = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'naturedocdatemodif', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Géré par PreUpdate
    private ?DateTimeImmutable $natureDocDateModif = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'naturedocdatedesactive', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $natureDocDateDesactive = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'naturedocdateactive', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $natureDocDateActive = null; // Type hint DateTimeImmutable

    // --- Relation ---
    /**
     * Médias ayant cette nature.
     * 'natureDoc' est la propriété dans Media qui référence cette entité (mappedBy).
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(mappedBy: 'natureDoc', targetEntity: Media::class, cascade: ['persist'])] // Vérifiez mappedBy='natureDoc', cascade à adapter
    private Collection $medias;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        // Initialisation des dates/statut dans PrePersist/PreUpdate
    }

    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void // Renommé, ajout type void
    {
        if ($this->natureDocDateAjout === null) {
            $this->natureDocDateAjout = new DateTimeImmutable();
        }
        if ($this->statutNatureDoc === null) { // Définit le statut initial
            $this->statutNatureDoc = 1; // 1 = Actif par défaut? A adapter. L'ancien code mettait 0.
        }
        // On pourrait définir natureDocAjoutPar ici si l'utilisateur est connu
    }

    #[ORM\PreUpdate]
    public function setTimestampsOnUpdate(): void
    {
        $this->natureDocDateModif = new DateTimeImmutable();
         // On pourrait définir natureDocModifPar ici si l'utilisateur est connu
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    public function getLibNatureDoc(): ?string
    {
        return $this->libNatureDoc;
    }

    public function setLibNatureDoc(string $libNatureDoc): self
    {
        $this->libNatureDoc = $libNatureDoc;
        return $this;
    }

    public function getStatutNatureDoc(): ?int // Type retour corrigé
    {
        return $this->statutNatureDoc;
    }

    public function setStatutNatureDoc(?int $statutNatureDoc): self // Type param corrigé, accepte null
    {
        $this->statutNatureDoc = $statutNatureDoc;
        return $this;
    }

    // Getters/Setters pour les champs *Par (types corrigés)
    public function getNatureDocAjoutPar(): ?int { return $this->natureDocAjoutPar; }
    public function setNatureDocAjoutPar(?int $id): self { $this->natureDocAjoutPar = $id; return $this; }
    public function getNatureDocModifPar(): ?int { return $this->natureDocModifPar; }
    public function setNatureDocModifPar(?int $id): self { $this->natureDocModifPar = $id; return $this; }
    public function getNatureDocActivePar(): ?int { return $this->natureDocActivePar; }
    public function setNatureDocActivePar(?int $id): self { $this->natureDocActivePar = $id; return $this; }
    public function getNatureDocDesactivePar(): ?int { return $this->natureDocDesactivePar; }
    public function setNatureDocDesactivePar(?int $id): self { $this->natureDocDesactivePar = $id; return $this; }

    // Getters/Setters pour les champs Date* (types corrigés)
    // Setters pour dates auto-gérées sont retirés
    public function getNatureDocDateAjout(): ?DateTimeImmutable { return $this->natureDocDateAjout; }
    public function getNatureDocDateModif(): ?DateTimeImmutable { return $this->natureDocDateModif; }
    public function getNatureDocDateDesactive(): ?DateTimeImmutable { return $this->natureDocDateDesactive; }
    public function setNatureDocDateDesactive(?DateTimeImmutable $date): self { $this->natureDocDateDesactive = $date; return $this; } // Gardé si défini manuellement
    public function getNatureDocDateActive(): ?DateTimeImmutable { return $this->natureDocDateActive; }
    public function setNatureDocDateActive(?DateTimeImmutable $date): self { $this->natureDocDateActive = $date; return $this; } // Gardé si défini manuellement

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    // --- Gestion de la collection Medias ---

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection // Type retour corrigé
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self // Type param corrigé
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            // Mettre à jour le côté propriétaire (ManyToOne dans Media)
            $media->setNatureDoc($this); // Assurez-vous que setNatureDoc existe dans Media
        }
        return $this;
    }

    public function removeMedia(Media $media): self // Type param corrigé
    {
        if ($this->medias->removeElement($media)) {
            // Mettre le côté propriétaire à null (si la relation est nullable dans Media)
            if ($media->getNatureDoc() === $this) { // Assurez-vous que getNatureDoc existe
                $media->setNatureDoc(null);
            }
        }
        return $this;
    }

    // La méthode preAjout a été renommée et intégrée dans les callbacks PrePersist/PreUpdate

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->libNatureDoc ?? 'NatureDoc #' . $this->id;
    }
}