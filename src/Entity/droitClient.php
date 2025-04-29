<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * droit
 *
 * #[ORM\Table(name="droitclient")]
 * #[ORM\Entity](repositoryClass="App\Entity\droitClientRepository")
 */
class droitClient {

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
     * #[ORM\ManyToOne(targetEntity: App\Entity\ProfilClient::class, inversedBy="droit", cascade={ "persist"})]
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
     * @param \App\Entity\ProfilClient $profil
     * @return droit
     */
    public function setProfil(\App\Entity\ProfilClient $profil = null) {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return \App\Entity\ProfilClient 
     */
    public function getProfil(): ?string {
        return $this->profil;
    }

}
