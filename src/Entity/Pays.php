<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="pays")]
 * #[ORM\Entity](repositoryClass="App\Entity\PaysRepository")
 *
 */
#[ORM\Entity(repositoryClass: App\Repository\PaysRepository::class)]
#[ORM\Table(name: 'pays')]
class Pays {

    public function __construct() {
        $this->internautes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idpays", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idpays', type: 'integer')]
    private ?int $idPays = null;

    /**
     * @var string $libPays
     * #[ORM\Column(name="libpays",type="string",length=100)]
     * #[Assert\NotBlank(message="Le nom du pays ne peut être vide! ")]
     * @Assert\MinLength(2)
     */
    #[ORM\Column(name: 'libellepays', type: 'string', length: 50)]
    #[Assert\NotBlank]
    private ?string $libellePays = null;

    #[ORM\Column(name: 'code', type: 'string', length: 3)]
    #[Assert\NotBlank]
    private ?string $code = null;

    /**
     * @var ArrayCollection Internaute $Internautes
     * #[ORM\OneToMany(targetEntity: App\Entity\Internaute::class, mappedBy="pays" )]
     * 
     */
    #[ORM\OneToMany(targetEntity: Internaute::class, mappedBy: 'pays')]
    private \Doctrine\Common\Collections\Collection $internautes;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getIdPays(): ?int {
        return $this->idPays;
    }

    /**
     * Set libPays
     *
     * @param string $libPays
     * @return Pays
     */
    public function setLibellePays(string $libellePays): self {
        $this->libellePays = $libellePays;

        return $this;
    }

    /**
     * Get libPays
     *
     * @return string 
     */
    public function getLibellePays(): ?string {
        return $this->libellePays;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return Collection<int, Internaute>
     */
    public function getInternautes(): \Doctrine\Common\Collections\Collection
    {
        return $this->internautes;
    }

    public function addInternaute(Internaute $internaute): self
    {
        if (!$this->internautes->contains($internaute)) {
            $this->internautes[] = $internaute;
            $internaute->setPays($this);
        }
        return $this;
    }

    public function removeInternaute(Internaute $internaute): self
    {
        if ($this->internautes->removeElement($internaute)) {
            if ($internaute->getPays() === $this) {
                $internaute->setPays(null);
            }
        }
        return $this;
    }

}
