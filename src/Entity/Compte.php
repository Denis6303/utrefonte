<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\CompteRepository")
 * #[ORM\Table(name="compte")]
 */
class Compte {

    public function __construct() {
        
    }

    /**
     * 
     * @var string $numeroCompte
     * #[ORM\Id]
     * #[ORM\Column(name="numerocompte", type="string",length=20)]	
     */
    private $numeroCompte;

    /**
     * #[ORM\Column(name="datecreation", type="datetime")]
     * @var string $dateCreation 
     */
    private $dateCreation;

    /**
     * @var integer $etatCompte
     * #[ORM\Column(name="etatcompte", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $etatCompte;

    /**
     * @var TypeCompte $typeCompte
     * #[ORM\ManyToOne(targetEntity: App\Entity\TypeCompte::class, inversedBy="comptes", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idtypecompte", referencedColumnName="idtypecompte")
     * })
     */
    private $typeCompte;

    /**
     * @var CategorieCompte $categorieCompte
     * #[ORM\ManyToOne(targetEntity: App\Entity\CategorieCompte::class, inversedBy="comptes", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="codecategorie", referencedColumnName="codecategorie")
     * })
     */
    private $categorieCompte;

    /**
     * @var integer $facturation
     * #[ORM\Column(name="facturation", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $facturation;

    /**
     * @var ArrayCollection Operation $operations
     * #[ORM\OneToMany(targetEntity: App\Entity\Operation::class, mappedBy="compte" )]
     * 
     */
    private $operations;

    /**
     * @var ArrayCollection SoldeCompte $soldeComptes
     * #[ORM\OneToMany(targetEntity: App\Entity\SoldeCompte::class, mappedBy="compte" )]
     * 
     */
    private $soldeComptes;	
	
    /**
     * @var Fonds $fonds
     * #[ORM\ManyToOne(targetEntity: App\Entity\Fonds::class, inversedBy="comptes", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idfonds", referencedColumnName="idfonds")
     * })
     */
    private $fonds;

    /**
     * @var Abonne $abonne
     * #[ORM\ManyToOne(targetEntity: App\Entity\Abonne::class, inversedBy="comptes", cascade={"persist","merge"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idabonne", referencedColumnName="idabonne")
     * })
     */
    private $abonne;

    /**
     * 
     * @var string $numRib
     * #[ORM\Column(name="numrib", type="string",length=30,nullable=true)]	
     */
    private $numRib;


    /**
     * Set numeroCompte
     *
     * @param string $numeroCompte
     * @return Compte
     */
    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;
    
        return $this;
    }

    /**
     * Get numeroCompte
     *
     * @return string 
     */
    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Compte
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

    /**
     * Set etatCompte
     *
     * @param integer $etatCompte
     * @return Compte
     */
    public function setEtatCompte(string $etatCompte): self
    {
        $this->etatCompte = $etatCompte;
    
        return $this;
    }

    /**
     * Get etatCompte
     *
     * @return integer 
     */
    public function getEtatCompte(): ?string
    {
        return $this->etatCompte;
    }

    /**
     * Set facturation
     *
     * @param integer $facturation
     * @return Compte
     */
    public function setFacturation(string $facturation): self
    {
        $this->facturation = $facturation;
    
        return $this;
    }

    /**
     * Get facturation
     *
     * @return integer 
     */
    public function getFacturation(): ?string
    {
        return $this->facturation;
    }

    /**
     * Set numRib
     *
     * @param string $numRib
     * @return Compte
     */
    public function setNumRib(string $numRib): self
    {
        $this->numRib = $numRib;
    
        return $this;
    }

    /**
     * Get numRib
     *
     * @return string 
     */
    public function getNumRib(): ?string
    {
        return $this->numRib;
    }

    /**
     * Set typeCompte
     *
     * @param \App\Entity\TypeCompte $typeCompte
     * @return Compte
     */
    public function setTypeCompte(\App\Entity\TypeCompte $typeCompte = null)
    {
        $this->typeCompte = $typeCompte;
    
        return $this;
    }

    /**
     * Get typeCompte
     *
     * @return \App\Entity\TypeCompte 
     */
    public function getTypeCompte(): ?string
    {
        return $this->typeCompte;
    }

    /**
     * Set categorieCompte
     *
     * @param \App\Entity\CategorieCompte $categorieCompte
     * @return Compte
     */
    public function setCategorieCompte(\App\Entity\CategorieCompte $categorieCompte = null)
    {
        $this->categorieCompte = $categorieCompte;
    
        return $this;
    }

    /**
     * Get categorieCompte
     *
     * @return \App\Entity\CategorieCompte 
     */
    public function getCategorieCompte(): ?string
    {
        return $this->categorieCompte;
    }

    /**
     * Add operations
     *
     * @param \App\Entity\Operation $operations
     * @return Compte
     */
    public function addOperation(\App\Entity\Operation $operations)
    {
        $this->operations[] = $operations;
    
        return $this;
    }

    /**
     * Remove operations
     *
     * @param \App\Entity\Operation $operations
     */
    public function removeOperation(\App\Entity\Operation $operations)
    {
        $this->operations->removeElement($operations);
    }

    /**
     * Get operations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOperations(): ?string
    {
        return $this->operations;
    }

    /**
     * Add soldeComptes
     *
     * @param \App\Entity\SoldeCompte $soldeComptes
     * @return Compte
     */
    public function addSoldeCompte(\App\Entity\SoldeCompte $soldeComptes)
    {
        $this->soldeComptes[] = $soldeComptes;
    
        return $this;
    }

    /**
     * Remove soldeComptes
     *
     * @param \App\Entity\SoldeCompte $soldeComptes
     */
    public function removeSoldeCompte(\App\Entity\SoldeCompte $soldeComptes)
    {
        $this->soldeComptes->removeElement($soldeComptes);
    }

    /**
     * Get soldeComptes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSoldeComptes(): ?string
    {
        return $this->soldeComptes;
    }

    /**
     * Set fonds
     *
     * @param \App\Entity\Fonds $fonds
     * @return Compte
     */
    public function setFonds(\App\Entity\Fonds $fonds = null)
    {
        $this->fonds = $fonds;
    
        return $this;
    }

    /**
     * Get fonds
     *
     * @return \App\Entity\Fonds 
     */
    public function getFonds(): ?string
    {
        return $this->fonds;
    }

    /**
     * Set abonne
     *
     * @param \App\Entity\Abonne $abonne
     * @return Compte
     */
    public function setAbonne(\App\Entity\Abonne $abonne = null)
    {
        $this->abonne = $abonne;
    
        return $this;
    }

    /**
     * Get abonne
     *
     * @return \App\Entity\Abonne 
     */
    public function getAbonne(): ?string
    {
        return $this->abonne;
    }
}
