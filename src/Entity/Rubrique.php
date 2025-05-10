<?php

namespace App\Entity;

use App\Repository\RubriqueRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;          // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;   // Importer Gedmo
use Symfony\Component\HttpFoundation\File\File; // Importer File
use Symfony\Component\HttpFoundation\File\UploadedFile; // Importer UploadedFile
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables

/**
 * Entité représentant une Rubrique (catégorie de contenu).
 */
#[ORM\Entity(repositoryClass: RubriqueRepository::class)]
#[ORM\Table(name: 'rubrique')]
#[ORM\HasLifecycleCallbacks] // Conserver pour les callbacks
class Rubrique
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idrubrique', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Nom de la rubrique (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'nomrubrique', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le nom de la rubrique est requis.", groups: ['translatable_validation'])]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $nomRubrique = null; // Type hint ?string

    /**
     * Description de la rubrique (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'descriptionrubrique', type: Types::TEXT, nullable: true)]
    // Pas de NotBlank car nullable
    private ?string $descriptionRubrique = null; // Type hint ?string

    /**
     * Type de présentation pour les articles/contenus de cette rubrique.
     */
    #[ORM\Column(name: 'typepresentation', type: Types::INTEGER, nullable: true)] // Rendu nullable
    private ?int $typePresentation = null; // Type hint ?int

    /**
     * Type de rubrique (ex: 1=Standard, 2=Lien, 3=Conteneur).
     */
    #[ORM\Column(name: 'typerubrique', type: Types::INTEGER)]
    #[Assert\NotNull] // Doit être défini (initialisé via PrePersist si null)
    #[Assert\Range(min: 1, max: 3, notInRangeMessage: "Le type doit être compris entre {{ min }} et {{ max }}.")] // Regex remplacé
    private ?int $typeRubrique = 3; // Initialisé dans PrePersist si null, type hint ?int

    /**
     * Indicateur FAQ.
     */
    #[ORM\Column(name: 'isfaq', type: Types::BOOLEAN, nullable: true)] // Changé en BOOLEAN
    private ?bool $isFaq = false; // Initialisé dans constructeur, type hint ?bool

    /**
     * Chemin de l'icône uploadée.
     */
    #[ORM\Column(name: 'urlicone', type: Types::STRING, length: 255, nullable: true)] // Longueur augmentée
    #[Assert\Length(max: 255)]
    private ?string $urlIcone = null; // Type hint ?string

    // --- Champs de suivi (Utilisateurs) ---
    // !! Recommandation : Remplacer par des relations ManyToOne vers User/Utilisateur !!
    #[ORM\Column(name: 'rubriqueajoutpar', type: Types::INTEGER, nullable: true)] // Rendu nullable
    private ?int $rubriqueAjoutPar = null;

    #[ORM\Column(name: 'rubriquemodifpar', type: Types::INTEGER, nullable: true)]
    private ?int $rubriqueModifPar = null;

    // --- Champs de suivi (Dates) ---
    #[ORM\Column(name: 'rubriqueDateAjout', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Géré par PrePersist
    private ?DateTimeImmutable $rubriqueDateAjout = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'rubriqueDateModif', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Géré par PreUpdate
    private ?DateTimeImmutable $rubriqueDateModif = null; // Type hint DateTimeImmutable

    // --- Gestion de l'upload (Non mappé) ---
    /**
     * @var File|null
     */
    #[Assert\File(
        maxSize: "6M", // Taille en MegaOctets
        mimeTypes: ["image/gif", "image/jpeg", "image/png"],
        mimeTypesMessage: "Format d'icône invalide ({{ type }}). Types autorisés : {{ types }}."
    )]
    private ?File $icone = null; // Visibilité private

    // --- Propriété Gedmo Locale (Non mappée) ---
    #[Gedmo\Locale]
    private ?string $locale = null;

     // --- Variable temporaire pour suppression fichier ---
    private ?string $tempFilename = null;

    // --- RELATIONS ---

    /**
     * Relation réflexive (Parent).
     */
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'idparent', referencedColumnName: 'idrubrique', nullable: true, onDelete: 'SET NULL')] // onDelete SET NULL si suppression d'un parent
    private ?Rubrique $parent = null; // Remplace l'ancien $idparent

    /**
     * Relation réflexive (Enfants).
     * @var Collection<int, Rubrique>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $children;

    /**
     * Cadres associés à cette rubrique (Owning Side).
     * @var Collection<int, Cadre>
     */
    #[ORM\ManyToMany(targetEntity: Cadre::class, inversedBy: 'rubriques', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'cadresrubrique')] // Nom de table conservé
    #[ORM\JoinColumn(name: 'idrubrique', referencedColumnName: 'idrubrique')]
    #[ORM\InverseJoinColumn(name: 'idcadre', referencedColumnName: 'idcadre')]
    private Collection $cadres;

    /**
     * Articles appartenant à cette rubrique.
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: Article::class, cascade: ['persist'])] // Adapter cascade si nécessaire
    private Collection $articles;

    /**
     * Médias appartenant à cette rubrique.
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: Media::class, cascade: ['persist'])] // Adapter cascade si nécessaire
    private Collection $medias;


    public function __construct()
    {
        $this->isFaq = false; // Valeur par défaut
        $this->cadres = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->children = new ArrayCollection();
        // Les dates sont gérées par callbacks
    }

    // --- Lifecycle Callbacks ---

    #[ORM\PrePersist]
    public function setTimestampsAndDefaultsOnCreate(): void
    {
        if ($this->rubriqueDateAjout === null) {
            $this->rubriqueDateAjout = new DateTimeImmutable();
        }
        if ($this->typeRubrique === null) {
            $this->typeRubrique = 3; // Type par défaut
        }
        // La logique complexe pour idgrandparent est supprimée car gérée par la relation parent/children
    }

    #[ORM\PreUpdate]
    public function setTimestampsOnUpdate(): void
    {
        $this->rubriqueDateModif = new DateTimeImmutable();
         // La logique complexe pour idgrandparent est supprimée
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function handleIconBeforeSave(): void
    {
        if ($this->icone instanceof UploadedFile) {
             // Stocker l'ancien nom si mise à jour
             if ($this->id !== null && $this->urlIcone !== null) {
                 $this->tempFilename = $this->urlIcone;
             }
             // Générer nom unique
             $extension = $this->icone->guessExtension() ?: $this->icone->getClientOriginalExtension();
             $this->urlIcone = sha1(uniqid(mt_rand(), true)) . '.' . $extension;
         }
         // Gérer le cas où on veut supprimer l'icône sans en uploader une nouvelle
         elseif ($this->urlIcone === null && $this->tempFilename !== null) {
             // Ne rien faire ici, suppression dans PostUpdate/PostRemove
         }
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function handleIconAfterSave(): void
    {
         if ($this->icone instanceof UploadedFile && $this->urlIcone !== null) {
             $targetDirectory = $this->getUploadRootDir();
             if ($targetDirectory) {
                  try {
                     $this->icone->move($targetDirectory, $this->urlIcone);
                     // Gérer permissions si nécessaire
                     // @chmod($targetDirectory . '/' . $this->urlIcone, 0644);
                  } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
                      // Log erreur
                  }
             }
             $this->icone = null; // Nettoyer
         }

         // Supprimer l'ancien fichier
         if ($this->tempFilename !== null) {
             $oldFilePath = $this->getUploadRootDir() . '/' . $this->tempFilename;
             if (file_exists($oldFilePath) && $this->tempFilename !== $this->urlIcone) {
                 @unlink($oldFilePath);
             }
             $this->tempFilename = null; // Nettoyer
         }
    }

    #[ORM\PreRemove]
    public function storeIconFilenameForRemoval(): void
    {
         if ($this->urlIcone) {
             $this->tempFilename = $this->getAbsolutePath(); // Chemin absolu
         }
    }

    #[ORM\PostRemove]
    public function handleIconDeletionAfterRemove(): void
    {
         if ($this->tempFilename !== null && file_exists($this->tempFilename)) {
             @unlink($this->tempFilename);
         }
    }

     /**
      * Prépare la suppression de l'icône sans supprimer l'entité.
      */
     public function prepareRemoveIcone(): void
     {
         if ($this->urlIcone) {
            $this->tempFilename = $this->urlIcone;
            $this->urlIcone = null;
         }
         $this->icone = null;
     }

    // --- Méthodes de chemin (Déconseillé - utiliser des paramètres injectés) ---

    public function getAbsolutePath(): ?string
    {
        return $this->urlIcone ? $this->getUploadRootDir() . '/' . $this->urlIcone : null;
    }

    public function getWebPath(): ?string
    {
        // Relatif au dossier public/
        return $this->urlIcone ? '/' . $this->getUploadDir() . '/' . $this->urlIcone : null;
    }

    /**
     * !!! FORTEMENT DÉCONSEILLÉ: Dépend de la structure du projet. !!!
     */
    public function getUploadRootDir(): ?string
    {
        // Assumer Symfony 4+ structure avec /public
        return __DIR__.'/../../public/' . $this->getUploadDir();
        // Chemin original Symfony < 4 :
        // return __DIR__.'/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir(): string
    {
        return 'upload/icones'; // Relatif au dossier public/
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int { return $this->id; }

    public function getNomRubrique(): ?string { return $this->nomRubrique; }
    public function setNomRubrique(string $nom): self { $this->nomRubrique = $nom; return $this; }

    public function getDescriptionRubrique(): ?string { return $this->descriptionRubrique; }
    public function setDescriptionRubrique(?string $desc): self { $this->descriptionRubrique = $desc; return $this; } // Accepte null

    public function getTypePresentation(): ?int { return $this->typePresentation; }
    public function setTypePresentation(?int $type): self { $this->typePresentation = $type; return $this; } // Accepte null

    public function getTypeRubrique(): ?int { return $this->typeRubrique; }
    public function setTypeRubrique(int $type): self { $this->typeRubrique = $type; return $this; }

    public function isFaq(): ?bool { return $this->isFaq; } // Getter booléen
    public function setIsFaq(?bool $isFaq): self { $this->isFaq = $isFaq; return $this; } // Setter booléen, accepte null

    public function getUrlIcone(): ?string { return $this->urlIcone; }
    // Setter manuel non recommandé si géré par upload
    // public function setUrlIcone(?string $url): self { $this->urlIcone = $url; return $this; }

    // Getters/Setters *Par (types corrigés, accepte null)
    public function getRubriqueAjoutPar(): ?int { return $this->rubriqueAjoutPar; }
    public function setRubriqueAjoutPar(?int $id): self { $this->rubriqueAjoutPar = $id; return $this; }
    public function getRubriqueModifPar(): ?int { return $this->rubriqueModifPar; }
    public function setRubriqueModifPar(?int $id): self { $this->rubriqueModifPar = $id; return $this; }

    // Getters Date* (types corrigés) - Setters retirés
    public function getRubriqueDateAjout(): ?DateTimeImmutable { return $this->rubriqueDateAjout; }
    public function getRubriqueDateModif(): ?DateTimeImmutable { return $this->rubriqueDateModif; }


    // --- Relations ---

    public function getParent(): ?self { return $this->parent; }
    public function setParent(?self $parent): self { $this->parent = $parent; return $this; }

    /** @return Collection<int, Rubrique> */
    public function getChildren(): Collection { return $this->children; }
    public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        } return $this; }
    public function removeChild(self $child): self {
        if ($this->children->removeElement($child)) {
            if ($child->getParent() === $this) { $child->setParent(null); }
        } return $this; }

    /** @return Collection<int, Cadre> */
    public function getCadres(): Collection { return $this->cadres; }
    public function addCadre(Cadre $cadre): self {
        if (!$this->cadres->contains($cadre)) {
            $this->cadres->add($cadre);
             // Gérer l'autre côté si nécessaire (si Cadre est l'inverse side pour cette relation)
             // $cadre->addRubrique($this);
        } return $this; }
    public function removeCadre(Cadre $cadre): self {
         $this->cadres->removeElement($cadre);
         // Gérer l'autre côté si nécessaire
         // $cadre->removeRubrique($this);
         return $this; }

    /** @return Collection<int, Article> */
    public function getArticles(): Collection { return $this->articles; }
    public function addArticle(Article $article): self {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setRubrique($this);
        } return $this; }
    public function removeArticle(Article $article): self {
        if ($this->articles->removeElement($article)) {
            if ($article->getRubrique() === $this) { $article->setRubrique(null); }
        } return $this; }

    /** @return Collection<int, Media> */
    public function getMedias(): Collection { return $this->medias; }
    public function addMedia(Media $media): self {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setRubrique($this);
        } return $this; }
    public function removeMedia(Media $media): self {
        if ($this->medias->removeElement($media)) {
            if ($media->getRubrique() === $this) { $media->setRubrique(null); }
        } return $this; }


    // --- Fichier & Locale ---
    public function setIcone(?File $icone = null): self { $this->icone = $icone;
         // Gérer ancien nom si update
         if ($icone && $this->id !== null && $this->urlIcone !== null) { $this->tempFilename = $this->urlIcone; }
         return $this; }
    public function getIcone(): ?File { return $this->icone; }
    public function setTranslatableLocale(string $locale): self { $this->locale = $locale; return $this; }

    // --- Méthodes Dépréciées (Parent/GrandParent ID) ---
    /** @deprecated Use getParent() instead. */
    public function getIdparent(): ?Rubrique {
        trigger_deprecation('app', '6.0', 'Method "%s()" is deprecated, use "getParent()" instead.', __METHOD__);
        return $this->parent;
    }
    /** @deprecated Use setParent() instead. */
    public function setIdparent(?Rubrique $parent): self {
         trigger_deprecation('app', '6.0', 'Method "%s()" is deprecated, use "setParent()" instead.', __METHOD__);
         $this->setParent($parent);
        return $this;
    }
    /** @deprecated Parent hierarchy is now managed via the parent relation. */
    public function getIdgrandparent(): ?int {
         trigger_deprecation('app', '6.0', 'Method "%s()" is deprecated, use parent hierarchy instead.', __METHOD__);
         return $this->parent?->getParent()?->getId(); // Exemple pour obtenir l'ID du grand-parent
    }
     /** @deprecated Parent hierarchy is now managed via the parent relation. */
    public function setIdgrandparent(int $id): self {
         trigger_deprecation('app', '6.0', 'Method "%s()" is deprecated and non-functional.', __METHOD__);
         // Impossible de setter le grand-parent par ID ici.
        return $this;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->nomRubrique ?? 'Rubrique #' . $this->id;
    }
}