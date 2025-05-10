<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: App\Repository\TypeCompteRepository::class)]
#[ORM\Table(name: 'typecompte')]
class TypeCompte
{
    public function __construct()
    {
        $this->comptes = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idtypecompte', type: 'integer')]
    private ?int $idTypeCompte = null;

    #[ORM\Column(name: 'libelletypecompte', type: 'string', length: 50)]
    #[Assert\NotBlank]
    private ?string $libelleTypeCompte = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Compte::class, mappedBy: 'typeCompte')]
    private Collection $comptes;

    public function getIdTypeCompte(): ?int
    {
        return $this->idTypeCompte;
    }

    public function getLibelleTypeCompte(): ?string
    {
        return $this->libelleTypeCompte;
    }

    public function setLibelleTypeCompte(string $libelleTypeCompte): self
    {
        $this->libelleTypeCompte = $libelleTypeCompte;
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

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes->add($compte);
            $compte->setTypeCompte($this);
        }
        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            if ($compte->getTypeCompte() === $this) {
                $compte->setTypeCompte(null);
            }
        }
        return $this;
    }
}
