<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\soldeCompteRepository")
 * #[ORM\Table(name="soldecompte")]
 */
class SoldeCompte {

    public function __construct() {
        
    }

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="idsolde", type="integer")]	
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var Compte $compte
     * #[ORM\ManyToOne(targetEntity: App\Entity\Compte::class, inversedBy="soldeComptes", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="numerocompte", referencedColumnName="numerocompte")
     * })
     */
    private $compte;

    /**
     * #[ORM\Column(name="datesolde", type="datetime")]
     * @var string $dateSolde 
     */
    private $dateSolde;

    /**
     * @var integer $solde
     * #[ORM\Column(name="solde", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $solde;
    
    /**
     * @var string $sens
     * #[ORM\Column(name="sensop", type="string",length=2)]
     * #[Assert\NotBlank()]  
     */
    private $sens;

    /**
     * @var integer $idfile
     * #[ORM\Column(name="idfile", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $idfile;    
    

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
     * Set dateSolde
     *
     * @param \DateTime $dateSolde
     * @return SoldeCompte
     */
    public function setDateSolde(string $dateSolde): self
    {
        $this->dateSolde = $dateSolde;
    
        return $this;
    }

    /**
     * Get dateSolde
     *
     * @return \DateTime 
     */
    public function getDateSolde(): ?string
    {
        return $this->dateSolde;
    }

    /**
     * Set solde
     *
     * @param integer $solde
     * @return SoldeCompte
     */
    public function setSolde(string $solde): self
    {
        $this->solde = $solde;
    
        return $this;
    }

    /**
     * Get solde
     *
     * @return integer 
     */
    public function getSolde(): ?string
    {
        return $this->solde;
    }

    /**
     * Set sens
     *
     * @param string $sens
     * @return SoldeCompte
     */
    public function setSens(string $sens): self
    {
        $this->sens = $sens;
    
        return $this;
    }

    /**
     * Get sens
     *
     * @return string 
     */
    public function getSens(): ?string
    {
        return $this->sens;
    }

    /**
     * Set idfile
     *
     * @param integer $idfile
     * @return SoldeCompte
     */
    public function setIdfile(string $idfile): self
    {
        $this->idfile = $idfile;
    
        return $this;
    }

    /**
     * Get idfile
     *
     * @return integer 
     */
    public function getIdfile(): ?string
    {
        return $this->idfile;
    }

    /**
     * Set compte
     *
     * @param \App\Entity\Compte $compte
     * @return SoldeCompte
     */
    public function setCompte(\App\Entity\Compte $compte = null)
    {
        $this->compte = $compte;
    
        return $this;
    }

    /**
     * Get compte
     *
     * @return \App\Entity\Compte 
     */
    public function getCompte(): ?string
    {
        return $this->compte;
    }
}
