<?php

namespace App\Entity;

use App\Repository\InfosAfterLoadRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;               // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables

/**
 * Entité pour stocker des statistiques ou informations après un chargement de fichier.
 */
#[ORM\Entity(repositoryClass: InfosAfterLoadRepository::class)]
#[ORM\Table(name: 'infosafterload')]
class InfosAfterLoad
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idinfos', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Date à laquelle les statistiques se réfèrent.
     */
    #[ORM\Column(name: 'datestat', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull] // Si cette date est toujours requise
    private ?DateTimeImmutable $dateStat = null; // Nom corrigé, Type hint DateTimeImmutable

    /**
     * Type de compte concerné par le chargement (pourrait être une relation ManyToOne vers TypeCompte).
     */
    #[ORM\Column(name: 'typecompte', type: Types::INTEGER)] // Gardé INTEGER, mais relation serait mieux
    #[Assert\NotNull(message: "Le type de compte doit être spécifié.")]
    private ?int $typeCompteId = null; // Renommé pour clarifier (ID), Type hint ?int

    /**
     * Nombre total de lignes dans le fichier chargé.
     */
    #[ORM\Column(name: 'nbretotal', type: Types::INTEGER)]
    #[Assert\NotNull(message: "Le nombre total de lignes est requis.")]
    #[Assert\PositiveOrZero(message: "Le nombre total de lignes doit être positif ou zéro.")]
    private ?int $nbreTotalLigne = null; // Type hint ?int

    /**
     * Nombre de lignes effectivement importées.
     */
    #[ORM\Column(name: 'nbreimport', type: Types::INTEGER)]
    #[Assert\NotNull(message: "Le nombre de lignes importées est requis.")]
    #[Assert\PositiveOrZero(message: "Le nombre de lignes importées doit être positif ou zéro.")]
    private ?int $nbreImportLigne = null; // Type hint ?int

    /**
     * Pourcentage de lignes importées (calculé?).
     * Utiliser Types::DECIMAL pour plus de précision que float.
     * Ex: precision=5, scale=2 pour stocker 999.99
     */
    #[ORM\Column(name: 'prcentimport', type: Types::DECIMAL, precision: 5, scale: 2)] // Changé en DECIMAL
    #[Assert\NotNull(message: "Le pourcentage importé est requis.")]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: "Le pourcentage doit être entre 0 et 100.")]
    private ?string $prcentImport = null; // Type hint string car DECIMAL est souvent retourné comme string par Doctrine

    /**
     * Nombre de comptes mentionnés dans le fichier mais inexistants dans la base.
     */
    #[ORM\Column(name: 'nbrecpteinexistant', type: Types::INTEGER)]
    #[Assert\NotNull(message: "Le nombre de comptes inexistants est requis.")]
    #[Assert\PositiveOrZero(message: "Le nombre de comptes inexistants doit être positif ou zéro.")]
    private ?int $nbreCpteInexistant = null; // Type hint ?int

    /**
     * ID du fichier (Chargement?) auquel ces infos se réfèrent.
     * Devrait être une relation OneToOne ou ManyToOne vers l'entité Chargement.
     */
    #[ORM\Column(name: 'idfile', type: Types::INTEGER)] // Gardé INTEGER, mais relation serait mieux
    #[Assert\NotNull(message: "L'ID du fichier associé est requis.")]
    private ?int $idFile = null; // Renommé, Type hint ?int

    // Constructeur initialisant dateCreation (qui n'existe plus) supprimé.
    // Ajouter des initialisations si nécessaire (ex: compteurs à 0).
    public function __construct()
    {
        $this->nbreTotalLigne = 0;
        $this->nbreImportLigne = 0;
        $this->prcentImport = '0.00'; // Initialiser comme string pour DECIMAL
        $this->nbreCpteInexistant = 0;
        $this->dateStat = new DateTimeImmutable(); // Date de création des infos par défaut
    }


    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    public function getDateStat(): ?DateTimeImmutable // Nom et type retour corrigés
    {
        return $this->dateStat;
    }

    public function setDateStat(DateTimeImmutable $dateStat): self // Nom et type param corrigés
    {
        $this->dateStat = $dateStat;
        return $this;
    }

    public function getTypeCompteId(): ?int // Nom et type retour corrigés
    {
        return $this->typeCompteId;
    }

    public function setTypeCompteId(int $typeCompteId): self // Nom et type param corrigés
    {
        $this->typeCompteId = $typeCompteId;
        return $this;
    }

    public function getNbreTotalLigne(): ?int // Type retour corrigé
    {
        return $this->nbreTotalLigne;
    }

    public function setNbreTotalLigne(int $nbreTotalLigne): self // Type param corrigé
    {
        $this->nbreTotalLigne = $nbreTotalLigne;
        return $this;
    }

    public function getNbreImportLigne(): ?int // Type retour corrigé
    {
        return $this->nbreImportLigne;
    }

    public function setNbreImportLigne(int $nbreImportLigne): self // Type param corrigé
    {
        $this->nbreImportLigne = $nbreImportLigne;
        return $this;
    }

    /**
     * Retourne le pourcentage comme string (format BDD).
     */
    public function getPrcentImport(): ?string // Type retour string (pour DECIMAL)
    {
        return $this->prcentImport;
    }

    /**
     * Retourne le pourcentage comme float.
     */
    public function getPrcentImportAsFloat(): ?float
    {
        return $this->prcentImport === null ? null : (float) $this->prcentImport;
    }

    public function setPrcentImport(string $prcentImport): self // Type param string (pour DECIMAL)
    {
        // Ajouter une validation du format si nécessaire
        $this->prcentImport = $prcentImport;
        return $this;
    }

    public function getNbreCpteInexistant(): ?int // Type retour corrigé
    {
        return $this->nbreCpteInexistant;
    }

    public function setNbreCpteInexistant(int $nbreCpteInexistant): self // Type param corrigé
    {
        $this->nbreCpteInexistant = $nbreCpteInexistant;
        return $this;
    }

    public function getIdFile(): ?int // Nom et type retour corrigés
    {
        return $this->idFile;
    }

    public function setIdFile(int $idFile): self // Nom et type param corrigés
    {
        $this->idFile = $idFile;
        return $this;
    }

    /**
     * Méthode utilitaire pour calculer le pourcentage si nécessaire.
     * Attention: éviter la division par zéro.
     */
    public function calculatePercentImport(): ?string // Retourne string pour être cohérent avec setPrcentImport
    {
        if ($this->nbreTotalLigne > 0) {
            $percent = ($this->nbreImportLigne / $this->nbreTotalLigne) * 100;
            // Formater avec 2 décimales pour correspondre à DECIMAL(5,2)
            return number_format($percent, 2, '.', '');
        }
        return '0.00'; // Ou null si préféré
    }

    /**
     * Peut être appelé avant la persistance si le pourcentage n'est pas défini ailleurs.
     */
    public function updatePercentImportIfNeeded(): void
    {
         if ($this->prcentImport === null || $this->prcentImport === '0.00') { // Ou autre condition
            $this->prcentImport = $this->calculatePercentImport();
         }
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        $dateStr = $this->dateStat ? $this->dateStat->format('Y-m-d') : 'N/A';
        return 'Infos Chargement Fichier #' . $this->idFile . ' (' . $dateStr . ')';
    }
}