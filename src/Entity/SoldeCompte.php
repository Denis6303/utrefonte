<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: App\Repository\SoldeCompteRepository::class)]
#[ORM\Table(name: 'soldecompte')]
class SoldeCompte
{
    public function __construct()
    {
        $this->dateSolde = new \DateTime();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idsoldecompte', type: 'integer')]
    private ?int $idSoldeCompte = null;

    #[ORM\Column(name: 'montant', type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    private ?string $montant = null;

    #[ORM\Column(name: 'datesolde', type: 'datetime')]
    private ?\DateTimeInterface $dateSolde = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Compte::class)]
    #[ORM\JoinColumn(name: 'idcompte', referencedColumnName: 'idcompte')]
    private ?Compte $compte = null;

    public function getIdSoldeCompte(): ?int
    {
        return $this->idSoldeCompte;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getDateSolde(): ?\DateTimeInterface
    {
        return $this->dateSolde;
    }

    public function setDateSolde(\DateTimeInterface $dateSolde): self
    {
        $this->dateSolde = $dateSolde;
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

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;
        return $this;
    }
}
