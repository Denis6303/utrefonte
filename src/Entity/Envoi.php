<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity
 *
 * #[ORM\Table(name="envoi")]
 * #[ORM\Entity](repositoryClass="App\Entity\EnvoiRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Envoi {

    function __construct() {
        $this->typeMessage=0;
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idenvoi", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var Utulisateur $utilisateur
     * #[ORM\ManyToOne(targetEntity: App\Entity\Utilisateur::class, inversedBy="envois", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idutilisateur", referencedColumnName="idutilisateur")
     * })
     */
    private $utilisateur;

    /**
     * @var MessageClient $messageclient
     * #[ORM\ManyToOne(targetEntity: App\Entity\MessageClient::class, inversedBy="envois", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idmessageclient", referencedColumnName="idmessageclient")
     * })
     */
    private $messageclient;

    /**
     * @var Abonne $abonne
     * #[ORM\ManyToOne(targetEntity: App\Entity\Abonne::class, inversedBy="envois", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idabonne", referencedColumnName="idabonne")
     * })
     */
    private $abonne;

    /**
     * @var integer $destUtil
     * #[ORM\Column(name="destutil", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $destUtil;

    /**
     * @var integer $destAb
     * #[ORM\Column(name="destab", type="integer")]  
     */
    private $destAb;

    /**
     * @var integer $statutMsg
     * #[ORM\Column(name="statutmsg", type="integer")]  
     */
    private $statutMsg;

    /**
     * @var integer $statutMsgEnvoye
     * #[ORM\Column(name="statutmsgenvoye", type="integer")]  
     */
    private $statutMsgEnvoye;

    /**
     * @var integer $msgParent
     * #[ORM\Column(name="msgparent", type="integer")]  
     */
    private $msgParent;

    /**
     * @var integer $msgLu
     * #[ORM\Column(name="msglu", type="integer")]  
     */
    private $msgLu;

    /**
     * @var integer $typeEnvoi
     * #[ORM\Column(name="typeenvoi", type="integer")]  
     */
    private $typeEnvoi;

    /**
     * @var datetime $dateEnvoiMsg
     * #[ORM\Column(name="dateenvoimsg", type="datetime")]  
     */
    private $dateEnvoiMsg;

    /**
     * @var integer $typeMessage
     * #[ORM\Column(name="typemessage", type="integer")]  
     */
    private $typeMessage;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set destUtil
     *
     * @param integer $destUtil
     * @return Envoi
     */
    public function setDestUtil(string $destUtil): self {
        $this->destUtil = $destUtil;

        return $this;
    }

    /**
     * Get destUtil
     *
     * @return integer 
     */
    public function getDestUtil(): ?string {
        return $this->destUtil;
    }

    /**
     * Set destAb
     *
     * @param integer $destAb
     * @return Envoi
     */
    public function setDestAb(string $destAb): self {
        $this->destAb = $destAb;

        return $this;
    }

    /**
     * Get destAb
     *
     * @return integer 
     */
    public function getDestAb(): ?string {
        return $this->destAb;
    }

    /**
     * Set utilisateur
     *
     * @param \App\Entity\Utilisateur $utilisateur
     * @return Envoi
     */
    public function setUtilisateur(\App\Entity\Utilisateur $utilisateur = null) {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \App\Entity\Utilisateur 
     */
    public function getUtilisateur(): ?string {
        return $this->utilisateur;
    }

    /**
     * Set messageclient
     *
     * @param \App\Entity\MessageClient $messageclient
     * @return Envoi
     */
    public function setMessageclient(\App\Entity\MessageClient $messageclient = null) {
        $this->messageclient = $messageclient;

        return $this;
    }

    /**
     * Get messageclient
     *
     * @return \App\Entity\MessageClient 
     */
    public function getMessageclient(): ?string {
        return $this->messageclient;
    }

    /**
     * Set abonne
     *
     * @param \App\Entity\Abonne $abonne
     * @return Envoi
     */
    public function setAbonne(\App\Entity\Abonne $abonne = null) {
        $this->abonne = $abonne;

        return $this;
    }

    /**
     * Get abonne
     *
     * @return \App\Entity\Abonne 
     */
    public function getAbonne(): ?string {
        return $this->abonne;
    }

    /**
     * Set statutMsg
     *
     * @param integer $statutMsg
     * @return Envoi
     */
    public function setStatutMsg(string $statutMsg): self {
        $this->statutMsg = $statutMsg;

        return $this;
    }

    /**
     * Get statutMsg
     *
     * @return integer 
     */
    public function getStatutMsg(): ?string {
        return $this->statutMsg;
    }

    /**
     * Set msgParent
     *
     * @param integer $msgParent
     * @return Envoi
     */
    public function setMsgParent(string $msgParent): self {
        $this->msgParent = $msgParent;

        return $this;
    }

    /**
     * Get msgParent
     *
     * @return integer 
     */
    public function getMsgParent(): ?string {
        return $this->msgParent;
    }

    /**
     * Set msgLu
     *
     * @param integer $msgLu
     * @return Envoi
     */
    public function setMsgLu(string $msgLu): self {
        $this->msgLu = $msgLu;

        return $this;
    }

    /**
     * Get msgLu
     *
     * @return integer 
     */
    public function getMsgLu(): ?string {
        return $this->msgLu;
    }

    /**
     * Set typeEnvoi
     *
     * @param integer $typeEnvoi
     * @return Envoi
     */
    public function setTypeEnvoi(string $typeEnvoi): self {
        $this->typeEnvoi = $typeEnvoi;

        return $this;
    }

    /**
     * Get typeEnvoi
     *
     * @return integer 
     */
    public function getTypeEnvoi(): ?string {
        return $this->typeEnvoi;
    }

    /**
     * Set dateEnvoiMsg
     *
     * @param \DateTime $dateEnvoiMsg
     * @return Envoi
     */
    public function setDateEnvoiMsg(string $dateEnvoiMsg): self {
        $this->dateEnvoiMsg = $dateEnvoiMsg;

        return $this;
    }

    /**
     * Get dateEnvoiMsg
     *
     * @return \DateTime 
     */
    public function getDateEnvoiMsg(): ?string {
        return $this->dateEnvoiMsg;
    }

    /**
     * Set statutMsgEnvoye
     *
     * @param integer $statutMsgEnvoye
     * @return Envoi
     */
    public function setStatutMsgEnvoye(string $statutMsgEnvoye): self {
        $this->statutMsgEnvoye = $statutMsgEnvoye;

        return $this;
    }

    /**
     * Get statutMsgEnvoye
     *
     * @return integer 
     */
    public function getStatutMsgEnvoye(): ?string {
        return $this->statutMsgEnvoye;
    }

    /**
     * Set typeMessage
     *
     * @param integer $typeMessage
     * @return Envoi
     */
    public function setTypeMessage(string $typeMessage): self {
        $this->typeMessage = $typeMessage;

        return $this;
    }

    /**
     * Get typeMessage
     *
     * @return integer 
     */
    public function getTypeMessage(): ?string {
        return $this->typeMessage;
    }

}
