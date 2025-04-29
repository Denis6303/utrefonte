<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\ProfilClientRepository")
 * #[ORM\Table(name="profilclient")]
 */
class ProfilClient {

    function __construct() {
        $this->etatProfil = 1;
        $this->suppr = 0;
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idprofil", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * 
     * @var string $libProfil
     * #[ORM\Column(name="libprofil",type="string",length=70)]
     * #[Assert\NotBlank(message=" Le libellé du profil ne peut être vide ")]
     * @Assert\MinLength(3)
     */
    private $libProfil;

    /**
     * @var integer $etatProfil
     * #[ORM\Column(name="etatprofil",type="integer" )]
     *   
     */
    private $etatProfil;

    /**
     * @var integer $typeProfil
     * #[ORM\Column(name="typeprofil",type="integer" )]
     *   
     */
    private $typeProfil;

    /**
     * @var integer $ordre
     * #[ORM\Column(name="ordre",type="integer", nullable=True)]
     *   
     */
    private $ordre;

    /**
     * @var integer $suppr
     * #[ORM\Column(name="suppr", type="integer", nullable=True)]
     * #[Assert\NotBlank()]  
     */
    private $suppr;
    
    /**
     * @var ArrayCollection Utilisateur $utilisateurs
     * #[ORM\OneToMany(targetEntity: App\Entity\Utilisateur::class, mappedBy="Profil")]
     * 
     */
    private $utilisateurs;

    /**
     * @var ArrayCollection Abonne $abonnes
     * #[ORM\OneToMany(targetEntity: App\Entity\Abonne::class, mappedBy="Profil")]
     * 
     */
    private $abonnes;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    
    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return ProfilClient
     */
    public function setOrdre(string $ordre): self {
        $this->libProfil = $ordre;

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
    
    /**
     * Set libProfil
     *
     * @param string $libProfil
     * @return ProfilClient
     */
    public function setLibProfil(string $libProfil): self {
        $this->libProfil = $libProfil;

        return $this;
    }

    /**
     * Get libProfil
     *
     * @return string 
     */
    public function getLibProfil(): ?string {
        return $this->libProfil;
    }

    /**
     * Set etatProfil
     *
     * @param integer $etatProfil
     * @return ProfilClient
     */
    public function setEtatProfil(string $etatProfil): self {
        $this->etatProfil = $etatProfil;

        return $this;
    }

    /**
     * Get etatProfil
     *
     * @return integer 
     */
    public function getEtatProfil(): ?string {
        return $this->etatProfil;
    }

    /**
     * Add utilisateurs
     *
     * @param \App\Entity\Utilisateur $utilisateurs
     * @return ProfilClient
     */
    public function addUtilisateur(\App\Entity\Utilisateur $utilisateurs) {
        $this->utilisateurs[] = $utilisateurs;

        return $this;
    }

    /**
     * Remove utilisateurs
     *
     * @param \App\Entity\Utilisateur $utilisateurs
     */
    public function removeUtilisateur(\App\Entity\Utilisateur $utilisateurs) {
        $this->utilisateurs->removeElement($utilisateurs);
    }

    /**
     * Get utilisateurs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUtilisateurs(): ?string {
        return $this->utilisateurs;
    }

    /**
     * Add abonnes
     *
     * @param \App\Entity\Abonne $abonnes
     * @return ProfilClient
     */
    public function addAbonne(\App\Entity\Abonne $abonnes) {
        $this->abonnes[] = $abonnes;

        return $this;
    }

    /**
     * Remove abonnes
     *
     * @param \App\Entity\Abonne $abonnes
     */
    public function removeAbonne(\App\Entity\Abonne $abonnes) {
        $this->abonnes->removeElement($abonnes);
    }

    /**
     * Get abonnes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAbonnes(): ?string {
        return $this->abonnes;
    }

    /**
     * Set typeProfil
     *
     * @param integer $typeProfil
     * @return ProfilClient
     */
    public function setTypeProfil(string $typeProfil): self {
        $this->typeProfil = $typeProfil;

        return $this;
    }

    /**
     * Get typeProfil
     *
     * @return integer 
     */
    public function getTypeProfil(): ?string {
        return $this->typeProfil;
    }
        
    /**
     * Set suppr
     *
     * @param integer $suppr
     * @return ProfilClient
     */
    public function setSuppr(string $suppr): self {
        $this->suppr = $suppr;

        return $this;
    }

    /**
     * Get suppr
     *
     * @return integer 
     */
    public function getSuppr(): ?string {
        return $this->suppr;
    }

}
