<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Classe qui va gere les articles du point de vue détails et articles au sens proprement dit
 *
 * @author Gautier
 * #[ORM\Entity](repositoryClass="App\Entity\ArticleRepository")
 * #[ORM\Table(name="article")]
 * @ORM\HasLifecycleCallbacks
 */
class Article {

    function __construct() {

        $this->setCompteurArticle(0);
    }

    /**
     * #[ORM\OneToMany(targetEntity: App\Entity\Menu::class, mappedBy="Menu")]
     */
    private $menu;

    /**
     * @var ArrayCollection Media $medias
     * Owning Side
     * @ORM\ManyToMany(targetEntity="App\Entity\Media", inversedBy="articles", cascade={"all"})
     * @ORM\JoinTable(name="pointer",
     * joinColumns={@ORM\JoinColumn(name="article_idarticle", referencedColumnName="idarticle")},
     * inverseJoinColumns={@ORM\JoinColumn(name="media_idmedia", referencedColumnName="idmedia" )}
     * )
     */
    private $medias;

    /**
     * @var ArrayCollection Cadre $cadres
     * Owning Side
     * @ORM\ManyToMany(targetEntity="App\Entity\Cadre", inversedBy="articles", cascade={"persist","merge"})
     * @ORM\JoinTable(name="positionner",
     * joinColumns={@ORM\JoinColumn(name="article_idarticle", referencedColumnName="idarticle")},
     * inverseJoinColumns={@ORM\JoinColumn(name="cadre_idcadre", referencedColumnName="idcadre" )}
     * )
     */
    private $cadres;

    /**
     * @var Rubrique $rubrique
     * #[ORM\ManyToOne(targetEntity: App\Entity\Rubrique::class, inversedBy="articles", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idrubrique", referencedColumnName="idrubrique")
     * })
     */
    private $rubrique;

    /**
     * @var integer $id
     * #[ORM\Id] 
     * #[ORM\GeneratedValue](strategy="AUTO")
     * #[ORM\Column(name="idarticle" , type="integer")]
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var string $titreArticle
     * #[ORM\Column(name="titrearticle", type="string",length=100, nullable=true)]
     * #[Assert\NotBlank()]
     */
    private $titreArticle;

    /**
     * @Gedmo\Translatable
     * @var text $introTexteArticle
     * #[ORM\Column(name="introtextearticle" , type="text", nullable=true)]
     *  
     */
    private $introTexteArticle;

    /**
     * @Gedmo\Translatable
     * @var text $descriptionArticle
     * #[ORM\Column(name="descriptionarticle" , type="text", nullable=true)]
     *   
     */
    private $descriptionArticle;

    /**
     * @var integer $statutArticle
     * #[ORM\Column(name="statutarticle" , type="integer")]
     * @Assert\Regex(pattern="/^[0-6]$/", message="la valeur doit être comprise entre 0 et 6")  
     */
    private $statutArticle;

    /**
     * @var string $urlArticle
     * #[ORM\Column(name="urlarticle" , type="string", nullable=true)]
     */
    private $urlArticle;

    /**
     * @var string $referenceArticle
     * #[ORM\Column(name="referencearticle" , type="string", nullable=true)]
     *   
     */
    private $referenceArticle;

    /**
     * @var integer $corbeilleArticle
     * #[ORM\Column(name="corbeillearticle" , type="integer", nullable=true)]
     *   
     */
    private $corbeilleArticle;

    /**
     * @var integer $archiveArticle
     * #[ORM\Column(name="archivearticle" , type="integer", nullable=true)]
     *   
     */
    private $archiveArticle;

    /**
     * @var integer $lastRubriqueArticle
     * #[ORM\Column(name="lastrubriquearticle" , type="integer", nullable=true)]
     *   
     */
    private $lastRubriqueArticle;

    /**
     * @var integer $compteurArticle
     * #[ORM\Column(name="compteurarticle" , type="integer", nullable=true)]
     *   
     */
    private $compteurArticle;

    /**
     * @var datetime $articleDatePublie
     * #[ORM\Column(name="articledatepublie" , type="datetime", nullable=true)]
     *   
     */
    private $articleDatePublie;

    /**
     * @var datetime $articleDateAjout
     * #[ORM\Column(name="articledateajout" , type="datetime", nullable=true)]
     *   
     */
    private $articleDateAjout;

    /**
     * @var datetime $articleDateModif
     * #[ORM\Column(name="articledatemodif" , type="datetime", nullable=true)]
     *   
     */
    private $articleDateModif;

