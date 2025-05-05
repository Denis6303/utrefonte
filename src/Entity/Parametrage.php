<?php

namespace App\Entity;

use App\Repository\ParametrageRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;           // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;    // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables (même si pas de date ici, bonne pratique)

/**
 * Classe qui gère les parametrages sur le site
 */
#[ORM\Entity(repositoryClass: ParametrageRepository::class)]
#[ORM\Table(name: 'parametrage')]
#[ORM\HasLifecycleCallbacks] // Conserver pour le PrePersist
class Parametrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idparam', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    /**
     * Titre du paramètre (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'paramTitre', type: Types::STRING, length: 255)] // Longueur par défaut si non spécifiée
    #[Assert\NotBlank(message: "Le titre du paramètre est requis.", groups: ['translatable_validation'])]
    #[Assert\Length(max: 255)]
    private ?string $paramTitre = null; // Type hint ?string

    /**
     * Description du paramètre (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    // NotBlank retiré car nullable
    private ?string $paramDescription = null; // Type hint ?string

    /**
     * Valeur du paramètre. Stockée en TEXT pour flexibilité (peut être int, string, bool...).
     * Le type réel est déterminé par paramType.
     * @var string|null
     */
    #[ORM\Column(name: 'valeur', type: Types::TEXT, nullable: true)] // Changé en TEXT pour flexibilité
    // NotBlank retiré, la valeur peut être null ou avoir un défaut
    private ?string $paramValeur = null; // Type hint ?string

    /**
     * Type de la valeur stockée dans paramValeur (ex: 1=string, 2=int, 3=bool, 4=texte long).
     */
    #[ORM\Column(name: 'paramtype', type: Types::INTEGER)]
    #[Assert\NotNull(message: "Le type de paramètre est requis.")]
    // Optionnel: #[Assert\Choice(choices: [1, 2, 3, 4], message: "Type invalide.")]
    private ?int $paramType = null; // Type hint ?int

    /**
     * Description du type (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'typedescription', type: Types::STRING, length: 255, nullable: true)] // Type string
    #[Assert\Length(max: 255)]
    private ?string $typeDescription = null; // Nom corrigé, Type hint ?string

    /**
     * Statut actif/inactif du paramètre.
     */
    #[ORM\Column(name: 'actif', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $actif = true; // Actif par défaut, type hint ?bool

    /**
     * ID du groupe auquel ce paramètre appartient.
     * !! Recommandation : Remplacer par une relation ManyToOne vers une entité GroupeParametrage !!
     */
    #[ORM\Column(name: 'idgroupe', type: Types::INTEGER, nullable: true)]
    private ?int $idGroupe = null; // Type hint ?int

    /**
     * Description du groupe (redondant si idGroupe est une relation).
     */
    #[ORM\Column(name: 'grpedescription', type: Types::TEXT, nullable: true)] // Changé en TEXT
    private ?string $grpeDescription = null; // Type hint ?string

    // --- Champs de suivi (Utilisateur) ---
    // !! Recommandation : Remplacer par une relation ManyToOne vers User/Utilisateur !!
    #[ORM\Column(name: 'paramajoutpar', type: Types::INTEGER, nullable: true)] // Rendu nullable
    private ?int $paramAjoutPar = null; // Type hint ?int


    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;


    public function __construct()
    {
        // Initialisation des valeurs par défaut
        $this->actif = true;
        // $paramValeur pourrait être initialisé selon $paramType si connu ici, sinon PrePersist
    }

    #[ORM\PrePersist]
    public function initializeDefaultsBeforePersist(): void // Renommé, ajout type void
    {
        // Assurer des valeurs par défaut si elles sont toujours nulles avant la sauvegarde
        if ($this->paramValeur === null) {
            // Le défaut dépend du type, mais '0' ou '' est souvent sûr pour TEXT
            $this->paramValeur = ''; // Ou '0' selon la sémantique voulue
        }
        if ($this->paramTitre === null) {
             $this->paramTitre = 'Paramètre sans titre'; // Mettre un défaut plus explicite
        }
        if ($this->paramDescription === null) {
             $this->paramDescription = 'Pas de description'; // Mettre un défaut plus explicite
        }
         if ($this->actif === null) { // Sécurité si constructeur non appelé
             $this->actif = true;
         }
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    public function getParamTitre(): ?string
    {
        return $this->paramTitre;
    }

    public function setParamTitre(string $paramTitre): self
    {
        $this->paramTitre = $paramTitre;
        return $this;
    }

    public function getParamDescription(): ?string
    {
        return $this->paramDescription;
    }

    public function setParamDescription(?string $paramDescription): self // Accepte null
    {
        $this->paramDescription = $paramDescription;
        return $this;
    }

    /**
     * Retourne la valeur brute (string).
     */
    public function getParamValeur(): ?string // Type retour corrigé
    {
        return $this->paramValeur;
    }

    /**
     * Définit la valeur brute (string).
     */
    public function setParamValeur(?string $paramValeur): self // Type param corrigé, accepte null
    {
        $this->paramValeur = $paramValeur;
        return $this;
    }

    /**
     * Helper pour obtenir la valeur castée selon son type.
     */
    public function getValeurCastee(): int|string|bool|null|float // PHP 8 union type
    {
        if ($this->paramValeur === null) {
            return null;
        }
        switch ($this->paramType) {
            case 2: // Supposons 2 = integer
                return (int) $this->paramValeur;
            case 3: // Supposons 3 = boolean
                return filter_var($this->paramValeur, FILTER_VALIDATE_BOOLEAN);
            case 5: // Supposons 5 = float/decimal
                 return (float) $this->paramValeur;
            // case 1: // string (default)
            // case 4: // text (default)
            default:
                return $this->paramValeur;
        }
    }

    public function getParamType(): ?int // Type retour corrigé
    {
        return $this->paramType;
    }

    public function setParamType(int $paramType): self // Type param corrigé
    {
        $this->paramType = $paramType;
        return $this;
    }

    public function getTypeDescription(): ?string
    {
        return $this->typeDescription;
    }

    public function setTypeDescription(?string $typeDescription): self // Accepte null
    {
        $this->typeDescription = $typeDescription;
        return $this;
    }

    public function isActif(): ?bool // Getter booléen
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self // Setter booléen
    {
        $this->actif = $actif;
        return $this;
    }

    public function getIdGroupe(): ?int // Type retour corrigé
    {
        return $this->idGroupe;
    }

    public function setIdGroupe(?int $idGroupe): self // Type param corrigé, accepte null
    {
        $this->idGroupe = $idGroupe;
        return $this;
    }

    public function getGrpeDescription(): ?string
    {
        return $this->grpeDescription;
    }

    public function setGrpeDescription(?string $grpeDescription): self // Accepte null
    {
        $this->grpeDescription = $grpeDescription;
        return $this;
    }

    public function getParamAjoutPar(): ?int // Type retour corrigé
    {
        return $this->paramAjoutPar;
    }

    public function setParamAjoutPar(?int $paramAjoutPar): self // Type param corrigé, accepte null
    {
        $this->paramAjoutPar = $paramAjoutPar;
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
        return $this->paramTitre ?? 'Parametrage #' . $this->id;
    }
}