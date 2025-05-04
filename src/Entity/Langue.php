<?php

namespace App\Entity;

use App\Repository\LangueRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe qui va gerer les langues du site
 *
 * @author Gautier
 */
#[ORM\Entity(repositoryClass: LangueRepository::class)]
#[ORM\Table(name: 'langue')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class Langue
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idlangue', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Libellé de la langue (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'liblangue', type: Types::STRING, length: 255)] // Longueur par défaut si non spécifiée
    #[Assert\NotBlank(message: "Le libellé de la langue ne peut être vide.", groups: ['translatable_validation'])] // Valider si besoin par langue
    #[Assert\Length(max: 255)]
    private ?string $libLangue = null;

    /**
     * Code ISO de la langue (ex: 'fr', 'en', 'es').
     * Devrait être unique.
     * @var string|null
     */
    #[ORM\Column(name: 'codelangue', type: Types::STRING, length: 5, unique: true)] // Rendu unique
    #[Assert\NotBlank(message: "Le code langue est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 5, // Généralement 2 ('fr') ou 5 ('fr_FR')
        minMessage: "Le code langue doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le code langue ne doit pas dépasser {{ limit }} caractères."
    )]
    // Optionnel : valider que c'est un code langue valide
    // #[Assert\Locale(message: "Le code '{{ value }}' n'est pas un code langue valide.")]
    private ?string $codeLangue = null;

    /**
     * État de la langue (ex: active/inactive).
     * @var bool|null
     */
    #[ORM\Column(name: 'langueetat', type: Types::BOOLEAN)] // Changé en BOOLEAN pour la sémantique
    #[Assert\NotNull]
    private ?bool $langueEtat = true; // Initialisé à true (actif) par défaut

    /**
     * Chemin vers l'icône ou classe CSS pour l'icône.
     * @var string|null
     */
    #[ORM\Column(name: 'iconelangue', type: Types::STRING, length: 255, nullable: true)] // Rendu nullable, longueur par défaut
    #[Assert\Length(max: 255)]
    private ?string $iconeLangue = null;

    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;

    // Constructeur vide supprimé car non nécessaire

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    public function getLibLangue(): ?string
    {
        return $this->libLangue;
    }

    public function setLibLangue(string $libLangue): self
    {
        $this->libLangue = $libLangue;
        return $this;
    }

    public function getCodeLangue(): ?string
    {
        return $this->codeLangue;
    }

    public function setCodeLangue(string $codeLangue): self
    {
        $this->codeLangue = $codeLangue;
        return $this;
    }

    /**
     * Retourne true si la langue est active.
     */
    public function isLangueEtat(): ?bool // Getter standard pour booléen
    {
        return $this->langueEtat;
    }

    public function setLangueEtat(bool $langueEtat): self // Type param corrigé en bool
    {
        $this->langueEtat = $langueEtat;
        return $this;
    }

    public function getIconeLangue(): ?string
    {
        return $this->iconeLangue;
    }

    public function setIconeLangue(?string $iconeLangue): self // Accepte null
    {
        $this->iconeLangue = $iconeLangue;
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
        return $this->libLangue . ' (' . $this->codeLangue . ')' ?? 'Langue #' . $this->id;
    }
}