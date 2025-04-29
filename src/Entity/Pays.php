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
class Pays {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idpays", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $libPays
     * #[ORM\Column(name="libpays",type="string",length=100)]
     * #[Assert\NotBlank(message="Le nom du pays ne peut être vide! ")]
     * @Assert\MinLength(2)
     */
    private $libPays;

    /**
     * @var ArrayCollection Internaute $Internautes
     * #[ORM\OneToMany(targetEntity: App\Entity\Internaute::class, mappedBy="pays" )]
     * 
     */
    private $internautes;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libPays
     *
     * @param string $libPays
     * @return Pays
     */
    public function setLibPays(string $libPays): self {
        $this->libPays = $libPays;

        return $this;
    }

    /**
     * Get libPays
     *
     * @return string 
     */
    public function getLibPays(): ?string {
        return $this->libPays;
    }

    /**
     * Add internautes
     *
     * @param \App\Entity\Internaute $internautes
     * @return Pays
     */
    public function addInternaute(\App\Entity\Internaute $internautes) {
        $this->internautes[] = $internautes;

        return $this;
    }

    /**
     * Remove internautes
     *
     * @param \App\Entity\Internaute $internautes
     */
    public function removeInternaute(\App\Entity\Internaute $internautes) {
        $this->internautes->removeElement($internautes);
    }

    /**
     * Get internautes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInternautes(): ?string {
        return $this->internautes;
    }

}
