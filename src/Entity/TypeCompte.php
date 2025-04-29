<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\TypeCompteRepository")
 * #[ORM\Table(name="typecompte")]
 */
class TypeCompte {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idtypecompte", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * 
     * @var string $libTypeCompte
     * #[ORM\Column(name="libtypecompte",type="string",length=70)]
     * #[Assert\NotBlank(message=" Le libellé du profil ne peut être vide ")]
     * @Assert\MinLength(3)
     */
    private $libTypeCompte;

    /**
     * @var ArrayCollection Compte $comptes
     * #[ORM\OneToMany(targetEntity: App\Entity\Compte::class, mappedBy="typeCompte")]
     * 
     */
    private $comptes;

    /**
     * @var ArrayCollection CompteInexistant $compteInexistants
     * #[ORM\OneToMany(targetEntity: App\Entity\CompteInexistant::class, mappedBy="typeCompte")]
     * 
     */
    private $compteInexistants;

    /**
     * @var ArrayCollection Chargement $chargements
     * #[ORM\OneToMany(targetEntity: App\Entity\Chargement::class, mappedBy="typeCompte")]
     * 
     */
    private $chargements;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libTypeCompte
     *
     * @param string $libTypeCompte
     * @return TypeCompte
     */
    public function setLibTypeCompte(string $libTypeCompte): self {
        $this->libTypeCompte = $libTypeCompte;

        return $this;
    }

    /**
     * Get libTypeCompte
     *
     * @return string 
     */
    public function getLibTypeCompte(): ?string {
        return $this->libTypeCompte;
    }

    /**
     * Add comptes
     *
     * @param \App\Entity\Compte $comptes
     * @return TypeCompte
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
     * Add compteInexistants
     *
     * @param \App\Entity\CompteInexistant $compteInexistants
     * @return TypeCompte
     */
    public function addCompteInexistant(\App\Entity\CompteInexistant $compteInexistants) {
        $this->compteInexistants[] = $compteInexistants;

        return $this;
    }

    /**
     * Remove compteInexistants
     *
     * @param \App\Entity\CompteInexistant $compteInexistants
     */
    public function removeCompteInexistant(\App\Entity\CompteInexistant $compteInexistants) {
        $this->compteInexistants->removeElement($compteInexistants);
    }

    /**
     * Get compteInexistants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompteInexistants(): ?string {
        return $this->compteInexistants;
    }

    /**
     * Add chargements
     *
     * @param \App\Entity\Chargement $chargements
     * @return TypeCompte
     */
    public function addChargement(\App\Entity\Chargement $chargements) {
        $this->chargements[] = $chargements;

        return $this;
    }

    /**
     * Remove chargements
     *
     * @param \App\Entity\Chargement $chargements
     */
    public function removeChargement(\App\Entity\Chargement $chargements) {
        $this->chargements->removeElement($chargements);
    }

    /**
     * Get chargements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChargements(): ?string {
        return $this->chargements;
    }

}
