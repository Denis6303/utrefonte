<?php

namespace App\Entity;

use App\Repository\FacturationRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;           // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant potentiellement des paramètres de facturation.
 */
#[ORM\Entity(repositoryClass: FacturationRepository::class)]
#[ORM\Table(name: 'facturation')]
// Pas besoin de @ORM\HasLifecycleCallbacks si aucune méthode de callback n'est définie
class Facturation
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idfacturation', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    /**
     * Montant UWEB (Unité Web? à clarifier).
     * Doit être un entier positif ou zéro.
     */
    #[ORM\Column(name: 'montantuweb', type: Types::INTEGER)]
    #[Assert\NotNull(message: "Le montant uweb ne peut être vide")] // NotNull car c'est un entier
    #[Assert\PositiveOrZero(message: "Le montant uweb doit être positif ou zéro.")] // MinLength(2) n'a pas de sens pour un entier, remplacé par PositiveOrZero
    private ?int $montantuweb = null; // Type hint ?int

    /**
     * Montant AFBW (?).
     * Doit être un entier positif ou zéro.
     */
    #[ORM\Column(name: 'montantafbw', type: Types::INTEGER)]
    #[Assert\NotNull(message: "Le montant afbw ne peut être vide")] // NotNull car c'est un entier
    #[Assert\PositiveOrZero(message: "Le montant afbw doit être positif ou zéro.")] // MinLength(2) remplacé
    private ?int $montantafbw = null; // Type hint ?int

    // Constructeur vide ou commenté peut être omis
    // function __construct() {
    //     //$this->etat = 0;
    // }

    /**
     * Get id
     *
     * @return integer|null
     */
    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    // Pas de setter pour l'ID auto-généré

    /**
     * Set montantuweb
     *
     * @param integer $montantuweb
     * @return Facturation
     */
    public function setMontantuweb(int $montantuweb): self // Type paramètre corrigé en int
    {
        $this->montantuweb = $montantuweb;
        return $this;
    }

    /**
     * Get montantuweb
     *
     * @return integer|null
     */
    public function getMontantuweb(): ?int // Type retour corrigé
    {
        return $this->montantuweb;
    }

    /**
     * Set montantafbw
     *
     * @param integer $montantafbw
     * @return Facturation
     */
    public function setMontantafbw(int $montantafbw): self // Type paramètre corrigé en int
    {
        $this->montantafbw = $montantafbw;
        return $this;
    }

    /**
     * Get montantafbw
     *
     * @return integer|null
     */
    public function getMontantafbw(): ?int // Type retour corrigé
    {
        return $this->montantafbw;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return 'Facturation #' . $this->id . ' (UWEB: ' . $this->montantuweb . ', AFBW: ' . $this->montantafbw . ')';
    }
}