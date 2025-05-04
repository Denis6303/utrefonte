<?php

namespace App\Entity;

use App\Repository\MessageReponseRepository; // Importer le Repository (nom corrigé)
use Doctrine\DBAL\Types\Types;              // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer les entités liées si nécessaire
// use App\Entity\User; // Supposant que vous avez une entité User
// use App\Entity\Message;

/**
 * Entité représentant une réponse à un Message.
 */
#[ORM\Entity(repositoryClass: MessageReponseRepository::class)] // Nom Repository corrigé
#[ORM\Table(name: 'messagereponse')] // Nom de table suggéré (ou adaptez)
#[ORM\HasLifecycleCallbacks] // Garder si PrePersist est utilisé
class MessageReponse // Nom de classe corrigé (Reponse au lieu de Reponce)
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(type: Types::INTEGER)] // name="id" est la valeur par défaut
    private ?int $id = null;

    #[ORM\Column(name: 'titremessage', type: Types::STRING, length: 150, nullable: true)] // Rendu nullable, longueur augmentée
    #[Assert\Length(max: 150, maxMessage: "Le titre ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $titreMessage = null; // Type hint ?string

    #[ORM\Column(name: 'contenumessage', type: Types::TEXT)] // Nom de colonne supposé
    #[Assert\NotBlank(message: "Le contenu de la réponse est obligatoire.")]
    private ?string $contenuMessage = null; // Type hint ?string

    #[ORM\Column(name: 'dateenvoi', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull] // Initialisé dans PrePersist
    private ?DateTimeImmutable $dateEnvoi = null; // Type hint ?DateTimeImmutable

    // --- RELATIONS ---

    /**
     * L'utilisateur (admin?) qui a envoyé la réponse.
     * Supposons qu'une réponse DOIT avoir un auteur.
     * Remplacer 'User' par 'Utilisateur' si c'est le nom de votre entité.
     */
    #[ORM\ManyToOne(targetEntity: User::class)] // inversedBy non nécessaire si User n'a pas besoin de lister les réponses
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)] // Nom de colonne et référence standardisés
    #[Assert\NotNull(message: "L'utilisateur auteur est obligatoire.")]
    private ?User $user = null; // Type hint ?User

    /**
     * Le message original auquel cette réponse est liée.
     * Supposons qu'une réponse DOIT être liée à un message.
     */
    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'messageReponses')] // inversedBy correspond à la collection dans Message
    #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'idmessage', nullable: false)] // Nom de colonne et référence standardisés
    #[Assert\NotNull(message: "Le message original est obligatoire.")]
    private ?Message $message = null; // Type hint ?Message


    // Le constructeur n'est pas forcément utile si dateEnvoi est géré par PrePersist
    // public function __construct() { }

    #[ORM\PrePersist]
    public function setDateOnPersist(): void // Nom méthode corrigé, ajout type void
    {
        if ($this->dateEnvoi === null) {
             $this->dateEnvoi = new DateTimeImmutable();
        }
    }

    // --- GETTERS & SETTERS ---

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
     * Set titreMessage
     *
     * @param string|null $titreMessage
     * @return MessageReponse
     */
    public function setTitreMessage(?string $titreMessage): self // Accepte null
    {
        $this->titreMessage = $titreMessage;
        return $this;
    }

    /**
     * Get titreMessage
     *
     * @return string|null
     */
    public function getTitreMessage(): ?string
    {
        return $this->titreMessage;
    }

    /**
     * Set contenuMessage
     *
     * @param string $contenuMessage
     * @return MessageReponse
     */
    public function setContenuMessage(string $contenuMessage): self
    {
        $this->contenuMessage = $contenuMessage;
        return $this;
    }

    /**
     * Get contenuMessage
     *
     * @return string|null
     */
    public function getContenuMessage(): ?string
    {
        return $this->contenuMessage;
    }

    /**
     * Get dateEnvoi
     *
     * @return DateTimeImmutable|null
     */
    public function getDateEnvoi(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateEnvoi;
    }

    // Setter retiré car géré par PrePersist
    // public function setDateEnvoi(DateTimeImmutable $dateEnvoi): self
    // {
    //     $this->dateEnvoi = $dateEnvoi;
    //     return $this;
    // }

    /**
     * Set user
     *
     * @param User $user L'utilisateur auteur (non nullable)
     * @return MessageReponse
     */
    public function setUser(User $user): self // Param non nullable, type corrigé
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return User|null
     */
    public function getUser(): ?User // Type retour corrigé
    {
        return $this->user;
    }

    /**
     * Set message
     *
     * @param Message $message Le message original (non nullable)
     * @return MessageReponse
     */
    public function setMessage(Message $message): self // Param non nullable, type corrigé
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return Message|null
     */
    public function getMessage(): ?Message // Type retour corrigé
    {
        return $this->message;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        $dateStr = $this->dateEnvoi ? $this->dateEnvoi->format('Y-m-d H:i') : 'N/A';
        return 'Réponse #' . $this->id . ' (' . $dateStr . ')';
    }
}