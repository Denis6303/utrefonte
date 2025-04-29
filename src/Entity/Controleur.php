<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\ControleurRepository")
 * #[ORM\Table(name="controleur")]
 */
class Controleur {

    function __construct() {
        //$this->etat = 0;
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idcontroleur", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nomControleur
     * #[ORM\Column(name="nomcontroleur",type="string",length=100)]
     * #[Assert\NotBlank(message="Libellé requis! ")]
     * @Assert\MinLength(3)
     */
    private $nomControleur;

    /**
     * @var string $description
     * #[ORM\Column(name="description",type="string",length=200, nullable=true)]
     * #[Assert\NotBlank(message="Description requise!")]
     * @Assert\MinLength(3)
     */
    private $description;

    /**
     * @var ArrayCollection Action $actions    
     * #[ORM\OneToMany(targetEntity: App\Entity\Action::class, mappedBy="Action")]
     */
    private $actions;

    /**
     * @var integer $client
     * #[ORM\Column(name="client", type="integer" ,length=1)]
     * #[Assert\NotBlank()]  
     */
    private $client;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set nomControleur
     *
     * @param string $nomControleur
     * @return Controleur
     */
    public function setNomControleur(string $nomControleur): self {
        $this->nomControleur = $nomControleur;

        return $this;
    }

    /**
     * Get nomControleur
     *
     * @return string 
     */
    public function getNomControleur(): ?string {
        return $this->nomControleur;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Controleur
     */
    public function setDescription(string $description): self {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Add actions
     *
     * @param \App\Entity\Action $actions
     * @return Controleur
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
     * @param integer $client
     * @return client
     */
    public function setClient(string $client): self {
        $this->client = $client;

        return $this;
    }

}
