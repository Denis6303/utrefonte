<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\AbonneRepository")
 * #[ORM\Table(name="abonne")]
 */
class Abonne implements UserInterface {

    //constructeur    
    public function __construct() {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->attempt = 0;
        $this->suppr = 0;
        $this->controleCode=0;
        $this->genPsswd = "";
    }

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="idabonne", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $username
     * #[ORM\Column(name="username", type="string",length=50)]
     */
    private $username;

    /**
     * @var string $nomPrenom
     * #[ORM\Column(name="nomprenom", type="string",length=100)]
     * #[Assert\NotBlank(message="Please enter your name", groups={"Registration", "Profile"})]  
     */
    private $nomPrenom;

    /**
     * @var string $email
     * #[ORM\Column(name="email", type="string",length=50)]
     * #[Assert\NotBlank(message="Please enter your name", groups={"Registration", "Profile"})]  
     */
    private $email;

    /**
     * @var string $telAbonne
     * #[ORM\Column(name="telabonne", type="string",length=16,nullable=true)]  
     */
    private $telAbonne;

    /**
     * @var text $adresseAbonnee
     * #[ORM\Column(name="adresseabonne", type="text")]
     * #[Assert\NotBlank()]  
     */
    private $adresseAbonne;

    /**
     * @var string $celAbonne
     * #[ORM\Column(name="celabonne", type="string",length=16)]
     * #[Assert\NotBlank()]  
     */
    private $celAbonne;

    /**
     * @var string $genPsswd
     * #[ORM\Column(name="genPsswd", type="string",length=16)]
     * #[Assert\NotBlank()]  
     */
    private $genPsswd;

    /**
     * @var integer $etatAbonne
     * #[ORM\Column(name="etatabonne", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $etatAbonne;

    /**
     * @var integer $attempt
     * #[ORM\Column(name="attempt", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $attempt;

    /**
     * @var integer $suppr
     * #[ORM\Column(name="suppr", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $suppr;

    /**
     * @var string $radicalAbonne
     * #[ORM\Column(name="radicalAbonne", type="string",length=8)]
     * #[Assert\NotBlank()]  
     */
    private $radicalAbonne;

    /**
     * #[ORM\Column(type="string", length=32)]
     */
    private $salt;

    /**
     * Encrypted password. Must be persisted.
     * 
     * @var string $password
     * #[ORM\Column(name="password", type="string")]
     */
    private $password;

    /**
     * @var integer $controleCode
     * #[ORM\Column(name="controlecode", type="integer", nullable=true)]
     * #[Assert\NotBlank()]  
     */
    private $controleCode;
    
    /**
     * Encrypted password. Must not be persisted.
     * @var string $cpassword 
     * #[Assert\NotBlank(message="Please enter your password confirm")]
     * @var string
     */
    public $cpassword;

    /**
     * @var Profil $profil
     * #[ORM\ManyToOne(targetEntity: App\Entity\ProfilClient::class, inversedBy="abonnes", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idprofil", referencedColumnName="idprofil")
     * })
     */
    private $profil;

    /**
     * @var ArrayCollection Compte $comptes
     * #[ORM\OneToMany(targetEntity: App\Entity\Compte::class, mappedBy="abonne" )]
     * 
     */
    private $comptes;

    /**
     * @var ArrayCollection Envoi $envois
     * #[ORM\OneToMany(targetEntity: App\Entity\Envoi::class, mappedBy="Abonne" )]
     */
    private $envois;

    /**
     * @var ArrayCollection HistoriqueConnexion $historiques
     * #[ORM\OneToMany(targetEntity: App\Entity\HistoriqueConnexion::class, mappedBy="Abonne" )]
     */
    private $historiques;    
    
    /**
     * @var string $codeBase
     * #[ORM\Column(name="codebase", type="string",length=16)]) 
     */
    private $codeBase;
    
    /**
     * @var string $codeOp
     * #[ORM\Column(name="codeop", type="string",length=16)] 
     */
    private $codeOp;
    
    
    /**
     * @var Abonne $idAbonneParent
     * #[ORM\ManyToOne(targetEntity: App\Entity\Abonne::class)]
     * @ORM\JoinColumn(name="idabonneparent", referencedColumnName="idabonne", nullable=true)
     */
    private $idAbonneParent;
    
    /**
     * @var text $compteParents
     * #[ORM\Column(name="compteParents",type="text",nullable=true)] 
     */
    private $compteParents;
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Abonne
     */
    public function setUsername(string $username): self {
        $this->username = $username;

        return $this;
    }

    /**
     * Set nomPrenom
     *
     * @param string $nomPrenom
     * @return Abonne
     */
    public function setNomPrenom(string $nomPrenom): self {
        $this->nomPrenom = $nomPrenom;

        return $this;
    }

