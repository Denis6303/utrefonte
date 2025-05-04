<?php

namespace App\Entity;

use App\Repository\RechercheRepository; // Importer le Repository (nom de classe corrigé)
use Doctrine\DBAL\Types\Types;       // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant un terme de recherche (potentiellement pour historique ou suggestions).
 */
#[ORM\Entity(repositoryClass: RechercheRepository::class)] // Nom Repository corrigé
#[ORM\Table(name: 'recherche')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class Recherche
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idrecherche', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    /**
     * Le mot clé recherché.
     * @var string|null
     */
    #[ORM\Column(name: 'motcle', type: Types::STRING, length: 70)]
    #[Assert\NotBlank(message: "Le mot clé ne peut pas être vide.")] // Ajouté car essentiel
    #[Assert\Length(
        min: 3,
        max: 70, // Correspond à la longueur ORM
        minMessage: "Le mot clé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le mot clé ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $motcle = null; // Visibilité private, type hint ?string


    // Constructeur vide supprimé

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
     * Set motcle
     *
     * @param string $motcle
     * @return Recherche
     */
    public function setMotcle(string $motcle): self
    {
        $this->motcle = $motcle;
        return $this;
    }

    /**
     * Get motcle
     *
     * @return string|null
     */
    public function getMotcle(): ?string
    {
        return $this->motcle;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->motcle ?? 'Recherche #' . $this->id;
    }
}