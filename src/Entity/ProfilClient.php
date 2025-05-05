<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: App\Repository\ProfilClientRepository::class)]
#[ORM\Table(name: 'profilclient')]
class ProfilClient
{
    public function __construct()
    {
        $this->abonnes = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idprofilclient', type: 'integer')]
    private ?int $idProfilClient = null;

    #[ORM\Column(name: 'libelleprofil', type: 'string', length: 100)]
    #[Assert\NotBlank]
    private ?string $libelleProfil = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'etat', type: 'integer')]
    private ?int $etat = null;

    #[ORM\OneToMany(targetEntity: Abonne::class, mappedBy: 'profilClient')]
    private Collection $abonnes;

    public function getIdProfilClient(): ?int
    {
        return $this->idProfilClient;
    }

    public function getLibelleProfil(): ?string
    {
        return $this->libelleProfil;
    }

    public function setLibelleProfil(string $libelleProfil): self
    {
        $this->libelleProfil = $libelleProfil;
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
     * @return Collection<int, Abonne>
     */
    public function getAbonnes(): Collection
    {
        return $this->abonnes;
    }

    public function addAbonne(Abonne $abonne): self
    {
        if (!$this->abonnes->contains($abonne)) {
            $this->abonnes->add($abonne);
            $abonne->setProfilClient($this);
        }
        return $this;
    }

    public function removeAbonne(Abonne $abonne): self
    {
        if ($this->abonnes->removeElement($abonne)) {
            if ($abonne->getProfilClient() === $this) {
                $abonne->setProfilClient(null);
            }
        }
        return $this;
    }
}
