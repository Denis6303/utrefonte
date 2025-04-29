<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity
 *
 * #[ORM\Table(name="operation")]
 * #[ORM\Entity](repositoryClass="App\Entity\OperationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Operation {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idoperation", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $libOperation
     * #[ORM\Column(name="liboperation",type="string",length=100)]
     */
    private $libOperation;

    /**
     * @var datetime $dateValeur
     * #[ORM\Column(name="datevaleur",type="datetime",length=100)]
     */
    private $dateValeur;

    /**
     * @var datetime $dateOperation
     * #[ORM\Column(name="dateoperation",type="datetime",length=100)]
     */
    private $dateOperation;

    /**
     * @var datetime $dateCompta
     * #[ORM\Column(name="datecompta",type="datetime",length=100)]
     */
    private $dateCompta;    
    
    /**
     * @var float $montant
     * #[ORM\Column(name="montant",type="float")]
     */
    private $montant;

    /**
     * @var Compte $compte
     * #[ORM\ManyToOne(targetEntity: App\Entity\Compte::class, inversedBy="operations", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="numerocompte", referencedColumnName="numerocompte")
     * })
     */
    private $compte;

    /**
     * @var string $sensOperation
     * #[ORM\Column(name="sensoperation",type="string",length=2)]
     */
    private $sensOperation;

    /**
     * @var integer $coef
     * #[ORM\Column(name="coef",type="integer")]
     */
    private $coef;

    /**
     * @var string $numeroMvt
     * #[ORM\Column(name="numeromvt",type="string",length=15)]
     */
    private $numeroMvt;

    /**
     * @var Devise $devise
     * #[ORM\ManyToOne(targetEntity: App\Entity\Devise::class, inversedBy="operations", cascade={"persist","merge"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="iddevise", referencedColumnName="iddevise")
     * })
     */
    private $devise;

    /**
     * @var string $codOperation
     * #[ORM\Column(name="codoperation",type="string",length=5)]
     */
    private $codOperation;

    /**
     * @var string $periode
     * #[ORM\Column(name="periode",type="string",length=10)]
     */
    private $periode;

    /**
     * @var integer $traite
     * #[ORM\Column(name="traite",type="integer")]
     */
    private $traite;

    /**
     * @var integer $idfile
     * #[ORM\Column(name="idfile",type="integer")]
     */
    private $idfile;
    
    /**
     * @var integer $soldeEnLigne
     * #[ORM\Column(name="soldeligne",type="integer")]
     */
    private $soldeEnLigne;
    
    /**
     * @var integer $journalier
     * #[ORM\Column(name="chrgjr",type="integer")]
     */
    private $journalier;
    
    /**
     * @var integer $ordre
     * #[ORM\Column(name="ordre",type="integer")]
     */
    private $ordre;	

    /**
     * @var string $mttafbw
     * #[ORM\Column(name="mttafbw",type="string",nullable=true,length=14)]
     */
    private $mttafbw;

    /**
     * @var string $codeBnq
     * #[ORM\Column(name="bnqcod",type="string",nullable=true,length=5)]
     */
    private $codeBnq;
    
    /**
     * @var string $codeGui
     * #[ORM\Column(name="guichet",type="string",nullable=true,length=5)]
     */
    private $codeGui;
    
    /**
     * @var string $codeDevise
     * #[ORM\Column(name="ladevise",type="string",nullable=true,length=3)]
     */
    private $codeDevise;    
    
    /**
     * @var string $motrej
     * #[ORM\Column(name="motrej",type="string",nullable=true,length=2)]
     */
    private $motrej;   
    
    /**
     * @var string $monori
     * #[ORM\Column(name="monori",type="string",nullable=true,length=1)]
     */
    private $monori;  
    
    /**
     * @var string $virgul
     * #[ORM\Column(name="virgul",type="string",nullable=true,length=1)]
     */
    private $virgul;    
    
    /**
     * @var string $res21
     * #[ORM\Column(name="res21",type="string",nullable=true,length=4)]
     */
    private $res21;     
    
    /**
     * @var string $exocom
     * #[ORM\Column(name="exocom",type="string",nullable=true,length=1)]
     */
    private $exocom;
    
    /**
     * @var string $ind
     * #[ORM\Column(name="ind",type="string",nullable=true,length=1)]
     */
    private $ind;
	
	 /**
     * @var string $res22
     * #[ORM\Column(name="res22",type="string",nullable=true,length=2)]
     */
    private $res22;    
    
    /**
     * @var string $noecri
     * #[ORM\Column(name="noecri",type="string",nullable=true,length=7)]
     */
    private $noecri; 
	
	 /**
     * @var string $cdafb
     * #[ORM\Column(name="cdafb",type="string",nullable=true,length=2)]
     */
    private $cdafb;    
    
    /**
     * @var string $res23
     * #[ORM\Column(name="res23",type="string",nullable=true,length=2)]
     */
    private $res23; 	
    
    /**
     * @var string $res13
     * #[ORM\Column(name="res13",type="string",nullable=true,length=2)]
     */
    private $res13; 	
	
    /**
     * @var string $cdcoib
     * #[ORM\Column(name="cdcoib",type="string",nullable=true,length=4)]
     */
    private $cdcoib; 		
	
    /**
     * @var string $sign
     * #[ORM\Column(name="sign",type="string",nullable=true,length=1)]
     */
    private $sign; 	

    /**
     * @var string $cdexo
     * #[ORM\Column(name="cdexo",type="string",nullable=true,length=1)]
     */
    private $cdexo; 

    /**
     * @var string $codop
     * #[ORM\Column(name="codop",type="string",nullable=true,length=3)]
     */
    private $codop; 
	
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set libOperation
     *
     * @param string $libOperation
     * @return Operation
     */
    public function setLibOperation(string $libOperation): self
    {
        $this->libOperation = $libOperation;
    
        return $this;
    }

    /**
     * Get libOperation
     *
     * @return string 
     */
    public function getLibOperation(): ?string
    {
        return $this->libOperation;
    }

    /**
     * Set dateValeur
     *
     * @param \DateTime $dateValeur
     * @return Operation
     */
    public function setDateValeur(string $dateValeur): self
    {
        $this->dateValeur = $dateValeur;
    
        return $this;
    }

    /**
     * Get dateValeur
     *
     * @return \DateTime 
     */
    public function getDateValeur(): ?string
    {
        return $this->dateValeur;
    }

    /**
     * Set dateOperation
     *
     * @param \DateTime $dateOperation
     * @return Operation
     */
    public function setDateOperation(string $dateOperation): self
    {
        $this->dateOperation = $dateOperation;
    
        return $this;
    }

    /**
     * Get dateOperation
     *
     * @return \DateTime 
     */
    public function getDateOperation(): ?string
    {
        return $this->dateOperation;
    }

    /**
     * Set dateCompta
     *
     * @param \DateTime $dateCompta
     * @return Operation
     */
    public function setDateCompta(string $dateCompta): self
    {
        $this->dateCompta = $dateCompta;
    
        return $this;
    }

    /**
     * Get dateCompta
     *
     * @return \DateTime 
     */
    public function getDateCompta(): ?string
    {
        return $this->dateCompta;
    }    
    
    /**
     * Set montant
     *
     * @param float $montant
     * @return Operation
     */
    public function setMontant(string $montant): self
    {
        $this->montant = $montant;
    
        return $this;
    }

    /**
     * Get montant
     *
     * @return float 
     */
    public function getMontant(): ?string
    {
        return $this->montant;
    }

    /**
     * Set sensOperation
     *
     * @param string $sensOperation
     * @return Operation
     */
    public function setSensOperation(string $sensOperation): self
    {
        $this->sensOperation = $sensOperation;
    
        return $this;
    }

    /**
     * Get sensOperation
     *
     * @return string 
     */
    public function getSensOperation(): ?string
    {
        return $this->sensOperation;
    }

    /**
     * Set coef
     *
     * @param integer $coef
     * @return Operation
     */
    public function setCoef(string $coef): self
    {
        $this->coef = $coef;
    
        return $this;
    }

    /**
     * Get coef
     *
     * @return integer 
     */
    public function getCoef(): ?string
    {
        return $this->coef;
    }

    /**
     * Set numeroMvt
     *
     * @param string $numeroMvt
     * @return Operation
     */
    public function setNumeroMvt(string $numeroMvt): self
    {
        $this->numeroMvt = $numeroMvt;
    
        return $this;
    }

    /**
     * Get numeroMvt
     *
     * @return string 
     */
    public function getNumeroMvt(): ?string
    {
        return $this->numeroMvt;
    }

    /**
     * Set codOperation
     *
     * @param string $codOperation
     * @return Operation
     */
    public function setCodOperation(string $codOperation): self
    {
        $this->codOperation = $codOperation;
    
        return $this;
    }

    /**
     * Get codOperation
     *
     * @return string 
     */
    public function getCodOperation(): ?string
    {
        return $this->codOperation;
    }

    /**
     * Set periode
     *
     * @param string $periode
     * @return Operation
     */
    public function setPeriode(string $periode): self
    {
        $this->periode = $periode;
    
        return $this;
    }

    /**
     * Get periode
     *
     * @return string 
     */
    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    /**
     * Set traite
     *
     * @param integer $traite
     * @return Operation
     */
    public function setTraite(string $traite): self
    {
        $this->traite = $traite;
    
        return $this;
    }

    /**
     * Get traite
     *
     * @return integer 
     */
    public function getTraite(): ?string
    {
        return $this->traite;
    }

    /**
     * Set idfile
     *
     * @param integer $idfile
     * @return Operation
     */
    public function setIdfile(string $idfile): self
    {
        $this->idfile = $idfile;
    
        return $this;
    }

    /**
     * Get idfile
     *
     * @return integer 
     */
    public function getIdfile(): ?string
    {
        return $this->idfile;
    }

    /**
     * Set soldeEnLigne
     *
     * @param integer $soldeEnLigne
     * @return Operation
     */
    public function setSoldeEnLigne(string $soldeEnLigne): self
    {
        $this->soldeEnLigne = $soldeEnLigne;
    
        return $this;
    }

    /**
     * Get soldeEnLigne
     *
     * @return integer 
     */
    public function getSoldeEnLigne(): ?string
    {
        return $this->soldeEnLigne;
    }

    /**
     * Set journalier
     *
     * @param integer $journalier
     * @return Operation
     */
    public function setJournalier(string $journalier): self
    {
        $this->journalier = $journalier;
    
        return $this;
    }

    /**
     * Get journalier
     *
     * @return integer 
     */
    public function getJournalier(): ?string
    {
        return $this->journalier;
    }

    /**
     * Set compte
     *
     * @param \App\Entity\Compte $compte
     * @return Operation
     */
    public function setCompte(\App\Entity\Compte $compte = null)
    {
        $this->compte = $compte;
    
        return $this;
    }

    /**
     * Get compte
     *
     * @return \App\Entity\Compte 
     */
    public function getCompte(): ?string
    {
        return $this->compte;
    }

    /**
     * Set devise
     *
     * @param \App\Entity\Devise $devise
     * @return Operation
     */
    public function setDevise(\App\Entity\Devise $devise = null)
    {
        $this->devise = $devise;
    
        return $this;
    }

    /**
     * Get devise
     *
     * @return \App\Entity\Devise 
     */
    public function getDevise(): ?string
    {
        return $this->devise;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return Operation
     */
    public function setOrdre(string $ordre): self
    {
        $this->ordre = $ordre;
    
        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre(): ?string
    {
        return $this->ordre;
    }

    /**
     * Set mttafbw
     *
     * @param string $mttafbw
     * @return Operation
     */
    public function setMttafbw(string $mttafbw): self
    {
        $this->mttafbw = $mttafbw;
    
        return $this;
    }

    /**
     * Get mttafbw
     *
     * @return string 
     */
    public function getMttafbw(): ?string
    {
        return $this->mttafbw;
    }

    /**
     * Set codeBnq
     *
     * @param string $codeBnq
     * @return Operation
     */
    public function setCodeBnq(string $codeBnq): self
    {
        $this->codeBnq = $codeBnq;
    
        return $this;
    }

    /**
     * Get codeBnq
     *
     * @return string 
     */
    public function getCodeBnq(): ?string
    {
        return $this->codeBnq;
    }

    /**
     * Set codeGui
     *
     * @param string $codeGui
     * @return Operation
     */
    public function setCodeGui(string $codeGui): self
    {
        $this->codeGui = $codeGui;
    
        return $this;
    }

    /**
     * Get codeGui
     *
     * @return string 
     */
    public function getCodeGui(): ?string
    {
        return $this->codeGui;
    }

    /**
     * Set codeDevise
     *
     * @param string $codeDevise
     * @return Operation
     */
    public function setCodeDevise(string $codeDevise): self
    {
        $this->codeDevise = $codeDevise;
    
        return $this;
    }

    /**
     * Get codeDevise
     *
     * @return string 
     */
    public function getCodeDevise(): ?string
    {
        return $this->codeDevise;
    }

    /**
     * Set motrej
     *
     * @param string $motrej
     * @return Operation
     */
    public function setMotrej(string $motrej): self
    {
        $this->motrej = $motrej;
    
        return $this;
    }

    /**
     * Get motrej
     *
     * @return string 
     */
    public function getMotrej(): ?string
    {
        return $this->motrej;
    }

    /**
     * Set monori
     *
     * @param string $monori
     * @return Operation
     */
    public function setMonori(string $monori): self
    {
        $this->monori = $monori;
    
        return $this;
    }

    /**
     * Get monori
     *
     * @return string 
     */
    public function getMonori(): ?string
    {
        return $this->monori;
    }

    /**
     * Set virgul
     *
     * @param string $virgul
     * @return Operation
     */
    public function setVirgul(string $virgul): self
    {
        $this->virgul = $virgul;
    
        return $this;
    }

    /**
     * Get virgul
     *
     * @return string 
     */
    public function getVirgul(): ?string
    {
        return $this->virgul;
    }

    /**
     * Set res21
     *
     * @param string $res21
     * @return Operation
     */
    public function setRes21(string $res21): self
    {
        $this->res21 = $res21;
    
        return $this;
    }

    /**
     * Get res21
     *
     * @return string 
     */
    public function getRes21(): ?string
    {
        return $this->res21;
    }

    /**
     * Set exocom
     *
     * @param string $exocom
     * @return Operation
     */
    public function setExocom(string $exocom): self
    {
        $this->exocom = $exocom;
    
        return $this;
    }

    /**
     * Get exocom
     *
     * @return string 
     */
    public function getExocom(): ?string
    {
        return $this->exocom;
    }

    /**
     * Set ind
     *
     * @param string $ind
     * @return Operation
     */
    public function setInd(string $ind): self
    {
        $this->ind = $ind;
    
        return $this;
    }

    /**
     * Get ind
     *
     * @return string 
     */
    public function getInd(): ?string
    {
        return $this->ind;
    }

    /**
     * Set res22
     *
     * @param string $res22
     * @return Operation
     */
    public function setRes22(string $res22): self
    {
        $this->res22 = $res22;
    
        return $this;
    }

    /**
     * Get res22
     *
     * @return string 
     */
    public function getRes22(): ?string
    {
        return $this->res22;
    }

    /**
     * Set noecri
     *
     * @param string $noecri
     * @return Operation
     */
    public function setNoecri(string $noecri): self
    {
        $this->noecri = $noecri;
    
        return $this;
    }

    /**
     * Get noecri
     *
     * @return string 
     */
    public function getNoecri(): ?string
    {
        return $this->noecri;
    }

    /**
     * Set cdafb
     *
     * @param string $cdafb
     * @return Operation
     */
    public function setCdafb(string $cdafb): self
    {
        $this->cdafb = $cdafb;
    
        return $this;
    }

    /**
     * Get cdafb
     *
     * @return string 
     */
    public function getCdafb(): ?string
    {
        return $this->cdafb;
    }

    /**
     * Set res23
     *
     * @param string $res23
     * @return Operation
     */
    public function setRes23(string $res23): self
    {
        $this->res23 = $res23;
    
        return $this;
    }

    /**
     * Get res23
     *
     * @return string 
     */
    public function getRes23(): ?string
    {
        return $this->res23;
    }

    /**
     * Set res13
     *
     * @param string $res13
     * @return Operation
     */
    public function setRes13(string $res13): self
    {
        $this->res13 = $res13;
    
        return $this;
    }

    /**
     * Get res13
     *
     * @return string 
     */
    public function getRes13(): ?string
    {
        return $this->res13;
    }

    /**
     * Set cdcoib
     *
     * @param string $cdcoib
     * @return Operation
     */
    public function setCdcoib(string $cdcoib): self
    {
        $this->cdcoib = $cdcoib;
    
        return $this;
    }

    /**
     * Get cdcoib
     *
     * @return string 
     */
    public function getCdcoib(): ?string
    {
        return $this->cdcoib;
    }

    /**
     * Set sign
     *
     * @param string $sign
     * @return Operation
     */
    public function setSign(string $sign): self
    {
        $this->sign = $sign;
    
        return $this;
    }

    /**
     * Get sign
     *
     * @return string 
     */
    public function getSign(): ?string
    {
        return $this->sign;
    }

    /**
     * Set cdexo
     *
     * @param string $cdexo
     * @return Operation
     */
    public function setCdexo(string $cdexo): self
    {
        $this->cdexo = $cdexo;
    
        return $this;
    }

    /**
     * Get cdexo
     *
     * @return string 
     */
    public function getCdexo(): ?string
    {
        return $this->cdexo;
    }
    
    /**
     * Set codop
     *
     * @param string $codop
     * @return Operation
     */
    public function setCodop(string $codop): self
    {
        $this->codop = $codop;
    
        return $this;
    }

    /**
     * Get codop
     *
     * @return string 
     */
    public function getCodop(): ?string
    {
        return $this->codop;
    }    
    
}
