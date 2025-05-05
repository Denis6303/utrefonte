<?php

namespace App\Entity;

use App\Repository\CategorieCompteRepository; // Assurez-vous que ce repository existe
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieCompteRepository::class)]
#[ORM\Table(name: 'categoriecompte')]
class CategorieCompte
{
    #[ORM\Id]
    #[ORM\Column(name: 'codecategorie', type: Types::STRING, length: 4)]
    #[Assert\NotBlank(message: "Le code catégorie ne peut être vide.")]
    #[Assert\Length(exactly: 4, exactMessage: "Le code catégorie doit faire exactement {{ limit }} caractères.")]
    private ?string $codecategorie = null; // Changé de protected à private, type hint ajouté

    #[ORM\Column(name: 'libcategorie', type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: "Le libellé ne peut être vide.")]
    #[Assert\Length(max: 50, maxMessage: "Le libellé ne peut dépasser {{ limit }} caractères.")]
    private ?string $libCategorie = null;

    /**
     * Indique si la catégorie est active.
     */
    #[ORM\Column(name: 'active', type: Types::BOOLEAN)] // Changé en BOOLEAN pour plus de sémantique
    #[Assert\NotNull] // Un booléen ne peut pas être "blank", mais il doit être défini (true/false)
    private ?bool $active = null; // Type hint changé en bool

    /**
     * Indique si les comptes de cette catégorie peuvent avoir une carte.
     */
    #[ORM\Column(name: 'sicarte', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $carteAllowed = null; // Renommé pour plus de clarté, type hint changé en bool

    /**
     * Indique si les comptes de cette catégorie peuvent avoir un chéquier.
     */
    #[ORM\Column(name: 'sicheque', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $chequeAllowed = null; // Renommé pour plus de clarté, type hint changé en bool

    /**
     * Relation OneToMany vers les comptes de cette catégorie.
     * 'categorieCompte' doit être le nom de la propriété ManyToOne dans l'entité Compte.
     * @var Collection<int, Compte>
     */
    #[ORM\OneToMany(mappedBy: 'categorieCompte', targetEntity: Compte::class)] // Assurez-vous que 'categorieCompte' est correct
    private Collection $comptes;


    public function __construct()
    {
        $this->active = true; // Initialise à true (équivalent de 1)
        $this->carteAllowed = false; // Valeur par défaut (à ajuster si nécessaire)
        $this->chequeAllowed = false; // Valeur par défaut (à ajuster si nécessaire)
        $this->comptes = new ArrayCollection();
    }

    public function getCodecategorie(): ?string
    {
        return $this->codecategorie;
    }

    /**
     * Le code catégorie est l'ID, ne devrait généralement pas être modifié après création.
     */
    public function setCodecategorie(string $codecategorie): self
    {
        $this->codecategorie = $codecategorie;
        return $this;
    }

    public function getLibCategorie(): ?string
    {
        return $this->libCategorie;
    }

    public function setLibCategorie(string $libCategorie): self
    {
        $this->libCategorie = $libCategorie;
        return $this;
    }

    public function isActive(): ?bool // Nom de getter standard pour booléen
    {
        return $this->active;
    }

    public function setActive(bool $active): self // Type hint corrigé en bool
    {
        $this->active = $active;
        return $this;
    }

    public function isCarteAllowed(): ?bool // Nom de getter amélioré
    {
        return $this->carteAllowed;
    }

    public function setCarteAllowed(bool $carteAllowed): self // Nom de setter amélioré, type hint bool
    {
        $this->carteAllowed = $carteAllowed;
        return $this;
    }

    public function isChequeAllowed(): ?bool // Nom de getter amélioré
    {
        return $this->chequeAllowed;
    }

    public function setChequeAllowed(bool $chequeAllowed): self // Nom de setter amélioré, type hint bool
    {
        $this->chequeAllowed = $chequeAllowed;
        return $this;
    }

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection // Type de retour corrigé
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self // Type hint corrigé
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes->add($compte);
            // Mettre à jour le côté propriétaire (ManyToOne dans Compte)
            $compte->setCategorieCompte($this); // Assurez-vous que setCategorieCompte existe dans Compte
        }
        return $this;
    }

    public function removeCompte(Compte $compte): self // Type hint corrigé
    {
        if ($this->comptes->removeElement($compte)) {
            // Mettre le côté propriétaire à null (si la relation est nullable)
            if ($compte->getCategorieCompte() === $this) { // Assurez-vous que getCategorieCompte existe
                $compte->setCategorieCompte(null);
            }
        }
        return $this;
    }

    // --- Méthodes setSicarte / getSicarte (dépréciées, utiliser *Allowed) ---
    // Gardées pour compatibilité si nécessaire, mais marquées deprecated

    /**
     * @deprecated Utiliser setCarteAllowed() à la place.
     */
    public function setSicarte(bool $sicarte): self
    {
        $this->setCarteAllowed($sicarte);
        return $this;
    }

    /**
     * @deprecated Utiliser isCarteAllowed() à la place.
     */
    public function getSicarte(): ?bool
    {
        return $this->isCarteAllowed();
    }

    /**
     * @deprecated Utiliser setChequeAllowed() à la place.
     */
    public function setSicheque(bool $sicheque): self
    {
        $this->setChequeAllowed($sicheque);
        return $this;
    }

    /**
     * @deprecated Utiliser isChequeAllowed() à la place.
     */
    public function getSicheque(): ?bool
    {
        return $this->isChequeAllowed();
    }


    // --- Méthode __toString ---
    public function __toString(): string
    {
        return $this->libCategorie ?? $this->codecategorie ?? 'Catégorie Compte';
    }
}