    /**
     * @var datetime $articleDatePublie
     * #[ORM\Column(name="affichedatepublie" , type="integer", nullable=true)]
     *   
     */
    private $afficheDatePublie;

    /**
     * @var integer $afficheAuteur
     * #[ORM\Column(name="afficheauteur" , type="integer", nullable=true)]
     *   
     */
    private $afficheAuteur;

    /**
     * @var integer $afficheAccueil
     * #[ORM\Column(name="afficheaccueil" , type="integer", nullable=true)]
     *   
     */
    private $afficheAccueil;

    /**
     * @var datetime $afficheReference
     * #[ORM\Column(name="affichereference" , type="integer", nullable=true)]
     *   
     */
    private $afficheReference;

    /**
     * @var datetime $articleDateSupprime
     * #[ORM\Column(name="articledatesupprime" , type="datetime", nullable=true)]
     *   
     */
    private $articleDateSupprime;

    /**
     * @var datetime $articleDateRestaure
     * #[ORM\Column(name="articledaterestaure" , type="datetime", nullable=true)]
     *   
     */
    private $articleDateRestaure;

    /**
     * @var datetime $articleDateDepublie
     * #[ORM\Column(name="articledatedepublie" , type="datetime", nullable=true)]
     *   
     */
    private $articleDateDepublie;

    /**
     * @var datetime $articleDateArchive
     * #[ORM\Column(name="articledatearchive" , type="datetime", nullable=true)]
     *   
     */
    private $articleDateArchive;

    /**
     * @var datetime $articleDateValide
     * #[ORM\Column(name="articledatevalide" , type="datetime", nullable=true)]
     *   
     */
    private $articleDateValide;

    /**
     * @var integer $articleModifPar
     * #[ORM\Column(name="articlemodifpar" , type="integer", nullable=true)]
     *   
     */
    private $articleModifPar;

    /**
     * @var integer $articleSupprimePar
     * #[ORM\Column(name="articlesupprimepar" , type="integer", nullable=true)]
     * 
     *   
     */
    private $articleSupprimePar;

    /**
     * @var integer $articleAjoutPar
     * #[ORM\Column(name="articleajoutPar" , type="integer", nullable=true)]
     * 
     *   
     */
    private $articleAjoutPar;

    /**
     * @var integer $articleValidePar
     * #[ORM\Column(name="articlevalidepar" , type="integer", nullable=true)]
     *   
     */
    private $articleValidePar;

    /**
     * @var integer $articleArchivePar
     * #[ORM\Column(name="articlearchivepar" , type="integer", nullable=true)]
     *   
     */
    private $articleArchivePar;

    /**
     * @var integer $articleDepubliePar
     * #[ORM\Column(name="articledepubliepar" , type="integer", nullable=true)]
     *   
     */
    private $articleDepubliePar;

    /**
     * @var integer $articleRestaurePar
     * #[ORM\Column(name="articlerestaurepar" , type="integer", nullable=true)]
     *   
     */
    private $articleRestaurePar;

    /**
     * @var integer $articlePubliePar
     * #[ORM\Column(name="articlepubliepar" , type="integer", nullable=true)]
     *   
     */
    private $articlePubliePar;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * @var integer $ordre
     * #[ORM\Column(name="ordre" , type="integer", nullable=true)]
     *   
     */
    private $ordre;
    
    
    /**
     * @var integer $typePre 
     * #[ORM\Column(name="typepresentation",type="integer",nullable=true)]
     */
    
    private $typePre; 
    
    /**
     * @ORM\PrePersist()
     * 
     */
    public function presend() {
        $this->articleDateAjout = new \DateTime();
        $this->corbeilleArticle = 0;  // pas a la corbeille
        $this->archiveArticle = 0;  // pas archive
        $this->statutArticle = 1; /// en cours de redaction 
        $this->corbeilleArticle = 0;
    }

