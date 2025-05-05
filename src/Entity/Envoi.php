<?php

namespace App\Entity;

use App\Repository\EnvoiRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer les entités liées si nécessaire
// use App\Entity\Utilisateur;
// use App\Entity\MessageClient;
// use App\Entity\Abonne;

/**
 * Entité représentant un enregistrement d'envoi de message.
 */
#[ORM\Entity(repositoryClass: EnvoiRepository::class)]
#[ORM\Table(name: 'envoi')]
#[ORM\HasLifecycleCallbacks] // Conserver pour le PrePersist
class Envoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idenvoi', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * ID du destinataire Utilisateur (si applicable).
     * Relation ManyToOne serait mieux si un Envoi a UN destinataire spécifique.
     * Sinon, garder comme ID.
     */
    #[ORM\Column(name: 'destutil', type: Types::INTEGER, nullable: true)] // Rendu nullable car destAb existe aussi
    // NotBlank supprimé car potentiellement null si destAb est défini
    private ?int $destUtil = null;

    /**
     * ID du destinataire Abonne (si applicable).
     */
    #[ORM\Column(name: 'destab', type: Types::INTEGER, nullable: true)] // Rendu nullable
    private ?int $destAb = null;

    /**
     * Statut du message (côté réception/traitement?).
     */
    #[ORM\Column(name: 'statutmsg', type: Types::INTEGER, nullable: true)]
    private ?int $statutMsg = null;

    /**
     * Statut de l'envoi effectif du message.
     */
    #[ORM\Column(name: 'statutmsgenvoye', type: Types::INTEGER, nullable: true)]
    private ?int $statutMsgEnvoye = null;

    /**
     * ID du message parent (pour les conversations/réponses).
     * Envisager une relation ManyToOne self-référencée si c'est un lien direct.
     */
    #[ORM\Column(name: 'msgparent', type: Types::INTEGER, nullable: true)]
    private ?int $msgParent = null;

    /**
     * Indicateur de lecture (0=non lu, 1=lu).
     */
    #[ORM\Column(name: 'msglu', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $msgLu = false; // Initialisé à non lu

    /**
     * Type d'envoi (ex: email, notification, etc.).
     */
    #[ORM\Column(name: 'typeenvoi', type: Types::INTEGER, nullable: true)]
    private ?int $typeEnvoi = null;

    #[ORM\Column(name: 'dateenvoimsg', type: Types::DATETIME_IMMUTABLE)] // Non nullable, géré par PrePersist
    #[Assert\NotNull]
    private ?DateTimeImmutable $dateEnvoiMsg = null; // Changé en DateTimeImmutable

    /**
     * Type de message (catégorisation).
     */
    #[ORM\Column(name: 'typemessage', type: Types::INTEGER)]
    #[Assert\NotNull]
    private ?int $typeMessage = 0; // Initialisé dans le constructeur

    // --- RELATIONS ---

    /**
     * L'utilisateur qui a initié l'envoi (peut être null si initié par un abonné?).
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'envois', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idutilisateur', referencedColumnName: 'id', nullable: true)]
    private ?User $utilisateur = null;

    /**
     * Le message client concerné par cet envoi.
     */
    #[ORM\ManyToOne(targetEntity: MessageClient::class, inversedBy: 'envois', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idmessageclient', referencedColumnName: 'idmessageclient', nullable: true)] // Gardé nullable
    private ?MessageClient $messageclient = null;

    /**
     * L'abonné qui a initié l'envoi (peut être null si initié par un utilisateur?).
     */
    #[ORM\ManyToOne(targetEntity: Abonne::class, inversedBy: 'envois', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idabonne', referencedColumnName: 'idabonne', nullable: true)] // Gardé nullable
    private ?Abonne $abonne = null;


    public function __construct()
    {
        $this->typeMessage = 0; // Valeur par défaut
        $this->msgLu = false;  // Non lu par défaut
        // dateEnvoiMsg sera défini dans PrePersist
    }

    #[ORM\PrePersist]
    public function setDateOnPrePersist(): void
    {
        if ($this->dateEnvoiMsg === null) { // Défini seulement s'il n'existe pas déjà
             $this->dateEnvoiMsg = new DateTimeImmutable();
        }
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Nom et type retour standardisés
    {
        return $this->id;
    }

    public function getDestUtil(): ?int // Type retour corrigé
    {
        return $this->destUtil;
    }

    public function setDestUtil(?int $destUtil): self // Type param corrigé, accepte null
    {
        $this->destUtil = $destUtil;
        return $this;
    }

    public function getDestAb(): ?int // Type retour corrigé
    {
        return $this->destAb;
    }

    public function setDestAb(?int $destAb): self // Type param corrigé, accepte null
    {
        $this->destAb = $destAb;
        return $this;
    }

    public function getStatutMsg(): ?int // Type retour corrigé
    {
        return $this->statutMsg;
    }

    public function setStatutMsg(?int $statutMsg): self // Type param corrigé, accepte null
    {
        $this->statutMsg = $statutMsg;
        return $this;
    }

    public function getStatutMsgEnvoye(): ?int // Type retour corrigé
    {
        return $this->statutMsgEnvoye;
    }

    public function setStatutMsgEnvoye(?int $statutMsgEnvoye): self // Type param corrigé, accepte null
    {
        $this->statutMsgEnvoye = $statutMsgEnvoye;
        return $this;
    }

    public function getMsgParent(): ?int // Type retour corrigé
    {
        return $this->msgParent;
    }

    public function setMsgParent(?int $msgParent): self // Type param corrigé, accepte null
    {
        $this->msgParent = $msgParent;
        return $this;
    }

    public function isMsgLu(): ?bool // Getter standard pour booléen
    {
        return $this->msgLu;
    }

    public function setMsgLu(bool $msgLu): self // Type param corrigé en bool
    {
        $this->msgLu = $msgLu;
        return $this;
    }

    public function getTypeEnvoi(): ?int // Type retour corrigé
    {
        return $this->typeEnvoi;
    }

    public function setTypeEnvoi(?int $typeEnvoi): self // Type param corrigé, accepte null
    {
        $this->typeEnvoi = $typeEnvoi;
        return $this;
    }

    public function getDateEnvoiMsg(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateEnvoiMsg;
    }

    // Setter retiré car géré par PrePersist
    // public function setDateEnvoiMsg(DateTimeImmutable $dateEnvoiMsg): self
    // {
    //     $this->dateEnvoiMsg = $dateEnvoiMsg;
    //     return $this;
    // }

    public function getTypeMessage(): ?int // Type retour corrigé
    {
        return $this->typeMessage;
    }

    public function setTypeMessage(int $typeMessage): self // Type param corrigé en int
    {
        $this->typeMessage = $typeMessage;
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

    public function getMessageclient(): ?MessageClient // Type retour corrigé
    {
        return $this->messageclient;
    }

    public function setMessageclient(?MessageClient $messageclient): self // Type param corrigé
    {
        $this->messageclient = $messageclient;
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
        $dateStr = $this->dateEnvoiMsg ? $this->dateEnvoiMsg->format('Y-m-d H:i') : 'N/A';
        return 'Envoi #' . $this->id . ' (' . $dateStr . ')';
    }
}