<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\InfosAfterLoadRepository")
 * #[ORM\Table(name="infosafterload")]
 */
class InfosAfterLoad {

    public function __construct() {
        $this->dateCreation = new \Datetime();
    }

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="idinfos", type="integer")]	
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var string $datestat 	
     * #[ORM\Column(name="datestat", type="datetime")]
     */
    private $datestat;

    /**
     * @var integer $typecompte
     * #[ORM\Column(name="typecompte", type="integer")]
     */
    private $typeCompte;

    /**
     * @var integer $nbreTotalLigne
     * #[ORM\Column(name="nbretotal", type="integer" )]
     * #[Assert\NotBlank()]  
     */
    private $nbreTotalLigne;

    /**
     * @var integer $nbreImportLigne
     * #[ORM\Column(name="nbreimport", type="integer" )]
     */
    private $nbreImportLigne;

    /**
     * @var float $prcentImport
     * #[ORM\Column(name="prcentimport", type="float" )]
     */
    private $prcentImport;

    /**
     * @var integer $nbreCpteInexistant
     * #[ORM\Column(name="nbrecpteinexistant", type="integer" )]
     */
    private $nbreCpteInexistant;

    /**
     * @var integer $idfile
     * #[ORM\Column(name="idfile", type="integer" )]
     */
    private $idfile;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set datestat
     *
     * @param \DateTime $datestat
     * @return InfosAfterLoad
     */
    public function setDatestat(string $datestat): self {
        $this->datestat = $datestat;

        return $this;
    }

    /**
     * Get datestat
     *
     * @return \DateTime 
     */
    public function getDatestat(): ?string {
        return $this->datestat;
    }

    /**
     * Set typeCompte
     *
     * @param integer $typeCompte
     * @return InfosAfterLoad
     */
    public function setTypeCompte(string $typeCompte): self {
        $this->typeCompte = $typeCompte;

        return $this;
    }

    /**
     * Get typeCompte
     *
     * @return integer 
     */
    public function getTypeCompte(): ?string {
        return $this->typeCompte;
    }

    /**
     * Set nbreTotalLigne
     *
     * @param integer $nbreTotalLigne
     * @return InfosAfterLoad
     */
    public function setNbreTotalLigne(string $nbreTotalLigne): self {
        $this->nbreTotalLigne = $nbreTotalLigne;

        return $this;
    }

    /**
     * Get nbreTotalLigne
     *
     * @return integer 
     */
    public function getNbreTotalLigne(): ?string {
        return $this->nbreTotalLigne;
    }

    /**
     * Set nbreImportLigne
     *
     * @param integer $nbreImportLigne
     * @return InfosAfterLoad
     */
    public function setNbreImportLigne(string $nbreImportLigne): self {
        $this->nbreImportLigne = $nbreImportLigne;

        return $this;
    }

    /**
     * Get nbreImportLigne
     *
     * @return integer 
     */
    public function getNbreImportLigne(): ?string {
        return $this->nbreImportLigne;
    }

    /**
     * Set prcentImport
     *
     * @param float $prcentImport
     * @return InfosAfterLoad
     */
    public function setPrcentImport(string $prcentImport): self {
        $this->prcentImport = $prcentImport;

        return $this;
    }

    /**
     * Get prcentImport
     *
     * @return float 
     */
    public function getPrcentImport(): ?string {
        return $this->prcentImport;
    }

    /**
     * Set nbreCpteInexistant
     *
     * @param integer $nbreCpteInexistant
     * @return InfosAfterLoad
     */
    public function setNbreCpteInexistant(string $nbreCpteInexistant): self {
        $this->nbreCpteInexistant = $nbreCpteInexistant;

        return $this;
    }

    /**
     * Get nbreCpteInexistant
     *
     * @return integer 
     */
    public function getNbreCpteInexistant(): ?string {
        return $this->nbreCpteInexistant;
    }

    /**
     * Set idfile
     *
     * @param integer $idfile
     * @return InfosAfterLoad
     */
    public function setIdfile(string $idfile): self {
        $this->idfile = $idfile;

        return $this;
    }

    /**
     * Get idfile
     *
     * @return integer 
     */
    public function getIdfile(): ?string {
        return $this->idfile;
    }

}
