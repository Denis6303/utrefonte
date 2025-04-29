<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\ChargementRepository")
 * #[ORM\Table(name="chargement")]
 * @ORM\HasLifecycleCallbacks
 */
class Chargement {

    public function __construct() {
        $this->statut = 0;
    }

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="idchargement", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $nomFile
     * #[ORM\Column(name="nomfile", type="string",length=200)]
     */
    private $nomFile;

    /**
     * @var string $urlFile
     * #[ORM\Column(name="urlfile", type="string",length=200)]
     */
    private $urlFile;

    /**
     * @var TypeCompte $typecompte
     * #[ORM\ManyToOne(targetEntity: App\Entity\TypeCompte::class, inversedBy="chargements", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idtypecompte", referencedColumnName="idtypecompte")
     * })
     */
    private $typeCompte;

    /**
     * @var integer $statut
     * #[ORM\Column(name="statut", type="integer")]
     */
    private $statut;

    /**
     * @var integer $archive
     * #[ORM\Column(name="archive", type="integer")]
     */
    private $archive;

    /**
     * @var integer $typeChargement
     * #[ORM\Column(name="typechargement", type="integer")]
     */
    private $typeChargement;

    /**
     * @var datetime $dateDeb
     * #[ORM\Column(name="datedeb", type="datetime",nullable=true)]
     */
    private $dateDeb;

    /**
     * @var datetime $dateFin
     * #[ORM\Column(name="datefin", type="datetime",nullable=true)]
     */
    private $dateFin;
    
    /**
     * @var integer $natureChargement
     * #[ORM\Column(name="natureChargement", type="integer",nullable=true)]
     */
    private $natureChargement;

    /**
     * 
     * @Assert\File(
     * maxSize = "100000M",
     * mimeTypes = {"/txt" },
     * mimeTypesMessage = "Format invalide"
     * )
     */
    public $file;

    /**
     * 
     * @var datetime $fileDateAjout
     * #[ORM\Column(name="filedateajout" , type="datetime", nullable=false)] 
     */
    private $fileDateAjout;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {

        $this->fileDateAjout = new \DateTime();
        //$nomFichier = ""; 

        if($this->natureChargement==1){                
            $dateJour = date("dmY");
            //$dateJ = substr($dateJour, 0, 2).substr($dateJour, 3, 2).substr($dateJour, 6, 4);
            $nomFichier = $dateJour;//$dateJ;

            if($this->getTypeChargement()== 0) $nomFichier = $nomFichier."A";
            elseif($this->getTypeChargement()== 2) $nomFichier = $nomFichier."B";
            $typeCpte = $this->getTypeCompte()->getId();
            $nomFichier = $nomFichier.$typeCpte;
        }       

        if (null !== $this->file) {
            // Ceci pour générer un nom unique            
            if($this->natureChargement==1){
                $this->urlFile = $nomFichier. '.' . $this->file->guessExtension();
                $this->nomFile = $nomFichier;
            }else{
               // $this->urlFile = uniqid(mt_rand(), true) . '.' . $this->file->guessExtension();
                $this->urlFile = $this->file->getClientOriginalName() ;
                $this->nomFile = $this->file->getClientOriginalName();
            }
        }
         
    }

    /* public function initialiser($ext=array()){
      $this-> extensions = $ext;
      } */

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->file) {
            return;
        }
        if(file_exists($this->getUploadRootDir()."/".$this->urlFile)){
            unlink($this->getUploadRootDir()."/".$this->urlFile);
        }
        $this->file->move($this->getUploadRootDir(), $this->urlFile);
        //chmod($this->getUploadRootDir(), 0755);
        unset($this->file);
    }

    public function removeUpload($file) {
        unlink($file);
    }

    public function getAbsolutePath(): ?string {
        return null === $this->urlFile ? null : $this->getUploadRootDir() . '' . $this->urlFile;
    }

    public function getWebPath(): ?string {
        return null === $this->urlFile ? null : $this->getUploadDir() . '' . $this->urlFile;
    }

    public function getUploadRootDir(): ?string {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir(): ?string {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.

        return 'upload/chargement';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set nomFile
     *
     * @param string $nomFile
     * @return Chargement
     */
    public function setNomFile(string $nomFile): self {
        $this->nomFile = $nomFile;

        return $this;
    }

    /**
     * Get nomFile
     *
     * @return string 
     */
    public function getNomFile(): ?string {
        return $this->nomFile;
    }

    /**
     * Set urlFile
     *
     * @param string $urlFile
     * @return Chargement
     */
    public function setUrlFile(string $urlFile): self {
        $this->urlFile = $urlFile;

        return $this;
    }

    /**
     * Get urlFile
     *
     * @return string 
     */
    public function getUrlFile(): ?string {
        return $this->urlFile;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     * @return Chargement
     */
    public function setStatut(string $statut): self {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer 
     */
    public function getStatut(): ?string {
        return $this->statut;
    }

    /**
     * Set fileDateAjout
     *
     * @param \DateTime $fileDateAjout
     * @return Chargement
     */
    public function setFileDateAjout(string $fileDateAjout): self {
        $this->fileDateAjout = $fileDateAjout;

        return $this;
    }

    /**
     * Get fileDateAjout
     *
     * @return \DateTime 
     */
    public function getFileDateAjout(): ?string {
        return $this->fileDateAjout;
    }

    /**
     * Set typeCompte
     *
     * @param \App\Entity\TypeCompte $typeCompte
     * @return Chargement
     */
    public function setTypeCompte(\App\Entity\TypeCompte $typeCompte = null) {
        $this->typeCompte = $typeCompte;

        return $this;
    }

    /**
     * Get typeCompte
     *
     * @return \App\Entity\TypeCompte 
     */
    public function getTypeCompte(): ?string {
        return $this->typeCompte;
    }

    /**
     * Set dateDeb
     *
     * @param \DateTime $dateDeb
     * @return Chargement
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
     * @return Chargement
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
     * Set typeChargement
     *
     * @param integer $typeChargement
     * @return Chargement
     */
    public function setTypeChargement(string $typeChargement): self {
        $this->typeChargement = $typeChargement;

        return $this;
    }

    /**
     * Get typeChargement
     *
     * @return integer 
     */
    public function getTypeChargement(): ?string {
        return $this->typeChargement;
    }

    /**
     * Set archive
     *
     * @param integer $archive
     * @return Chargement
     */
    public function setArchive(string $archive): self {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get archive
     *
     * @return integer 
     */
    public function getArchive(): ?string {
        return $this->archive;
    }


    /**
     * Set natureChargement
     *
     * @param integer $natureChargement
     * @return Chargement
     */
    public function setNatureChargement(string $natureChargement): self
    {
        $this->natureChargement = $natureChargement;
    
        return $this;
    }

    /**
     * Get natureChargement
     *
     * @return integer 
     */
    public function getNatureChargement(): ?string
    {
        return $this->natureChargement;
    }
}
