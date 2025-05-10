<?php

namespace App\Entity;

use App\Repository\StatistiqueRepository; // Importer le Repository (nom de classe corrigé)
use Doctrine\DBAL\Types\Types;         // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;   // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Bonne pratique, même si pas de date ici

// ArrayCollection n'est pas utilisé ici

/**
 * Classe qui gère les statistiques (potentiellement pour affichage dashboard/rapports).
 *
 * @author Gautier
 */
#[ORM\Entity(repositoryClass: StatistiqueRepository::class)] // Nom Repository corrigé
#[ORM\Table(name: 'statistique')]
// Pas besoin de @ORM\HasLifecycleCallbacks car aucune méthode de callback n'est utilisée
class Statistique
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idstat', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Libellé de la ligne statistique (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'libstat', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé de la statistique est requis.", groups: ['translatable_validation'])]
    #[Assert\Length(max: 100, maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $libStat = null; // Type hint ?string

    /**
     * Type de statistique (catégorisation).
     */
    #[ORM\Column(name: 'typestat', type: Types::INTEGER)]
    #[Assert\NotNull(message: "Le type de statistique est requis.")]
    // Optionnel: #[Assert\Choice(choices: [1, 2, ...], message: "Type invalide.")]
    private ?int $typeStat = null; // Type hint ?int

    /**
     * État de la statistique (active/inactive).
     */
    #[ORM\Column(name: 'etatstat', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull(message: "L'état doit être précisé.")]
    private ?bool $etatStat = true; // Initialisé à true (actif) par défaut, type hint ?bool

    /**
     * Description associée au type (ou à la statistique elle-même?).
     */
    #[ORM\Column(name: 'descriptiontype', type: Types::TEXT, nullable: true)] // Changé en TEXT, nullable
    // Pas de NotBlank car nullable
    private ?string $descriptionType = null; // Type hint ?string

    /**
     * Valeur numérique associée (signification dépend du type).
     */
    #[ORM\Column(name: 'valeur', type: Types::INTEGER)] // Gardé INTEGER
    #[Assert\NotNull(message: "La valeur est requise.")]
    #[Assert\Range(min: 0, max: 9, notInRangeMessage: "La valeur doit être comprise entre {{ min }} et {{ max }}.")] // Regex remplacé
    private ?int $valeur = 0; // Initialisé, Type hint ?int

    /**
     * Nom de la route Symfony associée (si applicable).
     */
    #[ORM\Column(name: 'route', type: Types::STRING, length: 255)] // Longueur augmentée
    #[Assert\NotBlank(message: "Le nom de la route est requis.")]
    #[Assert\Length(max: 255)]
    private ?string $route = null; // Type hint ?string

    /**
     * Écart ou différence (signification à clarifier).
     * Gardé en STRING faute de précision.
     */
    #[ORM\Column(name: 'ecart', type: Types::STRING, length: 255, nullable: true)] // Rendu nullable, longueur par défaut
    #[Assert\Length(max: 255)]
    // NotBlank retiré car nullable
    private ?string $ecart = null; // Type hint ?string

    /**
     * Paramètres additionnels, stockés en JSON.
     * @var array|null
     */
    #[ORM\Column(name: 'parametres', type: Types::JSON)] // Changé en JSON
    #[Assert\NotNull] // Le tableau peut être vide mais pas null
    private ?array $parametres = []; // Initialisé à tableau vide, Type hint ?array

    /**
     * Ordre d'affichage.
     */
    #[ORM\Column(name: 'ordre', type: Types::INTEGER)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: "L'ordre doit être positif ou zéro.")]
    private ?int $ordre = 0; // Initialisé, Type hint ?int

    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;


    public function __construct()
    {
        $this->etatStat = true; // Actif par défaut
        $this->valeur = 0;     // Valeur 0 par défaut
        $this->parametres = []; // Tableau vide par défaut
        $this->ordre = 0;      // Ordre 0 par défaut
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    // Pas de setter pour l'ID

    public function getLibStat(): ?string
    {
        return $this->libStat;
    }

    public function setLibStat(string $libStat): self
    {
        $this->libStat = $libStat;
        return $this;
    }

    public function getTypeStat(): ?int // Type retour corrigé
    {
        return $this->typeStat;
    }

    public function setTypeStat(int $typeStat): self // Type param corrigé
    {
        $this->typeStat = $typeStat;
        return $this;
    }

    /**
     * Vérifie si la statistique est active.
     */
    public function isEtatStat(): ?bool // Getter booléen
    {
        return $this->etatStat;
    }

    /**
     * Définit l'état de la statistique.
     */
    public function setEtatStat(bool $etatStat): self // Setter booléen
    {
        $this->etatStat = $etatStat;
        return $this;
    }

    /**
      * Get etatStat (moins sémantique que isEtatStat)
      * @return boolean|null
      */
     public function getEtatStat(): ?bool // Type retour corrigé
     {
         return $this->etatStat;
     }

    public function getDescriptionType(): ?string
    {
        return $this->descriptionType;
    }

    public function setDescriptionType(?string $descriptionType): self // Accepte null
    {
        $this->descriptionType = $descriptionType;
        return $this;
    }

    public function getValeur(): ?int // Type retour corrigé
    {
        return $this->valeur;
    }

    public function setValeur(int $valeur): self // Type param corrigé
    {
        $this->valeur = $valeur;
        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;
        return $this;
    }

    public function getEcart(): ?string
    {
        return $this->ecart;
    }

    public function setEcart(?string $ecart): self // Accepte null
    {
        $this->ecart = $ecart;
        return $this;
    }

    public function getParametres(): ?array // Type retour corrigé
    {
        return $this->parametres;
    }

    public function setParametres(array $parametres): self // Type param corrigé
    {
        $this->parametres = $parametres;
        return $this;
    }

    public function getOrdre(): ?int // Type retour corrigé
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self // Type param corrigé
    {
        $this->ordre = $ordre;
        return $this;
    }


    // --- Gestionnaire de locale Gedmo ---
    public function setTranslatableLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    // La méthode preAdding() est remplacée par le constructeur pour les initialisations simples

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->libStat ?? 'Statistique #' . $this->id;
    }
}