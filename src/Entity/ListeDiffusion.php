<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="listediffusion")]
 * #[ORM\Entity](repositoryClass="App\Entity\ListeDiffusionRepository")
 *
 */
class ListeDiffusion {

    function __construct() {
        $this->typeListeDiffusion = 1;
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idliste", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $nomListeDiffusion
     * #[ORM\Column(name="nomlistediffusion",type="string",length=100)]
     * #[Assert\NotBlank(message="Le libellé de la liste de diffusion ne peut être vide")]
     * @Assert\MinLength(2)
     */
    private $nomListeDiffusion;

    /**
     * @var integer $actif
     * #[ORM\Column(name="actif",type="integer")]
     */
    private $actif;

    /**
     * @var text $lesMails
     * #[ORM\Column(name="lesMails",type="text")]
     */
    private $lesMails;

    /**
     * @var integer $typeListeDiffusion
     * #[ORM\Column(name="typeliste",type="integer")]
     */
    private $typeListeDiffusion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set nomListeDiffusion
     *
     * @param string $nomListeDiffusion
     * @return ListeDiffusion
     */
    public function setNomListeDiffusion(string $nomListeDiffusion): self {
        $this->nomListeDiffusion = $nomListeDiffusion;

        return $this;
    }

    /**
     * Get nomListeDiffusion
     *
     * @return string 
     */
    public function getNomListeDiffusion(): ?string {
        return $this->nomListeDiffusion;
    }

    /**
     * Set actif
     *
     * @param integer $actif
     * @return ListeDiffusion
     */
    public function setActif(string $actif): self {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return integer 
     */
    public function getActif(): ?string {
        return $this->actif;
    }

    /**
     * Set lesMails
     *
     * @param string $lesMails
     * @return ListeDiffusion
     */
    public function setLesMails(string $lesMails): self {
        $this->lesMails = $lesMails;

        return $this;
    }

    /**
     * Get lesMails
     *
     * @return string 
     */
    public function getLesMails(): ?string {
        return $this->lesMails;
    }

    /**
     * Set typeListeDiffusion
     *
     * @param integer $typeListeDiffusion
     * @return ListeDiffusion
     */
    public function setTypeListeDiffusion(string $typeListeDiffusion): self {
        $this->typeListeDiffusion = $typeListeDiffusion;

        return $this;
    }

    /**
     * Get typeListeDiffusion
     *
     * @return integer 
     */
    public function getTypeListeDiffusion(): ?string {
        return $this->typeListeDiffusion;
    }

}
