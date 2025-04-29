<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\HistoriqueConnexionRepository")
 * #[ORM\Table(name="historique")]
 */
class HistoriqueConnexion {

    public function __construct() {
        $this->dateDeb = new \Datetime();
    }

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="idconnexion", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var datetime $dateDeb
     * #[ORM\Column(name="datedeb", type="datetime")]
     */
    private $dateDeb;

    /**
     * @var datetime $dateFin
     * #[ORM\Column(name="datefin", type="datetime",nullable=true)]
     */
    private $dateFin;

    /**
     * @var strinng $adresseIp
     * #[ORM\Column(name="adresseip", type="string",length=24,nullable=true)]
     */
    private $adresseIp;

    /**
     * @var strinng $lieu
     * #[ORM\Column(name="lieu", type="string",length=60,nullable=true)]
     */
    private $lieu;

    /**
     * @var private $duree
     * #[ORM\Column(name="duree", type="string",length=100,nullable=true)]
     */
    private $duree;

    /**
     * @var Utilisateur $utilisateur
     * #[ORM\ManyToOne(targetEntity: App\Entity\Utilisateur::class, inversedBy="historiques", cascade={"persist","merge"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idutilisateur", referencedColumnName="idutilisateur")
     * })
     */
    private $utilisateur;

    /**
     * @var Abonne $abonne
     * #[ORM\ManyToOne(targetEntity: App\Entity\Abonne::class, inversedBy="historiques", cascade={"persist","merge"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idabonne", referencedColumnName="idabonne")
     * })
     */
    private $abonne;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set dateDeb
     *
     * @param \DateTime $dateDeb
     * @return HistoriqueConnexion
     */
    public function setDateDeb(string $dateDeb): self {
        $this->dateDeb = $dateDeb;

        return $this;
    }

    /**
     * Get dateDeb
     *
     * @return \DateTime 
     */
    public function getDateDeb(): ?string {
        return $this->dateDeb;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return HistoriqueConnexion
     */
    public function setDateFin(string $dateFin): self {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin(): ?string {
        return $this->dateFin;
    }

    /**
     * Set adresseIp
     *
     * @param string $adresseIp
     * @return HistoriqueConnexion
     */
    public function setAdresseIp(string $adresseIp): self {
        $this->adresseIp = $adresseIp;

        return $this;
    }

    /**
     * Get adresseIp
     *
     * @return string 
     */
    public function getAdresseIp(): ?string {
        return $this->adresseIp;
    }

    /**
     * Set lieu
     *
     * @param string $lieu
     * @return HistoriqueConnexion
     */
    public function setLieu(string $lieu): self {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu
     *
     * @return string 
     */
    public function getLieu(): ?string {
        return $this->lieu;
    }

    /**
     * Set utilisateur
     *
     * @param \App\Entity\Utilisateur $utilisateur
     * @return HistoriqueConnexion
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
     * Set abonne
     *
     * @param \App\Entity\Abonne $abonne
     * @return HistoriqueConnexion
     */
    public function setAbonne(\App\Entity\Abonne $abonne = null) {
        $this->abonne = $abonne;

        return $this;
    }

    /**
     * Get abonne
     *
     * @return \App\Entity\Abonne 
     */
    public function getAbonne(): ?string {
        return $this->abonne;
    }

    /**
     * Set duree
     *
     * @param string $duree
     * @return HistoriqueConnexion
     */
    public function setDuree(string $duree): self {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return string 
     */
    public function getDuree(): ?string {
        return $this->duree;
    }

}
