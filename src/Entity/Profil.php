<?php

namespace App\Entity;

use App\Repository\ProfilRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Imports Gedmo/Translatable et ArrayCollection non nécessaires ici

/**
 * Entité représentant un profil utilisateur (administrateur, modérateur, etc.).
 */
#[ORM\Entity(repositoryClass: ProfilRepository::class)]
#[ORM\Table(name: 'profil')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idprofil', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    /**
     * Libellé du profil.
     * @var string|null
     */
    #[ORM\Column(name: 'libprofil', type: Types::STRING, length: 70)]
    #[Assert\NotBlank(message: "Le libellé du profil ne peut être vide.")]
    #[Assert\Length(
        min: 3,
        max: 70, // Correspond à la longueur ORM
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libProfil = null; // Type hint ?string

    /**
     * État du profil (actif/inactif).
     * @var bool|null
     */
    #[ORM\Column(name: 'etatprofil', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull] // Un booléen doit être défini
    private ?bool $etatProfil = true; // Initialisé dans le constructeur, type hint ?bool

    // --- Relations potentielles (à ajouter si nécessaire) ---
    /*
    // Relation vers les utilisateurs ayant ce profil
    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: Utilisateur::class)] // Vérifiez 'profil' dans Utilisateur
    private Collection $utilisateurs;

    // Relation vers les droits associés à ce profil
    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: Droit::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // Vérifiez 'profil' dans Droit
    private Collection $droits;
    */

    public function __construct()
    {
        $this->etatProfil = true; // Actif par défaut
        // Initialiser les collections si les relations sont ajoutées
        // $this->utilisateurs = new ArrayCollection();
        // $this->droits = new ArrayCollection();
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
     * Set libProfil
     *
     * @param string $libProfil
     * @return Profil
     */
    public function setLibProfil(string $libProfil): self
    {
        $this->libProfil = $libProfil;
        return $this;
    }

    /**
     * Get libProfil
     *
     * @return string|null
     */
    public function getLibProfil(): ?string
    {
        return $this->libProfil;
    }

    /**
     * Vérifie si le profil est actif.
     */
    public function isEtatProfil(): ?bool // Getter booléen
    {
        return $this->etatProfil;
    }

    /**
     * Set etatProfil
     *
     * @param boolean $etatProfil
     * @return Profil
     */
    public function setEtatProfil(bool $etatProfil): self // Type paramètre corrigé en bool
    {
        $this->etatProfil = $etatProfil;
        return $this;
    }

    /**
     * Get etatProfil (moins sémantique que isEtatProfil)
     * @return boolean|null
     */
     public function getEtatProfil(): ?bool // Type retour corrigé
     {
         return $this->etatProfil;
     }

    // Les méthodes pour les relations (getUtilisateurs, addUtilisateur, etc.) seraient ajoutées ici si les relations étaient décommentées.

    // La méthode setTranslatableLocale n'est pas nécessaire si aucun champ n'est marqué @Gedmo\Translatable

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->libProfil ?? 'Profil #' . $this->id;
    }
}