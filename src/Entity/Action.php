<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: App\Repository\ActionRepository::class)]
#[ORM\Table(name: 'action')]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idaction', type: 'integer')]
    private ?int $idAction = null;

    #[ORM\Column(name: 'libelleaction', type: 'string', length: 100)]
    #[Assert\NotBlank]
    private ?string $libelleAction = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'datecreation', type: 'datetime')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'dateexpiration', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column(name: 'etat', type: 'boolean')]
    private ?bool $etat = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->etat = true;
    }

    public function getIdAction(): ?int
    {
        return $this->idAction;
    }

    public function getLibelleAction(): ?string
    {
        return $this->libelleAction;
    }

    public function setLibelleAction(string $libelleAction): self
    {
        $this->libelleAction = $libelleAction;
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(?\DateTimeInterface $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;
        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;
        return $this;
    }
}
