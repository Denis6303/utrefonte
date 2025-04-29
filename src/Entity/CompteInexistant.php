<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\CompteInexistantRepository")
 * #[ORM\Table(name="compteinexistant")]
 */
class CompteInexistant {

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="idcompte", type="integer")]	
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    public function __construct() {
        $this->dateCreation = new \Datetime();
    }

    /**
     * #[ORM\Column(name="datecreation", type="datetime" , nullable =true)]
     * @var string $dateCreation 
     */
    private $dateNotification;

    /**
     * @var string $compte
     * #[ORM\Column(name="compte", type="string", length =20)]
     */
    private $compte;

    /**
     * @var integer $idfile
     * #[ORM\Column(name="idfile", type="integer")]
     */
    private $idfile;

    /**
     * @var integer $traite
     * #[ORM\Column(name="traite", type="integer")]
     */
    private $traite;

    /**
     * @var TypeCompte $typeCompte
     * #[ORM\ManyToOne(targetEntity: App\Entity\TypeCompte::class, inversedBy="comptes", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idtypecompte", referencedColumnName="idtypecompte")
     * })
     */
    private $typeCompte;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set dateNotification
     *
     * @param \DateTime $dateNotification
     * @return CompteInexistant
     */
    public function setDateNotification(string $dateNotification): self {
        $this->dateNotification = $dateNotification;

        return $this;
    }

    /**
     * Get dateNotification
     *
     * @return \DateTime 
     */
    public function getDateNotification(): ?string {
        return $this->dateNotification;
    }

    /**
     * Set compte
     *
     * @param string $compte
     * @return CompteInexistant
     */
    public function setCompte(string $compte): self {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return string 
     */
    public function getCompte(): ?string {
        return $this->compte;
    }

    /**
     * Set typeCompte
     *
     * @param integer $typeCompte
     * @return CompteInexistant
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
     * Set idfile
     *
     * @param integer $idfile
     * @return CompteInexistant
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

    /**
     * Set traite
     *
     * @param integer $traite
     * @return CompteInexistant
     */
    public function setTraite(string $traite): self {
        $this->traite = $traite;

        return $this;
    }

    /**
     * Get traite
     *
     * @return integer 
     */
    public function getTraite(): ?string {
        return $this->traite;
    }

}
