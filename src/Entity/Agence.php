<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\AgenceRepository")
 * #[ORM\Table(name="agence")]
 */
class Agence{

    //constructeur    
    public function __construct() {
        $this->etatAgence = 1;
        $this->suppr = 0;
    }

    /**
     * @var string $codeAgence
     * #[ORM\Id]
     * #[ORM\Column(name="codeagence", type="string",length=04)]
     */
    private $codeAgence;

    /**
     * @var string $libAgence
     * #[ORM\Column(name="libagence", type="string",length=100)]
     */
    private $libAgence;

    /**
     * @var string $telAgence
     * #[ORM\Column(name="telagence", type="string",length=25)] 
     */
    private $telAgence;

    /**
     * @var text $adresseAgence
     * #[ORM\Column(name="adresseagence", type="text")]
     * #[Assert\NotBlank()]  
     */
    private $adresseAgence;

    /**
     * @var integer $etatAgence
     * #[ORM\Column(name="etatagence", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $etatAgence;
    
    /**
     * #[ORM\Column(name="datecreation", type="datetime")]
     * @var string $dateCreation 
     */
    private $dateCreation;   

    /**
     * @var integer $suppr
     * #[ORM\Column(name="suppr", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $suppr;


    /**
     * Set codeAgence
     *
     * @param string $codeAgence
     * @return Agence
     */
    public function setCodeAgence(string $codeAgence): self
    {
        $this->codeAgence = $codeAgence;
    
        return $this;
    }

    /**
     * Get codeAgence
     *
     * @return string 
     */
    public function getCodeAgence(): ?string
    {
        return $this->codeAgence;
    }

    /**
     * Set libAgence
     *
     * @param string $libAgence
     * @return Agence
     */
    public function setLibAgence(string $libAgence): self
    {
        $this->libAgence = $libAgence;
    
        return $this;
    }

    /**
     * Get libAgence
     *
     * @return string 
     */
    public function getLibAgence(): ?string
    {
        return $this->libAgence;
    }

    /**
     * Set telAgence
     *
     * @param string $telAgence
     * @return Agence
     */
    public function setTelAgence(string $telAgence): self
    {
        $this->telAgence = $telAgence;
    
        return $this;
    }

    /**
     * Get telAgence
     *
     * @return string 
     */
    public function getTelAgence(): ?string
    {
        return $this->telAgence;
    }

    /**
     * Set adresseAgence
     *
     * @param string $adresseAgence
     * @return Agence
     */
    public function setAdresseAgence(string $adresseAgence): self
    {
        $this->adresseAgence = $adresseAgence;
    
        return $this;
    }

    /**
     * Get adresseAgence
     *
     * @return string 
     */
    public function getAdresseAgence(): ?string
    {
        return $this->adresseAgence;
    }

    /**
     * Set etatAgence
     *
     * @param integer $etatAgence
     * @return Agence
     */
    public function setEtatAgence(string $etatAgence): self
    {
        $this->etatAgence = $etatAgence;
    
        return $this;
    }

    /**
     * Get etatAgence
     *
     * @return integer 
     */
    public function getEtatAgence(): ?string
    {
        return $this->etatAgence;
    }

    /**
     * Set suppr
     *
     * @param integer $suppr
     * @return Agence
     */
    public function setSuppr(string $suppr): self
    {
        $this->suppr = $suppr;
    
        return $this;
    }

    /**
     * Get suppr
     *
     * @return integer 
     */
    public function getSuppr(): ?string
    {
        return $this->suppr;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Agence
     */
    public function setDateCreation(string $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
    
        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation(): ?string
    {
        return $this->dateCreation;
    }
}
