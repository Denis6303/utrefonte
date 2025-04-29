<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity
 *
 * #[ORM\Table(name="messagereponse")]
 * #[ORM\Entity](repositoryClass="App\Entity\MessageReponseRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MessageReponse {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idmessagereponse", type="integer")]
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
     * #[ORM\Column(name="contenuMessage",type="text")]
     * #[Assert\NotBlank(message="Le titre du message ne peut être vide! ")]
     * @Assert\MinLength(2)
     */
    private $contenuMessage;

    /**
     * @var string $dateEnvoi
     * #[ORM\Column(name="dateEnvoi",type="datetime")]
     * @Assert\MinLength(2)
     */
    private $dateEnvoi;

    /**
     * @var User $user
     * #[ORM\ManyToOne(targetEntity: App\Entity\User::class, inversedBy="messagerenponces", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var Message $message
     * #[ORM\ManyToOne(targetEntity: App\Entity\Message::class, inversedBy="messagerenponces", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idmessage", referencedColumnName="idmessage")
     * })
     */
    private $message;

    /**
     * @var integer $messageLu
     * #[ORM\Column(name="messageLu",type="integer")]
     */
    private $messageLu;

    /**
     * @var text $destinatairesMsg
     * #[ORM\Column(name="destinataireMsg",type="text")]
     */
    private $destinatairesMsg;

    /**
     * @ORM\PrePersist()
     * 
     */
    public function preAjout() {
        $this->dateEnvoi = new \DateTime();
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
     * Set titreMessage
     *
     * @param string $titreMessage
     * @return MessageReponce
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
     * @param string $contenuMessage
     * @return MessageReponce
     */
    public function setContenuMessage(string $contenuMessage): self {
        $this->contenuMessage = $contenuMessage;

        return $this;
    }

    /**
     * Get contenuMessage
     *
     * @return string 
     */
    public function getContenuMessage(): ?string {
        return $this->contenuMessage;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return MessageReponce
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
     * Set datetime
     *
     * @param \App\Entity\Internaute $datetime
     * @return MessageReponce
     */
    public function setDatetime(\App\Entity\Internaute $datetime = null) {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \App\Entity\Internaute 
     */
    public function getDatetime(): ?string {
        return $this->datetime;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     * @return MessageReponce
     */
    public function setUser(\App\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User 
     */
    public function getUser(): ?string {
        return $this->user;
    }

    /**
     * Set message
     *
     * @param \App\Entity\Message $message
     * @return MessageReponce
     */
    public function setMessage(\App\Entity\Message $message = null) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \App\Entity\Message 
     */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * Set messageLu
     *
     * @param integer $messageLu
     * @return MessageReponse
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

    /**
     * Add internautes
     *
     * @param \App\Entity\Internaute $internautes
     * @return MessageReponse
     */
    public function addInternaute(\App\Entity\Internaute $internautes) {
        $this->internautes[] = $internautes;

        return $this;
    }

    /**
     * Remove internautes
     *
     * @param \App\Entity\Internaute $internautes
     */
    public function removeInternaute(\App\Entity\Internaute $internautes) {
        $this->internautes->removeElement($internautes);
    }

    /**
     * Get internautes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInternautes(): ?string {
        return $this->internautes;
    }

    /**
     * Set destinatairesMsg
     *
     * @param string $destinatairesMsg
     * @return MessageReponse
     */
    public function setDestinatairesMsg(string $destinatairesMsg): self {
        $this->destinatairesMsg = $destinatairesMsg;

        return $this;
    }

    /**
     * Get destinatairesMsg
     *
     * @return string 
     */
    public function getDestinatairesMsg(): ?string {
        return $this->destinatairesMsg;
    }

}
