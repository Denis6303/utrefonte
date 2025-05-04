<?php

namespace App\Entity;

use App\Repository\DroitRepository; // Assurez-vous que le nom du repository est correct (DroitRepository)
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Importer Profil si ce n'est pas dans le même namespace
// use App\Entity\Profil;

/**
 * Entité représentant les droits associés à un Profil.
 * Le nom de classe a été changé en Droit (majuscule) pour respecter PSR-1.
 */
#[ORM\Entity(repositoryClass: DroitRepository::class)] // Référence au bon Repository
#[ORM\Table(name: 'droit')] // Nom de la table explicite
class Droit // Nom de classe corrigé
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(type: Types::INTEGER)] // name="id" est la valeur par défaut
    private ?int $id = null;

    /**
     * Stockage des droits.
     * Le type TEXT suggère une chaîne (peut-être JSON sérialisé, CSV, etc.).
     * Si c'est une liste de rôles/permissions, envisager Types::JSON et ?array comme type PHP.
     * @var string|null
     */
    #[ORM\Column(name: 'droits', type: Types::TEXT)]
    #[Assert\NotBlank(message: "La définition des droits ne peut pas être vide.")]
    private ?string $droits = null;

    /**
     * Le Profil auquel ces droits sont associés.
     * Supposons qu'un enregistrement Droit DOIT être lié à un Profil.
     * 'droits' est la propriété dans Profil qui référence cette entité (inversedBy).
     * @var Profil|null
     */
    // La propriété 'droit' (singulier) dans inversedBy est inhabituelle pour une relation ToMany,
    // changé en 'droits' (pluriel). VÉRIFIEZ la propriété dans l'entité Profil.
    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: 'droits', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idprofil', referencedColumnName: 'idprofil', nullable: false)] // Rendu non nullable
    #[Assert\NotNull(message: "Le profil associé est obligatoire.")]
    private ?Profil $profil = null; // Type hint corrigé

    // Pas de constructeur nécessaire par défaut

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
     * Set droits
     *
     * @param string $droits
     * @return Droit
     */
    public function setDroits(string $droits): self
    {
        $this->droits = $droits;
        return $this;
    }

    /**
     * Get droits
     *
     * @return string|null
     */
    public function getDroits(): ?string
    {
        return $this->droits;
    }

    /**
     * Set profil
     *
     * @param Profil $profil Le profil associé (ne peut pas être null ici)
     * @return Droit // Le type de retour dans le docblock original (Action) était incorrect
     */
    public function setProfil(Profil $profil): self // Paramètre ne peut plus être null
    {
        $this->profil = $profil;
        return $this;
    }

    /**
     * Get profil
     *
     * @return Profil|null Le profil associé
     */
    public function getProfil(): ?Profil // Type retour corrigé
    {
        return $this->profil;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        $profilIdentifier = $this->profil ? $this->profil->getId() : 'aucun'; // Adapter si Profil a une méthode __toString ou getName()
        return 'Droits pour Profil #' . $profilIdentifier ?? 'Droit #' . $this->id;
    }
}