<?php

namespace App\Entity;

use App\Repository\DroitClientRepository; // Assurez-vous que le nom du repository est correct (DroitClientRepository)
use Doctrine\DBAL\Types\Types;           // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Importer ProfilClient si ce n'est pas dans le même namespace
// use App\Entity\ProfilClient;

/**
 * Entité représentant les droits spécifiques aux clients (ProfilClient).
 * Le nom de classe a été changé en DroitClient (majuscule) pour respecter PSR-1.
 */
#[ORM\Entity(repositoryClass: DroitClientRepository::class)] // Référence au bon Repository
#[ORM\Table(name: 'droitclient')] // Nom de la table explicite
class DroitClient // Nom de classe corrigé
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(type: Types::INTEGER)] // name="id" est la valeur par défaut
    private ?int $id = null;

    /**
     * Stockage des droits spécifiques au client.
     * Le type TEXT suggère une chaîne (peut-être JSON sérialisé, CSV, etc.).
     * Si c'est une liste de rôles/permissions, envisager Types::JSON et ?array comme type PHP.
     * @var string|null
     */
    #[ORM\Column(name: 'droits', type: Types::TEXT)]
    #[Assert\NotBlank(message: "La définition des droits ne peut pas être vide.")]
    private ?string $droits = null;

    /**
     * Le ProfilClient auquel ces droits sont associés.
     * Supposons qu'un enregistrement DroitClient DOIT être lié à un ProfilClient.
     * 'droits' est la propriété dans ProfilClient qui référence cette entité (inversedBy).
     * @var ProfilClient|null
     */
    // La propriété 'droit' (singulier) dans inversedBy est inhabituelle pour une relation ToMany,
    // changé en 'droits' (pluriel). VÉRIFIEZ la propriété dans l'entité ProfilClient.
    #[ORM\ManyToOne(targetEntity: ProfilClient::class, inversedBy: 'droits', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idprofil', referencedColumnName: 'idprofil', nullable: false)] // Rendu non nullable, vérifiez 'idprofil' sur ProfilClient
    #[Assert\NotNull(message: "Le profil client associé est obligatoire.")]
    private ?ProfilClient $profil = null; // Type hint corrigé

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
     * @return DroitClient // Type retour corrigé
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
     * @param ProfilClient $profil Le profil client associé (ne peut pas être null ici)
     * @return DroitClient // Type retour corrigé
     */
    public function setProfil(ProfilClient $profil): self // Paramètre ne peut plus être null, Type hint corrigé
    {
        $this->profil = $profil;
        return $this;
    }

    /**
     * Get profil
     *
     * @return ProfilClient|null Le profil client associé
     */
    public function getProfil(): ?ProfilClient // Type retour corrigé
    {
        return $this->profil;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        $profilIdentifier = $this->profil ? $this->profil->getId() : 'aucun'; // Adapter si ProfilClient a une méthode __toString ou getName()
        return 'Droits pour ProfilClient #' . $profilIdentifier ?? 'DroitClient #' . $this->id;
    }
}