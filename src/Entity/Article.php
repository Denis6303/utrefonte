<?php

namespace App\Entity;

use App\Repository\ArticleRepository; // Assurez-vous que ce repository existe
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // Gedmo annotations use attributes too
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables pour les dates

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'article')]
#[ORM\HasLifecycleCallbacks] // Conserve les callbacks de cycle de vie
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idarticle', type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * @var string|null Titre de l'article (traduisible)
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'titrearticle', type: Types::STRING, length: 100, nullable: true)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.", groups: ['translatable_validation'])] // Valider si besoin pour chaque langue
    #[Assert\Length(max: 100, maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $titreArticle = null;

    /**
     * @var string|null Texte d'introduction (traduisible)
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'introtextearticle', type: Types::TEXT, nullable: true)]
    private ?string $introTexteArticle = null;

    /**
     * @var string|null Description complète (traduisible)
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'descriptionarticle', type: Types::TEXT, nullable: true)]
    private ?string $descriptionArticle = null;

    /**
     * @var int|null Statut de l'article (0-6)
     */
    #[ORM\Column(name: 'statutarticle', type: Types::INTEGER, nullable: true)] // Rendu nullable pour correspondre à la DB
    #[Assert\NotNull(message: "Le statut doit être défini.")] // NotNull car c'est un integer
    #[Assert\Range(min: 0, max: 6, notInRangeMessage: "Le statut doit être compris entre {{ min }} et {{ max }}.")] // Assert\Range est plus approprié
    private ?int $statutArticle = null;

    #[ORM\Column(name: 'urlarticle', type: Types::STRING, length: 255, nullable: true)] // Prévoir une longueur pour l'URL
    #[Assert\Length(max: 255)]
    // #[Assert\Url(message: "L'URL '{{ value }}' n'est pas une URL valide.")] // Décommentez si c'est toujours une URL complète
    private ?string $urlArticle = null;

    #[ORM\Column(name: 'referencearticle', type: Types::STRING, length: 255, nullable: true)] // Prévoir une longueur
    #[Assert\Length(max: 255)]
    private ?string $referenceArticle = null;

    /**
     * @var int|null Indicateur de corbeille (0/1). Utiliser un boolean serait mieux.
     */
    #[ORM\Column(name: 'corbeillearticle', type: Types::INTEGER, nullable: true)] // Ou Types::BOOLEAN
    private ?int $corbeilleArticle = 0; // Initialisé à 0 (non supprimé)

    /**
     * @var int|null Indicateur d'archive (0/1). Utiliser un boolean serait mieux.
     */
    #[ORM\Column(name: 'archivearticle', type: Types::INTEGER, nullable: true)] // Ou Types::BOOLEAN
    private ?int $archiveArticle = 0; // Initialisé à 0 (non archivé)

    /**
     * @var int|null ID de la dernière rubrique (utilité à clarifier)
     */
    #[ORM\Column(name: 'lastrubriquearticle', type: Types::INTEGER, nullable: true)]
    private ?int $lastRubriqueArticle = null;

    #[ORM\Column(name: 'compteurarticle', type: Types::INTEGER, nullable: true)]
    private ?int $compteurArticle = 0; // Initialisé via constructeur

    #[ORM\Column(name: 'articledatepublie', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleDatePublie = null;

    #[ORM\Column(name: 'articledateajout', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleDateAjout = null;

    #[ORM\Column(name: 'articledatemodif', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleDateModif = null;

    /**
     * @var int|null Indicateur affichage date publication (0/1). Utiliser un boolean serait mieux.
     */
    #[ORM\Column(name: 'affichedatepublie', type: Types::INTEGER, nullable: true)] // Ou Types::BOOLEAN
    private ?int $afficheDatePublie = null;

    /**
     * @var int|null Indicateur affichage auteur (0/1). Utiliser un boolean serait mieux.
     */
    #[ORM\Column(name: 'afficheauteur', type: Types::INTEGER, nullable: true)] // Ou Types::BOOLEAN
    private ?int $afficheAuteur = null;

     /**
     * @var int|null Indicateur affichage accueil (0/1). Utiliser un boolean serait mieux.
     */
    #[ORM\Column(name: 'afficheaccueil', type: Types::INTEGER, nullable: true)] // Ou Types::BOOLEAN
    private ?int $afficheAccueil = null;

    /**
     * @var int|null Indicateur affichage référence (0/1). Utiliser un boolean serait mieux.
     */
    #[ORM\Column(name: 'affichereference', type: Types::INTEGER, nullable: true)] // Ou Types::BOOLEAN
    private ?int $afficheReference = null;

    #[ORM\Column(name: 'articledatesupprime', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleDateSupprime = null;

    #[ORM\Column(name: 'articledaterestaure', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleDateRestaure = null;

    #[ORM\Column(name: 'articledatedepublie', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleDateDepublie = null;

    #[ORM\Column(name: 'articledatearchive', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleDateArchive = null;

    #[ORM\Column(name: 'articledatevalide', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleDateValide = null;

    /**
     * @var int|null ID de l'utilisateur ayant modifié. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'articlemodifpar', type: Types::INTEGER, nullable: true)]
    private ?int $articleModifPar = null;

    /**
     * @var int|null ID de l'utilisateur ayant supprimé. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'articlesupprimepar', type: Types::INTEGER, nullable: true)]
    private ?int $articleSupprimePar = null;

    /**
     * @var int|null ID de l'utilisateur ayant ajouté. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'articleajoutpar', type: Types::INTEGER, nullable: true)] // Nom de colonne corrigé: articleajoutPar
    private ?int $articleAjoutPar = null;

    /**
     * @var int|null ID de l'utilisateur ayant validé. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'articlevalidepar', type: Types::INTEGER, nullable: true)]
    private ?int $articleValidePar = null;

    /**
     * @var int|null ID de l'utilisateur ayant archivé. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'articlearchivepar', type: Types::INTEGER, nullable: true)]
    private ?int $articleArchivePar = null;

    /**
     * @var int|null ID de l'utilisateur ayant dépublié. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'articledepubliepar', type: Types::INTEGER, nullable: true)]
    private ?int $articleDepubliePar = null;

    /**
     * @var int|null ID de l'utilisateur ayant restauré. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'articlerestaurepar', type: Types::INTEGER, nullable: true)]
    private ?int $articleRestaurePar = null;

    /**
     * @var int|null ID de l'utilisateur ayant publié. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'articlepubliepar', type: Types::INTEGER, nullable: true)]
    private ?int $articlePubliePar = null;

    /**
     * @var int|null Ordre d'affichage
     */
    #[ORM\Column(name: 'ordre', type: Types::INTEGER, nullable: true)]
    private ?int $ordre = null;

    /**
     * @var int|null Type de présentation
     */
    #[ORM\Column(name: 'typepresentation', type: Types::INTEGER, nullable: true)]
    private ?int $typePre = null;

    /**
     * Locale utilisée pour les traductions (non mappée).
     */
    #[Gedmo\Locale]
    private ?string $locale = null;

    // --- RELATIONS ---

    #[ORM\ManyToOne(targetEntity: Rubrique::class, inversedBy: 'articles', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idrubrique', referencedColumnName: 'idrubrique', nullable: true)] // Rendre nullable si un article peut ne pas avoir de rubrique
    private ?Rubrique $rubrique = null;

    /**
     * @var Collection<int, Menu> Menus associés à cet article (inverse side).
     * Attention: 'mappedBy="Menu"' semble incorrect. Ce devrait être le nom de la propriété
     * dans l'entité Menu qui pointe vers Article (ex: 'article'). A vérifier/corriger.
     */
    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Menu::class)] // Vérifiez/corrigez 'article'
    private Collection $menu;

    /**
     * @var Collection<int, Media> Médias associés à cet article (owning side).
     */
    #[ORM\ManyToMany(targetEntity: Media::class, inversedBy: 'articles', cascade: ['persist'])] // Utiliser seulement 'persist' pour la cascade
    #[ORM\JoinTable(name: 'pointer')]
    #[ORM\JoinColumn(name: 'article_idarticle', referencedColumnName: 'idarticle')]
    #[ORM\InverseJoinColumn(name: 'media_idmedia', referencedColumnName: 'idmedia')]
    private Collection $medias;

    /**
     * @var Collection<int, Cadre> Cadres associés à cet article (owning side).
     */
    #[ORM\ManyToMany(targetEntity: Cadre::class, inversedBy: 'articles', cascade: ['persist'])] // Utiliser seulement 'persist' pour la cascade
    #[ORM\JoinTable(name: 'positionner')]
    #[ORM\JoinColumn(name: 'article_idarticle', referencedColumnName: 'idarticle')]
    #[ORM\InverseJoinColumn(name: 'cadre_idcadre', referencedColumnName: 'idcadre')]
    private Collection $cadres;


    public function __construct()
    {
        $this->compteurArticle = 0;
        $this->menu = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->cadres = new ArrayCollection();
        $this->corbeilleArticle = 0;
        $this->archiveArticle = 0;
        // La date d'ajout et le statut initial sont gérés par PrePersist
    }

    #[ORM\PrePersist]
    public function initializeOnPrePersist(): void
    {
        if ($this->articleDateAjout === null) { // Evite d'écraser si déjà défini
            $this->articleDateAjout = new DateTimeImmutable();
        }
        if ($this->statutArticle === null) { // Statut par défaut si non défini
            $this->statutArticle = 1; // 1 = En cours de rédaction (supposition)
        }
        // corbeille et archive sont déjà initialisés dans le constructeur
        // $this->corbeilleArticle = 0;
        // $this->archiveArticle = 0;
    }

    // --- GETTERS & SETTERS ---
    // (Types corrigés et cohérents avec les propriétés)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreArticle(): ?string
    {
        return $this->titreArticle;
    }

    public function setTitreArticle(?string $titreArticle): self // Peut être null
    {
        $this->titreArticle = $titreArticle;
        return $this;
    }

    public function getIntroTexteArticle(): ?string
    {
        return $this->introTexteArticle;
    }

    public function setIntroTexteArticle(?string $introTexteArticle): self // Peut être null
    {
        $this->introTexteArticle = $introTexteArticle;
        return $this;
    }

    public function getDescriptionArticle(): ?string
    {
        return $this->descriptionArticle;
    }

    public function setDescriptionArticle(?string $descriptionArticle): self // Peut être null
    {
        $this->descriptionArticle = $descriptionArticle;
        return $this;
    }

    public function getStatutArticle(): ?int
    {
        return $this->statutArticle;
    }

    public function setStatutArticle(?int $statutArticle): self // Peut être null
    {
        $this->statutArticle = $statutArticle;
        return $this;
    }

    public function getUrlArticle(): ?string
    {
        return $this->urlArticle;
    }

    public function setUrlArticle(?string $urlArticle): self // Peut être null
    {
        $this->urlArticle = $urlArticle;
        return $this;
    }

    public function getReferenceArticle(): ?string
    {
        return $this->referenceArticle;
    }

    public function setReferenceArticle(?string $referenceArticle): self // Peut être null
    {
        $this->referenceArticle = $referenceArticle;
        return $this;
    }

    public function getCorbeilleArticle(): ?int // Ou ?bool si type boolean
    {
        return $this->corbeilleArticle;
    }

    public function isCorbeille(): bool
    {
        return $this->corbeilleArticle === 1;
    }

    public function setCorbeilleArticle(?int $corbeilleArticle): self // Ou ?bool
    {
        $this->corbeilleArticle = $corbeilleArticle;
        return $this;
    }

    public function getArchiveArticle(): ?int // Ou ?bool
    {
        return $this->archiveArticle;
    }

     public function isArchive(): bool
    {
        return $this->archiveArticle === 1;
    }

    public function setArchiveArticle(?int $archiveArticle): self // Ou ?bool
    {
        $this->archiveArticle = $archiveArticle;
        return $this;
    }

    public function getLastRubriqueArticle(): ?int
    {
        return $this->lastRubriqueArticle;
    }

    public function setLastRubriqueArticle(?int $lastRubriqueArticle): self
    {
        $this->lastRubriqueArticle = $lastRubriqueArticle;
        return $this;
    }

    public function getCompteurArticle(): ?int
    {
        return $this->compteurArticle;
    }

    public function setCompteurArticle(?int $compteurArticle): self
    {
        $this->compteurArticle = $compteurArticle;
        return $this;
    }

    // ... (Getters/Setters pour toutes les dates avec ?DateTimeImmutable / DateTimeImmutable)

    public function getArticleDatePublie(): ?DateTimeImmutable
    {
        return $this->articleDatePublie;
    }

    public function setArticleDatePublie(?DateTimeImmutable $articleDatePublie): self
    {
        $this->articleDatePublie = $articleDatePublie;
        return $this;
    }

    public function getArticleDateAjout(): ?DateTimeImmutable
    {
        return $this->articleDateAjout;
    }

    // Ne pas permettre de setter la date d'ajout manuellement si géré par PrePersist
    // public function setArticleDateAjout(?DateTimeImmutable $articleDateAjout): self
    // {
    //     $this->articleDateAjout = $articleDateAjout;
    //     return $this;
    // }

    public function getArticleDateModif(): ?DateTimeImmutable
    {
        return $this->articleDateModif;
    }

    public function setArticleDateModif(?DateTimeImmutable $articleDateModif): self
    {
        $this->articleDateModif = $articleDateModif;
        return $this;
    }

     public function getAfficheDatePublie(): ?int // Ou ?bool
    {
        return $this->afficheDatePublie;
    }

    public function setAfficheDatePublie(?int $afficheDatePublie): self // Ou ?bool
    {
        $this->afficheDatePublie = $afficheDatePublie;
        return $this;
    }

    public function getAfficheAuteur(): ?int // Ou ?bool
    {
        return $this->afficheAuteur;
    }

    public function setAfficheAuteur(?int $afficheAuteur): self // Ou ?bool
    {
        $this->afficheAuteur = $afficheAuteur;
        return $this;
    }

     public function getAfficheAccueil(): ?int // Ou ?bool
    {
        return $this->afficheAccueil;
    }

    public function setAfficheAccueil(?int $afficheAccueil): self // Ou ?bool
    {
        $this->afficheAccueil = $afficheAccueil;
        return $this;
    }

    public function getAfficheReference(): ?int // Ou ?bool
    {
        return $this->afficheReference;
    }

    public function setAfficheReference(?int $afficheReference): self // Ou ?bool
    {
        $this->afficheReference = $afficheReference;
        return $this;
    }

     public function getArticleDateSupprime(): ?DateTimeImmutable { return $this->articleDateSupprime; }
     public function setArticleDateSupprime(?DateTimeImmutable $d): self { $this->articleDateSupprime = $d; return $this; }
     public function getArticleDateRestaure(): ?DateTimeImmutable { return $this->articleDateRestaure; }
     public function setArticleDateRestaure(?DateTimeImmutable $d): self { $this->articleDateRestaure = $d; return $this; }
     public function getArticleDateDepublie(): ?DateTimeImmutable { return $this->articleDateDepublie; }
     public function setArticleDateDepublie(?DateTimeImmutable $d): self { $this->articleDateDepublie = $d; return $this; }
     public function getArticleDateArchive(): ?DateTimeImmutable { return $this->articleDateArchive; }
     public function setArticleDateArchive(?DateTimeImmutable $d): self { $this->articleDateArchive = $d; return $this; }
     public function getArticleDateValide(): ?DateTimeImmutable { return $this->articleDateValide; }
     public function setArticleDateValide(?DateTimeImmutable $d): self { $this->articleDateValide = $d; return $this; }


    // ... (Getters/Setters pour toutes les propriétés *Par avec ?int / int)
     public function getArticleModifPar(): ?int { return $this->articleModifPar; }
     public function setArticleModifPar(?int $id): self { $this->articleModifPar = $id; return $this; }
     public function getArticleSupprimePar(): ?int { return $this->articleSupprimePar; }
     public function setArticleSupprimePar(?int $id): self { $this->articleSupprimePar = $id; return $this; }
     public function getArticleAjoutPar(): ?int { return $this->articleAjoutPar; }
     public function setArticleAjoutPar(?int $id): self { $this->articleAjoutPar = $id; return $this; }
     public function getArticleValidePar(): ?int { return $this->articleValidePar; }
     public function setArticleValidePar(?int $id): self { $this->articleValidePar = $id; return $this; }
     public function getArticleArchivePar(): ?int { return $this->articleArchivePar; }
     public function setArticleArchivePar(?int $id): self { $this->articleArchivePar = $id; return $this; }
     public function getArticleDepubliePar(): ?int { return $this->articleDepubliePar; }
     public function setArticleDepubliePar(?int $id): self { $this->articleDepubliePar = $id; return $this; }
     public function getArticleRestaurePar(): ?int { return $this->articleRestaurePar; }
     public function setArticleRestaurePar(?int $id): self { $this->articleRestaurePar = $id; return $this; }
     public function getArticlePubliePar(): ?int { return $this->articlePubliePar; }
     public function setArticlePubliePar(?int $id): self { $this->articlePubliePar = $id; return $this; }


    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;
        return $this;
    }

    public function getTypePre(): ?int
    {
        return $this->typePre;
    }

    public function setTypePre(?int $typePre): self
    {
        $this->typePre = $typePre;
        return $this;
    }


    // --- Gestionnaire de locale pour Gedmo ---
    public function setTranslatableLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    // --- Gestion des collections ---

    public function getRubrique(): ?Rubrique
    {
        return $this->rubrique;
    }

    public function setRubrique(?Rubrique $rubrique): self // Type hint corrigé
    {
        $this->rubrique = $rubrique;
        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getMenu(): Collection
    {
        return $this->menu;
    }

    public function addMenu(Menu $menu): self // Type hint corrigé
    {
        if (!$this->menu->contains($menu)) {
            $this->menu->add($menu);
            // Si la relation est bidirectionnelle, mettez à jour l'autre côté:
            // $menu->setArticle($this); // Assurez-vous que setArticle existe dans Menu
        }
        return $this;
    }

    public function removeMenu(Menu $menu): self // Type hint corrigé
    {
        if ($this->menu->removeElement($menu)) {
            // Si la relation est bidirectionnelle et Menu est l'owning side,
            // ou si orphanRemoval=true, mettez l'autre côté à null:
            // if ($menu->getArticle() === $this) { // Assurez-vous que getArticle existe
            //     $menu->setArticle(null);
            // }
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

    public function addMedia(Media $media): self // Type hint corrigé
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            // Si Media est le côté inverse et que vous gérez la relation des deux côtés :
            // $media->addArticle($this); // Assurez-vous qu'une méthode addArticle existe dans Media
        }
        return $this;
    }

    public function removeMedia(Media $media): self // Type hint corrigé
    {
        $this->medias->removeElement($media);
        // Si Media est le côté inverse et que vous gérez la relation des deux côtés :
        // $media->removeArticle($this); // Assurez-vous qu'une méthode removeArticle existe dans Media
        return $this;
    }

    /**
     * @return Collection<int, Cadre>
     */
    public function getCadres(): Collection
    {
        return $this->cadres;
    }

    public function addCadre(Cadre $cadre): self // Type hint corrigé
    {
        if (!$this->cadres->contains($cadre)) {
            $this->cadres->add($cadre);
             // Si Cadre est le côté inverse et que vous gérez la relation des deux côtés :
             // $cadre->addArticle($this);
        }
        return $this;
    }

    public function removeCadre(Cadre $cadre): self // Type hint corrigé
    {
         $this->cadres->removeElement($cadre);
         // Si Cadre est le côté inverse et que vous gérez la relation des deux côtés :
         // $cadre->removeArticle($this);
        return $this;
    }

     // Optionnel: toString pour affichage facile
    public function __toString(): string
    {
        return $this->titreArticle ?? 'Article #' . $this->id;
    }
}