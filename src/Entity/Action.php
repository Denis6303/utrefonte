<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\ActionRepository")
 * #[ORM\Table(name="action")]
 */
class Action {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idaction", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var string $libAction
     * #[ORM\Column(name="libaction",type="string",length=50)]
     * #[Assert\NotBlank(message="Libellé requis! ")]
     * @Assert\MinLength(3)
     */
    private $libAction;

    /**
     * @var string $descriptionAction
     * #[ORM\Column(name="descriptionaction" , type="string", nullable=True)]
     * 
     *   
     */
    private $descriptionAction;

    /**
     * @var integer $client
     * #[ORM\Column(name="client", type="integer" ,length=1)]
     * #[Assert\NotBlank()]  
     */
    private $client;

    /**
     * @var Controleur $controleur
     * #[ORM\ManyToOne(targetEntity: App\Entity\Controleur::class, inversedBy="actions", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idcontroleur", referencedColumnName="idcontroleur")
     * })
     */
    private $controleur;

    /**
     * @var Module $module
     * #[ORM\ManyToOne(targetEntity: App\Entity\Module::class, inversedBy="actions", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idmodule", referencedColumnName="idmodule")
     * })
     */
    private $module;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libAction
     *
     * @param string $libAction
     * @return Action
     */
    public function setLibAction(string $libAction): self {
        $this->libAction = $libAction;

        return $this;
    }

    /**
     * Get libAction
     *
     * @return string 
     */
    public function getLibAction(): ?string {
        return $this->libAction;
    }

    /**
     * Set descriptionAction
     *
     * @param string $descriptionAction
     * @return Action
     */
    public function setDescriptionAction(string $descriptionAction): self {
        $this->descriptionAction = $descriptionAction;

        return $this;
    }

    /**
     * Get descriptionAction
     *
     * @return string 
     */
    public function getDescriptionAction(): ?string {
        return $this->descriptionAction;
    }

    /**
     * Set controleur
     *
     * @param \App\Entity\Controleur $controleur
     * @return Action
     */
    public function setControleur(\App\Entity\Controleur $controleur = null) {
        $this->controleur = $controleur;

        return $this;
    }

    /**
     * Get controleur
     *
     * @return \App\Entity\Controleur 
     */
    public function getControleur(): ?string {
        return $this->controleur;
    }

    /**
     * Set module
     *
     * @param \App\Entity\Module $module
     * @return Action
     */
    public function setModule(\App\Entity\Module $module = null) {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return \App\Entity\Module 
     */
    public function getModule(): ?string {
        return $this->module;
    }

    /**
     * Set client
     *
     * @param integer $client
     * @return Action
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

}
