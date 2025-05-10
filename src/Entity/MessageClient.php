<?php

namespace App\Entity;

use App\Repository\MessageClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant un message client (peut-être un modèle ou un message spécifique).
 */
#[ORM\Entity(repositoryClass: MessageClientRepository::class)]
#[ORM\Table(name: 'messageclient')]
// @ORM\HasLifecycleCallbacks supprimé car pas de méthodes de callback définies
class MessageClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idmessageclient')]
    private ?int $id = null;

    #[ORM\Column(name: 'objetmessageclient', type: Types::STRING, length: 150)]
    #[Assert\NotBlank(message: "L'objet du message ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 150, // Correspond à la longueur ORM (ou ajustée)
        minMessage: "L'objet doit contenir au moins {{ limit }} caractères.",
        maxMessage: "L'objet ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $objetMessageClient = null;

    #[ORM\Column(name: 'contenumessageclient', type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        min: 2,
        minMessage: "Le contenu doit contenir au moins {{ limit }} caractères."
        // Pas de NotBlank car nullable=true
    )]
    private ?string $contenuMessageClient = null;

    /**
     * Indique si c'est un message système (automatique).
     */
    #[ORM\Column(name: 'messagesysteme', type: Types::BOOLEAN)]
    #[Assert\NotNull]
    private ?bool $messageSysteme = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMessage = null;

    /**
     * Relation vers les envois utilisant ce message.
     * 'messageclient' est la propriété dans Envoi qui référence cette entité (mappedBy).
     * @var Collection<int, Envoi>
     */
    #[ORM\OneToMany(mappedBy: 'messageclient', targetEntity: Envoi::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $envois;

    public function __construct()
    {
        $this->messageSysteme = false;
        $this->envois = new ArrayCollection();
        $this->dateMessage = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenuMessageClient;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenuMessageClient = $contenu;

        return $this;
    }

    public function getDateMessage(): ?\DateTimeInterface
    {
        return $this->dateMessage;
    }

    public function setDateMessage(\DateTimeInterface $dateMessage): static
    {
        $this->dateMessage = $dateMessage;

        return $this;
    }

    /**
     * @return Collection<int, Envoi>
     */
    public function getEnvois(): Collection
    {
        return $this->envois;
    }

    public function addEnvoi(Envoi $envoi): static
    {
        if (!$this->envois->contains($envoi)) {
            $this->envois->add($envoi);
            $envoi->setMessageclient($this);
        }

        return $this;
    }

    public function removeEnvoi(Envoi $envoi): static
    {
        if ($this->envois->removeElement($envoi)) {
            // set the owning side to null (unless already changed)
            if ($envoi->getMessageclient() === $this) {
                $envoi->setMessageclient(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->objetMessageClient ?? 'MessageClient #' . $this->id;
    }
}