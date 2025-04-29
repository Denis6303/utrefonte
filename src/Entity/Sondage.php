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
 * #[ORM\Table(name="sondage")]
 */
class Sondage {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idsondage", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var string $question
     * #[ORM\Column(name="question" , type="string")]
     */
    private $question;

    /**
     * @var integer $actif
     * #[ORM\Column(name="actif" , type="integer")]
     *   
     */
    private $actif;

    /**
     * @var integer $questionAjoutPar
     * #[ORM\Column(name="questionajoutpar" , type="integer")]
     *   
     */
    private $questionAjoutPar;

    /**
     * @var Datetime $questionDateAjout
     * #[ORM\Column(name="questiondateajout" , type="datetime")]
     *   
     */
    private $questionDateAjout;

    /**
     * @var ArrayCollection SondageOpinion $sondageOpinions
     * #[ORM\OneToMany(targetEntity: App\Entity\SondageOpinion::class, mappedBy="sondage" )]
     * 
     */
    private $sondageOpinions;

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
     * Set question
     *
     * @param string $question
     * @return Sondage
     */
    public function setQuestion(string $question): self {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion(): ?string {
        return $this->question;
    }

    /**
     * Set actif
     *
     * @param integer $actif
     * @return Sondage
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
     * Set questionAjoutPar
     *
     * @param integer $questionAjoutPar
     * @return Sondage
     */
    public function setQuestionAjoutPar(string $questionAjoutPar): self {
        $this->questionAjoutPar = $questionAjoutPar;

        return $this;
    }

    /**
     * Get questionAjoutPar
     *
     * @return integer 
     */
    public function getQuestionAjoutPar(): ?string {
        return $this->questionAjoutPar;
    }

    /**
     * Set questionDateAjout
     *
     * @param \DateTime $questionDateAjout
     * @return Sondage
     */
    public function setQuestionDateAjout(string $questionDateAjout): self {
        $this->questionDateAjout = $questionDateAjout;

        return $this;
    }

    /**
     * Get questionDateAjout
     *
     * @return \DateTime 
     */
    public function getQuestionDateAjout(): ?string {
        return $this->questionDateAjout;
    }

    /**
     * Add sondageOpinions
     *
     * @param \App\Entity\SondageOpinion $sondageOpinions
     * @return Sondage
     */
    public function addSondageOpinion(\App\Entity\SondageOpinion $sondageOpinions) {
        $this->sondageOpinions[] = $sondageOpinions;

        return $this;
    }

    /**
     * Remove sondageOpinions
     *
     * @param \App\Entity\SondageOpinion $sondageOpinions
     */
    public function removeSondageOpinion(\App\Entity\SondageOpinion $sondageOpinions) {
        $this->sondageOpinions->removeElement($sondageOpinions);
    }

    /**
     * Get sondageOpinions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSondageOpinions(): ?string {
        return $this->sondageOpinions;
    }

}
