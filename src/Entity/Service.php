<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="service")]
 * #[ORM\Entity](repositoryClass="App\Entity\ServiceRepository")
 *
 */
class Service {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idservice", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $libService
     * #[ORM\Column(name="libservice",type="string",length=100)]
     * #[Assert\NotBlank(message="Le libellé du service ne peut être vide")]
     * @Assert\MinLength(2)
     */
    private $libService;

    /**
     * @var string $emailService
     * #[ORM\Column(name="emailservice",type="string",length=100)]
     * @Assert\MinLength(2)
     */
    private $emailService;

    /**
     * @var text $descriptionService
     * #[ORM\Column(name="descriptionservice",type="string")]
     */
    private $descriptionService;

    /**
     * @var integer $serviceAjoutPar
     * #[ORM\Column(name="serviceajoutpar" , type="integer")]
     *   
     */
    private $serviceAjoutPar;

    /**
     * @var integer $serviceModifPar
     * #[ORM\Column(name="servicemodifpar" , type="integer", nullable=true)]
     *   
     */
    private $serviceModifPar;

    /**
     * @var datetime $serviceDateAjout
     * #[ORM\Column(name="servicedateajout" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $serviceDateAjout;

    /**
     * @var datetime $serviceDateModif
     * #[ORM\Column(name="servicedatemodif" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $serviceDateModif;

    /**
     * @var integer $etatService
     * #[ORM\Column(name="etatservice",type="integer" )]
     *   
     */
    private $etatService;

    /**
     * @var integer $typeService
     * #[ORM\Column(name="typeService",type="integer" )]
     *   
     */
    private $typeService;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libService
     *
     * @param string $libService
     * @return Service
     */
    public function setLibService(string $libService): self {
        $this->libService = $libService;

        return $this;
    }

    /**
     * Get libService
     *
     * @return string 
     */
    public function getLibService(): ?string {
        return $this->libService;
    }

    /**
     * Set descriptionService
     *
     * @param string $descriptionService
     * @return Service
     */
    public function setDescriptionService(string $descriptionService): self {
        $this->descriptionService = $descriptionService;

        return $this;
    }

    /**
     * Get descriptionService
     *
     * @return string 
     */
    public function getDescriptionService(): ?string {
        return $this->descriptionService;
    }

    /**
     * Set serviceAjoutPar
     *
     * @param integer $serviceAjoutPar
     * @return Service
     */
    public function setServiceAjoutPar(string $serviceAjoutPar): self {
        $this->serviceAjoutPar = $serviceAjoutPar;

        return $this;
    }

    /**
     * Get serviceAjoutPar
     *
     * @return integer 
     */
    public function getServiceAjoutPar(): ?string {
        return $this->serviceAjoutPar;
    }

    /**
     * Set serviceModifPar
     *
     * @param integer $serviceModifPar
     * @return Service
     */
    public function setServiceModifPar(string $serviceModifPar): self {
        $this->serviceModifPar = $serviceModifPar;

        return $this;
    }

    /**
     * Get serviceModifPar
     *
     * @return integer 
     */
    public function getServiceModifPar(): ?string {
        return $this->serviceModifPar;
    }

    /**
     * Set serviceDateAjout
     *
     * @param \DateTime $serviceDateAjout
     * @return Service
     */
    public function setServiceDateAjout(string $serviceDateAjout): self {
        $this->serviceDateAjout = $serviceDateAjout;

        return $this;
    }

    /**
     * Get serviceDateAjout
     *
     * @return \DateTime 
     */
    public function getServiceDateAjout(): ?string {
        return $this->serviceDateAjout;
    }

    /**
     * Set serviceDateModif
     *
     * @param \DateTime $serviceDateModif
     * @return Service
     */
    public function setServiceDateModif(string $serviceDateModif): self {
        $this->serviceDateModif = $serviceDateModif;

        return $this;
    }

    /**
     * Get serviceDateModif
     *
     * @return \DateTime 
     */
    public function getServiceDateModif(): ?string {
        return $this->serviceDateModif;
    }

    /**
     * Set etatService
     *
     * @param integer $etatService
     * @return Service
     */
    public function setEtatService(string $etatService): self {
        $this->etatService = $etatService;

        return $this;
    }

    /**
     * Get etatService
     *
     * @return integer 
     */
    public function getEtatService(): ?string {
        return $this->etatService;
    }

    /**
     * Set emailService
     *
     * @param string $emailService
     * @return Service
     */
    public function setEmailService(string $emailService): self {
        $this->emailService = $emailService;

        return $this;
    }

    /**
     * Get emailService
     *
     * @return string 
     */
    public function getEmailService(): ?string {
        return $this->emailService;
    }

    /**
     * Set typeService
     *
     * @param integer $typeService
     * @return Service
     */
    public function setTypeService(string $typeService): self {
        $this->typeService = $typeService;

        return $this;
    }

    /**
     * Get typeService
     *
     * @return integer 
     */
    public function getTypeService(): ?string {
        return $this->typeService;
    }

}
