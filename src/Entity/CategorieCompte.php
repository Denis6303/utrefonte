<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\CategorieCompteRepository")
 * #[ORM\Table(name="categoriecompte")]
 */
class CategorieCompte {

    public function __construct() {
        $this->active = 1;
    }

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="codecategorie", type="string",length=4)]
     */
    protected $codecategorie;

    /**
     * @var string $libCategorie
     * #[ORM\Column(name="libcategorie", type="string",length=50)]
     */
    private $libCategorie;

    /**
     * @var integer $active
     * #[ORM\Column(name="active", type="integer")]
     */
    private $active;

    /**
     * @var integer $sicarte
     * #[ORM\Column(name="sicarte", type="integer")]
     */
    private $sicarte;

    /**
     * @var integer $sicheque
     * #[ORM\Column(name="sicheque", type="integer")]
     */
    private $sicheque;

    /**
     * Set libCategorie
     *
     * @param string $libCategorie
     * @return CategorieCompte
     */
    public function setLibCategorie(string $libCategorie): self {
        $this->libCategorie = $libCategorie;

        return $this;
    }

    /**
     * Get libCategorie
     *
     * @return string 
     */
    public function getLibCategorie(): ?string {
        return $this->libCategorie;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return CategorieCompte
     */
    public function setActive(string $active): self {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer 
     */
    public function getActive(): ?string {
        return $this->active;
    }

    /**
     * Add comptes
     *
     * @param \App\Entity\Compte $comptes
     * @return CategorieCompte
     */
    public function addCompte(\App\Entity\Compte $comptes) {
        $this->comptes[] = $comptes;

        return $this;
    }

    /**
     * Remove comptes
     *
     * @param \App\Entity\Compte $comptes
     */
    public function removeCompte(\App\Entity\Compte $comptes) {
        $this->comptes->removeElement($comptes);
    }

    /**
     * Get comptes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComptes(): ?string {
        return $this->comptes;
    }

    /**
     * Set codecategorie
     *
     * @param string $codecategorie
     * @return CategorieCompte
     */
    public function setCodecategorie(string $codecategorie): self {
        $this->codecategorie = $codecategorie;

        return $this;
    }

    /**
     * Get codecategorie
     *
     * @return string 
     */
    public function getCodecategorie(): ?string {
        return $this->codecategorie;
    }

    /**
     * Set sicarte
     *
     * @param integer $sicarte
     * @return CategorieCompte
     */
    public function setSicarte(string $sicarte): self {
        $this->sicarte = $sicarte;

        return $this;
    }

    /**
     * Get sicarte
     *
     * @return integer 
     */
    public function getSicarte(): ?string {
        return $this->sicarte;
    }

    /**
     * Set sicheque
     *
     * @param integer $sicheque
     * @return CategorieCompte
     */
    public function setSicheque(string $sicheque): self {
        $this->sicheque = $sicheque;

        return $this;
    }

    /**
     * Get sicheque
     *
     * @return integer 
     */
    public function getSicheque(): ?string {
        return $this->sicheque;
    }

}
