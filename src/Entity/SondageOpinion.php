<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Classe qui va gere les opinions sur les sondages du site web
 *     
 * @author Gautier
 * #[ORM\Entity](repositoryClass="App\Entity\SondageOpinionRepository")
 * #[ORM\Table(name="sondageopinion")]
 */
class SondageOpinion {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idopinion", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var Sondage $sondage
     * #[ORM\ManyToOne(targetEntity: App\Entity\Sondage::class, inversedBy="sondageOpinnions", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idsondage", referencedColumnName="idsondage")
     * })
     */
    private $sondage;

    /**
     * @var ArrayCollection AdresseIp $adresseips
     * #[ORM\OneToMany(targetEntity: App\Entity\AdresseIp::class, mappedBy="sondageopinion" )]
     * 
     */
    private $adresseips;

    /**
     * @Gedmo\Translatable
     * @var string $reponse
     * #[ORM\Column(name="reponse" , type="string")]
     */
    private $reponse;

    /**
     * @var integer $nbReponse
     * #[ORM\Column(name="nbreponse" , type="integer")]
     *   
     */
    private $nbReponse;

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
     * Set reponse
     *
     * @param string $reponse
     * @return SondageOpinion
     */
    public function setReponse(string $reponse): self {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get reponse
     *
     * @return string 
     */
    public function getReponse(): ?string {
        return $this->reponse;
    }

    /**
     * Set nbReponse
     *
     * @param integer $nbReponse
     * @return SondageOpinion
     */
    public function setNbReponse(string $nbReponse): self {
        $this->nbReponse = $nbReponse;

        return $this;
    }

    /**
     * Get nbReponse
     *
     * @return integer 
     */
    public function getNbReponse(): ?string {
        return $this->nbReponse;
    }

    /**
     * Set sondage
     *
     * @param \App\Entity\Sondage $sondage
     * @return SondageOpinion
     */
    public function setSondage(\App\Entity\Sondage $sondage = null) {
        $this->sondage = $sondage;

        return $this;
    }

    /**
     * Get sondage
     *
     * @return \App\Entity\Sondage 
     */
    public function getSondage(): ?string {
        return $this->sondage;
    }

    /**
     * Add adresseips
     *
     * @param \App\Entity\AdresseIp $adresseips
     * @return SondageOpinion
     */
    public function addAdresseip(\App\Entity\AdresseIp $adresseips) {
        $this->adresseips[] = $adresseips;

        return $this;
    }

    /**
     * Remove adresseips
     *
     * @param \App\Entity\AdresseIp $adresseips
     */
    public function removeAdresseip(\App\Entity\AdresseIp $adresseips) {
        $this->adresseips->removeElement($adresseips);
    }

    /**
     * Get adresseips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdresseips(): ?string {
        return $this->adresseips;
    }

}
