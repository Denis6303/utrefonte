<?php

namespace App\Entity;

use App\Repository\HistoriqueConnexionRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;                   // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer Utilisateur et Abonne si nécessaire
// use App\Entity\Utilisateur;
// use App\Entity\Abonne;

/**
 * Historique des connexions des utilisateurs et/ou abonnés.
 */
#[ORM\Entity(repositoryClass: HistoriqueConnexionRepository::class)]
#[ORM\Table(name: 'historique')] // Nom de table explicite
class HistoriqueConnexion
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idconnexion', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    #[ORM\Column(name: 'datedeb', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull] // Assurer que la date est définie (via constructeur)
    private ?DateTimeImmutable $dateDeb = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'datefin', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Changé en DATETIME_IMMUTABLE
    private ?DateTimeImmutable $dateFin = null; // Type hint DateTimeImmutable

    /**
     * Adresse IP de connexion.
     */
    #[ORM\Column(name: 'adresseip', type: Types::STRING, length: 45, nullable: true)] // Longueur suffisante pour IPv6
    #[Assert\Ip(message: "L'adresse IP '{{ value }}' n'est pas valide.")] // Valider le format IP
    #[Assert\Length(max: 45)] // Ajouter contrainte de longueur
    private ?string $adresseIp = null;

    /**
     * Lieu estimé de la connexion (peut venir de GeoIP).
     */
    #[ORM\Column(name: 'lieu', type: Types::STRING, length: 100, nullable: true)] // Longueur augmentée
    #[Assert\Length(max: 100)]
    private ?string $lieu = null;

    /**
     * Durée de la session (stockée comme string? Envisager integer pour secondes).
     * Type 'private' dans le docblock original était incorrect.
     */
    #[ORM\Column(name: 'duree', type: Types::STRING, length: 100, nullable: true)] // Gardé STRING, mais INTEGER (secondes) ou JSON seraient mieux
    private ?string $duree = null;

    // --- RELATIONS ---

    /**
     * L'Utilisateur qui s'est connecté (peut être null si c'est un Abonne?).
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'historiques', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'iduser', referencedColumnName: 'id', nullable: true)]
    private ?User $utilisateur = null;

    /**
     * L'Abonne qui s'est connecté (peut être null si c'est un Utilisateur?).
     */
    #[ORM\ManyToOne(targetEntity: Abonne::class, inversedBy: 'historiques', cascade: ['persist'])] // Merge est moins courant ici
    #[ORM\JoinColumn(name: 'idabonne', referencedColumnName: 'idabonne', nullable: true)] // Gardé nullable
    private ?Abonne $abonne = null;


    public function __construct()
    {
        // Initialiser avec DateTimeImmutable
        $this->dateDeb = new DateTimeImmutable();
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    public function getDateDeb(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateDeb;
    }

    /**
     * La date de début est définie à la construction, ce setter n'est peut-être pas nécessaire.
     */
    public function setDateDeb(DateTimeImmutable $dateDeb): self // Type paramètre corrigé
    {
        $this->dateDeb = $dateDeb;
        return $this;
    }

    public function getDateFin(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateFin;
    }

    public function setDateFin(?DateTimeImmutable $dateFin): self // Type paramètre corrigé, accepte null
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getAdresseIp(): ?string
    {
        return $this->adresseIp;
    }

    public function setAdresseIp(?string $adresseIp): self // Accepte null
    {
        $this->adresseIp = $adresseIp;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self // Accepte null
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): self // Accepte null
    {
        $this->duree = $duree;
        return $this;
    }

    // --- Getters/Setters pour les relations ---

    public function getUtilisateur(): ?User // Type retour corrigé
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): self // Type param corrigé
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getAbonne(): ?Abonne // Type retour corrigé
    {
        return $this->abonne;
    }

    public function setAbonne(?Abonne $abonne): self // Type param corrigé
    {
        $this->abonne = $abonne;
        return $this;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        $userIdentifier = $this->utilisateur ? 'User:' . $this->utilisateur->getId() : ($this->abonne ? 'Abonne:' . $this->abonne->getId() : 'Inconnu');
        $dateStr = $this->dateDeb ? $this->dateDeb->format('Y-m-d H:i') : 'N/A';
        return 'Connexion ' . $userIdentifier . ' le ' . $dateStr;
    }
}
