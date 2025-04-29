<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\MediaRepository")
 * #[ORM\Table(name="media")]
 * @ORM\HasLifecycleCallbacks
 * 
 */
class Media {

    /**
     * Constructeur avec initialisation de(s) champ(s)
     */
    function __construct() {
        $this->ajoutmodifMedia = 0;
        $this->urlVariable ='';
    }

    /**
     * @var integer $id
     * #[ORM\Id] 
     * #[ORM\GeneratedValue](strategy="AUTO")
     * #[ORM\Column(name="idmedia" , type="integer")]
     */
    private $id;

    /**
     * @var ArrayCollection Article $articles
     *
     * Inverse Side
     * @ORM\ManyToMany(targetEntity="App\Entity\Article", mappedBy="medias", cascade={"persist"})
     * 
     */
    private $articles;

    /**
     * @var Dimension $dimension
     * #[ORM\ManyToOne(targetEntity: App\Entity\Dimension::class, inversedBy="medias", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="iddimension", referencedColumnName="iddimension")
     * })
     */
    private $dimension;

    /**
     * @var Rubrique $rubrique
     * #[ORM\ManyToOne(targetEntity: App\Entity\Rubrique::class, inversedBy="medias", cascade={ "persist","merge"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idrubrique", referencedColumnName="idrubrique")
     * })
     */
    private $rubrique;

    /**
     * @var Cadre $cadre
     * #[ORM\ManyToOne(targetEntity: App\Entity\Cadre::class, inversedBy="medias", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idcadre", referencedColumnName="idcadre")
     * })
     */
    private $cadre;

    /**
     * @var integer $typeMedia
     * #[ORM\Column(name="typemedia" , type="integer")]
     *   
     */
    private $typeMedia;

    /**
     * @Gedmo\Translatable
     * @var string $urlMedia
     * #[ORM\Column(name="urlmedia" , type="string")]
     *  
     */
    private $urlMedia;

    /**
     * @Gedmo\Translatable
     * @var string $urlFistMedia
     * #[ORM\Column(name="urlfistmedia" , type="string")]
     *  
     */
    private $urlFistMedia;

    /**
     * @var integer $positionMedia
     * #[ORM\Column(name="positionmedia" , type="integer")]
     *   
     */
    private $positionMedia;

    /**
     * @Gedmo\Translatable
     * @var string $nomMedia
     * #[ORM\Column(name="nommedia" , type="string", nullable=true)]
     *   
     */
    private $nomMedia;

    /**
     * @Gedmo\Translatable
     * @var string $descriptionMedia
     * #[ORM\Column(name="descriptionMedia" , type="string", nullable=true)]
     *   
     */
    private $descriptionMedia;

    /**
     * 
     * @var integer $illustreimgmedia
     * #[ORM\Column(name="illustreImgMedia" , type="integer", nullable=false)] 
     */
    private $illustreImgMedia;

    /**
     * 
     * @var integer $mediaAjoutPar
     * #[ORM\Column(name="mediaajoutpar" , type="integer", nullable=false)] 
     */
    private $mediaAjoutPar;

    /**
     * 
     * @var datetime $mediaDateAjout
     * #[ORM\Column(name="mediadateajout" , type="datetime", nullable=false)] 
     */
    private $mediaDateAjout;

    /**
     * 
     * @var integer $mediaModifPar
     * #[ORM\Column(name="mediamodifpar" , type="integer", nullable=true)] 
     */
    private $mediaModifPar;

    /**
     * 
     * @var datetime $mediaDateModif
     * #[ORM\Column(name="mediadatemodif" , type="datetime", nullable=true)] 
     */
    private $mediaDateModif;

    /**
     * @var NatureDoc $natureDoc
     * #[ORM\ManyToOne(targetEntity: App\Entity\NatureDoc::class, inversedBy="medias", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idnaturedoc", referencedColumnName="idnaturedoc")
     * })
     */
    private $natureDoc;

    /**
     * 
     * @Assert\File(
     * maxSize = "2M",
     * mimeTypes = {"image/gif", "image/jpeg", "image/png" , "/pdf" },
     * mimeTypesMessage = "Format invalide"
     * )
     */
    public $file;
    
    /**
     * @Gedmo\Translatable
     * @var integer $ajoutmodifMedia
     * #[ORM\Column(name="ajoutmodifmedia" , type="integer")]
     *   
     */
    private $ajoutmodifMedia;

    /**

     * @var string $urlVariable
     * #[ORM\Column(name="urlvariable" , type="string")]
     *  
     */
    private $urlVariable;    
    
