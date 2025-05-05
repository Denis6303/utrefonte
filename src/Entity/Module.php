<?php

namespace App\Entity;

use App\Repository\ModuleRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Importer Action si nécessaire
// use App\Entity\Action;

/**
 * Entité représentant un Module fonctionnel (groupe d'actions/contrôleurs).
 */
#[ORM\Entity(repositoryClass: ModuleRepository::class)]
#[ORM\Table(name: 'module')]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idmodule', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    #[ORM\Column(name: 'libmodule', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé du module ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 100, // Correspond à la longueur ORM
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libmodule = null; // Type hint ?string

    /**
     * Flag indiquant si ce module est pertinent pour l'espace client.
     */
    #[ORM\Column(name: 'client', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull] // Un booléen doit être défini
    private ?bool $client = false; // Initialisé à false par défaut, type hint ?bool

    /**
     * Ordre d'affichage du module.
     */
    #[ORM\Column(name: 'ordre', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero(message: "L'ordre doit être un nombre positif ou zéro.")] // Ordre généralement positif
    private ?int $ordre = 0; // Initialisé dans le constructeur, type hint ?int

    /**
     * Actions appartenant à ce module.
     * 'module' est la propriété dans Action qui référence cette entité (mappedBy).
     * @var Collection<int, Action>
     */
    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Action::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // Vérifiez mappedBy='module'
    private Collection $actions;


    public function __construct()
    {
        $this->ordre = 0; // Ordre par défaut
        $this->client = false; // Non client par défaut
        $this->actions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer|null
     */
    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    // Pas de setter pour l'ID

    /**
     * Set libmodule
     *
     * @param string $libmodule
     * @return Module
     */
    public function setLibmodule(string $libmodule): self
    {
        $this->libmodule = $libmodule;
        return $this;
    }

    /**
     * Get libmodule
     *
     * @return string|null
     */
    public function getLibmodule(): ?string
    {
        return $this->libmodule;
    }

    /**
     * Vérifie si le module concerne le client.
     */
    public function isClient(): ?bool // Getter standard pour booléen
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param boolean $client
     * @return Module
     */
    public function setClient(bool $client): self // Type paramètre corrigé en bool
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Set ordre
     *
     * @param integer|null $ordre
     * @return Module
     */
    public function setOrdre(?int $ordre): self // Type paramètre corrigé, accepte null
    {
        $this->ordre = $ordre;
        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer|null
     */
    public function getOrdre(): ?int // Type retour corrigé
    {
        return $this->ordre;
    }


    // --- Gestion de la collection Actions ---

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection // Type retour corrigé
    {
        return $this->actions;
    }

    public function addAction(Action $action): self // Type paramètre corrigé
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            // Mettre à jour le côté propriétaire (ManyToOne dans Action)
            $action->setModule($this); // Assurez-vous que setModule existe dans Action
        }
        return $this;
    }

    public function removeAction(Action $action): self // Type paramètre corrigé
    {
        if ($this->actions->removeElement($action)) {
            // Mettre le côté propriétaire à null (si la relation est nullable dans Action)
            if ($action->getModule() === $this) { // Assurez-vous que getModule existe
                $action->setModule(null);
            }
        }
        return $this;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->libmodule ?? 'Module #' . $this->id;
    }
}