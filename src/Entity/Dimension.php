<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\DimensionRepository")
 * #[ORM\Table(name="dimension")]
 * @ORM\HasLifecycleCallbacks
 */
class Dimension {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="iddimension", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection Media $medias
     * #[ORM\OneToMany(targetEntity: App\Entity\Media::class, mappedBy="dimension" )]
     * 
     */
    private $medias;

    /**
     * @var string $libDimension
     * #[ORM\Column(name="libdimension",type="string",length=70)]
     */
    private $libDimension;

    /**
     * @var integer $hauteur
     * #[ORM\Column(name="hauteur",type="integer" )]
     *   
     */
    private $hauteur;

    /**
     * @var integer $largeur
     * #[ORM\Column(name="largeur",type="integer" )]
     *   
     */
    private $largeur;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libDimension
     *
     * @param string $libDimension
     * @return Dimension
     */
    public function setLibDimension(string $libDimension): self {
        $this->libDimension = $libDimension;

        return $this;
    }

    /**
     * Get libDimension
     *
     * @return string 
     */
    public function getLibDimension(): ?string {
        return $this->libDimension;
    }

    /**
     * Set hauteur
     *
     * @param integer $hauteur
     * @return Dimension
     */
    public function setHauteur(string $hauteur): self {
        $this->hauteur = $hauteur;

        return $this;
    }

    /**
     * Get hauteur
     *
     * @return integer 
     */
    public function getHauteur(): ?string {
        return $this->hauteur;
    }

    /**
     * Set largeur
     *
     * @param integer $largeur
     * @return Dimension
     */
    public function setLargeur(string $largeur): self {
        $this->largeur = $largeur;

        return $this;
    }

    /**
     * Get largeur
     *
     * @return integer 
     */
    public function getLargeur(): ?string {
        return $this->largeur;
    }

    /**
     * Add medias
     *
     * @param \App\Entity\Media $medias
     * @return Dimension
     */
    public function addMedia(\App\Entity\Media $medias) {
        $this->medias[] = $medias;

        return $this;
    }

    /**
     * Remove medias
     *
     * @param \App\Entity\Media $medias
     */
    public function removeMedia(\App\Entity\Media $medias) {
        $this->medias->removeElement($medias);
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedias(): ?string {
        return $this->medias;
    }

    /**
     * @ORM\PrePersist()
     */
    public function preDimension() {
        $this->libDimension = $this->largeur . "x" . $this->hauteur;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdateDimension() {
        $this->libDimension = $this->largeur . "x" . $this->hauteur;
    }

}
