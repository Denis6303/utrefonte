<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Classe qui gère les statistiques des articles
 *
 * #[ORM\Entity](repositoryClass="App\Entity\StatistiqueClientRepository")
 * #[ORM\Table(name="statistiqueclient")]
 * @ORM\HasLifecycleCallbacks
 */
class StatistiqueClient {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Id] 
     * #[ORM\GeneratedValue](strategy="AUTO")
     * #[ORM\Column(name="idstat" , type="integer")]
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var string $libStat
     * #[ORM\Column(name="libstat", type="string",length=100)]
     * #[Assert\NotBlank(message="Libellé ligne statistique requis!")]
     */
    private $libStat;

    /**
     * 
     * @var integer $typeStat
     * #[ORM\Column(name="typestat", type="integer")]
     * #[Assert\NotBlank(message="Type ligne statistique requis!")]
     */
    private $typeStat;

    /**
     * 
     * @var integer $etatstat
     * #[ORM\Column(name="etatstat", type="integer")]
     * #[Assert\NotBlank(message="Préciser l'état ")]
     * @Assert\Regex(pattern="/^[0-1]$/", message="la valeur doit être comprise entre 0 et 1")  
     */
    private $etatStat;

    /**
     * 
     * @var string $descriptionType
     * #[ORM\Column(name="descriptiontype", type="string")]
     * #[Assert\NotBlank()]
     */
    private $descriptionType;

    /**
     * 
     * @var integer $valeur
     * #[ORM\Column(name="valeur", type="integer")]
     * #[Assert\NotBlank()]  
     * @Assert\Regex(pattern="/^[0-9]$/", message="la valeur doit être comprise entre 0 et 9") 
     */
    private $valeur;

    /**
     * 
     * @var string $route
     * #[ORM\Column(name="route", type="string")]
     * #[Assert\NotBlank()]  
     */
    private $route;

    /**
     * 
     * @var string $ecart
     * #[ORM\Column(name="ecart", type="string")]
     * #[Assert\NotBlank()]  
     */
    private $ecart;

    /**
     * 
     * @var string $valeur
     * #[ORM\Column(name="parametres", type="text")]
     * #[Assert\NotBlank()]  
     */
    private $parametres;

    /**
     * 
     * @var integer $ordre 
     * #[ORM\Column(name="ordre", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $ordre;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

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
     * Set libStat
     *
     * @param string $libStat
     * @return Statistique
     */
    public function setLibStat(string $libStat): self {
        $this->libStat = $libStat;

        return $this;
    }

    /**
     * Get libStat
     *
     * @return string 
     */
    public function getLibStat(): ?string {
        return $this->libStat;
    }

    /**
     * Set typeStat
     *
     * @param integer $typeStat
     * @return Statistique
     */
    public function setTypeStat(string $typeStat): self {
        $this->typeStat = $typeStat;

        return $this;
    }

    /**
     * Get typeStat
     *
     * @return integer 
     */
    public function getTypeStat(): ?string {
        return $this->typeStat;
    }

    /**
     * Set etatStat
     *
     * @param integer $etatStat
     * @return Statistique
     */
    public function setEtatStat(string $etatStat): self {
        $this->etatStat = $etatStat;

        return $this;
    }

    /**
     * Get etatStat
     *
     * @return integer 
     */
    public function getEtatStat(): ?string {
        return $this->etatStat;
    }

    /**
     * Set descriptionType
     *
     * @param string $descriptionType
     * @return Statistique
     */
    public function setDescriptionType(string $descriptionType): self {
        $this->descriptionType = $descriptionType;

        return $this;
    }

    /**
     * Get descriptionType
     *
     * @return string 
     */
    public function getDescriptionType(): ?string {
        return $this->descriptionType;
    }

    /**
     * Set valeur
     *
     * @param integer $valeur
     * @return Statistique
     */
    public function setValeur(string $valeur): self {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return integer 
     */
    public function getValeur(): ?string {
        return $this->valeur;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Statistique
     */
    public function setRoute(string $route): self {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute(): ?string {
        return $this->route;
    }

    /**
     * Set parametres
     *
     * @param string $parametres
     * @return Statistique
     */
    public function setParametres(string $parametres): self {
        $this->parametres = $parametres;

        return $this;
    }

    /**
     * Get parametres
     *
     * @return string 
     */
    public function getParametres(): ?string {
        return $this->parametres;
    }

    /**
     * Set ecart
     *
     * @param string $ecart
     * @return Statistique
     */
    public function setEcart(string $ecart): self {
        $this->ecart = $ecart;

        return $this;
    }

    /**
     * Get ecart
     *
     * @return string 
     */
    public function getEcart(): ?string {
        return $this->ecart;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return Statistique
     */
    public function setOrdre(string $ordre): self {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre(): ?string {
        return $this->ordre;
    }

}
