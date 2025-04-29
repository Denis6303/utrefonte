<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Classe qui va gere les questions de sondages du site web
 *
 * @author Gautier
 * #[ORM\Entity](repositoryClass="App\Entity\SondageRepository")
 * #[ORM\Table(name="adresseip")]
 */
class AdresseIp {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idip", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var string $ip
     * #[ORM\Column(name="question" , type="string")]
     */
    private $ip;

    /**
     * @var SondageOpinion $sondageopinion
     * #[ORM\ManyToOne(targetEntity: App\Entity\SondageOpinion::class, inversedBy="adresseips", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idopinion", referencedColumnName="idopinion")
     * })
     */
    private $sondageopinion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return AdresseIp
     */
    public function setIp(string $ip): self {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp(): ?string {
        return $this->ip;
    }

    /**
     * Set sondageopinion
     *
     * @param \App\Entity\SondageOpinion $sondageopinion
     * @return AdresseIp
     */
    public function setSondageopinion(\App\Entity\SondageOpinion $sondageopinion = null) {
        $this->sondageopinion = $sondageopinion;

        return $this;
    }

    /**
     * Get sondageopinion
     *
     * @return \App\Entity\SondageOpinion 
     */
    public function getSondageopinion(): ?string {
        return $this->sondageopinion;
    }

}