    public function setTranslatableLocale(string $locale): self {
        $this->locale = $locale;
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
     * Set titreArticle
     *
     * @param string $titreArticle
     * @return Article
     */
    public function setTitreArticle(string $titreArticle): self {
        $this->titreArticle = $titreArticle;

        return $this;
    }

    /**
     * Get titreArticle
     *
     * @return string 
     */
    public function getTitreArticle(): ?string {
        return $this->titreArticle;
    }

    /**
     * Set introTexteArticle
     *
     * @param string $introTexteArticle
     * @return Article
     */
    public function setIntroTexteArticle(string $introTexteArticle): self {
        $this->introTexteArticle = $introTexteArticle;

        return $this;
    }

    /**
     * Get introTexteArticle
     *
     * @return string 
     */
    public function getIntroTexteArticle(): ?string {
        return $this->introTexteArticle;
    }

    /**
     * Set descriptionArticle
     *
     * @param string $descriptionArticle
     * @return Article
     */
    public function setDescriptionArticle(string $descriptionArticle): self {
        $this->descriptionArticle = $descriptionArticle;

        return $this;
    }

    /**
     * Get descriptionArticle
     *
     * @return string 
     */
    public function getDescriptionArticle(): ?string {
        return $this->descriptionArticle;
    }

    /**
     * Set statutArticle
     *
     * @param integer $statutArticle
     * @return Article
     */
    public function setStatutArticle(string $statutArticle): self {
        $this->statutArticle = $statutArticle;

        return $this;
    }

    /**
     * Get statutArticle
     *
     * @return integer 
     */
    public function getStatutArticle(): ?string {
        return $this->statutArticle;
    }

    /**
     * Set urlArticle
     *
     * @param string $urlArticle
     * @return Article
     */
    public function setUrlArticle(string $urlArticle): self {
        $this->urlArticle = $urlArticle;

        return $this;
    }

    /**
     * Get urlArticle
     *
     * @return string 
     */
    public function getUrlArticle(): ?string {
        return $this->urlArticle;
    }

    /**
     * Set referenceArticle
     *
     * @param string $referenceArticle
     * @return Article
     */
    public function setReferenceArticle(string $referenceArticle): self {
        $this->referenceArticle = $referenceArticle;

        return $this;
    }

    /**
     * Get referenceArticle
     *
     * @return string 
     */
    public function getReferenceArticle(): ?string {
        return $this->referenceArticle;
    }

    /**
     * Set corbeilleArticle
     *
     * @param integer $corbeilleArticle
     * @return Article
     */
    public function setCorbeilleArticle(string $corbeilleArticle): self {
        $this->corbeilleArticle = $corbeilleArticle;

        return $this;
    }

    /**
     * Get corbeilleArticle
     *
     * @return integer 
     */
    public function getCorbeilleArticle(): ?string {
        return $this->corbeilleArticle;
    }

    /**
     * Set archiveArticle
     *
     * @param integer $archiveArticle
     * @return Article
     */
    public function setArchiveArticle(string $archiveArticle): self {
        $this->archiveArticle = $archiveArticle;

        return $this;
    }

    /**
     * Get archiveArticle
     *
     * @return integer 
     */
    public function getArchiveArticle(): ?string {
        return $this->archiveArticle;
    }

    /**
     * Set lastRubriqueArticle
     *
     * @param integer $lastRubriqueArticle
     * @return Article
     */
    public function setLastRubriqueArticle(string $lastRubriqueArticle): self {
        $this->lastRubriqueArticle = $lastRubriqueArticle;

        return $this;
    }

    /**
     * Get lastRubriqueArticle
     *
     * @return integer 
     */
    public function getLastRubriqueArticle(): ?string {
        return $this->lastRubriqueArticle;
    }

    /**
     * Set articleDatePublie
     *
     * @param \DateTime $articleDatePublie
     * @return Article
     */
    public function setArticleDatePublie(string $articleDatePublie): self {
        $this->articleDatePublie = $articleDatePublie;

        return $this;
    }

    /**
     * Get articleDatePublie
     *
     * @return \DateTime 
     */
    public function getArticleDatePublie(): ?string {
        return $this->articleDatePublie;
    }

    /**
     * Set articleDateAjout
     *
     * @param \DateTime $articleDateAjout
     * @return Article
     */
    public function setArticleDateAjout(string $articleDateAjout): self {
        $this->articleDateAjout = $articleDateAjout;

        return $this;
    }

    /**
     * Get articleDateAjout
     *
     * @return \DateTime 
     */
    public function getArticleDateAjout(): ?string {
        return $this->articleDateAjout;
    }

    /**
     * Set articleDateModif
     *
     * @param \DateTime $articleDateModif
     * @return Article
     */
    public function setArticleDateModif(string $articleDateModif): self {
        $this->articleDateModif = $articleDateModif;

        return $this;
    }

    /**
     * Get articleDateModif
     *
     * @return \DateTime 
     */
    public function getArticleDateModif(): ?string {
        return $this->articleDateModif;
    }

    /**
     * Set articleDateSupprime
     *
     * @param \DateTime $articleDateSupprime
     * @return Article
     */
    public function setArticleDateSupprime(string $articleDateSupprime): self {
        $this->articleDateSupprime = $articleDateSupprime;

        return $this;
    }

    /**
     * Get articleDateSupprime
     *
     * @return \DateTime 
     */
    public function getArticleDateSupprime(): ?string {
        return $this->articleDateSupprime;
    }

    /**
     * Set articleDateRestaure
     *
     * @param \DateTime $articleDateRestaure
     * @return Article
     */
    public function setArticleDateRestaure(string $articleDateRestaure): self {
        $this->articleDateRestaure = $articleDateRestaure;

        return $this;
    }

    /**
     * Get articleDateRestaure
     *
     * @return \DateTime 
     */
    public function getArticleDateRestaure(): ?string {
        return $this->articleDateRestaure;
    }

    /**
     * Set articleDateDepublie
     *
     * @param \DateTime $articleDateDepublie
     * @return Article
     */
    public function setArticleDateDepublie(string $articleDateDepublie): self {
        $this->articleDateDepublie = $articleDateDepublie;

        return $this;
    }

    /**
     * Get articleDateDepublie
     *
     * @return \DateTime 
     */
    public function getArticleDateDepublie(): ?string {
        return $this->articleDateDepublie;
    }

    /**
     * Set articleDateArchive
     *
     * @param \DateTime $articleDateArchive
     * @return Article
     */
    public function setArticleDateArchive(string $articleDateArchive): self {
        $this->articleDateArchive = $articleDateArchive;

        return $this;
    }

    /**
     * Get articleDateArchive
     *
     * @return \DateTime 
     */
    public function getArticleDateArchive(): ?string {
        return $this->articleDateArchive;
    }

    /**
     * Set articleDateValide
     *
     * @param \DateTime $articleDateValide
     * @return Article
     */
    public function setArticleDateValide(string $articleDateValide): self {
        $this->articleDateValide = $articleDateValide;

        return $this;
    }

    /**
     * Get articleDateValide
     *
     * @return \DateTime 
     */
    public function getArticleDateValide(): ?string {
        return $this->articleDateValide;
    }

    /**
     * Set articleModifPar
     *
     * @param integer $articleModifPar
     * @return Article
     */
    public function setArticleModifPar(string $articleModifPar): self {
        $this->articleModifPar = $articleModifPar;

        return $this;
    }

    /**
     * Get articleModifPar
     *
     * @return integer 
     */
    public function getArticleModifPar(): ?string {
        return $this->articleModifPar;
    }

    /**
     * Set articleSupprimePar
     *
     * @param integer $articleSupprimePar
     * @return Article
     */
    public function setArticleSupprimePar(string $articleSupprimePar): self {
        $this->articleSupprimePar = $articleSupprimePar;

        return $this;
    }

    /**
     * Get articleSupprimePar
     *
     * @return integer 
     */
    public function getArticleSupprimePar(): ?string {
        return $this->articleSupprimePar;
    }

    /**
     * Set articleAjoutPar
     *
     * @param integer $articleAjoutPar
     * @return Article
     */
    public function setArticleAjoutPar(string $articleAjoutPar): self {
        $this->articleAjoutPar = $articleAjoutPar;

        return $this;
    }

    /**
     * Get articleAjoutPar
     *
     * @return integer 
     */
    public function getArticleAjoutPar(): ?string {
        return $this->articleAjoutPar;
    }

    /**
     * Set articleValidePar
     *
     * @param integer $articleValidePar
     * @return Article
     */
    public function setArticleValidePar(string $articleValidePar): self {
        $this->articleValidePar = $articleValidePar;

        return $this;
    }

    /**
     * Get articleValidePar
     *
     * @return integer 
     */
    public function getArticleValidePar(): ?string {
        return $this->articleValidePar;
    }

    /**
     * Set articleArchivePar
     *
     * @param integer $articleArchivePar
     * @return Article
     */
    public function setArticleArchivePar(string $articleArchivePar): self {
        $this->articleArchivePar = $articleArchivePar;

        return $this;
    }

    /**
     * Get articleArchivePar
     *
     * @return integer 
     */
    public function getArticleArchivePar(): ?string {
        return $this->articleArchivePar;
    }

    /**
     * Set articleDepubliePar
     *
     * @param integer $articleDepubliePar
     * @return Article
     */
    public function setArticleDepubliePar(string $articleDepubliePar): self {
        $this->articleDepubliePar = $articleDepubliePar;

        return $this;
    }

    /**
     * Get articleDepubliePar
     *
     * @return integer 
     */
    public function getArticleDepubliePar(): ?string {
        return $this->articleDepubliePar;
    }

    /**
     * Set articleRestaurePar
     *
     * @param integer $articleRestaurePar
     * @return Article
     */
    public function setArticleRestaurePar(string $articleRestaurePar): self {
        $this->articleRestaurePar = $articleRestaurePar;

        return $this;
    }

    /**
     * Get articleRestaurePar
     *
     * @return integer 
     */
    public function getArticleRestaurePar(): ?string {
        return $this->articleRestaurePar;
    }

    /**
     * Set articlePubliePar
     *
     * @param integer $articlePubliePar
     * @return Article
     */
    public function setArticlePubliePar(string $articlePubliePar): self {
        $this->articlePubliePar = $articlePubliePar;

        return $this;
    }

    /**
     * Get articlePubliePar
     *
     * @return integer 
     */
    public function getArticlePubliePar(): ?string {
        return $this->articlePubliePar;
    }

    /**
     * Add menu
     *
     * @param \App\Entity\Menu $menu
     * @return Article
     */
    public function addMenu(\App\Entity\Menu $menu) {
        $this->menu[] = $menu;

        return $this;
    }

    /**
     * Remove menu
     *
     * @param \App\Entity\Menu $menu
     */
    public function removeMenu(\App\Entity\Menu $menu) {
        $this->menu->removeElement($menu);
    }

    /**
     * Get menu
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenu(): ?string {
        return $this->menu;
    }

    /**
     * Add medias
     *
     * @param \App\Entity\Media $medias
     * @return Article
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
     * Add cadres
     *
     * @param \App\Entity\Cadre $cadres
     * @return Article
     */
    public function addCadre(\App\Entity\Cadre $cadres) {
        $this->cadres[] = $cadres;

        return $this;
    }

    /**
     * Remove cadres
     *
     * @param \App\Entity\Cadre $cadres
     */
    public function removeCadre(\App\Entity\Cadre $cadres) {
        $this->cadres->removeElement($cadres);
    }

    /**
     * Get cadres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCadres(): ?string {
        return $this->cadres;
    }

    /**
     * Set rubrique
     *
     * @param \App\Entity\Rubrique $rubrique
     * @return Article
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
     * Set compteurArticle
     *
     * @param integer $compteurArticle
     * @return Article
     */
    public function setCompteurArticle(string $compteurArticle): self {
        $this->compteurArticle = $compteurArticle;

        return $this;
    }

    /**
     * Get compteurArticle
     *
     * @return integer 
     */
    public function getCompteurArticle(): ?string {
        return $this->compteurArticle;
    }

    /**
     * Set afficheDatePublie
     *
     * @param integer $afficheDatePublie
     * @return Article
     */
    public function setAfficheDatePublie(string $afficheDatePublie): self {
        $this->afficheDatePublie = $afficheDatePublie;

        return $this;
    }

    /**
     * Get afficheDatePublie
     *
     * @return integer 
     */
    public function getAfficheDatePublie(): ?string {
        return $this->afficheDatePublie;
    }

    /**
     * Set afficheAuteur
     *
     * @param integer $afficheAuteur
     * @return Article
     */
    public function setAfficheAuteur(string $afficheAuteur): self {
        $this->afficheAuteur = $afficheAuteur;

        return $this;
    }

    /**
     * Get afficheAuteur
     *
     * @return integer 
     */
    public function getAfficheAuteur(): ?string {
        return $this->afficheAuteur;
    }

    /**
     * Set afficheReference
     *
     * @param integer $afficheReference
     * @return Article
     */
    public function setAfficheReference(string $afficheReference): self {
        $this->afficheReference = $afficheReference;

        return $this;
    }

    /**
     * Get afficheReference
     *
     * @return integer 
     */
    public function getAfficheReference(): ?string {
        return $this->afficheReference;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return Article
     */
    public function setOrdre(string $ordre): self {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get $ordre
     *
     * @return integer 
     */
    public function getOrdre(): ?string {
        return $this->ordre;
    }

    /**
     * Set afficheAccueil
     *
     * @param integer $afficheAccueil
     * @return Article
     */
    public function setAfficheAccueil(string $afficheAccueil): self {
        $this->afficheAccueil = $afficheAccueil;

        return $this;
    }

    /**
     * Get afficheAccueil
     *
     * @return integer 
     */
    public function getAfficheAccueil(): ?string {
        return $this->afficheAccueil;
    }



    /**
     * Set typePre
     *
     * @param integer $typePre
     * @return Article
     */
    public function setTypePre(string $typePre): self
    {
        $this->typePre = $typePre;
    
        return $this;
    }

    /**
     * Get typePre
     *
     * @return integer 
     */
    public function getTypePre(): ?string
    {
        return $this->typePre;
    }
}
