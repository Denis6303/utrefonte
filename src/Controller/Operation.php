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
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="operations", cascade={"persist"})
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Devise", inversedBy="operations", cascade={"persist","merge"})
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libOperation
     *
     * @param string $libOperation
     * @return Operation
     */
    public function setLibOperation($libOperation)
    {
        $this->libOperation = $libOperation;
    
        return $this;
    }

    /**
     * Get libOperation
     *
     * @return string 
     */
    public function getLibOperation()
    {
        return $this->libOperation;
    }

    /**
     * Set dateValeur
     *
     * @param \DateTime $dateValeur
     * @return Operation
     */
    public function setDateValeur($dateValeur)
    {
        $this->dateValeur = $dateValeur;
    
        return $this;
    }

    /**
     * Get dateValeur
     *
     * @return \DateTime 
     */
    public function getDateValeur()
    {
        return $this->dateValeur;
    }

    /**
     * Set dateOperation
     *
     * @param \DateTime $dateOperation
     * @return Operation
     */
    public function setDateOperation($dateOperation)
    {
        $this->dateOperation = $dateOperation;
    
        return $this;
    }

    /**
     * Get dateOperation
     *
     * @return \DateTime 
     */
    public function getDateOperation()
    {
        return $this->dateOperation;
    }

    /**
     * Set dateCompta
     *
     * @param \DateTime $dateCompta
     * @return Operation
     */
    public function setDateCompta($dateCompta)
    {
        $this->dateCompta = $dateCompta;
    
        return $this;
    }

    /**
     * Get dateCompta
     *
     * @return \DateTime 
     */
    public function getDateCompta()
    {
        return $this->dateCompta;
    }    
    
    /**
     * Set montant
     *
     * @param float $montant
     * @return Operation
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
    
        return $this;
    }

    /**
     * Get montant
     *
     * @return float 
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set sensOperation
     *
     * @param string $sensOperation
     * @return Operation
     */
    public function setSensOperation($sensOperation)
    {
        $this->sensOperation = $sensOperation;
    
        return $this;
    }

    /**
     * Get sensOperation
     *
     * @return string 
     */
    public function getSensOperation()
    {
        return $this->sensOperation;
    }

    /**
     * Set coef
     *
     * @param integer $coef
     * @return Operation
     */
    public function setCoef($coef)
    {
        $this->coef = $coef;
    
        return $this;
    }

    /**
     * Get coef
     *
     * @return integer 
     */
    public function getCoef()
    {
        return $this->coef;
    }

    /**
     * Set numeroMvt
     *
     * @param string $numeroMvt
     * @return Operation
     */
    public function setNumeroMvt($numeroMvt)
    {
        $this->numeroMvt = $numeroMvt;
    
        return $this;
    }

    /**
     * Get numeroMvt
     *
     * @return string 
     */
    public function getNumeroMvt()
    {
        return $this->numeroMvt;
    }

    /**
     * Set codOperation
     *
     * @param string $codOperation
     * @return Operation
     */
    public function setCodOperation($codOperation)
    {
        $this->codOperation = $codOperation;
    
        return $this;
    }

    /**
     * Get codOperation
     *
     * @return string 
     */
    public function getCodOperation()
    {
        return $this->codOperation;
    }

    /**
     * Set periode
     *
     * @param string $periode
     * @return Operation
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;
    
        return $this;
    }

    /**
     * Get periode
     *
     * @return string 
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set traite
     *
     * @param integer $traite
     * @return Operation
     */
    public function setTraite($traite)
    {
        $this->traite = $traite;
    
        return $this;
    }

    /**
     * Get traite
     *
     * @return integer 
     */
    public function getTraite()
    {
        return $this->traite;
    }

    /**
     * Set idfile
     *
     * @param integer $idfile
     * @return Operation
     */
    public function setIdfile($idfile)
    {
        $this->idfile = $idfile;
    
        return $this;
    }

    /**
     * Get idfile
     *
     * @return integer 
     */
    public function getIdfile()
    {
        return $this->idfile;
    }

    /**
     * Set soldeEnLigne
     *
     * @param integer $soldeEnLigne
     * @return Operation
     */
    public function setSoldeEnLigne($soldeEnLigne)
    {
        $this->soldeEnLigne = $soldeEnLigne;
    
        return $this;
    }

    /**
     * Get soldeEnLigne
     *
     * @return integer 
     */
    public function getSoldeEnLigne()
    {
        return $this->soldeEnLigne;
    }

    /**
     * Set journalier
     *
     * @param integer $journalier
     * @return Operation
     */
    public function setJournalier($journalier)
    {
        $this->journalier = $journalier;
    
        return $this;
    }

    /**
     * Get journalier
     *
     * @return integer 
     */
    public function getJournalier()
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
    public function getCompte()
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
    public function getDevise()
    {
        return $this->devise;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return Operation
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    
        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set mttafbw
     *
     * @param string $mttafbw
     * @return Operation
     */
    public function setMttafbw($mttafbw)
    {
        $this->mttafbw = $mttafbw;
    
        return $this;
    }

    /**
     * Get mttafbw
     *
     * @return string 
     */
    public function getMttafbw()
    {
        return $this->mttafbw;
    }

    /**
     * Set codeBnq
     *
     * @param string $codeBnq
     * @return Operation
     */
    public function setCodeBnq($codeBnq)
    {
        $this->codeBnq = $codeBnq;
    
        return $this;
    }

    /**
     * Get codeBnq
     *
     * @return string 
     */
    public function getCodeBnq()
    {
        return $this->codeBnq;
    }

    /**
     * Set codeGui
     *
     * @param string $codeGui
     * @return Operation
     */
    public function setCodeGui($codeGui)
    {
        $this->codeGui = $codeGui;
    
        return $this;
    }

    /**
     * Get codeGui
     *
     * @return string 
     */
    public function getCodeGui()
    {
        return $this->codeGui;
    }

    /**
     * Set codeDevise
     *
     * @param string $codeDevise
     * @return Operation
     */
    public function setCodeDevise($codeDevise)
    {
        $this->codeDevise = $codeDevise;
    
        return $this;
    }

    /**
     * Get codeDevise
     *
     * @return string 
     */
    public function getCodeDevise()
    {
        return $this->codeDevise;
    }

    /**
     * Set motrej
     *
     * @param string $motrej
     * @return Operation
     */
    public function setMotrej($motrej)
    {
        $this->motrej = $motrej;
    
        return $this;
    }

    /**
     * Get motrej
     *
     * @return string 
     */
    public function getMotrej()
    {
        return $this->motrej;
    }

    /**
     * Set monori
     *
     * @param string $monori
     * @return Operation
     */
    public function setMonori($monori)
    {
        $this->monori = $monori;
    
        return $this;
    }

    /**
     * Get monori
     *
     * @return string 
     */
    public function getMonori()
    {
        return $this->monori;
    }

    /**
     * Set virgul
     *
     * @param string $virgul
     * @return Operation
     */
    public function setVirgul($virgul)
    {
        $this->virgul = $virgul;
    
        return $this;
    }

    /**
     * Get virgul
     *
     * @return string 
     */
    public function getVirgul()
    {
        return $this->virgul;
    }

    /**
     * Set res21
     *
     * @param string $res21
     * @return Operation
     */
    public function setRes21($res21)
    {
        $this->res21 = $res21;
    
        return $this;
    }

    /**
     * Get res21
     *
     * @return string 
     */
    public function getRes21()
    {
        return $this->res21;
    }

    /**
     * Set exocom
     *
     * @param string $exocom
     * @return Operation
     */
    public function setExocom($exocom)
    {
        $this->exocom = $exocom;
    
        return $this;
    }

    /**
     * Get exocom
     *
     * @return string 
     */
    public function getExocom()
    {
        return $this->exocom;
    }

    /**
     * Set ind
     *
     * @param string $ind
     * @return Operation
     */
    public function setInd($ind)
    {
        $this->ind = $ind;
    
        return $this;
    }

    /**
     * Get ind
     *
     * @return string 
     */
    public function getInd()
    {
        return $this->ind;
    }

    /**
     * Set res22
     *
     * @param string $res22
     * @return Operation
     */
    public function setRes22($res22)
    {
        $this->res22 = $res22;
    
        return $this;
    }

    /**
     * Get res22
     *
     * @return string 
     */
    public function getRes22()
    {
        return $this->res22;
    }

    /**
     * Set noecri
     *
     * @param string $noecri
     * @return Operation
     */
    public function setNoecri($noecri)
    {
        $this->noecri = $noecri;
    
        return $this;
    }

    /**
     * Get noecri
     *
     * @return string 
     */
    public function getNoecri()
    {
        return $this->noecri;
    }

    /**
     * Set cdafb
     *
     * @param string $cdafb
     * @return Operation
     */
    public function setCdafb($cdafb)
    {
        $this->cdafb = $cdafb;
    
        return $this;
    }

    /**
     * Get cdafb
     *
     * @return string 
     */
    public function getCdafb()
    {
        return $this->cdafb;
    }

    /**
     * Set res23
     *
     * @param string $res23
     * @return Operation
     */
    public function setRes23($res23)
    {
        $this->res23 = $res23;
    
        return $this;
    }

    /**
     * Get res23
     *
     * @return string 
     */
    public function getRes23()
    {
        return $this->res23;
    }

    /**
     * Set res13
     *
     * @param string $res13
     * @return Operation
     */
    public function setRes13($res13)
    {
        $this->res13 = $res13;
    
        return $this;
    }

    /**
     * Get res13
     *
     * @return string 
     */
    public function getRes13()
    {
        return $this->res13;
    }

    /**
     * Set cdcoib
     *
     * @param string $cdcoib
     * @return Operation
     */
    public function setCdcoib($cdcoib)
    {
        $this->cdcoib = $cdcoib;
    
        return $this;
    }

    /**
     * Get cdcoib
     *
     * @return string 
     */
    public function getCdcoib()
    {
        return $this->cdcoib;
    }

    /**
     * Set sign
     *
     * @param string $sign
     * @return Operation
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
    
        return $this;
    }

    /**
     * Get sign
     *
     * @return string 
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * Set cdexo
     *
     * @param string $cdexo
     * @return Operation
     */
    public function setCdexo($cdexo)
    {
        $this->cdexo = $cdexo;
    
        return $this;
    }

    /**
     * Get cdexo
     *
     * @return string 
     */
    public function getCdexo()
    {
        return $this->cdexo;
    }
	
    /**
     * Set codop
     *
     * @param string $codop
     * @return Operation
     */
    public function setCodop($codop)
    {
        $this->codop = $codop;
    
        return $this;
    }

    /**
     * Get codop
     *
     * @return string 
     */
    public function getCodop()
    {
        return $this->codop;
    }
	
}
