<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: App\Repository\ServiceRepository::class)]
#[ORM\Table(name: 'service')]
class Service
{
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idservice', type: 'integer')]
    private ?int $idService = null;

    #[ORM\Column(name: 'libelleservice', type: 'string', length: 100)]
    #[Assert\NotBlank]
    private ?string $libelleService = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'prix', type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $prix = null;

    #[ORM\Column(name: 'etat', type: 'integer')]
    private ?int $etat = null;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'service')]
    private Collection $messages;

    public function getIdService(): ?int
    {
        return $this->idService;
    }

    public function getLibelleService(): ?string
    {
        return $this->libelleService;
    }

    public function setLibelleService(string $libelleService): self
    {
        $this->libelleService = $libelleService;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setService($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getService() === $this) {
                $message->setService(null);
            }
        }
        return $this;
    }
}