     /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale; 

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {

    if($this->positionMedia != 3){
            if (null === $this->positionMedia) {
                $this->positionMedia = 1;
            }
            $this->mediaDateAjout = new \DateTime();

            if (null !== $this->file) {
                // Ceci pour générer un nom unique
                //$this->setTranslatableLocale($this->locale);
                if ( ($this->ajoutmodifMedia == 0 ) || ($this->ajoutmodifMedia == 2 ) ){
                    $this->urlVariable = uniqid(mt_rand(), true) . '.' . $this->file->guessExtension();
                    //if ($this->ajoutmodifMedia == 2){                   
                        $this->urlMedia  = $this->urlVariable ;
                    //}                
                }  
            }

            if (strpos($this->urlMedia, '/') === false) {
                $this->urlFistMedia = $this->urlMedia;
            }
        }
    }
    
    
    public function setTranslatableLocale(string $locale): self {
        $this->locale = $locale;
    }    

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        
         if($this->positionMedia != 3){
               if (null !== $this->file) {                                  
                   $this->file->move($this->getUploadRootDir(), $this->urlMedia); 
                   unset($this->file);
              }
         }            
    }

    public function removeUpload($file) {

        unlink($file);
    }

    public function getAbsolutePath(): ?string {
        return null === $this->urlMedia ? null : $this->getUploadRootDir() . '' . $this->urlMedia;
    }

    public function getWebPath(): ?string {
        return null === $this->urlMedia ? null : $this->getUploadDir() . '' . $this->urlMedia;
    }

    public function getUploadRootDir(): ?string {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir(): ?string {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.


        if ($this->rubrique !== null) {
            return 'upload/rubriques';
        } else {
            
            if ($this->illustreImgMedia == 1) {
                return 'upload/articles';
            } else {

                if ($this->typeMedia == 1) {
                    return 'upload/articles/images';
                } elseif ($this->typeMedia == 2) {
                    return 'upload/articles/docs';
                } elseif ($this->typeMedia == 3) {
                    return 'upload/cadres';
                }
            }
        }
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
     * Set typeMedia
     *
     * @param integer $typeMedia
     * @return Media
     */
    public function setTypeMedia(string $typeMedia): self {
        $this->typeMedia = $typeMedia;

        return $this;
    }

    /**
     * Get typeMedia
     *
     * @return integer 
     */
    public function getTypeMedia(): ?string {
        return $this->typeMedia;
    }

    /**
     * Set urlMedia
     *
     * @param string $urlMedia
     * @return Media
     */
    public function setUrlMedia(string $urlMedia): self {
        $this->urlMedia = $urlMedia;

        return $this;
    }

    /**
     * Get urlMedia
     *
     * @return string 
     */
    public function getUrlMedia(): ?string {
        return $this->urlMedia;
    }

    /**
     * Set positionMedia
     *
     * @param integer $positionMedia
     * @return Media
     */
    public function setPositionMedia(string $positionMedia): self {
        $this->positionMedia = $positionMedia;

        return $this;
    }

    /**
     * Get positionMedia
     *
     * @return integer 
     */
    public function getPositionMedia(): ?string {
        return $this->positionMedia;
    }
    
    /**
     * Set ajoutmodifMediaMedia
     *
     * @param integer $ajoutmodifMedia
     * @return Media
     */
    public function setAjoutmodifMedia(string $ajoutmodifMedia): self {
        $this->ajoutmodifMedia = $ajoutmodifMedia;

        return $this;
    }

    /**
     * Get ajoutmodifMedia
     *
     * @return integer 
     */
    public function getAjoutmodifMedia(): ?string {
        return $this->ajoutmodifMedia;
    }

    /**
     * Set nomMedia
     *
     * @param string $nomMedia
     * @return Media
     */
    public function setNomMedia(string $nomMedia): self {
        $this->nomMedia = $nomMedia;

        return $this;
    }

    /**
     * Get nomMedia
     *
     * @return string 
     */
    public function getNomMedia(): ?string {
        return $this->nomMedia;
    }

    /**
     * Set descriptionMedia
     *
     * @param string $descriptionMedia
     * @return Media
     */
    public function setDescriptionMedia(string $descriptionMedia): self {
        $this->descriptionMedia = $descriptionMedia;

        return $this;
    }

    /**
     * Get descriptionMedia
     *
     * @return string 
     */
    public function getDescriptionMedia(): ?string {
        return $this->descriptionMedia;
    }

    /**
     * Set illustreImgMedia
     *
     * @param integer $illustreImgMedia
     * @return Media
     */
    public function setIllustreImgMedia(string $illustreImgMedia): self {
        $this->illustreImgMedia = $illustreImgMedia;

        return $this;
    }

    /**
     * Get illustreImgMedia
     *
     * @return integer 
     */
    public function getIllustreImgMedia(): ?string {
        return $this->illustreImgMedia;
    }

    /**
     * Set mediaAjoutPar
     *
     * @param integer $mediaAjoutPar
     * @return Media
     */
    public function setMediaAjoutPar(string $mediaAjoutPar): self {
        $this->mediaAjoutPar = $mediaAjoutPar;

        return $this;
    }

    /**
     * Get mediaAjoutPar
     *
     * @return integer 
     */
    public function getMediaAjoutPar(): ?string {
        return $this->mediaAjoutPar;
    }

    /**
     * Set mediaDateAjout
     *
     * @param \DateTime $mediaDateAjout
     * @return Media
     */
    public function setMediaDateAjout(string $mediaDateAjout): self {
        $this->mediaDateAjout = $mediaDateAjout;

        return $this;
    }

    /**
     * Get mediaDateAjout
     *
     * @return \DateTime 
     */
    public function getMediaDateAjout(): ?string {
        return $this->mediaDateAjout;
    }

    /**
     * Set mediaModifPar
     *
     * @param integer $mediaModifPar
     * @return Media
     */
    public function setMediaModifPar(string $mediaModifPar): self {
        $this->mediaModifPar = $mediaModifPar;

        return $this;
    }

    /**
     * Get mediaModifPar
     *
     * @return integer 
     */
    public function getMediaModifPar(): ?string {
        return $this->mediaModifPar;
    }

    /**
     * Set mediaDateModif
     *
     * @param \DateTime $mediaDateModif
     * @return Media
     */
    public function setMediaDateModif(string $mediaDateModif): self {
        $this->mediaDateModif = $mediaDateModif;

        return $this;
    }

    /**
     * Get mediaDateModif
     *
     * @return \DateTime 
     */
    public function getMediaDateModif(): ?string {
        return $this->mediaDateModif;
    }

    /**
     * Add articles
     *
     * @param \App\Entity\Article $articles
     * @return Media
     */
    public function addArticle(\App\Entity\Article $articles) {
        $this->articles[] = $articles;

        return $this;
    }

    /**
     * Remove articles
     *
     * @param \App\Entity\Article $articles
     */
    public function removeArticle(\App\Entity\Article $articles) {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles(): ?string {
        return $this->articles;
    }

    /**
     * Set dimension
     *
     * @param \App\Entity\Dimension $dimension
     * @return Media
     */
    public function setDimension(\App\Entity\Dimension $dimension = null) {
        $this->dimension = $dimension;

        return $this;
    }

    /**
     * Get dimension
     *
     * @return \App\Entity\Dimension 
     */
    public function getDimension(): ?string {
        return $this->dimension;
    }

    /**
     * Set rubrique
     *
     * @param \App\Entity\Rubrique $rubrique
     * @return Media
     */
    public function setRubrique(\App\Entity\Rubrique $rubrique = null) {
        $this->rubrique = $rubrique;

        return $this;
    }

    /**
     * Get rubrique
     *
     * @return \App\Entity\Rubrique 
     */
    public function getRubrique(): ?string {
        return $this->rubrique;
    }

    /**
     * Set cadre
     *
     * @param \App\Entity\Cadre $cadre
     * @return Media
     */
    public function setCadre(\App\Entity\Cadre $cadre = null) {
        $this->cadre = $cadre;

        return $this;
    }

    /**
     * Get cadre
     *
     * @return \App\Entity\Cadre 
     */
    public function getCadre(): ?string {
        return $this->cadre;
    }

    /**
     * Set natureDoc
     *
     * @param \App\Entity\NatureDoc $natureDoc
     * @return Media
     */
    public function setNatureDoc(\App\Entity\NatureDoc $natureDoc = null) {
        $this->natureDoc = $natureDoc;

        return $this;
    }

    /**
     * Get natureDoc
     *
     * @return \App\Entity\NatureDoc 
     */
    public function getNatureDoc(): ?string {
        return $this->natureDoc;
    }

    /**
     * Set urlFistMedia
     *
     * @param string $urlFistMedia
     * @return Media
     */
    public function setUrlFistMedia(string $urlFistMedia): self {
        $this->urlFistMedia = $urlFistMedia;

        return $this;
    }

    /**
     * Get urlFistMedia
     *
     * @return string 
     */
    public function getUrlFistMedia(): ?string {
        return $this->urlFistMedia;
    }


    /**
     * Set urlVariable
     *
     * @param string $urlVariable
     * @return Media
     */
    public function setUrlVariable(string $urlVariable): self
    {
        $this->urlVariable = $urlVariable;
    
        return $this;
    }

    /**
     * Get urlVariable
     *
     * @return string 
     */
    public function getUrlVariable(): ?string
    {
        return $this->urlVariable;
    }
    
    
}
