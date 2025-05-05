<?php

namespace App\Entity;

use App\Repository\ParamSystemeRepository; // Importer le Repository (nom de classe corrigé)
use Doctrine\DBAL\Types\Types;          // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant un paramètre système clé/valeur.
 */
#[ORM\Entity(repositoryClass: ParamSystemeRepository::class)] // Nom Repository corrigé
#[ORM\Table(name: 'paramsysteme')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class ParamSysteme
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idparam', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Clé unique identifiant le paramètre.
     */
    #[ORM\Column(name: 'cle', type: Types::STRING, length: 100, unique: true)] // Rendu unique
    #[Assert\NotBlank(message: "La clé du paramètre est obligatoire.")]
    #[Assert\Length(max: 100, maxMessage: "La clé ne doit pas dépasser {{ limit }} caractères.")]
    #[Assert\Regex(pattern: "/^[a-zA-Z0-9_.-]+$/", message: "La clé ne peut contenir que des lettres, chiffres, underscores, points et tirets.")] // Valider format clé
    private ?string $cle = null; // Type hint ?string

    /**
     * Valeur du paramètre. Stockée en TEXT pour flexibilité.
     */
    #[ORM\Column(name: 'valeur', type: Types::TEXT, nullable: true)] // Changé en TEXT nullable
    // Pas de NotBlank/MinLength ici, la valeur peut être vide/null ou spécifique au type/clé
    private ?string $valeur = null; // Type hint ?string

    /**
     * Description expliquant le rôle du paramètre.
     */
    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)] // Changé en TEXT nullable
    private ?string $description = null; // Type hint ?string

    /**
     * Type de la valeur stockée (pour cast éventuel, ex: 1=string, 2=int, 3=bool).
     * !! Recommandation : Pourrait être une relation ManyToOne vers une entité ParamType !!
     */
    #[ORM\Column(name: 'idtype', type: Types::INTEGER)] // Nom de colonne conservé
    #[Assert\NotNull(message: "Le type de paramètre est requis.")]
    // Optionnel: #[Assert\Choice(choices: [1, 2, 3], message: "Type invalide.")]
    private ?int $type = null; // Nom de propriété 'type', Type hint ?int

    /**
     * Indicateur de suppression logique.
     */
    #[ORM\Column(name: 'suppr', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $suppr = false; // Initialisé dans le constructeur, Type hint ?bool

    /**
     * État du paramètre (actif/inactif).
     */
    #[ORM\Column(name: 'etatparametre', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $etatParametre = true; // Initialisé dans le constructeur, Type hint ?bool


    public function __construct()
    {
        $this->suppr = false; // Non supprimé par défaut
        $this->etatParametre = true; // Actif par défaut
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
     * Set cle
     *
     * @param string $cle
     * @return ParamSysteme
     */
    public function setCle(string $cle): self
    {
        $this->cle = $cle;
        return $this;
    }

    /**
     * Get cle
     *
     * @return string|null
     */
    public function getCle(): ?string
    {
        return $this->cle;
    }

    /**
     * Set valeur
     *
     * @param string|null $valeur // Accepte null
     * @return ParamSysteme
     */
    public function setValeur(?string $valeur): self // Accepte null
    {
        $this->valeur = $valeur;
        return $this;
    }

    /**
     * Get valeur (brute, string)
     *
     * @return string|null
     */
    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return ParamSysteme
     */
    public function setType(int $type): self // Type param corrigé en int
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return integer|null
     */
    public function getType(): ?int // Type retour corrigé
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string|null $description // Accepte null
     * @return ParamSysteme
     */
    public function setDescription(?string $description): self // Accepte null
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Vérifie si le paramètre est supprimé.
     */
    public function isSuppr(): ?bool // Getter booléen
    {
        return $this->suppr;
    }

    /**
     * Set suppr
     *
     * @param boolean $suppr
     * @return ParamSysteme
     */
    public function setSuppr(bool $suppr): self // Type param corrigé en bool
    {
        $this->suppr = $suppr;
        return $this;
    }

    /**
     * Get suppr (moins sémantique que isSuppr)
     * @return boolean|null
     */
     public function getSuppr(): ?bool // Type retour corrigé
     {
         return $this->suppr;
     }

    /**
     * Vérifie si le paramètre est actif.
     */
    public function isEtatParametre(): ?bool // Getter booléen
    {
        return $this->etatParametre;
    }

    /**
     * Set etatParametre
     *
     * @param boolean $etatParametre
     * @return ParamSysteme
     */
    public function setEtatParametre(bool $etatParametre): self // Type param corrigé en bool
    {
        $this->etatParametre = $etatParametre;
        return $this;
    }

    /**
     * Get etatParametre (moins sémantique que isEtatParametre)
     * @return boolean|null
     */
     public function getEtatParametre(): ?bool // Type retour corrigé
     {
         return $this->etatParametre;
     }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->cle ?? 'ParamSysteme #' . $this->id;
    }
}