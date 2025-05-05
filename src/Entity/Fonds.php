<?php

namespace App\Entity;

use App\Repository\FondsRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Importer Compte et Utilisateur si nécessaire
// use App\Entity\Compte;
// use App\Entity\Utilisateur;

#[ORM\Entity(repositoryClass: FondsRepository::class)]
#[ORM\Table(name: 'fonds')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class Fonds
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idfonds', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    #[ORM\Column(name: 'codefonds', type: Types::STRING, length: 10, unique: true)] // Un code est souvent unique
    #[Assert\NotBlank(message: "Le code du fonds ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 10, // Correspond à la longueur ORM
        minMessage: "Le code doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le code ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $codeFonds = null;

    #[ORM\Column(name: 'libfonds', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé du fonds ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 100, // Correspond à la longueur ORM
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libFonds = null;

    /**
     * État du fonds (ex: 0=inactif, 1=actif).
     */
    #[ORM\Column(name: 'etatfonds', type: Types::INTEGER)] // Gardé INTEGER, mais BOOLEAN serait mieux si 0/1
    #[Assert\NotNull] // Si un état doit toujours être défini
    // #[Assert\Choice(choices: [0, 1], message: "L'état doit être 0 ou 1.")] // Si limité
    private ?int $etatFonds = 1; // Initialiser un état par défaut (ex: actif)

    /**
     * Indicateur de suppression logique.
     */
    #[ORM\Column(name: 'suppr', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $suppr = false; // Initialisé dans le constructeur

    // --- RELATIONS ---

    /**
     * Comptes associés à ce fonds.
     * 'fonds' est la propriété dans Compte qui référence cette entité (mappedBy).
     * @var Collection<int, Compte>
     */
    // mappedBy="Fonds" (avec majuscule) est probablement incorrect. Corrigé en 'fonds'. VÉRIFIEZ DANS L'ENTITÉ Compte.
    #[ORM\OneToMany(mappedBy: 'fonds', targetEntity: Compte::class, cascade: ['persist'])]
    private Collection $comptes;

    /**
     * Utilisateur lié (créateur? gestionnaire?).
     * 'fonds' est la propriété dans User qui référence cette entité (inversedBy).
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'fonds', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idutilisateur', referencedColumnName: 'id', nullable: true)] // Changed from 'iduser' to 'id'
    private ?User $utilisateur = null;


    public function __construct()
    {
        $this->suppr = false; // Non supprimé par défaut
        $this->etatFonds = 1; // Actif par défaut
        $this->comptes = new ArrayCollection();
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
     * Set codeFonds
     *
     * @param string $codeFonds
     * @return Fonds
     */
    public function setCodeFonds(string $codeFonds): self
    {
        $this->codeFonds = $codeFonds;
        return $this;
    }

    /**
     * Get codeFonds
     *
     * @return string|null
     */
    public function getCodeFonds(): ?string
    {
        return $this->codeFonds;
    }

    /**
     * Set libFonds
     *
     * @param string $libFonds
     * @return Fonds
     */
    public function setLibFonds(string $libFonds): self
    {
        $this->libFonds = $libFonds;
        return $this;
    }

    /**
     * Get libFonds
     *
     * @return string|null
     */
    public function getLibFonds(): ?string
    {
        return $this->libFonds;
    }

    /**
     * Set etatFonds
     *
     * @param integer $etatFonds
     * @return Fonds
     */
    public function setEtatFonds(int $etatFonds): self // Type param corrigé en int
    {
        $this->etatFonds = $etatFonds;
        return $this;
    }

    /**
     * Get etatFonds
     *
     * @return integer|null
     */
    public function getEtatFonds(): ?int // Type retour corrigé
    {
        return $this->etatFonds;
    }

     /**
      * Vérifie si le fonds est supprimé.
      */
    public function isSuppr(): ?bool // Getter booléen
    {
        return $this->suppr;
    }

    /**
     * Set suppr
     *
     * @param boolean $suppr
     * @return Fonds
     */
    public function setSuppr(bool $suppr): self // Type param corrigé en bool
    {
        $this->suppr = $suppr;
        return $this;
    }

    /**
     * Get suppr (moins sémantique que isSuppr)
     *
     * @return boolean|null
     */
    public function getSuppr(): ?bool // Type retour corrigé
    {
        return $this->suppr;
    }

    /**
     * Set utilisateur
     *
     * @param User|null $utilisateur
     * @return Fonds
     */
    public function setUtilisateur(?User $utilisateur): self // Type param corrigé, accepte null
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return User|null
     */
    public function getUtilisateur(): ?User // Type retour corrigé
    {
        return $this->utilisateur;
    }


    // --- Gestion de la collection Comptes ---

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection // Type retour corrigé
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self // Type param corrigé
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes->add($compte);
            // Mettre à jour le côté propriétaire (ManyToOne dans Compte)
            $compte->setFonds($this); // Assurez-vous que setFonds existe et est correct dans Compte
        }
        return $this;
    }

    public function removeCompte(Compte $compte): self // Type param corrigé
    {
        if ($this->comptes->removeElement($compte)) {
            // Mettre le côté propriétaire à null (si la relation est nullable dans Compte)
            if ($compte->getFonds() === $this) { // Assurez-vous que getFonds existe dans Compte
                $compte->setFonds(null);
            }
        }
        return $this;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->libFonds . ' (' . $this->codeFonds . ')' ?? 'Fonds #' . $this->id;
    }
}