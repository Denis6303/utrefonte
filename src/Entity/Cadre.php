<?php

namespace App\Entity;

use App\Repository\CadreRepository; // Assurez-vous que ce repository existe
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // Import Gedmo pour l'attribut Locale
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables pour les dates

/**
 * Entité représentant un Cadre (potentiellement un bloc de contenu).
 */
#[ORM\Entity(repositoryClass: CadreRepository::class)]
#[ORM\Table(name: 'cadre')]
#[ORM\HasLifecycleCallbacks] // Conserve les callbacks
class Cadre
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idcadre', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'libcadre', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé du cadre ne peut être vide")]
    #[Assert\Length(min: 2, minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.", max: 100, maxMessage: "Le libellé ne peut pas dépasser {{ limit }} caractères.")] // Combinaison de MinLength et longueur max
    private ?string $libCadre = null;

    #[ORM\Column(name: 'contenucadre', type: Types::TEXT, nullable: true)] // Rendre nullable si le contenu peut être vide
    private ?string $contenuCadre = null;

    #[ORM\Column(name: 'positioncadre', type: Types::INTEGER, nullable: true)] // Rendre nullable si non obligatoire
    private ?int $positionCadre = null;

    #[ORM\Column(name: 'naturecadre', type: Types::INTEGER, nullable: true)] // Rendre nullable si non obligatoire
    // Ajoutez une validation si nécessaire (ex: Assert\Choice)
    private ?int $natureCadre = null;

    /**
     * @var int|null ID de l'utilisateur ayant ajouté. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'cadreajoutpar', type: Types::INTEGER, nullable: true)] // Rendre nullable pour éviter erreur si non défini avant PrePersist
    private ?int $cadreAjoutPar = null;

    /**
     * @var int|null ID de l'utilisateur ayant modifié. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'cadremodifpar', type: Types::INTEGER, nullable: true)]
    private ?int $cadreModifPar = null;

    #[ORM\Column(name: 'cadredateajout', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Géré par PrePersist, nullable=true évite erreur avant flush
    private ?DateTimeImmutable $cadreDateAjout = null;

    #[ORM\Column(name: 'cadredatemodif', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Géré par PreUpdate
    private ?DateTimeImmutable $cadreDateModif = null;

    /**
     * @var int État du cadre (ex: 1=actif, 0=inactif)
     */
    #[ORM\Column(name: 'etatcadre', type: Types::INTEGER)]
    #[Assert\NotNull] // Ne peut pas être null car initialisé
    // #[Assert\Choice(choices: [0, 1], message: "L'état doit être 0 ou 1.")] // Si limité à 0/1
    private ?int $etatCadre = null; // Initialisé dans le constructeur

    /**
     * @var int|null ID de la rubrique pointée (utilité à clarifier)
     */
    #[ORM\Column(name: 'rubpointer', type: Types::INTEGER, nullable: true)]
    private ?int $rubPointer = null;

    /**
     * @var int|null ID de l'article pointé (utilité à clarifier)
     */
    #[ORM\Column(name: 'articlepointer', type: Types::INTEGER, nullable: true)]
    private ?int $articlePointer = null;

    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     */
    #[Gedmo\Locale]
    private ?string $locale = null;

    // --- RELATIONS ---

    /**
     * @var Collection<int, Article> Articles liés (Inverse Side).
     */
    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'cadres', cascade: ['persist'])]
    private Collection $articles;

    /**
     * @var Collection<int, Media> Médias liés (OneToMany).
     * orphanRemoval=true: si un Media est retiré de la collection, il est supprimé.
     */
    #[ORM\OneToMany(mappedBy: 'cadre', targetEntity: Media::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $medias;

    /**
     * @var Collection<int, Rubrique> Rubriques liées (Inverse Side).
     */
    #[ORM\ManyToMany(targetEntity: Rubrique::class, mappedBy: 'cadres', cascade: ['persist'])]
    private Collection $rubriques;

    #[ORM\ManyToOne(targetEntity: TypeCadre::class, inversedBy: 'cadres', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idtypecadre', referencedColumnName: 'idtypecadre', nullable: true)] // Rendre nullable si un cadre peut ne pas avoir de type
    private ?TypeCadre $typeCadre = null;


    public function __construct()
    {
        $this->etatCadre = 1; // Valeur par défaut : Actif
        $this->articles = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->rubriques = new ArrayCollection();
        // DateAjout sera défini dans PrePersist
    }

    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        if ($this->cadreDateAjout === null) {
             $this->cadreDateAjout = new DateTimeImmutable();
        }
        // On pourrait aussi définir cadreAjoutPar ici si l'utilisateur est accessible
    }

    #[ORM\PreUpdate]
    public function setTimestampsOnUpdate(): void
    {
        $this->cadreDateModif = new DateTimeImmutable();
        // On pourrait aussi définir cadreModifPar ici si l'utilisateur est accessible
    }

    // --- GETTERS & SETTERS ---
    // (Types corrigés et cohérents)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibCadre(): ?string
    {
        return $this->libCadre;
    }

    public function setLibCadre(string $libCadre): self
    {
        $this->libCadre = $libCadre;
        return $this;
    }

    public function getContenuCadre(): ?string
    {
        return $this->contenuCadre;
    }

    public function setContenuCadre(?string $contenuCadre): self // Accepte null
    {
        $this->contenuCadre = $contenuCadre;
        return $this;
    }

    public function getPositionCadre(): ?int
    {
        return $this->positionCadre;
    }

    public function setPositionCadre(?int $positionCadre): self // Accepte null
    {
        $this->positionCadre = $positionCadre;
        return $this;
    }

    public function getNatureCadre(): ?int
    {
        return $this->natureCadre;
    }

    public function setNatureCadre(?int $natureCadre): self // Accepte null
    {
        $this->natureCadre = $natureCadre;
        return $this;
    }

    public function getCadreAjoutPar(): ?int
    {
        return $this->cadreAjoutPar;
    }

    public function setCadreAjoutPar(?int $cadreAjoutPar): self // Accepte null
    {
        $this->cadreAjoutPar = $cadreAjoutPar;
        return $this;
    }

    public function getCadreModifPar(): ?int
    {
        return $this->cadreModifPar;
    }

    public function setCadreModifPar(?int $cadreModifPar): self // Accepte null
    {
        $this->cadreModifPar = $cadreModifPar;
        return $this;
    }

    public function getCadreDateAjout(): ?DateTimeImmutable
    {
        return $this->cadreDateAjout;
    }

    // Pas de setter pour cadreDateAjout car géré par PrePersist

    public function getCadreDateModif(): ?DateTimeImmutable
    {
        return $this->cadreDateModif;
    }

    // Pas de setter pour cadreDateModif car géré par PreUpdate

    public function getEtatCadre(): ?int
    {
        return $this->etatCadre;
    }

    public function setEtatCadre(int $etatCadre): self // Ne doit pas être null
    {
        $this->etatCadre = $etatCadre;
        return $this;
    }

     public function isActif(): bool // Méthode sémantique
    {
        return $this->etatCadre === 1;
    }

    public function getRubPointer(): ?int
    {
        return $this->rubPointer;
    }

    public function setRubPointer(?int $rubPointer): self // Accepte null
    {
        $this->rubPointer = $rubPointer;
        return $this;
    }

    public function getArticlePointer(): ?int
    {
        return $this->articlePointer;
    }

    public function setArticlePointer(?int $articlePointer): self // Accepte null
    {
        $this->articlePointer = $articlePointer;
        return $this;
    }

    // --- Gestionnaire de locale Gedmo ---
    public function setTranslatableLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    // --- Gestion des collections ---

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            // Comme c'est l'inverse side, on doit potentiellement mettre à jour l'owning side (Article)
            $article->addCadre($this); // Assurez-vous que addCadre existe dans Article
        }
        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // Mettre à jour l'owning side
            $article->removeCadre($this); // Assurez-vous que removeCadre existe dans Article
        }
        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            // Mettre à jour l'owning side dans Media
            $media->setCadre($this); // Assurez-vous que setCadre existe dans Media
        }
        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->removeElement($media)) {
            // Mettre à null l'owning side si la relation est nullable et qu'on ne veut pas supprimer Media (orphanRemoval=true le fera)
            if ($media->getCadre() === $this) { // Assurez-vous que getCadre existe
                 $media->setCadre(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Rubrique>
     */
    public function getRubriques(): Collection
    {
        return $this->rubriques;
    }

    public function addRubrique(Rubrique $rubrique): self
    {
        if (!$this->rubriques->contains($rubrique)) {
            $this->rubriques->add($rubrique);
            // Mettre à jour l'owning side dans Rubrique
            $rubrique->addCadre($this); // Assurez-vous que addCadre existe dans Rubrique
        }
        return $this;
    }

    public function removeRubrique(Rubrique $rubrique): self
    {
        if ($this->rubriques->removeElement($rubrique)) {
             // Mettre à jour l'owning side dans Rubrique
            $rubrique->removeCadre($this); // Assurez-vous que removeCadre existe dans Rubrique
        }
        return $this;
    }

    public function getTypeCadre(): ?TypeCadre
    {
        return $this->typeCadre;
    }

    public function setTypeCadre(?TypeCadre $typeCadre): self // Accepte null
    {
        $this->typeCadre = $typeCadre;
        return $this;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        return $this->libCadre ?? 'Cadre #' . $this->id;
    }
}