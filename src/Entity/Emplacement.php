<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Classe qui va gerer les emplacements ou blocs sur le site
 *
 * @author Edem
 * #[ORM\Entity](repositoryClass="App\Entity\EmplacementRepository")
 * #[ORM\Table(name="emplacement")]
 * @ORM\HasLifecycleCallbacks
 */
class Emplacement {

    function __construct() {
        
    }

    /**
     * @var Cadre $cadre
     * #[ORM\ManyToOne(targetEntity: App\Entity\Cadre::class, inversedBy="emplacements", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idcadre", referencedColumnName="idcadre")
     * })
     */
    private $cadre;

    /**
     * @var integer $id
     * #[ORM\Id] 
     * #[ORM\GeneratedValue](strategy="AUTO")
     * #[ORM\Column(name="idemplacement" , type="integer")]
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var text $libemplacement
     * #[ORM\Column(name="libemplacement", type="text", nullable=true)]
     * 
     */
    private $libEmplacement;

    /**
     * @var integer $statutEmplacement
     * #[ORM\Column(name="statutemplacement" , type="integer")]
     * @Assert\Regex(pattern="/^[0-6]$/", message="la valeur doit être comprise entre 0 et 6")  
     */
    private $statutEmplacement;
    
    /**
     * @var integer $suppr
     * #[ORM\Column(name="suppr", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $suppr;

    /**
     * @ORM\PrePersist()
     * 
     */
    public function presend() {
        $this->statutEmplacement = 1; /// en cours de redaction
    }

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
     * Set libEmplacement
     *
     * @param string $libEmplacement
     * @return Emplacement
     */
    public function setLibEmplacement(string $libEmplacement): self {
        $this->libEmplacement = $libEmplacement;

        return $this;
    }

    /**
     * Get libEmplacement
     *
     * @return string 
     */
    public function getLibEmplacement(): ?string {
        return $this->libEmplacement;
    }

    /**
     * Set statutEmplacement
     *
     * @param integer $statutEmplacement
     * @return Emplacement
     */
    public function setStatutEmplacement(string $statutEmplacement): self {
        $this->statutEmplacement = $statutEmplacement;

        return $this;
    }

    /**
     * Get statutEmplacement
     *
     * @return integer 
     */
    public function getStatutEmplacement(): ?string {
        return $this->statutEmplacement;
    }

    /**
     * Set cadre
     *
     * @param \ace3i\AdminBundle\Entity\Cadre $cadre
     * @return Emplacement
     */
    public function setCadre(\App\Entity\Cadre $cadre = null) {
        $this->cadre = $cadre;

        return $this;
    }

    /**
     * Get cadre
     *
     * @return \ace3i\AdminBundle\Entity\Cadre 
     */
    public function getCadre(): ?string {
        return $this->cadre;
    }
    
    /**
     * Set suppr
     *
     * @param integer $suppr
     * @return Emplacement
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

}
