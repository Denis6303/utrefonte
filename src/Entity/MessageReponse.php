<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: App\Repository\MessageReponseRepository::class)]
#[ORM\Table(name: 'messagereponse')]
class MessageReponse
{
    public function __construct()
    {
        $this->dateReponse = new \DateTime();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idmessagereponse', type: 'integer')]
    private ?int $idMessageReponse = null;

    #[ORM\Column(name: 'datereponse', type: 'datetime')]
    private ?\DateTimeInterface $dateReponse = null;

    #[ORM\Column(name: 'contenu', type: 'text')]
    #[Assert\NotBlank]
    private ?string $contenu = null;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'messageReponses')]
    #[ORM\JoinColumn(name: 'idmessage', referencedColumnName: 'idmessage')]
    private ?Message $message = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messagereponses')]
    #[ORM\JoinColumn(name: 'iduser', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(name: 'titreMessage', type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le titre du message ne peut être vide!')]
    #[Assert\Length(min: 2)]
    private ?string $titreMessage = null;

    #[ORM\Column(name: 'contenuMessage', type: 'text')]
    #[Assert\NotBlank(message: 'Le contenu du message ne peut être vide!')]
    #[Assert\Length(min: 2)]
    private ?string $contenuMessage = null;

    #[ORM\Column(name: 'dateEnvoi', type: 'datetime')]
    private ?\DateTimeInterface $dateEnvoi = null;

    #[ORM\Column(name: 'messageLu', type: 'integer')]
    private ?int $messageLu = null;

    #[ORM\Column(name: 'destinataireMsg', type: 'text')]
    private ?string $destinatairesMsg = null;

    #[ORM\PrePersist]
    public function preAjout()
    {
        $this->dateEnvoi = new \DateTime();
    }

    public function getIdMessageReponse(): ?int
    {
        return $this->idMessageReponse;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->dateReponse;
    }

    public function setDateReponse(\DateTimeInterface $dateReponse): self
    {
        $this->dateReponse = $dateReponse;
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

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
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

    public function getContenuMessage(): ?string
    {
        return $this->contenuMessage;
    }

    public function setContenuMessage(string $contenuMessage): self
    {
        $this->contenuMessage = $contenuMessage;
        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;
        return $this;
    }

    public function getMessageLu(): ?int
    {
        return $this->messageLu;
    }

    public function setMessageLu(int $messageLu): self
    {
        $this->messageLu = $messageLu;
        return $this;
    }

    public function getDestinatairesMsg(): ?string
    {
        return $this->destinatairesMsg;
    }

    public function setDestinatairesMsg(string $destinatairesMsg): self
    {
        $this->destinatairesMsg = $destinatairesMsg;
        return $this;
    }
}
