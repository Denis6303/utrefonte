<?php

namespace App\Entity;

use App\Repository\DimensionRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Importer Media si ce n'est pas dans le même namespace
// use App\Entity\Media;

#[ORM\Entity(repositoryClass: DimensionRepository::class)]
#[ORM\Table(name: 'dimension')]
#[ORM\HasLifecycleCallbacks] // Conserver l'attribut pour les callbacks
class Dimension
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'iddimension', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Libellé généré automatiquement (largeur x hauteur).
     */
    #[ORM\Column(name: 'libdimension', type: Types::STRING, length: 70)]
    // NotBlank n'est pas pertinent car généré, Length est utile
    #[Assert\Length(max: 70, maxMessage: "Le libellé généré ne peut dépasser {{ limit }} caractères.")]
    private ?string $libDimension = null;

    #[ORM\Column(name: 'hauteur', type: Types::INTEGER)]
    #[Assert\NotBlank(message: "La hauteur est obligatoire.")]
    #[Assert\PositiveOrZero(message: "La hauteur doit être un nombre positif ou zéro.")] // Une dimension ne peut pas être négative
    private ?int $hauteur = null;

    #[ORM\Column(name: 'largeur', type: Types::INTEGER)]
    #[Assert\NotBlank(message: "La largeur est obligatoire.")]
    #[Assert\PositiveOrZero(message: "La largeur doit être un nombre positif ou zéro.")] // Une dimension ne peut pas être négative
    private ?int $largeur = null;

    /**
     * Médias utilisant cette dimension.
     * 'dimension' est la propriété dans Media qui référence cette entité (mappedBy).
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(mappedBy: 'dimension', targetEntity: Media::class, cascade: ['persist'])] // Ne pas supprimer/modifier les médias si dimension change ? Adapter cascade.
    private Collection $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate] // Utiliser aussi PreUpdate pour recalculer si largeur/hauteur changent
    public function calculateLibelle(): void // Renommé pour clarté, ajout type void
    {
        if ($this->largeur !== null && $this->hauteur !== null) {
            $this->libDimension = $this->largeur . "x" . $this->hauteur;
        } else {
            $this->libDimension = null; // Ou une valeur par défaut/erreur si largeur/hauteur sont requis
        }
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Nom et type retour standardisés
    {
        return $this->id;
    }

    public function getLibDimension(): ?string
    {
        return $this->libDimension;
    }

    /**
     * Le libellé étant calculé automatiquement, ce setter n'est généralement pas utile
     * et peut potentiellement introduire des incohérences. À supprimer sauf cas d'usage spécifique.
     */
    // public function setLibDimension(string $libDimension): self
    // {
    //     $this->libDimension = $libDimension;
    //     return $this;
    // }

    public function getHauteur(): ?int // Type retour corrigé
    {
        return $this->hauteur;
    }

    public function setHauteur(int $hauteur): self // Type paramètre corrigé
    {
        $this->hauteur = $hauteur;
        // On pourrait déclencher le recalcul ici aussi, mais PreUpdate le fait avant sauvegarde
        // $this->calculateLibelle();
        return $this;
    }

    public function getLargeur(): ?int // Type retour corrigé
    {
        return $this->largeur;
    }

    public function setLargeur(int $largeur): self // Type paramètre corrigé
    {
        $this->largeur = $largeur;
         // On pourrait déclencher le recalcul ici aussi, mais PreUpdate le fait avant sauvegarde
        // $this->calculateLibelle();
        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection // Type retour corrigé
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self // Type paramètre corrigé
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            // Mettre à jour le côté propriétaire (ManyToOne dans Media)
            $media->setDimension($this); // Assurez-vous que setDimension existe dans Media
        }
        return $this;
    }

    public function removeMedia(Media $media): self // Type paramètre corrigé
    {
        if ($this->medias->removeElement($media)) {
            // Mettre le côté propriétaire à null (si la relation est nullable dans Media)
            if ($media->getDimension() === $this) { // Assurez-vous que getDimension existe
                $media->setDimension(null);
            }
        }
        return $this;
    }

    // Les méthodes preDimension et preUpdateDimension ont été fusionnées et renommées en calculateLibelle
    // et marquées avec #[ORM\PrePersist, ORM\PreUpdate]

    // --- Méthode __toString ---
    public function __toString(): string
    {
        return $this->libDimension ?? 'Dimension #' . $this->id;
    }
}