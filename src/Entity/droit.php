<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * droit
 *
 * #[ORM\Table()]
 * #[ORM\Entity](repositoryClass="App\Entity\droitRepository")
 */
class droit {

    /**
     * @var integer
     *
     * #[ORM\Column(name="id", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * #[ORM\Column(name="droits", type="text")]
     */
    private $droits;

    /**
     * @var Profil $profil
     * #[ORM\ManyToOne(targetEntity: App\Entity\Profil::class, inversedBy="droit", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idprofil", referencedColumnName="idprofil")
     * })
     */
    private $profil;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set droits
     *
     * @param string $droits
     * @return droit
     */
    public function setDroits(string $droits): self {
        $this->droits = $droits;

        return $this;
    }

    /**
     * Get droits
     *
     * @return string 
     */
    public function getDroits(): ?string {
        return $this->droits;
    }

    /**
     * Set profil
     *
     * @param \App\Entity\Profil $profil
     * @return Action
     */
    public function setProfil(\App\Entity\Profil $profil = null) {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return \App\Entity\Profil 
     */
    public function getProfil(): ?string {
        return $this->profil;
    }

}
