<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="facturation")]
 * #[ORM\Entity](repositoryClass="App\Entity\FacturationRepository")
 *
 */
class Facturation {

    function __construct() {
        //$this->etat = 0;
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idfacturation", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $montantuweb
     * #[ORM\Column(name="montantuweb",type="integer")]
     * #[Assert\NotBlank(message=" Le montant uweb ne peut être vide ")]
     * @Assert\MinLength(2)
     */
    private $montantuweb;

    /**
     * @var integer $montantafbw
     * #[ORM\Column(name="montantafbw",type="integer")]
     * #[Assert\NotBlank(message=" Le montant afbw|afbw2 ne peut être vide ")]
     * @Assert\MinLength(2)
     */
    private $montantafbw;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set montantuweb
     *
     * @param integer $montantuweb
     * @return Facturation
     */
    public function setMontantuweb(string $montantuweb): self {
        $this->montantuweb = $montantuweb;

        return $this;
    }

    /**
     * Get montantuweb
     *
     * @return integer 
     */
    public function getMontantuweb(): ?string {
        return $this->montantuweb;
    }

    /**
     * Set montantafbw
     *
     * @param integer $montantafbw
     * @return Facturation
     */
    public function setMontantafbw(string $montantafbw): self {
        $this->montantafbw = $montantafbw;

        return $this;
    }

    /**
     * Get montantafbw
     *
     * @return integer 
     */
    public function getMontantafbw(): ?string {
        return $this->montantafbw;
    }

}
