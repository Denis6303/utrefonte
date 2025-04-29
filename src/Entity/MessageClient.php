<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity
 *
 * #[ORM\Table(name="messageclient")]
 * #[ORM\Entity](repositoryClass="App\Entity\MessageClientRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MessageClient {

    function __construct() {
        
      $this->messageSysteme=0; 
      
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idmessageclient", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $objetMessageClient
     * #[ORM\Column(name="objetmessageclient",type="string",length=100)]
     * @Assert\MinLength(2)
     */
    private $objetMessageClient;

    /**
     * @var text $contenuMessageClient
     * #[ORM\Column(name="contenumessageclient",type="text",nullable=true)]
     * @Assert\MinLength(2)
     */
    private $contenuMessageClient;

    /**
     * @var integer $messageSysteme
     * #[ORM\Column(name="messageSysteme", type="integer",nullable=true)]
     * #[Assert\NotBlank()]  
     */
    private $messageSysteme;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set objetMessageClient
     *
     * @param string $objetMessageClient
     * @return MessageClient
     */
    public function setObjetMessageClient(string $objetMessageClient): self {
        $this->objetMessageClient = $objetMessageClient;

        return $this;
    }

    /**
     * Get objetMessageClient
     *
     * @return string 
     */
    public function getObjetMessageClient(): ?string {
        return $this->objetMessageClient;
    }

    /**
     * Set contenuMessageClient
     *
     * @param text $contenuMessageClient
     * @return MessageClient
     */
    public function setContenuMessageClient(string $contenuMessageClient): self {
        $this->contenuMessageClient = $contenuMessageClient;

        return $this;
    }

    /**
     * Get contenuMessageClient
     *
     * @return text 
     */
    public function getContenuMessageClient(): ?string {
        return $this->contenuMessageClient;
    }


    /**
     * Set messageSysteme
     *
     * @param integer $messageSysteme
     * @return MessageClient
     */
    public function setMessageSysteme(string $messageSysteme): self
    {
        $this->messageSysteme = $messageSysteme;
    
        return $this;
    }

    /**
     * Get messageSysteme
     *
     * @return integer 
     */
    public function getMessageSysteme(): ?string
    {
        return $this->messageSysteme;
    }
}
