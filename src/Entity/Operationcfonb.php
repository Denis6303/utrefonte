<?php

namespace App\Entity;

use App\Repository\OperationcfonbRepository; // Assurez-vous que ce repository existe et que le namespace est correct
use Doctrine\DBAL\Types\Types;               // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer les entités liées si nécessaire
// use App\Entity\Compte;
// use App\Entity\Devise;

/**
 * Operationcfonb
 * Représente une opération bancaire issue d'un fichier format CFONB.
 */
#[ORM\Entity(repositoryClass: OperationcfonbRepository::class)]
#[ORM\Table(name: 'operationcfonb')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class Operationcfonb
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'id', type: Types::INTEGER)] // name="id" est la valeur par défaut
    private ?int $id = null; // Visibilité private, type hint ?int

    #[ORM\Column(name: 'liboperation', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé de l'opération est requis.")]
    #[Assert\Length(max: 100, maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $libOperation = null; // Type hint ?string

    #[ORM\Column(name: 'datevaleur', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull(message: "La date de valeur est requise.")]
    private ?DateTimeImmutable $dateValeur = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'dateoperation', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull(message: "La date d'opération est requise.")]
    private ?DateTimeImmutable $dateOperation = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'datecompta', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull(message: "La date comptable est requise.")]
    private ?DateTimeImmutable $dateCompta = null; // Type hint DateTimeImmutable

    /**
     * Montant de l'opération. Utilisation de DECIMAL pour la précision monétaire.
     */
    #[ORM\Column(name: 'montant', type: Types::DECIMAL, precision: 14, scale: 2)] // Changé en DECIMAL, ajuster precision/scale
    #[Assert\NotNull(message: "Le montant est requis.")]
    #[Assert\Type(type: 'numeric', message: "Le montant doit être numérique.")]
    // Ajouter Positive ou NotEqualTo(0) si nécessaire
    private ?string $montant = null; // Type hint string car DECIMAL est souvent string

    /**
     * Sens de l'opération (ex: 'C' ou 'D').
     */
    #[ORM\Column(name: 'sensoperation', type: Types::STRING, length: 2)]
    #[Assert\NotBlank(message: "Le sens de l'opération est requis.")]
    #[Assert\Length(max: 2)]
    #[Assert\Choice(choices: ['C', 'D', 'CR', 'DR'], message: "Sens invalide.")] // Adapter les choix possibles
    private ?string $sensOperation = null;

    /**
     * Coefficient (utilité à déterminer).
     */
    #[ORM\Column(name: 'coef', type: Types::INTEGER, nullable: true)] // Rendu nullable si possible
    private ?int $coef = null;

    /**
     * Numéro de mouvement interne ou de référence.
     */
    #[ORM\Column(name: 'numeromvt', type: Types::STRING, length: 15)]
    #[Assert\NotBlank(message: "Le numéro de mouvement est requis.")]
    #[Assert\Length(max: 15)]
    private ?string $numeroMvt = null;

    /**
     * Code interne de l'opération bancaire.
     */
    #[ORM\Column(name: 'codoperation', type: Types::STRING, length: 5)]
    #[Assert\NotBlank(message: "Le code opération est requis.")]
    #[Assert\Length(max: 5)]
    private ?string $codOperation = null;

    /**
     * Période de référence (ex: '2023-10').
     */
    #[ORM\Column(name: 'periode', type: Types::STRING, length: 10)]
    #[Assert\NotBlank(message: "La période est requise.")]
    #[Assert\Length(max: 10)]
    // Optionnel: #[Assert\Regex(pattern: "/^\d{4}-\d{2}$/", message: "Format de période invalide (AAAA-MM).")]
    private ?string $periode = null;

    /**
     * Indicateur de traitement (0=non traité, 1=traité).
     */
    #[ORM\Column(name: 'traite', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $traite = false; // Non traité par défaut

    /**
     * ID du fichier (Chargement?) d'où provient l'opération. Relation serait mieux.
     */
    #[ORM\Column(name: 'idfile', type: Types::INTEGER)]
    #[Assert\NotNull(message: "L'ID du fichier est requis.")]
    private ?int $idfile = null;

    /**
     * Solde après cette ligne d'opération (si fourni dans le fichier).
     * Utiliser DECIMAL pour la précision.
     */
    #[ORM\Column(name: 'soldeligne', type: Types::DECIMAL, precision: 14, scale: 2, nullable: true)] // Changé en DECIMAL, rendu nullable
    #[Assert\Type(type: 'numeric', message: "Le solde doit être numérique.")]
    private ?string $soldeEnLigne = null; // Type hint string

    /**
     * Indicateur d'opération journalière? (0/1).
     */
    #[ORM\Column(name: 'chrgjr', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $journalier = false; // Défaut

    /**
     * Ordre de l'opération dans le fichier/journée.
     */
    #[ORM\Column(name: 'ordre', type: Types::INTEGER)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $ordre = 0; // Défaut

    // --- Champs Spécifiques CFONB (à conserver tels quels si nécessaires) ---

    #[ORM\Column(name: 'mttafbw', type: Types::STRING, length: 14, nullable: true)]
    #[Assert\Length(max: 14)]
    private ?string $mttafbw = null;

    #[ORM\Column(name: 'bnqcod', type: Types::STRING, length: 5, nullable: true)]
    #[Assert\Length(max: 5)]
    private ?string $codeBnq = null;

    #[ORM\Column(name: 'guichet', type: Types::STRING, length: 5, nullable: true)]
    #[Assert\Length(max: 5)]
    private ?string $codeGui = null;

    #[ORM\Column(name: 'ladevise', type: Types::STRING, length: 3, nullable: true)]
    #[Assert\Length(max: 3)]
    private ?string $codeDevise = null; // Nom déjà utilisé pour la relation, renommé pour éviter confusion

    #[ORM\Column(name: 'motrej', type: Types::STRING, length: 2, nullable: true)]
    #[Assert\Length(max: 2)]
    private ?string $motrej = null;

    #[ORM\Column(name: 'monori', type: Types::STRING, length: 1, nullable: true)]
    #[Assert\Length(max: 1)]
    private ?string $monori = null;

    #[ORM\Column(name: 'virgul', type: Types::STRING, length: 1, nullable: true)]
    #[Assert\Length(max: 1)]
    private ?string $virgul = null;

    #[ORM\Column(name: 'res21', type: Types::STRING, length: 4, nullable: true)]
    #[Assert\Length(max: 4)]
    private ?string $res21 = null;

    #[ORM\Column(name: 'exocom', type: Types::STRING, length: 1, nullable: true)]
    #[Assert\Length(max: 1)]
    private ?string $exocom = null;

    #[ORM\Column(name: 'ind', type: Types::STRING, length: 1, nullable: true)]
    #[Assert\Length(max: 1)]
    private ?string $ind = null;

    #[ORM\Column(name: 'res22', type: Types::STRING, length: 2, nullable: true)]
    #[Assert\Length(max: 2)]
    private ?string $res22 = null;

    #[ORM\Column(name: 'noecri', type: Types::STRING, length: 7, nullable: true)]
    #[Assert\Length(max: 7)]
    private ?string $noecri = null;

    #[ORM\Column(name: 'cdafb', type: Types::STRING, length: 2, nullable: true)]
    #[Assert\Length(max: 2)]
    private ?string $cdafb = null;

    #[ORM\Column(name: 'res23', type: Types::STRING, length: 2, nullable: true)]
    #[Assert\Length(max: 2)]
    private ?string $res23 = null;

    #[ORM\Column(name: 'res13', type: Types::STRING, length: 2, nullable: true)]
    #[Assert\Length(max: 2)]
    private ?string $res13 = null;

    #[ORM\Column(name: 'cdcoib', type: Types::STRING, length: 4, nullable: true)]
    #[Assert\Length(max: 4)]
    private ?string $cdcoib = null;

    #[ORM\Column(name: 'sign', type: Types::STRING, length: 1, nullable: true)]
    #[Assert\Length(max: 1)]
    private ?string $sign = null;

    #[ORM\Column(name: 'cdexo', type: Types::STRING, length: 1, nullable: true)]
    #[Assert\Length(max: 1)]
    private ?string $cdexo = null;

    // --- RELATIONS ---

    /**
     * Le compte bancaire concerné par l'opération.
     * !! ATTENTION à referencedColumnName !! Vérifier si c'est bien 'numerocompte' ou 'idcompte'.
     */
    #[ORM\ManyToOne(targetEntity: Compte::class, inversedBy: 'operations')] // Assumer 'operations' dans Compte
    #[ORM\JoinColumn(name: 'numerocompte', referencedColumnName: 'numerocompte', nullable: false)] // Gardé numerocompte, mais idcompte est plus probable
    // Si c'est idcompte: #[ORM\JoinColumn(name: 'idcompte', referencedColumnName: 'idcompte', nullable: false)]
    #[Assert\NotNull(message: "Le compte associé est obligatoire.")]
    private ?Compte $compte = null; // Type hint ?Compte

    /**
     * La devise de l'opération.
     */
    #[ORM\ManyToOne(targetEntity: Devise::class, inversedBy: 'operations', cascade: ['persist'])] // Merge retiré sauf besoin
    #[ORM\JoinColumn(name: 'iddevise', referencedColumnName: 'iddevise', nullable: false)] // Rendu non nullable
    #[Assert\NotNull(message: "La devise associée est obligatoire.")]
    private ?Devise $devise = null; // Type hint ?Devise


    // Constructeur vide supprimé

    // --- GETTERS & SETTERS --- (Types corrigés)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibOperation(): ?string { return $this->libOperation; }
    public function setLibOperation(string $lib): self { $this->libOperation = $lib; return $this; }

    public function getDateValeur(): ?DateTimeImmutable { return $this->dateValeur; }
    public function setDateValeur(DateTimeImmutable $date): self { $this->dateValeur = $date; return $this; }

    public function getDateOperation(): ?DateTimeImmutable { return $this->dateOperation; }
    public function setDateOperation(DateTimeImmutable $date): self { $this->dateOperation = $date; return $this; }

    public function getDateCompta(): ?DateTimeImmutable { return $this->dateCompta; }
    public function setDateCompta(DateTimeImmutable $date): self { $this->dateCompta = $date; return $this; }

    public function getMontant(): ?string { return $this->montant; } // Retourne string pour DECIMAL
    public function setMontant(string $montant): self { $this->montant = $montant; return $this; } // Prend string pour DECIMAL
    public function getMontantAsFloat(): ?float { return $this->montant === null ? null : (float) $this->montant; }

    public function getSensOperation(): ?string { return $this->sensOperation; }
    public function setSensOperation(string $sens): self { $this->sensOperation = $sens; return $this; }

    public function getCoef(): ?int { return $this->coef; }
    public function setCoef(?int $coef): self { $this->coef = $coef; return $this; } // Accepte null

    public function getNumeroMvt(): ?string { return $this->numeroMvt; }
    public function setNumeroMvt(string $num): self { $this->numeroMvt = $num; return $this; }

    public function getCodOperation(): ?string { return $this->codOperation; }
    public function setCodOperation(string $cod): self { $this->codOperation = $cod; return $this; }

    public function getPeriode(): ?string { return $this->periode; }
    public function setPeriode(string $p): self { $this->periode = $p; return $this; }

    public function isTraite(): ?bool { return $this->traite; } // Getter booléen
    public function setTraite(bool $t): self { $this->traite = $t; return $this; } // Setter booléen

    public function getIdfile(): ?int { return $this->idfile; }
    public function setIdfile(int $id): self { $this->idfile = $id; return $this; }

    public function getSoldeEnLigne(): ?string { return $this->soldeEnLigne; } // Retourne string pour DECIMAL
    public function setSoldeEnLigne(?string $solde): self { $this->soldeEnLigne = $solde; return $this; } // Prend string, accepte null
    public function getSoldeEnLigneAsFloat(): ?float { return $this->soldeEnLigne === null ? null : (float) $this->soldeEnLigne; }

    public function isJournalier(): ?bool { return $this->journalier; } // Getter booléen
    public function setJournalier(bool $j): self { $this->journalier = $j; return $this; } // Setter booléen

    public function getOrdre(): ?int { return $this->ordre; }
    public function setOrdre(int $o): self { $this->ordre = $o; return $this; }

    public function getMttafbw(): ?string { return $this->mttafbw; }
    public function setMttafbw(?string $m): self { $this->mttafbw = $m; return $this; } // Accepte null

    public function getCodeBnq(): ?string { return $this->codeBnq; }
    public function setCodeBnq(?string $c): self { $this->codeBnq = $c; return $this; } // Accepte null

    public function getCodeGui(): ?string { return $this->codeGui; }
    public function setCodeGui(?string $c): self { $this->codeGui = $c; return $this; } // Accepte null

    public function getCodeDevise(): ?string { return $this->codeDevise; } // Nom différent de la relation
    public function setCodeDevise(?string $c): self { $this->codeDevise = $c; return $this; } // Accepte null

    public function getMotrej(): ?string { return $this->motrej; }
    public function setMotrej(?string $m): self { $this->motrej = $m; return $this; } // Accepte null

    public function getMonori(): ?string { return $this->monori; }
    public function setMonori(?string $m): self { $this->monori = $m; return $this; } // Accepte null

    public function getVirgul(): ?string { return $this->virgul; }
    public function setVirgul(?string $v): self { $this->virgul = $v; return $this; } // Accepte null

    public function getRes21(): ?string { return $this->res21; }
    public function setRes21(?string $r): self { $this->res21 = $r; return $this; } // Accepte null

    public function getExocom(): ?string { return $this->exocom; }
    public function setExocom(?string $e): self { $this->exocom = $e; return $this; } // Accepte null

    public function getInd(): ?string { return $this->ind; }
    public function setInd(?string $i): self { $this->ind = $i; return $this; } // Accepte null

    public function getRes22(): ?string { return $this->res22; }
    public function setRes22(?string $r): self { $this->res22 = $r; return $this; } // Accepte null

    public function getNoecri(): ?string { return $this->noecri; }
    public function setNoecri(?string $n): self { $this->noecri = $n; return $this; } // Accepte null

    public function getCdafb(): ?string { return $this->cdafb; }
    public function setCdafb(?string $c): self { $this->cdafb = $c; return $this; } // Accepte null

    public function getRes23(): ?string { return $this->res23; }
    public function setRes23(?string $r): self { $this->res23 = $r; return $this; } // Accepte null

    public function getRes13(): ?string { return $this->res13; }
    public function setRes13(?string $r): self { $this->res13 = $r; return $this; } // Accepte null

    public function getCdcoib(): ?string { return $this->cdcoib; }
    public function setCdcoib(?string $c): self { $this->cdcoib = $c; return $this; } // Accepte null

    public function getSign(): ?string { return $this->sign; }
    public function setSign(?string $s): self { $this->sign = $s; return $this; } // Accepte null

    public function getCdexo(): ?string { return $this->cdexo; }
    public function setCdexo(?string $c): self { $this->cdexo = $c; return $this; } // Accepte null

    // --- Relations Getters/Setters ---
    public function getCompte(): ?Compte { return $this->compte; }
    public function setCompte(Compte $c): self { $this->compte = $c; return $this; } // Non nullable

    public function getDevise(): ?Devise { return $this->devise; }
    public function setDevise(Devise $d): self { $this->devise = $d; return $this; } // Non nullable

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        $compteStr = $this->compte ? $this->compte->getNumeroCompte() : 'N/A';
        return 'Op CFONB: ' . $this->libOperation . ' (' . $compteStr . ')' ?? 'OperationCFONB #' . $this->id;
    }
}