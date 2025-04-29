<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity
 *
 * #[ORM\Table(name="message")]
 * #[ORM\Entity](repositoryClass="App\Entity\MessageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Message {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idmessage", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $titreMessage
     * #[ORM\Column(name="titreMessage",type="string",length=100)]
     * #[Assert\NotBlank(message="Le titre du message ne peut être vide! ")]
     * @Assert\MinLength(2)
     */
    private $titreMessage;

    /**
     * @var text $contenuMessage
     * #[ORM\Column(name="contenuMessage",type="text",nullable=true)]
     * @Assert\MinLength(2)
     */
    private $contenuMessage;

    /**
     * @var Internaute $internaute
     * #[ORM\ManyToOne(targetEntity: App\Entity\Internaute::class, inversedBy="messages", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="mailinternaute", referencedColumnName="mailinternaute")
     * })
     */
    private $internaute;

    /**
     * @var integer $corbeilleMessage
     * #[ORM\Column(name="corbeilleMessage",type="integer")]
     */
    private $corbeilleMessage;

    /**
     * @var integer $messageLu
     * #[ORM\Column(name="messageLu",type="integer")]
     */
    private $messageLu;

    /**
     * @var Service $service
     * #[ORM\ManyToOne(targetEntity: App\Entity\Service::class, inversedBy="messages", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idservice", referencedColumnName="idservice")
     * })
     */
    private $service;

    /**
     * @var Objet $objet
     * #[ORM\ManyToOne(targetEntity: App\Entity\Objet::class, inversedBy="messages", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idobjet", referencedColumnName="idobjet")
     * })
     */
    private $objet;

    /**
     * @var string $dateEnvoi
     * #[ORM\Column(name="dateenvoi",type="datetime")]
     * @Assert\MinLength(2)
     */
    private $dateEnvoi;

    /**
     * @var ArrayCollection MessageReponse $messagereponses
     * #[ORM\OneToMany(targetEntity: App\Entity\MessageReponse::class, mappedBy="Message" )]
     * 
     */
    private $messagereponses;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set titreMessage
     *
     * @param string $titreMessage
     * @return Message
     */
    public function setTitreMessage(string $titreMessage): self {
        $this->titreMessage = $titreMessage;

        return $this;
    }

    /**
     * Get titreMessage
     *
     * @return string 
     */
    public function getTitreMessage(): ?string {
        return $this->titreMessage;
    }

    /**
     * Set contenuMessage
     *
     * @param text $contenuMessage
     * @return Message
     */
    public function setContenuMessage(string $contenuMessage): self {
        $this->contenuMessage = $contenuMessage;

        return $this;
    }

    /**
     * Get contenuMessage
     *
     * @return text 
     */
    public function getContenuMessage(): ?string {
        return $this->contenuMessage;
    }

    /**
     * Set corbeilleMessage
     *
     * @param integer $corbeilleMessage
     * @return Message
     */
    public function setCorbeilleMessage(string $corbeilleMessage): self {
        $this->corbeilleMessage = $corbeilleMessage;

        return $this;
    }

    /**
     * Get corbeilleMessage
     *
     * @return integer 
     */
    public function getCorbeilleMessage(): ?string {
        return $this->corbeilleMessage;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return Message
     */
    public function setDateEnvoi(string $dateEnvoi): self {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime 
     */
    public function getDateEnvoi(): ?string {
        return $this->dateEnvoi;
    }

    /**
     * Set internaute
     *
     * @param \App\Entity\Internaute $internaute
     * @return Message
     */
    public function setInternaute(\App\Entity\Internaute $internaute = null) {
        $this->internaute = $internaute;

        return $this;
    }

    /**
     * Get internaute
     *
     * @return \App\Entity\Internaute 
     */
    public function getInternaute(): ?string {
        return $this->internaute;
    }

    /**
     * Set service
     *
     * @param \App\Entity\Service $service
     * @return Message
     */
    public function setService(\App\Entity\Service $service = null) {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \App\Entity\Service 
     */
    public function getService(): ?string {
        return $this->service;
    }

    /**
     * Set objet
     *
     * @param \App\Entity\Objet $objet
     * @return Message
     */
    public function setObjet(\App\Entity\Objet $objet = null) {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return \App\Entity\Objet 
     */
    public function getObjet(): ?string {
        return $this->objet;
    }

    /**
     * Add messagereponses
     *
     * @param \App\Entity\MessageReponse $messagereponses
     * @return Message
     */
    public function addMessagereponse(\App\Entity\MessageReponse $messagereponses) {
        $this->messagereponses[] = $messagereponses;

        return $this;
    }

    /**
     * Remove messagereponses
     *
     * @param \App\Entity\MessageReponse $messagereponses
     */
    public function removeMessagereponse(\App\Entity\MessageReponse $messagereponses) {
        $this->messagereponses->removeElement($messagereponses);
    }

    /**
     * Get messagereponses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessagereponses(): ?string {
        return $this->messagereponses;
    }

    /**
     * Set messageLu
     *
     * @param integer $messageLu
     * @return Message
     */
    public function setMessageLu(string $messageLu): self {
        $this->messageLu = $messageLu;

        return $this;
    }

    /**
     * Get messageLu
     *
     * @return integer 
     */
    public function getMessageLu(): ?string {
        return $this->messageLu;
    }

}
