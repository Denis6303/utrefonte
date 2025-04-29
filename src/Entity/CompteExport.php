<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompteExport
 *
 * #[ORM\Table()]
 * #[ORM\Entity]
 */
class CompteExport
{
    /**
     * @var integer
     *
     * #[ORM\Column(name="id", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * #[ORM\Column(name="compte", type="string", length=32)]
     */
    private $compte;

    /**
     * @var string
     *
     * #[ORM\Column(name="lib", type="string", length=255)]
     */
    private $lib;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set compte
     *
     * @param string $compte
     * @return CompteExport
     */
    public function setCompte(string $compte): self
    {
        $this->compte = $compte;
    
        return $this;
    }

    /**
     * Get compte
     *
     * @return string 
     */
    public function getCompte(): ?string
    {
        return $this->compte;
    }

    /**
     * Set lib
     *
     * @param string $lib
     * @return CompteExport
     */
    public function setLib(string $lib): self
    {
        $this->lib = $lib;
    
        return $this;
    }

    /**
     * Get lib
     *
     * @return string 
     */
    public function getLib(): ?string
    {
        return $this->lib;
    }
}
