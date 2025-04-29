<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageReponce
 */
class MessageReponce {

    private $id;

    private $titreMessage;

    private $contenuMessage;

    private $dateEnvoi;

    private $user;

    private $message;

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
     * @ORM\PrePersist
     */
    public function preAjout() {
        // Add your code here
    }

}
