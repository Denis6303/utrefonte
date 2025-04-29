<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity
 *
 * #[ORM\Table(name="internaute")]
 * #[ORM\Entity](repositoryClass="App\Entity\InternauteRepository")
 *
 */
class Internaute {

    function __construct() {
        $this->typeInternaute = 0;
    }

    /**
     * @var string $mailInternaute
     * #[ORM\Id]
     * #[ORM\Column(name="mailinternaute", type="string", length=100, nullable=true)]
     * @Assert\Email(message = "email '{{ value }}'  n'est pas valide.")
     */
    protected $mailInternaute;

    /**
     * @var string $nomPrenom
     * #[ORM\Column(name="nomprenom",type="string",length=100, nullable=true)]
     * @Assert\MinLength(2)
     */
    private $nomPrenom;

    /**
     * 
     * @var string $numeroCompte
     * #[ORM\Column(name="numerocompte", type="string",length=20, nullable=true)]	
     */
    private $numeroCompte;
    
    /**
     * @var text $ville
     * #[ORM\Column(name="ville",type="string")]
     */
    private $ville;

    /**
     * @var ArrayCollection Message $messages
     * #[ORM\OneToMany(targetEntity: App\Entity\Message::class, mappedBy="Internaute" )]
     * 
     */
    private $messages;

    /**
     * @var Pays $pays
     * #[ORM\ManyToOne(targetEntity: App\Entity\Pays::class, inversedBy="internaute", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idpays", referencedColumnName="idpays")
     * })
     */
    private $pays;

    /**
     * @var integer $typeInternaute
     * #[ORM\Column(name="typeinternaute",type="integer")]
     */
    private $typeInternaute;

    /**
     * Set mailInternaute
     *
     * @param string $mailInternaute
     * @return Internaute
     */
    public function setMailInternaute(string $mailInternaute): self {
        $this->mailInternaute = $mailInternaute;

        return $this;
    }

    /**
     * Get mailInternaute
     *
     * @return string 
     */
    public function getMailInternaute(): ?string {
        return $this->mailInternaute;
    }

    /**
     * Set nomPrenom
     *
     * @param string $nomPrenom
     * @return Internaute
     */
    public function setNomPrenom(string $nomPrenom): self {
        $this->nomPrenom = $nomPrenom;

        return $this;
    }

    /**
     * Get nomPrenom
     *
     * @return string 
     */
    public function getNomPrenom(): ?string {
        return $this->nomPrenom;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Internaute
     */
    public function setVille(string $ville): self {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille(): ?string {
        return $this->ville;
    }

    /**
     * Add messages
     *
     * @param \App\Entity\Message $messages
     * @return Internaute
     */
    public function addMessage(\App\Entity\Message $messages) {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \App\Entity\Message $messages
     */
    public function removeMessage(\App\Entity\Message $messages) {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages(): ?string {
        return $this->messages;
    }

    /**
     * Set pays
     *
     * @param \App\Entity\Pays $pays
     * @return Internaute
     */
    public function setPays(\App\Entity\Pays $pays = null) {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return \App\Entity\Pays 
     */
    public function getPays(): ?string {
        return $this->pays;
    }

    /**
     * Set typeInternaute
     *
     * @param integer $typeInternaute
     * @return Internaute
     */
    public function setTypeInternaute(string $typeInternaute): self {
        $this->typeInternaute = $typeInternaute;

        return $this;
    }

    /**
     * Get typeInternaute
     *
     * @return integer 
     */
    public function getTypeInternaute(): ?string {
        return $this->typeInternaute;
    }


    /**
     * Set numeroCompte
     *
     * @param string $numeroCompte
     * @return Internaute
     */
    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;
    
        return $this;
    }

    /**
     * Get numeroCompte
     *
     * @return string 
     */
    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }
}
