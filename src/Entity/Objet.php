<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * App\Entity
 *
 * #[ORM\Table(name="objet")]
 * #[ORM\Entity](repositoryClass="App\Entity\ObjetRepository")
 * @ORM\HasLifecycleCallbacks
 * 
 */
class Objet {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idobjet", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $libObjet
     * @Gedmo\Translatable
     * #[ORM\Column(name="libobjet",type="string",length=100)]
     * #[Assert\NotBlank(message="Le libellé du objet ne peut être vide")]
     * @Assert\MinLength(2)
     */
    private $libObjet;

    /**
     * @var text $descriptionObjet
     * #[ORM\Column(name="descriptionobjet",type="string")]
     */
    private $descriptionObjet;

    /**
     * @var integer $objetAjoutPar
     * #[ORM\Column(name="objetajoutpar" , type="integer")]
     *   
     */
    private $objetAjoutPar;

    /**
     * @var integer $objetModifPar
     * #[ORM\Column(name="objetmodifpar" , type="integer", nullable=true)]
     *   
     */
    private $objetModifPar;

    /**
     * @var datetime $objetDateAjout
     * #[ORM\Column(name="objetdateajout" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $objetDateAjout;

    /**
     * @var datetime $objetDateModif
     * #[ORM\Column(name="objetdatemodif" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $objetDateModif;



    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
    /**
     * @var integer $etatObjet
     * #[ORM\Column(name="etatobjet",type="integer" )]
     *   
     */    
    private $etatObjet;

    
    public function setTranslatableLocale(string $locale): self {
        $this->locale = $locale;
    }    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libObjet
     *
     * @param string $libObjet
     * @return Objet
     */
    public function setLibObjet(string $libObjet): self {
        $this->libObjet = $libObjet;

        return $this;
    }

    /**
     * Get libObjet
     *
     * @return string 
     */
    public function getLibObjet(): ?string {
        return $this->libObjet;
    }

    /**
     * Set descriptionObjet
     *
     * @param string $descriptionObjet
     * @return Objet
     */
    public function setDescriptionObjet(string $descriptionObjet): self {
        $this->descriptionObjet = $descriptionObjet;

        return $this;
    }

    /**
     * Get descriptionObjet
     *
     * @return string 
     */
    public function getDescriptionObjet(): ?string {
        return $this->descriptionObjet;
    }

    /**
     * Set objetAjoutPar
     *
     * @param integer $objetAjoutPar
     * @return Objet
     */
    public function setObjetAjoutPar(string $objetAjoutPar): self {
        $this->objetAjoutPar = $objetAjoutPar;

        return $this;
    }

    /**
     * Get objetAjoutPar
     *
     * @return integer 
     */
    public function getObjetAjoutPar(): ?string {
        return $this->objetAjoutPar;
    }

    /**
     * Set objetModifPar
     *
     * @param integer $objetModifPar
     * @return Objet
     */
    public function setObjetModifPar(string $objetModifPar): self {
        $this->objetModifPar = $objetModifPar;

        return $this;
    }

    /**
     * Get objetModifPar
     *
     * @return integer 
     */
    public function getObjetModifPar(): ?string {
        return $this->objetModifPar;
    }

    /**
     * Set objetDateAjout
     *
     * @param \DateTime $objetDateAjout
     * @return Objet
     */
    public function setObjetDateAjout(string $objetDateAjout): self {
        $this->objetDateAjout = $objetDateAjout;

        return $this;
    }

    /**
     * Get objetDateAjout
     *
     * @return \DateTime 
     */
    public function getObjetDateAjout(): ?string {
        return $this->objetDateAjout;
    }

    /**
     * Set objetDateModif
     *
     * @param \DateTime $objetDateModif
     * @return Objet
     */
    public function setObjetDateModif(string $objetDateModif): self {
        $this->objetDateModif = $objetDateModif;

        return $this;
    }

    /**
     * Get objetDateModif
     *
     * @return \DateTime 
     */
    public function getObjetDateModif(): ?string {
        return $this->objetDateModif;
    }

    /**
     * Set etatObjet
     *
     * @param integer $etatObjet
     * @return Objet
     */
    public function setEtatObjet(string $etatObjet): self {
        $this->etatObjet = $etatObjet;

        return $this;
    }

    /**
     * Get etatObjet
     *
     * @return integer 
     */
    public function getEtatObjet(): ?string {
        return $this->etatObjet;
    }

}
