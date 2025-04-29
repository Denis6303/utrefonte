<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="module")]
 * #[ORM\Entity](repositoryClass="App\Entity\ModuleRepository")
 *
 */
class Module {

    function __construct() {
        $this->ordre = 0;
    }

    /**
     * @var ArrayCollection Action $actions
     * #[ORM\OneToMany(targetEntity: App\Entity\Action::class, mappedBy="module" )]
     * 
     */
    private $actions;

    /**
     * @var integer $id
     * #[ORM\Column(name="idmodule", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var string $libmodule
     * #[ORM\Column(name="libmodule",type="string",length=100)]
     * #[Assert\NotBlank(message="Le Libellé de la  module ne peut être vide! ")]
     * @Assert\MinLength(2)
     */
    private $libmodule;

    /**
     * @var integer $client
     * #[ORM\Column(name="client", type="integer" ,length=1)]
     * #[Assert\NotBlank()]  
     */
    private $client;

    /**
     * @var integer $ordre
     * #[ORM\Column(name="ordre", type="integer" ,nullable=true)]
     */
    private $ordre;
    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libmodule
     *
     * @param string $libmodule
     * @return Module
     */
    public function setLibmodule(string $libmodule): self {
        $this->libmodule = $libmodule;

        return $this;
    }

    /**
     * Get libmodule
     *
     * @return string 
     */
    public function getLibmodule(): ?string {
        return $this->libmodule;
    }

    /**
     * Add actions
     *
     * @param \App\Entity\Action $actions
     * @return Module
     */
    public function addAction(\App\Entity\Action $actions) {
        $this->actions[] = $actions;

        return $this;
    }

    /**
     * Remove actions
     *
     * @param \App\Entity\Action $actions
     */
    public function removeAction(\App\Entity\Action $actions) {
        $this->actions->removeElement($actions);
    }

    /**
     * Get actions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActions(): ?string {
        return $this->actions;
    }

    /**
     * Set client
     *
     * @param integer $client
     * @return Module
     */
    public function setClient(string $client): self {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return integer 
     */
    public function getClient(): ?string {
        return $this->client;
    }

    
    /**
     * Set client
     *
     * @param integer $ordre
     * @return Module
     */
    public function setOrdre(string $ordre): self {
        $this->ordre= $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre(): ?string {
        return $this->ordre;
    }
    
}
