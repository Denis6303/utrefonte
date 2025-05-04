<?php

namespace App\Entity;

use App\Repository\MessageRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;           // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer les entités liées si nécessaire
// use App\Entity\Internaute;
// use App\Entity\Service;
// use App\Entity\MessageReponse;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idmessage', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    #[ORM\Column(name: 'titremessage', type: Types::STRING, length: 150)] // Longueur augmentée si besoin
    #[Assert\NotBlank(message: "Le titre du message est obligatoire.")]
    #[Assert\Length(max: 150, maxMessage: "Le titre ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $titreMessage = null;

    #[ORM\Column(name: 'contenu', type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu du message est obligatoire.")]
    private ?string $contenu = null;

    #[ORM\Column(name: 'dateenvoi', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull] // Initialisé dans constructeur
    private ?DateTimeImmutable $dateEnvoi = null; // Changé en DateTimeImmutable

    #[ORM\Column(name: 'corbeillemessage', type: Types::BOOLEAN)]
    #[Assert\NotNull]
    private ?bool $corbeilleMessage = false; // Initialisé dans constructeur

    #[ORM\Column(name: 'messagelu', type: Types::BOOLEAN)]
    #[Assert\NotNull]
    private ?bool $messageLu = false; // Initialisé dans constructeur

    // --- RELATIONS ---

    /**
     * L'Internaute expéditeur.
     */
    #[ORM\ManyToOne(targetEntity: Internaute::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'mailinternaute', referencedColumnName: 'mailinternaute', nullable: true)] // Gardé nullable si un message peut être système
    private ?Internaute $internaute = null;

    /**
     * Le Service destinataire (ou lié).
     */
    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'idservice', referencedColumnName: 'idservice', nullable: true)] // Gardé nullable si non obligatoire
    private ?Service $service = null;

    /**
     * Les réponses associées à ce message.
     * 'message' est la propriété dans MessageReponse qui référence cette entité (mappedBy).
     * @var Collection<int, MessageReponse>
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageReponse::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // cascade/orphanRemoval ajoutés
    private Collection $messageReponses;


    public function __construct()
    {
        $this->dateEnvoi = new DateTimeImmutable();
        $this->messageReponses = new ArrayCollection();
        $this->corbeilleMessage = false;
        $this->messageLu = false;
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int // Nom et type retour standardisés
    {
        return $this->id;
    }

    public function getTitreMessage(): ?string
    {
        return $this->titreMessage;
    }

    public function setTitreMessage(string $titreMessage): self
    {
        $this->titreMessage = $titreMessage;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateEnvoi(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateEnvoi;
    }

    // Setter retiré car défini à la construction
    // public function setDateEnvoi(DateTimeImmutable $dateEnvoi): self
    // {
    //     $this->dateEnvoi = $dateEnvoi;
    //     return $this;
    // }

    public function isCorbeilleMessage(): ?bool
    {
        return $this->corbeilleMessage;
    }

    public function setCorbeilleMessage(bool $corbeilleMessage): self
    {
        $this->corbeilleMessage = $corbeilleMessage;
        return $this;
    }

    public function isMessageLu(): ?bool
    {
        return $this->messageLu;
    }

    public function setMessageLu(bool $messageLu): self
    {
        $this->messageLu = $messageLu;
        return $this;
    }

    public function getInternaute(): ?Internaute
    {
        return $this->internaute;
    }

    public function setInternaute(?Internaute $internaute): self
    {
        $this->internaute = $internaute;
        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return Collection<int, MessageReponse>
     */
    public function getMessageReponses(): Collection
    {
        return $this->messageReponses;
    }

    public function addMessageReponse(MessageReponse $messageReponse): self
    {
        if (!$this->messageReponses->contains($messageReponse)) {
            $this->messageReponses->add($messageReponse);
            $messageReponse->setMessage($this); // Mise à jour du côté propriétaire
        }
        return $this;
    }

    public function removeMessageReponse(MessageReponse $messageReponse): self
    {
        if ($this->messageReponses->removeElement($messageReponse)) {
            // Mettre le côté propriétaire à null si nécessaire (géré par orphanRemoval si non null)
            if ($messageReponse->getMessage() === $this) {
                $messageReponse->setMessage(null);
            }
        }
        return $this;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->titreMessage ?? 'Message #' . $this->id;
    }
}