<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Classe qui va gere les langues du site
 *
 * @author Gautier
 * #[ORM\Entity](repositoryClass="App\Entity\LangueRepository")
 * #[ORM\Table(name="langue")]
 */
class Langue {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idlangue", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var string $libLangue
     * #[ORM\Column(name="liblangue" , type="string")]
     */
    private $libLangue;

    /**
     * @var string $codeLangue
     * #[ORM\Column(name="codelangue" , type="string")]
     *   
     */
    private $codeLangue;

    /**
     * @var string $langueEtat
     * #[ORM\Column(name="langueetat" , type="integer")]
     *   
     */
    private $langueEtat;

    /**
     * @var string $iconeLangue
     * #[ORM\Column(name="iconelangue" , type="string")]
     *   
     */
    private $iconeLangue;

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
     * Set libLangue
     *
     * @param string $libLangue
     * @return Langue
     */
    public function setLibLangue(string $libLangue): self {
        $this->libLangue = $libLangue;

        return $this;
    }

    /**
     * Get libLangue
     *
     * @return string 
     */
    public function getLibLangue(): ?string {
        return $this->libLangue;
    }

    /**
     * Set codeLangue
     *
     * @param string $codeLangue
     * @return Langue
     */
    public function setCodeLangue(string $codeLangue): self {
        $this->codeLangue = $codeLangue;

        return $this;
    }

    /**
     * Get codeLangue
     *
     * @return string 
     */
    public function getCodeLangue(): ?string {
        return $this->codeLangue;
    }

    /**
     * Set langueEtat
     *
     * @param integer $langueEtat
     * @return Langue
     */
    public function setLangueEtat(string $langueEtat): self {
        $this->langueEtat = $langueEtat;

        return $this;
    }

    /**
     * Get langueEtat
     *
     * @return integer 
     */
    public function getLangueEtat(): ?string {
        return $this->langueEtat;
    }

    /**
     * Set iconeLangue
     *
     * @param string $iconeLangue
     * @return Langue
     */
    public function setIconeLangue(string $iconeLangue): self {
        $this->iconeLangue = $iconeLangue;

        return $this;
    }

    /**
     * Get iconeLangue
     *
     * @return string 
     */
    public function getIconeLangue(): ?string {
        return $this->iconeLangue;
    }

}
