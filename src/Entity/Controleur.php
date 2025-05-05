<?php

namespace App\Entity;

use App\Repository\ControleurRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Importer Action si ce n'est pas dans le même namespace
// use App\Entity\Action;

#[ORM\Entity(repositoryClass: ControleurRepository::class)]
#[ORM\Table(name: 'controleur')]
class Controleur
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idcontroleur', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Nom technique ou logique du contrôleur (ex: App\Controller\Admin\ArticleController).
     */
    #[ORM\Column(name: 'nomcontroleur', type: Types::STRING, length: 150)] // Longueur augmentée si nécessaire pour FQCN
    #[Assert\NotBlank(message: "Le nom du contrôleur est requis.")]
    #[Assert\Length(
        min: 3,
        max: 150,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nomControleur = null;

    /**
     * Description fonctionnelle du contrôleur.
     */
    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)] // Changé en TEXT, plus adapté
    #[Assert\Length(
        min: 3,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )] // NotBlank supprimé car nullable=true
    private ?string $description = null;

    /**
     * Flag indiquant si ce contrôleur est pertinent pour l'espace client.
     */
    #[ORM\Column(name: 'client', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull] // Un booléen doit être défini (true/false)
    private ?bool $client = false; // Initialisé à false par défaut

    /**
     * Actions associées à ce contrôleur.
     * 'controleur' est la propriété dans Action qui référence cette entité (mappedBy).
     * @var Collection<int, Action>
     */
    #[ORM\OneToMany(mappedBy: 'controleur', targetEntity: Action::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // 'controleur' en minuscule, ajout cascade/orphanRemoval
    private Collection $actions;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
        $this->client = false; // Initialisation explicite
    }

    public function getId(): ?int // Nom et type retour standardisés
    {
        return $this->id;
    }

    public function getNomControleur(): ?string
    {
        return $this->nomControleur;
    }

    public function setNomControleur(string $nomControleur): self
    {
        $this->nomControleur = $nomControleur;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self // Accepte null
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Retourne true si le contrôleur est lié à l'espace client.
     */
    public function isClient(): ?bool // Getter standard pour booléen
    {
        return $this->client;
    }

    public function setClient(bool $client): self // Type paramètre corrigé en bool
    {
        $this->client = $client;
        return $this;
    }

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
            $action->setControleur($this); // Assurez-vous que setControleur existe dans Action
        }
        return $this;
    }

    public function removeAction(Action $action): self // Type paramètre corrigé
    {
        if ($this->actions->removeElement($action)) {
            // Mettre le côté propriétaire à null (si la relation est nullable dans Action)
            if ($action->getControleur() === $this) { // Assurez-vous que getControleur existe
                $action->setControleur(null);
            }
        }
        return $this;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->nomControleur ?? 'Controleur #' . $this->id;
    }
}