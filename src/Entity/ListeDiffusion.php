<?php

namespace App\Entity;

use App\Repository\ListeDiffusionRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;              // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// ArrayCollection n'est pas utilisé directement dans les propriétés ici

/**
 * Entité représentant une liste de diffusion d'emails.
 */
#[ORM\Entity(repositoryClass: ListeDiffusionRepository::class)]
#[ORM\Table(name: 'listediffusion')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class ListeDiffusion
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idliste', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    #[ORM\Column(name: 'nomlistediffusion', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le nom de la liste de diffusion ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 100, // Correspond à la longueur ORM
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $nomListeDiffusion = null; // Type hint ?string

    /**
     * Statut d'activité de la liste.
     */
    #[ORM\Column(name: 'actif', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull] // Un booléen doit être défini
    private ?bool $actif = true; // Initialisé dans le constructeur, type hint ?bool

    /**
     * Contient les emails (séparés par virgule? saut de ligne? JSON?).
     * Envisager une relation ManyToMany vers une entité Contact/Internaute pour une meilleure gestion.
     */
    #[ORM\Column(name: 'lesMails', type: Types::TEXT)] // Gardé TEXT
    #[Assert\NotBlank(message: "La liste d'emails ne peut être vide.")]
    // Ajouter une validation personnalisée (Callback ou Regex) si le format est connu et strict
    private ?string $lesMails = null; // Type hint ?string

    /**
     * Type de liste (ex: 1=Interne, 2=Externe, ...).
     */
    #[ORM\Column(name: 'typeliste', type: Types::INTEGER)]
    #[Assert\NotNull] // Si un type est toujours requis
    // Optionnel: #[Assert\Choice(choices: [1, 2], message: "Type de liste invalide.")]
    private ?int $typeListeDiffusion = 1; // Initialisé dans le constructeur, type hint ?int


    public function __construct()
    {
        $this->typeListeDiffusion = 1; // Valeur par défaut
        $this->actif = true;         // Actif par défaut
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
     * Set nomListeDiffusion
     *
     * @param string $nomListeDiffusion
     * @return ListeDiffusion
     */
    public function setNomListeDiffusion(string $nomListeDiffusion): self
    {
        $this->nomListeDiffusion = $nomListeDiffusion;
        return $this;
    }

    /**
     * Get nomListeDiffusion
     *
     * @return string|null
     */
    public function getNomListeDiffusion(): ?string
    {
        return $this->nomListeDiffusion;
    }

    /**
     * Vérifie si la liste est active.
     */
    public function isActif(): ?bool // Getter standard pour booléen
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return ListeDiffusion
     */
    public function setActif(bool $actif): self // Type paramètre corrigé en bool
    {
        $this->actif = $actif;
        return $this;
    }

    /**
     * Set lesMails
     *
     * @param string $lesMails
     * @return ListeDiffusion
     */
    public function setLesMails(string $lesMails): self
    {
        $this->lesMails = $lesMails;
        return $this;
    }

    /**
     * Get lesMails
     *
     * @return string|null
     */
    public function getLesMails(): ?string
    {
        return $this->lesMails;
    }

    /**
     * Set typeListeDiffusion
     *
     * @param integer $typeListeDiffusion
     * @return ListeDiffusion
     */
    public function setTypeListeDiffusion(int $typeListeDiffusion): self // Type paramètre corrigé en int
    {
        $this->typeListeDiffusion = $typeListeDiffusion;
        return $this;
    }

    /**
     * Get typeListeDiffusion
     *
     * @return integer|null
     */
    public function getTypeListeDiffusion(): ?int // Type retour corrigé
    {
        return $this->typeListeDiffusion;
    }

    /**
     * Helper pour récupérer les emails sous forme de tableau.
     * Suppose que les emails sont séparés par des virgules, des points-virgules ou des sauts de ligne.
     * Adaptez les délimiteurs si nécessaire.
     *
     * @return array<int, string>
     */
    public function getEmailsAsArray(): array
    {
        if (empty($this->lesMails)) {
            return [];
        }
        // Remplace les sauts de ligne et points-virgules par des virgules, puis split
        $emailsString = str_replace(["\r\n", "\n", "\r", ";"], ',', $this->lesMails);
        $emails = explode(',', $emailsString);
        // Nettoyer les espaces et filtrer les entrées vides ou invalides
        return array_filter(array_map('trim', $emails), function ($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->nomListeDiffusion ?? 'ListeDiffusion #' . $this->id;
    }
}