<?php

namespace App\Entity;

use App\Repository\TypeCadreRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;         // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Importer Cadre si nécessaire
// use App\Entity\Cadre;

/**
 * Entité représentant un type de Cadre (ex: bannière, bloc texte, bloc image).
 */
#[ORM\Entity(repositoryClass: TypeCadreRepository::class)]
#[ORM\Table(name: 'typecadre')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class TypeCadre
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idtypecadre', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    #[ORM\Column(name: 'libTypeCadre', type: Types::STRING, length: 100)] // Nom de colonne conservé
    #[Assert\NotBlank(message: "Le libellé du type de cadre ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 100, // Correspond à la longueur ORM
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $libTypeCadre = null; // Type hint ?string

    /**
     * Cadres de ce type.
     * 'typeCadre' est la propriété dans Cadre qui référence cette entité (mappedBy).
     * @var Collection<int, Cadre>
     */
    #[ORM\OneToMany(mappedBy: 'typeCadre', targetEntity: Cadre::class, cascade: ['persist'])] // Vérifiez mappedBy='typeCadre', cascade à adapter
    private Collection $cadres;


    public function __construct()
    {
        $this->cadres = new ArrayCollection();
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
     * Set libTypeCadre
     *
     * @param string $libTypeCadre
     * @return TypeCadre
     */
    public function setLibTypeCadre(string $libTypeCadre): self
    {
        $this->libTypeCadre = $libTypeCadre;
        return $this;
    }

    /**
     * Get libTypeCadre
     *
     * @return string|null
     */
    public function getLibTypeCadre(): ?string
    {
        return $this->libTypeCadre;
    }

    // --- Gestion de la collection Cadres ---

    /**
     * @return Collection<int, Cadre>
     */
    public function getCadres(): Collection // Type retour corrigé
    {
        return $this->cadres;
    }

    public function addCadre(Cadre $cadre): self // Type param corrigé
    {
        if (!$this->cadres->contains($cadre)) {
            $this->cadres->add($cadre);
            // Mettre à jour le côté propriétaire (ManyToOne dans Cadre)
            $cadre->setTypeCadre($this); // Assurez-vous que setTypeCadre existe dans Cadre
        }
        return $this;
    }

    public function removeCadre(Cadre $cadre): self // Type param corrigé
    {
        if ($this->cadres->removeElement($cadre)) {
            // Mettre le côté propriétaire à null (si la relation est nullable dans Cadre)
            if ($cadre->getTypeCadre() === $this) { // Assurez-vous que getTypeCadre existe
                $cadre->setTypeCadre(null);
            }
        }
        return $this;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->libTypeCadre ?? 'TypeCadre #' . $this->id;
    }
}