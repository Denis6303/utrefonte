<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="paramsysteme")]
 * #[ORM\Entity](repositoryClass="App\Entity\ParamSystemeRepository")
 *
 */
class ParamSysteme {

    function __construct() {
        $this->suppr = 0;
        $this->etatParametre = 1;
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idparam", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $cle
     * #[ORM\Column(name="cle", type="string" ,length=100)]
     * #[Assert\NotBlank()]  
     */
    private $cle;    
    
    /**
     * @var string $valeur
     * #[ORM\Column(name="valeur",type="string",length=100)]
     * @Assert\MinLength(2)
     */
    private $valeur;
        
    /**
     * @var string $description
     * #[ORM\Column(name="description",type="string",length=255,nullable=true)]
     * 
     */
    private $description;
    
    /**
     * @var integer $type
     * #[ORM\Column(name="idtype", type="integer")]
     */
    private $type;  
    
    /**
     * @var integer $suppr
     * #[ORM\Column(name="suppr", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $suppr;
    
    /**
     * @var integer $etatParametre
     * #[ORM\Column(name="etatparametre",type="integer" )]
     *   
     */
    private $etatParametre;


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
     * Set cle
     *
     * @param string $cle
     * @return ParamSysteme
     */
    public function setCle(string $cle): self
    {
        $this->cle = $cle;
    
        return $this;
    }

    /**
     * Get cle
     *
     * @return string 
     */
    public function getCle(): ?string
    {
        return $this->cle;
    }

    /**
     * Set valeur
     *
     * @param string $valeur
     * @return ParamSysteme
     */
    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;
    
        return $this;
    }

    /**
     * Get valeur
     *
     * @return string 
     */
    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return ParamSysteme
     */
    public function setType(string $type): self
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ParamSysteme
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set suppr
     *
     * @param integer $suppr
     * @return ParamSysteme
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
     * Set etatParametre
     *
     * @param integer $etatParametre
     * @return ParamSysteme
     */
    public function setEtatParametre(string $etatParametre): self
    {
        $this->etatParametre = $etatParametre;
    
        return $this;
    }

    /**
     * Get etatParametre
     *
     * @return integer 
     */
    public function getEtatParametre(): ?string
    {
        return $this->etatParametre;
    }
}
