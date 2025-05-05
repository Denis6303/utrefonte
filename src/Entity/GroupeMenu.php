<?php

namespace App\Entity;

use App\Repository\GroupeMenuRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;           // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;    // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;
// Importer Menu si ce n'est pas dans le même namespace
// use App\Entity\Menu;

/**
 * Entité représentant un groupe de menus.
 */
#[ORM\Entity(repositoryClass: GroupeMenuRepository::class)]
#[ORM\Table(name: 'groupemenu')]
// Pas de @ORM\HasLifecycleCallbacks car aucune méthode de callback n'est définie
class GroupeMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idgroupemenu', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Libellé du groupe de menus (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'libgroupemenu', type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: "Le libellé du groupe menu ne peut être vide.", groups: ['translatable_validation'])] // Valider si besoin par langue
    #[Assert\Length(
        min: 3,
        max: 50, // Correspond à la longueur ORM
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libGroupeMenu = null;

    /**
     * Commentaire ou description du groupe (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'commentairegroupemenu', type: Types::TEXT, nullable: true)] // Nom de colonne corrigé
    private ?string $commentaireGroupeMenu = null;

    /**
     * Détermine la visibilité (ex: par rôles, public, private).
     * À adapter si c'est un booléen simple (Types::BOOLEAN) ou une relation.
     * @var string|null
     */
    #[ORM\Column(name: 'visibiletegroupemenu', type: Types::STRING, length: 255)] // Nom de colonne corrigé
    #[Assert\NotBlank(message: "La règle de visibilité ne peut être vide.")]
    #[Assert\Length(
        min: 5,
        max: 255, // Correspond à la longueur ORM
        minMessage: "La règle de visibilité doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La règle de visibilité ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $visibiliteGroupeMenu = null; // Nom de propriété corrigé (visibilité)

    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;

    /**
     * Menus appartenant à ce groupe.
     * 'groupeMenu' est la propriété dans Menu qui référence cette entité (mappedBy).
     * @var Collection<int, Menu>
     */
    #[ORM\OneToMany(mappedBy: 'groupeMenu', targetEntity: Menu::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // Vérifiez 'groupeMenu', ajout cascade/orphanRemoval
    private Collection $menus;


    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour standardisé
    {
        return $this->id;
    }

    public function getLibGroupeMenu(): ?string
    {
        return $this->libGroupeMenu;
    }

    public function setLibGroupeMenu(string $libGroupeMenu): self
    {
        $this->libGroupeMenu = $libGroupeMenu;
        return $this;
    }

    public function getCommentaireGroupeMenu(): ?string
    {
        return $this->commentaireGroupeMenu;
    }

    public function setCommentaireGroupeMenu(?string $commentaireGroupeMenu): self // Accepte null
    {
        $this->commentaireGroupeMenu = $commentaireGroupeMenu;
        return $this;
    }

    public function getVisibiliteGroupeMenu(): ?string // Nom de méthode corrigé
    {
        return $this->visibiliteGroupeMenu;
    }

    public function setVisibiliteGroupeMenu(string $visibiliteGroupeMenu): self // Nom de méthode et param corrigé
    {
        $this->visibiliteGroupeMenu = $visibiliteGroupeMenu;
        return $this;
    }


    // --- Gestion de la collection Menus ---

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection // Type retour corrigé
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self // Type paramètre corrigé
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            // Mettre à jour le côté propriétaire (ManyToOne dans Menu)
            $menu->setGroupeMenu($this); // Assurez-vous que setGroupeMenu existe dans Menu
        }
        return $this;
    }

    public function removeMenu(Menu $menu): self // Type paramètre corrigé
    {
        if ($this->menus->removeElement($menu)) {
            // Mettre le côté propriétaire à null (si la relation est nullable dans Menu)
            if ($menu->getGroupeMenu() === $this) { // Assurez-vous que getGroupeMenu existe
                $menu->setGroupeMenu(null);
            }
        }
        return $this;
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
        return $this->libGroupeMenu ?? 'GroupeMenu #' . $this->id;
    }
}