    /**
     * Get nomPrenom
     *
     * @return string 
     */
    public function getNomPrenom(): ?string {
        return $this->nomPrenom;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Abonne
     */
    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * Set telAbonne
     *
     * @param string $telAbonne
     * @return Abonne
     */
    public function setTelAbonne(string $telAbonne): self {
        $this->telAbonne = $telAbonne;

        return $this;
    }

    /**
     * Get telAbonne
     *
     * @return string 
     */
    public function getTelAbonne(): ?string {
        return $this->telAbonne;
    }

    /**
     * Set adresseAbonne
     *
     * @param string $adresseAbonne
     * @return Abonne
     */
    public function setAdresseAbonne(string $adresseAbonne): self {
        $this->adresseAbonne = $adresseAbonne;

        return $this;
    }

    /**
     * Get adresseAbonne
     *
     * @return string 
     */
    public function getAdresseAbonne(): ?string {
        return $this->adresseAbonne;
    }

    /**
     * Set celAbonne
     *
     * @param string $celAbonne
     * @return Abonne
     */
    public function setCelAbonne(string $celAbonne): self {
        $this->celAbonne = $celAbonne;

        return $this;
    }

    /**
     * Get celAbonne
     *
     * @return string 
     */
    public function getCelAbonne(): ?string {
        return $this->celAbonne;
    }

    /**
     * Set etatAbonne
     *
     * @param integer $etatAbonne
     * @return Abonne
     */
    public function setEtatAbonne(string $etatAbonne): self {
        $this->etatAbonne = $etatAbonne;

        return $this;
    }

    /**
     * Get attempt
     *
     * @return integer 
     */
    public function getAttempt(): ?string {
        return $this->attempt;
    }

    /**
     * Set attempt
     *
     * @param integer $attempt
     * @return attempt
     */
    public function setAttempt(string $attempt): self {
        $this->attempt = $attempt;

        return $this;
    }

    /**
     * Get etatAbonne
     *
     * @return integer 
     */
    public function getEtatAbonne(): ?string {
        return $this->etatAbonne;
    }

    /**
     * Set radicalAbonne
     *
     * @param integer $radicalAbonne
     * @return Abonne
     */
    public function setRadicalAbonne(string $radicalAbonne): self {
        $this->radicalAbonne = $radicalAbonne;

        return $this;
    }

    /**
     * Get radicalAbonne
     *
     * @return integer 
     */
    public function getRadicalAbonne(): ?string {
        return $this->radicalAbonne;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Abonne
     */
    public function setSalt(string $salt): self {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Abonne
     */
    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedias(): ?string {
        return $this->medias;
    }

    /**
     * Set profil
     *
     * @param \App\Entity\ProfilClient $profil
     * @return Abonne
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

    /**
     * Add comptes
     *
     * @param \App\Entity\Compte $comptes
     * @return Abonne
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
     * Add envois
     *
     * @param \App\Entity\Envoi $envois
     * @return Abonne
     */
    public function addEnvois(\App\Entity\Envoi $envois) {
        $this->envois[] = $envois;

        return $this;
    }

    /**
     * Remove envois
     *
     * @param \App\Entity\Envoi $envois
     */
    public function removeEnvois(\App\Entity\Envoi $envois) {
        $this->envois->removeElement($envois);
    }

    /**
     * Get envois
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnvois(): ?string {
        return $this->envois;
    }

    public function eraseCredentials() {
        
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function getRoles(): ?string {
        
    }

    public function getSalt(): ?string {
        return $this->salt;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    /**
     * Add historiques
     *
     * @param \App\Entity\HistoriqueConnexion $historiques
     * @return Abonne
     */
    public function addHistorique(\App\Entity\HistoriqueConnexion $historiques) {
        $this->historiques[] = $historiques;

        return $this;
    }

    /**
     * Remove historiques
     *
     * @param \App\Entity\HistoriqueConnexion $historiques
     */
    public function removeHistorique(\App\Entity\HistoriqueConnexion $historiques) {
        $this->historiques->removeElement($historiques);
    }

    /**
     * Get historiques
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHistoriques(): ?string {
        return $this->historiques;
    }

    /**
     * Set suppr
     *
     * @param integer $suppr
     * @return Abonne
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

    /**
     * Set genPsswd
     *
     * @param string $genPsswd
     * @return Abonne
     */
    public function setGenPsswd(string $genPsswd): self {
        $this->genPsswd = $genPsswd;

        return $this;
    }

    /**
     * Get genPsswd
     *
     * @return string 
     */
    public function getGenPsswd(): ?string {
        return $this->genPsswd;
    }
    
    /**
     * Set codeBase
     *
     * @param integer $codeBase
     * @return Abonne
     */
    public function setCodeBase(string $codeBase): self {
        $this->codeBase = $codeBase;

        return $this;
    }

    /**
     * Get codeBase
     *
     * @return integer 
     */
    public function getCodeBase(): ?string {
        return $this->codeBase;
    }
    
    /**
     * Set codeOp
     *
     * @param integer $codeOp
     * @return Abonne
     */
    public function setCodeOp(string $codeOp): self {
        $this->codeOp = $codeOp;

        return $this;
    }

    /**
     * Get codeOp
     *
     * @return integer 
     */
    public function getCodeOp(): ?string {
        return $this->codeOp;
    }



    /**
     * Set controleCode
     *
     * @param integer $controleCode
     * @return Abonne
     */
    public function setControleCode(string $controleCode): self
    {
        $this->controleCode = $controleCode;
    
        return $this;
    }

    /**
     * Get controleCode
     *
     * @return integer 
     */
    public function getControleCode(): ?string
    {
        return $this->controleCode;
    }

    /**
     * Set idAbonneParent
     *
     * @param \App\Entity\Abonne $idAbonneParent
     * @return Abonne
     */
    public function setIdAbonneParent(\App\Entity\Abonne $idAbonneParent = null)
    {
        $this->idAbonneParent = $idAbonneParent;
    
        return $this;
    }

    /**
     * Get idAbonneParent
     *
     * @return \App\Entity\Abonne 
     */
    public function getIdAbonneParent(): ?string
    {
        return $this->idAbonneParent;
    }
    
    

    /**
     * Set compteParents
     *
     * @param text $compteParents
     * @return Abonne
     */
    public function setCompteParents(string $compteParents): self
    {
        $this->compteParents = $compteParents;
    
        return $this;
    }

    /**
     * Get compteParents
     *
     * @return text 
     */
    public function getCompteParents(): ?string
    {
        return $this->compteParents;
    }
}
