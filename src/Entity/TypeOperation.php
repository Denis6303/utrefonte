<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: App\Repository\TypeOperationRepository::class)]
#[ORM\Table(name: 'typeoperation')]
class TypeOperation
{
    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idtypeoperation', type: 'integer')]
    private ?int $idTypeOperation = null;

    #[ORM\Column(name: 'libelletypeoperation', type: 'string', length: 50)]
    #[Assert\NotBlank]
    private ?string $libelleTypeOperation = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Operation::class, mappedBy: 'typeOperation')]
    private Collection $operations;

    public function getIdTypeOperation(): ?int
    {
        return $this->idTypeOperation;
    }

    public function getLibelleTypeOperation(): ?string
    {
        return $this->libelleTypeOperation;
    }

    public function setLibelleTypeOperation(string $libelleTypeOperation): self
    {
        $this->libelleTypeOperation = $libelleTypeOperation;
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
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
            $operation->setTypeOperation($this);
        }
        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            if ($operation->getTypeOperation() === $this) {
                $operation->setTypeOperation(null);
            }
        }
        return $this;
    }
} 