<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="fonds")]
 * #[ORM\Entity](repositoryClass="App\Entity\FondsRepository")
 *
 */
class Fonds {

    function __construct() {
        //$this->etat = 0;
        $this->suppr = 0;
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idfonds", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $codeFonds
     * #[ORM\Column(name="codefonds",type="string",length=10)]
     * #[Assert\NotBlank(message=" Le libellé de la table ne peut être vide ")]
     * @Assert\MinLength(2)
     */
    private $codeFonds;

    /**
     * @var string $libFonds
     * #[ORM\Column(name="libfonds",type="string",length=100)]
     * #[Assert\NotBlank(message=" Le libellé de la table ne peut être vide ")]
     * @Assert\MinLength(2)
     */
    private $libFonds;

    /**
     * @var ArrayCollection Compte $comptes
     * #[ORM\OneToMany(targetEntity: App\Entity\Compte::class, mappedBy="Fonds" )]
     * 
     */
    private $comptes;

    /**
     * @var Utilisateur $utilisateur
     * #[ORM\ManyToOne(targetEntity: App\Entity\Utilisateur::class, inversedBy="Fonds", cascade={"persist","merge"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idutilisateur", referencedColumnName="idutilisateur")
     * })
     */
    private $utilisateur;

    /**
     * @var integer $etatFonds
     * #[ORM\Column(name="etatfonds",type="integer" )]
     *   
     */
    private $etatFonds;
    
    /**
     * @var integer $suppr
     * #[ORM\Column(name="suppr", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $suppr;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set codeFonds
     *
     * @param string $codeFonds
     * @return Fonds
     */
    public function setCodeFonds(string $codeFonds): self {
        $this->codeFonds = $codeFonds;

        return $this;
    }

    /**
     * Get codeFonds
     *
     * @return string 
     */
    public function getCodeFonds(): ?string {
        return $this->codeFonds;
    }

    /**
     * Set libFonds
     *
     * @param string $libFonds
     * @return Fonds
     */
    public function setLibFonds(string $libFonds): self {
        $this->libFonds = $libFonds;

        return $this;
    }

    /**
     * Get libFonds
     *
     * @return string 
     */
    public function getLibFonds(): ?string {
        return $this->libFonds;
    }

    /**
     * Add comptes
     *
     * @param \App\Entity\Compte $comptes
     * @return Fonds
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
     * Set etatFonds
     *
     * @param integer $etatFonds
     * @return Fonds
     */
    public function setEtatFonds(string $etatFonds): self {
        $this->etatFonds = $etatFonds;

        return $this;
    }

    /**
     * Get etatFonds
     *
     * @return integer 
     */
    public function getEtatFonds(): ?string {
        return $this->etatFonds;
    }

    /**
     * Set utilisateur
     *
     * @param \App\Entity\Utilisateur $utilisateur
     * @return Fonds
     */
    public function setUtilisateur(\App\Entity\Utilisateur $utilisateur = null) {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \App\Entity\Utilisateur 
     */
    public function getUtilisateur(): ?string {
        return $this->utilisateur;
    }
    
    /**
     * Set suppr
     *
     * @param integer $suppr
     * @return Fonds
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
