<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\UtilisateurRepository")
 * #[ORM\Table(name="utilisateur")]
 * @ORM\HasLifecycleCallbacks
 */
class Utilisateur implements UserInterface {

    //constructeur
    public function __construct() {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->attempt = 0;
        $this->suppr = 0;
        $this->genPsswd = "";
    }

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="idutilisateur", type="integer")]	
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * #[ORM\Column(name="username", type="string",length=50, unique=true)]
     */
    protected $username;

    /**
     * @var string $nomPrenom
     * #[ORM\Column(name="nomprenom", type="string",length=50)]
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
     * @var string $telUtilisateur
     * #[ORM\Column(name="telutilisateur", type="string",length=16,nullable=true)]
     * #[Assert\NotBlank()]  
     */
    private $telUtilisateur;

    /**
     * @var string $adresseUtilisateure
     * #[ORM\Column(name="adresseutilisateur", type="text")]
     * #[Assert\NotBlank()]  
     */
    private $adresseUtilisateur;

    /**
     * @var string $celUtilisateur
     * #[ORM\Column(name="celutilisateur", type="string",length=16)]
     * #[Assert\NotBlank()]  
     */
    private $celUtilisateur;

    /**
     * @var integer $etatUtilisateur
     * #[ORM\Column(name="etatutilisateur", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $etatUtilisateur;

    /**
     * @var integer $attempt
     * #[ORM\Column(name="attempt", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $attempt;

    /**
     * @var string $genPsswd
     * #[ORM\Column(name="genPsswd", type="string",length=16)]
     * #[Assert\NotBlank()]  
     */
    private $genPsswd;

    /**
     * @var integer $suppr
     * #[ORM\Column(name="suppr", type="integer")]
     * #[Assert\NotBlank()]  
     */
    private $suppr;

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
     * Encrypted password. Must not be persisted.
     * @var string $cpassword 
     * #[Assert\NotBlank(message="Please enter your password confirm")]
     * @var string
     */
    public $cpassword;

    /**
     * @var Profil $profil
     * #[ORM\ManyToOne(targetEntity: App\Entity\ProfilClient::class, inversedBy="utilisateurs", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idprofil", referencedColumnName="idprofil")
     * })
     */
    private $profil;

    /**
     * @var ArrayCollection Media $medias
     * #[ORM\OneToMany(targetEntity: App\Entity\Media::class, mappedBy="Utilisateur")]
     * 
     */
    private $medias;

    /**
     * @var ArrayCollection HistoriqueConnexion $historiques
     * #[ORM\OneToMany(targetEntity: App\Entity\HistoriqueConnexion::class, mappedBy="Utilisateur")]
     * 
     */
    private $historiques;

    /**
     * @var ArrayCollection Fonds $fonds
     * #[ORM\OneToMany(targetEntity: App\Entity\Fonds::class, mappedBy="utilisateur" )]
     * 
     */
    private $fonds;

    /**
     * @var ArrayCollection Envoi $envois
     * #[ORM\OneToMany(targetEntity: App\Entity\Envoi::class, mappedBy="utilisateur" )]
     * 
     */
    private $envois;

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
     * @return Utilisateur
     */
    public function setUsername(string $username): self {
        $this->username = $username;

        return $this;
    }

    /**
     * Set nomPrenom
     *
     * @param string $nomPrenom
     * @return Utilisateur
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
     * @return Utilisateur
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
     * Set telUtilisateur
     *
     * @param string $telUtilisateur
     * @return Utilisateur
     */
    public function setTelUtilisateur(string $telUtilisateur): self {
        $this->telUtilisateur = $telUtilisateur;

        return $this;
    }

    /**
     * Get telUtilisateur
     *
     * @return string 
     */
    public function getTelUtilisateur(): ?string {
        return $this->telUtilisateur;
    }

    /**
     * Set adresseUtilisateur
     *
     * @param string $adresseUtilisateur
     * @return Utilisateur
     */
    public function setAdresseUtilisateur(string $adresseUtilisateur): self {
        $this->adresseUtilisateur = $adresseUtilisateur;

        return $this;
    }

    /**
     * Get adresseUtilisateur
     *
     * @return string 
     */
    public function getAdresseUtilisateur(): ?string {
        return $this->adresseUtilisateur;
    }

    /**
     * Set celUtilisateur
     *
     * @param string $celUtilisateur
     * @return Utilisateur
     */
    public function setCelUtilisateur(string $celUtilisateur): self {
        $this->celUtilisateur = $celUtilisateur;

        return $this;
    }

    /**
     * Get celUtilisateur
     *
     * @return string 
     */
    public function getCelUtilisateur(): ?string {
        return $this->celUtilisateur;
    }

    /**
     * Set etatUtilisateur
     *
     * @param integer $etatUtilisateur
     * @return Utilisateur
     */
    public function setEtatUtilisateur(string $etatUtilisateur): self {
        $this->etatUtilisateur = $etatUtilisateur;

        return $this;
    }

    /**
     * Get etatUtilisateur
     *
     * @return integer 
     */
    public function getEtatUtilisateur(): ?string {
        return $this->etatUtilisateur;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Utilisateur
     */
    public function setSalt(string $salt): self {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Utilisateur
     */
    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    public function setCpassword(string $cpassword): self {
        $this->cpassword = $cpassword;

        return $this;
    }

    /**
     * Set profil
     *
     * @param \App\Entity\ProfilClient $profil
     * @return Utilisateur
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
     * Add medias
     *
     * @param \App\Entity\Media $medias
     * @return Utilisateur
     */
    public function addMedia(\App\Entity\Media $medias) {
        $this->medias[] = $medias;

        return $this;
    }

    /**
     * Remove medias
     *
     * @param \App\Entity\Media $medias
     */
    public function removeMedia(\App\Entity\Media $medias) {
        $this->medias->removeElement($medias);
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
     * Add comptes
     *
     * @param \App\Entity\Compte $comptes
     * @return Utilisateur
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
     * @return Utilisateur
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

    public function getCpassword(): ?string {

        return $this->cpassword;
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
     * @return Utilisateur
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
     * Add fonds
     *
     * @param \App\Entity\Fonds $fonds
     * @return Utilisateur
     */
    public function addFond(\App\Entity\Fonds $fonds) {
        $this->fonds[] = $fonds;

        return $this;
    }

    /**
     * Remove fonds
     *
     * @param \App\Entity\Fonds $fonds
     */
    public function removeFond(\App\Entity\Fonds $fonds) {
        $this->fonds->removeElement($fonds);
    }

    /**
     * Get fonds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFonds(): ?string {
        return $this->fonds;
    }

    /**
     * Set attempt
     *
     * @param integer $attempt
     * @return Utilisateur
     */
    public function setAttempt(string $attempt): self {
        $this->attempt = $attempt;

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
     * Set suppr
     *
     * @param integer $suppr
     * @return Utilisateur
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
     * @return Utilisateur
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

}
