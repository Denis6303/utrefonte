<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Classe qui va gerer les cadres
 *
 * @author Edem
 * #[ORM\Entity](repositoryClass="App\Entity\CadreRepository")
 * #[ORM\Table(name="cadre")]
 * @ORM\HasLifecycleCallbacks
 */
class Cadre {

    function __construct() {
        $this->etatCadre = 1;
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idcadre", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection Article $articles
     *
     * Inverse Side
     * @ORM\ManyToMany(targetEntity="App\Entity\Article",mappedBy="cadres", cascade={"persist"})
     */
    private $articles;

    /**
     * @var Media $medias
     * #[ORM\OneToMany(targetEntity: App\Entity\Media::class, mappedBy="cadre" )]
     * 
     */
    private $medias;

    /**
     * @var ArrayCollection Rubrique $rubriques
     * Inverse Side
     * @ORM\ManyToMany(targetEntity="App\Entity\Rubrique",mappedBy="cadres", cascade={"persist"})
     */
    private $rubriques;

    /**
     * @var TypeCadre $typeCadre
     * #[ORM\ManyToOne(targetEntity: App\Entity\TypeCadre::class, inversedBy="cadres", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idtypecadre", referencedColumnName="idtypecadre")
     * })
     */
    private $typeCadre;

    /**
     * @var string $libCadre
     * #[ORM\Column(name="libcadre",type="string",length=100)]
     * #[Assert\NotBlank(message="Le libellé du cadre ne peut être vide")]
     * @Assert\MinLength(2)
     */
    private $libCadre;

    /**
     * @var text $contenuCadre
     * #[ORM\Column(name="contenucadre",type="text")]
     */
    private $contenuCadre;

    /**
     * @var integer $positionCadre
     * #[ORM\Column(name="positioncadre",type="integer")]
     */
    private $positionCadre;

    /**
     * @var integer $natureCadre
     * #[ORM\Column(name="naturecadre",type="integer")]
     */
    private $natureCadre;

    /**
     * @var integer $cadreAjoutPar
     * #[ORM\Column(name="cadreajoutpar" , type="integer")]
     *   
     */
    private $cadreAjoutPar;

    /**
     * @var integer $cadreModifPar
     * #[ORM\Column(name="cadremodifpar" , type="integer", nullable=true)]
     *   
     */
    private $cadreModifPar;

    /**
     * @var datetime $cadreDateAjout
     * #[ORM\Column(name="cadredateajout" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $cadreDateAjout;

    /**
     * @var datetime $cadreDateModif
     * #[ORM\Column(name="cadredatemodif" , type="datetime", nullable=true)]
     * #[Assert\NotBlank()]
     *   
     */
    private $cadreDateModif;

    /**
     * @var integer $etatCadre
     * #[ORM\Column(name="etatcadre",type="integer" )]
     *   
     */
    private $etatCadre;

    /**
     * @var integer $rubPointer
     * #[ORM\Column(name="rubPointer",type="integer", nullable=true )]
     *   
     */
    private $rubPointer;

    /**
     * @var integer $articlePointer
     * #[ORM\Column(name="articlePointer",type="integer", nullable=true )]
     *   
     */
    private $articlePointer;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

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
     * Set libCadre
     *
     * @param string $libCadre
     * @return Cadre
     */
    public function setLibCadre(string $libCadre): self {
        $this->libCadre = $libCadre;

        return $this;
    }

    /**
     * Get libCadre
     *
     * @return string 
     */
    public function getLibCadre(): ?string {
        return $this->libCadre;
    }

    /**
     * Set contenuCadre
     *
     * @param string $contenuCadre
     * @return Cadre
     */
    public function setContenuCadre(string $contenuCadre): self {
        $this->contenuCadre = $contenuCadre;

        return $this;
    }

    /**
     * Get contenuCadre
     *
     * @return string 
     */
    public function getContenuCadre(): ?string {
        return $this->contenuCadre;
    }

    /**
     * Set positionCadre
     *
     * @param integer $positionCadre
     * @return Cadre
     */
    public function setPositionCadre(string $positionCadre): self {
        $this->positionCadre = $positionCadre;

        return $this;
    }

    /**
     * Get positionCadre
     *
     * @return integer 
     */
    public function getPositionCadre(): ?string {
        return $this->positionCadre;
    }

    /**
     * Set natureCadre
     *
     * @param integer $natureCadre
     * @return Cadre
     */
    public function setNatureCadre(string $natureCadre): self {
        $this->natureCadre = $natureCadre;

        return $this;
    }

    /**
     * Get natureCadre
     *
     * @return integer 
     */
    public function getNatureCadre(): ?string {
        return $this->natureCadre;
    }

    /**
     * Set cadreAjoutPar
     *
     * @param integer $cadreAjoutPar
     * @return Cadre
     */
    public function setCadreAjoutPar(string $cadreAjoutPar): self {
        $this->cadreAjoutPar = $cadreAjoutPar;

        return $this;
    }

    /**
     * Get cadreAjoutPar
     *
     * @return integer 
     */
    public function getCadreAjoutPar(): ?string {
        return $this->cadreAjoutPar;
    }

    /**
     * Set cadreModifPar
     *
     * @param integer $cadreModifPar
     * @return Cadre
     */
    public function setCadreModifPar(string $cadreModifPar): self {
        $this->cadreModifPar = $cadreModifPar;

        return $this;
    }

    /**
     * Get cadreModifPar
     *
     * @return integer 
     */
    public function getCadreModifPar(): ?string {
        return $this->cadreModifPar;
    }

    /**
     * Set cadreDateAjout
     *
     * @param \DateTime $cadreDateAjout
     * @return Cadre
     */
    public function setCadreDateAjout(string $cadreDateAjout): self {
        $this->cadreDateAjout = $cadreDateAjout;

        return $this;
    }

    /**
     * Get cadreDateAjout
     *
     * @return \DateTime 
     */
    public function getCadreDateAjout(): ?string {
        return $this->cadreDateAjout;
    }

    /**
     * Set cadreDateModif
     *
     * @param \DateTime $cadreDateModif
     * @return Cadre
     */
    public function setCadreDateModif(string $cadreDateModif): self {
        $this->cadreDateModif = $cadreDateModif;

        return $this;
    }

    /**
     * Get cadreDateModif
     *
     * @return \DateTime 
     */
    public function getCadreDateModif(): ?string {
        return $this->cadreDateModif;
    }

    /**
     * Add articles
     *
     * @param \App\Entity\Article $articles
     * @return Cadre
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
     * Add medias
     *
     * @param \App\Entity\Media $medias
     * @return Cadre
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
     * Add rubriques
     *
     * @param \App\Entity\Rubrique $rubriques
     * @return Cadre
     */
    public function addRubrique(\App\Entity\Rubrique $rubriques) {
        $this->rubriques[] = $rubriques;

        return $this;
    }

    /**
     * Remove rubriques
     *
     * @param \App\Entity\Rubrique $rubriques
     */
    public function removeRubrique(\App\Entity\Rubrique $rubriques) {
        $this->rubriques->removeElement($rubriques);
    }

    /**
     * Get rubriques
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRubriques(): ?string {
        return $this->rubriques;
    }

    /**
     * Set typeCadre
     *
     * @param \App\Entity\TypeCadre $typeCadre
     * @return Cadre
     */
    public function setTypeCadre(\App\Entity\TypeCadre $typeCadre = null) {
        $this->typeCadre = $typeCadre;

        return $this;
    }

    /**
     * Get typeCadre
     *
     * @return \App\Entity\TypeCadre 
     */
    public function getTypeCadre(): ?string {
        return $this->typeCadre;
    }

    /**
     * Set etatCadre
     *
     * @param integer $etatCadre
     * @return Cadre
     */
    public function setEtatCadre(string $etatCadre): self {
        $this->etatCadre = $etatCadre;

        return $this;
    }

    /**
     * Get etatCadre
     *
     * @return integer 
     */
    public function getEtatCadre(): ?string {
        return $this->etatCadre;
    }

    /**
     * Set rubPointer
     *
     * @param integer $rubPointer
     * @return Cadre
     */
    public function setRubPointer(string $rubPointer): self {
        $this->rubPointer = $rubPointer;

        return $this;
    }

    /**
     * Get rubPointer
     *
     * @return integer 
     */
    public function getRubPointer(): ?string {
        return $this->rubPointer;
    }

    /**
     * Set articlePointer
     *
     * @param integer $articlePointer
     * @return Cadre
     */
    public function setArticlePointer(string $articlePointer): self {
        $this->articlePointer = $articlePointer;

        return $this;
    }

    /**
     * Get articlePointer
     *
     * @return integer 
     */
    public function getArticlePointer(): ?string {
        return $this->articlePointer;
    }

}
