<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Classe qui gère les parametrages sur le site
 *
 * #[ORM\Entity](repositoryClass="App\Entity\ParametrageRepository")
 * #[ORM\Table(name="parametrage")]
 * @ORM\HasLifecycleCallbacks
 */
class Parametrage {

    function __construct() {
        $this->valeur = 0;
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     * #[ORM\Column(name="idparam" , type="integer")]
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var text $paramDescription
     * #[ORM\Column(name="description", type="text",nullable=true)]
     * #[Assert\NotBlank()]
     */
    private $paramDescription;

    /**
     *
     * @var integer $paramValeur
     * #[ORM\Column(name="valeur", type="integer",nullable=true)]
     * #[Assert\NotBlank()]
     */
    private $paramValeur;

    /**
     *
     * @var integer $paramType
     * #[ORM\Column(name="paramtype", type="integer")]
     * #[Assert\NotBlank()]
     */
    private $paramType;

    /**
     *
     * @var integer $paramAjoutPar
     * #[ORM\Column(name="paramajoutpar", type="integer")]
     * #[Assert\NotBlank()]
     */
    private $paramAjoutPar;

    /**
     * @Gedmo\Translatable
     * @var string $paramTitre
     * #[ORM\Column(name="paramTitre", type="string")]
     * #[Assert\NotBlank()]
     */
    private $paramTitre;

    /**
     * @Gedmo\Translatable
     * @var text $TypeDescription
     * #[ORM\Column(name="typedescription", type="string",nullable=true)]
     * #[Assert\NotBlank()]
     */
    private $TypeDescription;

    /**
     *
     * @var integer $actif
     * #[ORM\Column(name="actif", type="integer",nullable=true)]
     * #[Assert\NotBlank()]
     */
    private $actif;

    /**
     *
     * @var integer $idgroupe
     * #[ORM\Column(name="idgroupe", type="integer",nullable=true)]
     * #[Assert\NotBlank()]
     */
    private $idGroupe;

    /**
     * @var text $grpeDescription
     * #[ORM\Column(name="grpedescription", type="string",nullable=true)]
     * #[Assert\NotBlank()]
     */
    private $grpeDescription;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set paramDescription
     *
     * @param string $paramDescription
     * @return Parametrage
     */
    public function setParamDescription(string $paramDescription): self {
        $this->paramDescription = $paramDescription;

        return $this;
    }

    /**
     * Get paramDescription
     *
     * @return string
     */
    public function getParamDescription(): ?string {
        return $this->paramDescription;
    }

    /**
     * Set paramValeur
     *
     * @param integer $paramValeur
     * @return Parametrage
     */
    public function setParamValeur(string $paramValeur): self {
        $this->paramValeur = $paramValeur;

        return $this;
    }

    /**
     * Get paramValeur
     *
     * @return integer
     */
    public function getParamValeur(): ?string {
        return $this->paramValeur;
    }

    /**
     * Set paramType
     *
     * @param integer $paramType
     * @return Parametrage
     */
    public function setParamType(string $paramType): self {
        $this->paramType = $paramType;

        return $this;
    }

    /**
     * Get paramType
     *
     * @return integer
     */
    public function getParamType(): ?string {
        return $this->paramType;
    }

    /**
     * Set paramAjoutPar
     *
     * @param integer $paramAjoutPar
     * @return Parametrage
     */
    public function setParamAjoutPar(string $paramAjoutPar): self {
        $this->paramAjoutPar = $paramAjoutPar;

        return $this;
    }

    /**
     * Get paramAjoutPar
     *
     * @return integer
     */
    public function getParamAjoutPar(): ?string {
        return $this->paramAjoutPar;
    }

    /**
     * Set paramTitre
     *
     * @param string $paramTitre
     * @return Parametrage
     */
    public function setParamTitre(string $paramTitre): self {
        $this->paramTitre = $paramTitre;

        return $this;
    }

    /**
     * Get paramTitre
     *
     * @return string
     */
    public function getParamTitre(): ?string {
        return $this->paramTitre;
    }

    /**
     * @ORM\PrePersist()
     * 
     */
    public function preAdding() {

        if (null === $this->paramValeur) {
            $this->paramValeur = 0;
        }

        if (null === $this->paramDescription) {
            $this->paramtitre = 'None';
        }

        if (null === $this->paramDescription) {
            $this->paramDescription = 'None';
        }
    }

    public function setTranslatableLocale(string $locale): self {
        $this->locale = $locale;
    }

    /**
     * Set TypeDescription
     *
     * @param string $typeDescription
     * @return Parametrage
     */
    public function setTypeDescription(string $typeDescription): self {
        $this->TypeDescription = $typeDescription;

        return $this;
    }

    /**
     * Get TypeDescription
     *
     * @return string 
     */
    public function getTypeDescription(): ?string {
        return $this->TypeDescription;
    }

    /**
     * Set actif
     *
     * @param integer $actif
     * @return Parametrage
     */
    public function setActif(string $actif): self {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return integer 
     */
    public function getActif(): ?string {
        return $this->actif;
    }

    /**
     * Set idGroupe
     *
     * @param integer $idGroupe
     * @return Parametrage
     */
    public function setIdGroupe(string $idGroupe): self {
        $this->idGroupe = $idGroupe;

        return $this;
    }

    /**
     * Get idGroupe
     *
     * @return integer 
     */
    public function getIdGroupe(): ?string {
        return $this->idGroupe;
    }

    /**
     * Set grpeDescription
     *
     * @param string $grpeDescription
     * @return Parametrage
     */
    public function setGrpeDescription(string $grpeDescription): self {
        $this->grpeDescription = $grpeDescription;

        return $this;
    }

    /**
     * Get grpeDescription
     *
     * @return string 
     */
    public function getGrpeDescription(): ?string {
        return $this->grpeDescription;
    }

}
