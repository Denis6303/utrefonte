<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity
 *
 * #[ORM\Table(name="naturedoc")]
 * #[ORM\Entity](repositoryClass="App\Entity\NatureDocRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class NatureDoc {

    function __construct() {
        //$this->etat = 0;
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idnaturedoc", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $libNatureDoc
     * #[ORM\Column(name="libnaturedoc",type="string",length=100)]
     * #[Assert\NotBlank(message="Libellé requis!")]
     * @Assert\MinLength(2)
     */
    private $libNatureDoc;

    /**
     * @var Media $medias
     * #[ORM\OneToMany(targetEntity: App\Entity\Media::class, mappedBy="naturedoc" )]
     * 
     */
    private $medias;

    /**
     * @var integer $natureDocAjoutPar
     * #[ORM\Column(name="naturedocajoutpar" , type="integer" , nullable=true)]
     *   
     */
    private $natureDocAjoutPar;

    /**
     * @var integer $natureDocModifPar
     * #[ORM\Column(name="naturedocmodifpar" , type="integer", nullable=true)]
     *   
     */
    private $natureDocModifPar;

    /**
     * @var integer $natureDocActivePar
     * #[ORM\Column(name="naturedocactivepar" , type="integer", nullable=true)]
     *   
     */
    private $natureDocActivePar;

    /**
     * @var integer $natureDocDesactivePar
     * #[ORM\Column(name="naturedocdesactivepar" , type="integer", nullable=true)]
     *   
     */
    private $natureDocDesactivePar;

    /**
     * @var datetime $natureDocDateAjout
     * #[ORM\Column(name="naturedocdateajout" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $natureDocDateAjout;

    /**
     * @var datetime $natureDocDateModif
     * #[ORM\Column(name="naturedocdatemodif" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $natureDocDateModif;

    /**
     * @var datetime $natureDocDateDesactive
     * #[ORM\Column(name="naturedocdatedesactive" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $natureDocDateDesactive;

    /**
     * @var datetime $natureDocDateActive
     * #[ORM\Column(name="naturedocdateactive" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $natureDocDateActive;

    /**
     * @var integer $statutNatureDoc
     * #[ORM\Column(name="statutnaturedoc" , type="integer", nullable=true)]
     * @Assert\Regex(pattern="/^[0-6]$/", message="la valeur doit être comprise entre 0 et 6")  
     */
    private $statutNatureDoc;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libNatureDoc
     *
     * @param string $libNatureDoc
     * @return NatureDoc
     */
    public function setLibNatureDoc(string $libNatureDoc): self {
        $this->libNatureDoc = $libNatureDoc;

        return $this;
    }

    /**
     * Get libNatureDoc
     *
     * @return string 
     */
    public function getLibNatureDoc(): ?string {
        return $this->libNatureDoc;
    }

    /**
     * Set natureDocAjoutPar
     *
     * @param integer $natureDocAjoutPar
     * @return NatureDoc
     */
    public function setNatureDocAjoutPar(string $natureDocAjoutPar): self {
        $this->natureDocAjoutPar = $natureDocAjoutPar;

        return $this;
    }

    /**
     * Get natureDocAjoutPar
     *
     * @return integer 
     */
    public function getNatureDocAjoutPar(): ?string {
        return $this->natureDocAjoutPar;
    }

    /**
     * Set natureDocModifPar
     *
     * @param integer $natureDocModifPar
     * @return NatureDoc
     */
    public function setNatureDocModifPar(string $natureDocModifPar): self {
        $this->natureDocModifPar = $natureDocModifPar;

        return $this;
    }

    /**
     * Get natureDocModifPar
     *
     * @return integer 
     */
    public function getNatureDocModifPar(): ?string {
        return $this->natureDocModifPar;
    }

    /**
     * Set natureDocActivePar
     *
     * @param integer $natureDocActivePar
     * @return NatureDoc
     */
    public function setNatureDocActivePar(string $natureDocActivePar): self {
        $this->natureDocActivePar = $natureDocActivePar;

        return $this;
    }

    /**
     * Get natureDocActivePar
     *
     * @return integer 
     */
    public function getNatureDocActivePar(): ?string {
        return $this->natureDocActivePar;
    }

    /**
     * Set natureDocDateAjout
     *
     * @param \DateTime $natureDocDateAjout
     * @return NatureDoc
     */
    public function setNatureDocDateAjout(string $natureDocDateAjout): self {
        $this->natureDocDateAjout = $natureDocDateAjout;

        return $this;
    }

    /**
     * Get natureDocDateAjout
     *
     * @return \DateTime 
     */
    public function getNatureDocDateAjout(): ?string {
        return $this->natureDocDateAjout;
    }

    /**
     * Set natureDocDateModif
     *
     * @param \DateTime $natureDocDateModif
     * @return NatureDoc
     */
    public function setNatureDocDateModif(string $natureDocDateModif): self {
        $this->natureDocDateModif = $natureDocDateModif;

        return $this;
    }

    /**
     * Get natureDocDateModif
     *
     * @return \DateTime 
     */
    public function getNatureDocDateModif(): ?string {
        return $this->natureDocDateModif;
    }

    /**
     * Set natureDocDateActive
     *
     * @param \DateTime $natureDocDateActive
     * @return NatureDoc
     */
    public function setNatureDocDateActive(string $natureDocDateActive): self {
        $this->natureDocDateActive = $natureDocDateActive;

        return $this;
    }

    /**
     * Get natureDocDateActive
     *
     * @return \DateTime 
     */
    public function getNatureDocDateActive(): ?string {
        return $this->natureDocDateActive;
    }

    /**
     * Add medias
     *
     * @param \App\Entity\Media $medias
     * @return NatureDoc
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
     * 
     */
    public function preAjout() {

        $this->natureDocDateAjout = new \DateTime();
        $this->statutNatureDoc = 0;
    }

    /**
     * Set statutNatureDoc
     *
     * @param integer $statutNatureDoc
     * @return NatureDoc
     */
    public function setStatutNatureDoc(string $statutNatureDoc): self {
        $this->statutNatureDoc = $statutNatureDoc;

        return $this;
    }

    /**
     * Get statutNatureDoc
     *
     * @return integer 
     */
    public function getStatutNatureDoc(): ?string {
        return $this->statutNatureDoc;
    }

    /**
     * Set natureDocDesactivePar
     *
     * @param integer $natureDocDesactivePar
     * @return NatureDoc
     */
    public function setNatureDocDesactivePar(string $natureDocDesactivePar): self {
        $this->natureDocDesactivePar = $natureDocDesactivePar;

        return $this;
    }

    /**
     * Get natureDocDesactivePar
     *
     * @return integer 
     */
    public function getNatureDocDesactivePar(): ?string {
        return $this->natureDocDesactivePar;
    }

    /**
     * Set natureDocDateDesactive
     *
     * @param \DateTime $natureDocDateDesactive
     * @return NatureDoc
     */
    public function setNatureDocDateDesactive(string $natureDocDateDesactive): self {
        $this->natureDocDateDesactive = $natureDocDateDesactive;

        return $this;
    }

    /**
     * Get natureDocDateDesactive
     *
     * @return \DateTime 
     */
    public function getNatureDocDateDesactive(): ?string {
        return $this->natureDocDateDesactive;
    }

}
