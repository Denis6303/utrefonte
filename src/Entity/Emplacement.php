<?php

namespace App\Entity;

use App\Repository\EmplacementRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;            // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;     // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;
// Importer Cadre si ce n'est pas dans le même namespace
// use App\Entity\Cadre;

/**
 * Classe qui va gerer les emplacements ou blocs sur le site
 *
 * @author Edem
 */
#[ORM\Entity(repositoryClass: EmplacementRepository::class)]
#[ORM\Table(name: 'emplacement')]
#[ORM\HasLifecycleCallbacks] // Conserver l'attribut pour les callbacks
class Emplacement
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idemplacement', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Libellé de l'emplacement (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'libemplacement', type: Types::TEXT, nullable: true)]
    // Pas de NotBlank ici car peut être null ou dépendre de la langue
    private ?string $libEmplacement = null;

    /**
     * Statut de l'emplacement (0-6).
     * @var int|null
     */
    #[ORM\Column(name: 'statutemplacement', type: Types::INTEGER)]
    #[Assert\NotNull] // Statut est initialisé, donc ne devrait pas être null
    #[Assert\Range(min: 0, max: 6, notInRangeMessage: "Le statut doit être compris entre {{ min }} et {{ max }}.")] // Plus approprié que Regex
    private ?int $statutEmplacement = null; // Sera initialisé dans PrePersist

    /**
     * Indicateur de suppression logique (0=non supprimé, 1=supprimé).
     * @var bool|null
     */
    #[ORM\Column(name: 'suppr', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull] // Un booléen doit être défini
    private ?bool $suppr = false; // Initialisé à false (non supprimé) par défaut

    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;

    /**
     * Cadre auquel cet emplacement appartient.
     * Supposons qu'un emplacement DOIT appartenir à un cadre.
     * @var Cadre|null
     */
    #[ORM\ManyToOne(targetEntity: Cadre::class, inversedBy: 'emplacements', cascade: ['persist'])] // Assurez-vous que 'emplacements' existe dans Cadre
    #[ORM\JoinColumn(name: 'idcadre', referencedColumnName: 'idcadre', nullable: false)] // Rendu non nullable
    #[Assert\NotNull(message: "Le cadre associé est obligatoire.")]
    private ?Cadre $cadre = null;


    public function __construct()
    {
        // Initialisation de $suppr à false
        $this->suppr = false;
        // $statutEmplacement est défini dans le callback PrePersist
    }

    #[ORM\PrePersist]
    public function initializeDefaults(): void // Renommé pour clarté, ajout type void
    {
        if ($this->statutEmplacement === null) {
             $this->statutEmplacement = 1; // Valeur par défaut: En cours de rédaction (supposition)
        }
         if ($this->suppr === null) { // Sécurité si constructeur non appelé
             $this->suppr = false;
         }
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Nom et type retour standardisés
    {
        return $this->id;
    }

    public function getLibEmplacement(): ?string
    {
        return $this->libEmplacement;
    }

    public function setLibEmplacement(?string $libEmplacement): self // Accepte null
    {
        $this->libEmplacement = $libEmplacement;
        return $this;
    }

    public function getStatutEmplacement(): ?int // Type retour corrigé
    {
        return $this->statutEmplacement;
    }

    public function setStatutEmplacement(int $statutEmplacement): self // Type paramètre corrigé
    {
        $this->statutEmplacement = $statutEmplacement;
        return $this;
    }

    /**
     * Vérifie si l'emplacement est marqué comme supprimé.
     */
    public function isSuppr(): ?bool // Getter standard pour booléen
    {
        return $this->suppr;
    }

    public function setSuppr(bool $suppr): self // Type paramètre corrigé en bool
    {
        $this->suppr = $suppr;
        return $this;
    }

    public function getCadre(): ?Cadre // Type retour corrigé
    {
        return $this->cadre;
    }

    // Le paramètre ne peut plus être null si nullable=false
    public function setCadre(Cadre $cadre): self
    {
        $this->cadre = $cadre;
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
        return $this->libEmplacement ?? 'Emplacement #' . $this->id;
    }
}