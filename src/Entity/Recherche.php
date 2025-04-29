<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\RechercheRepository")
 * #[ORM\Table(name="recherche")]
 */
class Recherche {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idrecherche", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * 
     * @var string $motcle
     * #[ORM\Column(name="motcle",type="string",length=70)]
     * @Assert\MinLength(3)
     */
    public $motcle;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set motcle
     *
     * @param string $motcle
     * @return Recherche
     */
    public function setMotcle(string $motcle): self {
        $this->motcle = $motcle;

        return $this;
    }

    /**
     * Get motcle
     *
     * @return string 
     */
    public function getMotcle(): ?string {
        return $this->motcle;
    }

}
