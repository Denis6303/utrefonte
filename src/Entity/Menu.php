<?php

namespace App\Entity;

use App\Repository\MenuRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer les entités liées si nécessaire
// use App\Entity\Article;
// use App\Entity\GroupeMenu;

/**
 * Entité représentant un élément de menu.
 */
#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: 'menu')]
#[ORM\HasLifecycleCallbacks] // Ajouter si PreUpdate est utilisé
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idmenu', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Libellé du menu (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'libmenu', type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: "Le libellé du menu ne peut être vide.", groups: ['translatable_validation'])] // Valider si besoin par langue
    #[Assert\Length(
        min: 2,
        max: 50, // Correspond à la longueur ORM
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libMenu = null;

    /**
     * Type de menu (ex: 1=Article, 2=Externe, 3=Groupeur...).
     */
    #[ORM\Column(name: 'typemenu', type: Types::INTEGER)]
    #[Assert\NotNull(message: "Le type de menu est obligatoire.")]
    // Optionnel: #[Assert\Choice(choices: [1, 2, 3], message: "Type de menu invalide.")]
    private ?int $typeMenu = null;

    /**
     * URL externe si typeMenu = 2.
     */
    #[ORM\Column(name: 'urlexternemenu', type: Types::STRING, length: 255, nullable: true)] // Longueur augmentée, nullable
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "L'URL externe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "L'URL externe ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Url(message: "L'URL externe '{{ value }}' n'est pas une URL valide.", groups: ['external_link_check'])] // Valider seulement si typeMenu=2 (via groups)
    private ?string $urlExterneMenu = null;

    /**
     * ID de l'utilisateur ayant ajouté. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'menuajoutpar', type: Types::INTEGER, nullable: true)] // Rendu nullable
    private ?int $menuAjoutPar = null;

    #[ORM\Column(name: 'menudateAjout', type: Types::DATETIME_IMMUTABLE)] // Nom de colonne corrigé?
    #[Assert\NotNull] // Initialisé dans constructeur
    private ?DateTimeImmutable $menuDateAjout = null; // Changé en DateTimeImmutable

    /**
     * ID de l'utilisateur ayant modifié. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'menumodifpar', type: Types::INTEGER, nullable: true)]
    private ?int $menuModifPar = null;

    #[ORM\Column(name: 'menudatemodif', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Changé en DATETIME_IMMUTABLE
    private ?DateTimeImmutable $menuDateModif = null; // Sera défini dans PreUpdate


    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;


    // --- RELATIONS ---

    /**
     * Article lié (si typeMenu = 1).
     */
    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'menu')] // Note: inversedBy='menu' (singulier)? Vérifiez dans Article.
    #[ORM\JoinColumn(name: 'idarticle', referencedColumnName: 'idarticle', nullable: true)] // Doit être nullable
    private ?Article $article = null;

    /**
     * Groupe auquel ce menu appartient.
     */
    #[ORM\ManyToOne(targetEntity: GroupeMenu::class, inversedBy: 'menus')] // inversedBy='menus' (pluriel) semble correct
    #[ORM\JoinColumn(name: 'idgroupemenu', referencedColumnName: 'idgroupemenu', nullable: true)] // Rendre nullable si un menu peut être orphelin
    private ?GroupeMenu $groupeMenu = null;

    /**
     * Relation réflexive pour la hiérarchie des menus (Parent).
     */
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'idparentmenu', referencedColumnName: 'idmenu', nullable: true)] // Utilise idmenu de cette entité
    private ?Menu $parent = null; // Remplace l'ancien $idParentMenu (integer)

    /**
     * Relation réflexive pour la hiérarchie des menus (Enfants).
     * @var Collection<int, Menu>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $children;


    public function __construct()
    {
        $this->menuDateAjout = new DateTimeImmutable();
        $this->children = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function setTimestampsOnUpdate(): void
    {
        $this->menuDateModif = new DateTimeImmutable();
        // On pourrait aussi définir menuModifPar ici si l'utilisateur est accessible
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour standardisé
    {
        return $this->id;
    }

    public function getLibMenu(): ?string
    {
        return $this->libMenu;
    }

    public function setLibMenu(string $libMenu): self
    {
        $this->libMenu = $libMenu;
        return $this;
    }

    public function getTypeMenu(): ?int // Type retour corrigé
    {
        return $this->typeMenu;
    }

    public function setTypeMenu(int $typeMenu): self // Type param corrigé
    {
        $this->typeMenu = $typeMenu;
        return $this;
    }

    public function getUrlExterneMenu(): ?string
    {
        return $this->urlExterneMenu;
    }

    public function setUrlExterneMenu(?string $urlExterneMenu): self // Accepte null
    {
        $this->urlExterneMenu = $urlExterneMenu;
        return $this;
    }

    public function getMenuAjoutPar(): ?int // Type retour corrigé
    {
        return $this->menuAjoutPar;
    }

    public function setMenuAjoutPar(?int $menuAjoutPar): self // Accepte null
    {
        $this->menuAjoutPar = $menuAjoutPar;
        return $this;
    }

    public function getMenuDateAjout(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->menuDateAjout;
    }

    // Setter pour menuDateAjout retiré (défini à la construction)

    public function getMenuDateModif(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->menuDateModif;
    }

     // Setter pour menuDateModif retiré (défini par PreUpdate)

    public function getMenuModifPar(): ?int // Type retour corrigé
    {
        return $this->menuModifPar;
    }

    public function setMenuModifPar(?int $menuModifPar): self // Accepte null
    {
        $this->menuModifPar = $menuModifPar;
        return $this;
    }

    // --- Relations ---

    public function getArticle(): ?Article // Type retour corrigé
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self // Type param corrigé
    {
        $this->article = $article;
        return $this;
    }

    public function getGroupeMenu(): ?GroupeMenu // Type retour corrigé
    {
        return $this->groupeMenu;
    }

    public function setGroupeMenu(?GroupeMenu $groupeMenu): self // Type param corrigé
    {
        $this->groupeMenu = $groupeMenu;
        return $this;
    }

    // --- Hiérarchie (Parent/Enfants) ---

    public function getParent(): ?self // Retourne un objet Menu ou null
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self // Accepte un objet Menu ou null
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self // Accepte un objet Menu
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this); // Met à jour le côté propriétaire
        }
        return $this;
    }

    public function removeChild(self $child): self // Accepte un objet Menu
    {
        if ($this->children->removeElement($child)) {
            // Mettre le côté propriétaire à null (important pour orphanRemoval)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
    }

    /**
     * Get idParentMenu (déprécié - utiliser getParent()->getId() si nécessaire)
     * Conservé pour compatibilité potentielle, mais à éviter.
     * @return integer|null
     * @deprecated Use getParent() instead.
     */
    public function getIdParentMenu(): ?int
    {
        trigger_deprecation('app', '6.0', 'Method "%s()" is deprecated, use "getParent()->getId()" instead.', __METHOD__);
        return $this->parent?->getId();
    }

    /**
     * Set idParentMenu (déprécié - utiliser setParent() avec un objet Menu)
     * @param integer|null $idParentMenu
     * @return Menu
     * @deprecated Use setParent() instead.
     */
    public function setIdParentMenu(?int $idParentMenu): self
    {
         trigger_deprecation('app', '6.0', 'Method "%s()" is deprecated, use "setParent()" instead.', __METHOD__);
        // Cette méthode ne peut pas fonctionner correctement sans injecter l'EntityManager
        // pour trouver le Menu parent par ID. Il est préférable de ne pas l'utiliser.
        // Pour la compatibilité la plus simple, on pourrait la laisser ne rien faire
        // ou lever une exception.
        // throw new \LogicException('Setting parent by ID is deprecated and not supported directly. Use setParent(Menu $parent).');
        return $this; // Ne fait rien pour éviter les erreurs mais signale la dépréciation.
    }


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
        return $this->libMenu ?? 'Menu #' . $this->id;
    }
}