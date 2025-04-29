<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity
 *
 * #[ORM\Table(name="rubrique")]
 * #[ORM\Entity](repositoryClass="App\Entity\RubriqueRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Rubrique {

    //initialisation des idparent et idgrandparent
    function __construct() {

        $this->isFaq = 0;
    }

    /**
     * @var ArrayCollection Cadre $cadres
     * Owning Side
     * @ORM\ManyToMany(targetEntity="App\Entity\Cadre", inversedBy="rubriques", cascade={"persist","merge"})
     * @ORM\JoinTable(name="cadresrubrique",
     * joinColumns={@ORM\JoinColumn(name="idrubrique", referencedColumnName="idrubrique")},
     * inverseJoinColumns={@ORM\JoinColumn(name="idcadre", referencedColumnName="idcadre" )}
     * )
     */
    private $cadres;

    /**
     * @var ArrayCollection Article $articles
     * #[ORM\OneToMany(targetEntity: App\Entity\Article::class, mappedBy="rubrique" )]
     * 
     */
    private $articles;

    /**
     * @var ArrayCollection Media $medias
     * #[ORM\OneToMany(targetEntity: App\Entity\Media::class, mappedBy="rubrique" )]
     * 
     */
    private $medias;

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idrubrique", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var string $nomRubrique
     * #[ORM\Column(name="nomrubrique",type="string", length=100)]
     * #[Assert\NotBlank()]
     * @Assert\MinLength(3)
     */
    private $nomRubrique;

    /**
     * @Gedmo\Translatable
     * @var text $descriptionRubrique
     * #[ORM\Column(name="descriptionrubrique",type="text", nullable=true)]
     * #[Assert\NotBlank(message="")]
     */
    private $descriptionRubrique;

    /**
     * @var integer $rubriqueAjoutPar
     * #[ORM\Column(name="rubriqueajoutpar",type="integer")]
     * @Assert\Notblank
     */
    private $rubriqueAjoutPar;

    /**
     * @var integer $typePresentation
     * #[ORM\Column(name="typepresentation",type="integer")]
     */
    private $typePresentation;

    /**
     * @var datetime $rubriqueDateAjout
     * #[ORM\Column(name="rubriqueDateAjout",type="datetime" , nullable=false)]
     * #[Assert\NotBlank()]
     */
    private $rubriqueDateAjout;

    /**
     * @var integer $rubriqueModifPar
     * #[ORM\Column(name="rubriquemodifpar",type="integer", nullable=true)]
     */
    private $rubriqueModifPar;

    /**
     * @var datetime $rubriqueDateModif
     * #[ORM\Column(name="rubriqueDateModif",type="datetime" , nullable=true)]
     */
    private $rubriqueDateModif;

    /**
     * @var integer $typeRubrique
     * #[ORM\Column(name="typerubrique",type="integer" )]
     * @Assert\Regex(pattern="/^[1-3]$/", message="la valeur doit être comprise entre 1 et 3")
     */
    private $typeRubrique;

    /**
     * @var Rubrique $idparent
     * #[ORM\ManyToOne(targetEntity: App\Entity\Rubrique::class)]
     * @ORM\JoinColumn(name="idparent", referencedColumnName="idrubrique")
     */
    private $idparent;

    /**
     * @var integer $idgrandparent
     * #[ORM\Column(name="idgrandparent", type="integer")]
     * 
     */
    private $idgrandparent;

    /**
     * @var integer $isFaq
     * #[ORM\Column(name="isfaq", type="integer", nullable =true)]
     * 
     */
    private $isFaq;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * @var string $urlIcone
     * #[ORM\Column(name="urlicone",type="string",length=170, nullable=true)]
     */
    private $urlIcone;

    /**
     * @Assert\File(maxSize="6000000")
     * mimeTypes = {"image/gif", "image/jpeg", "image/png"},
     */
    public $icone;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {



        if (null !== $this->icone) {
            // faites ce que vous voulez pour générer un nom unique
            $this->urlIcone = sha1(uniqid(mt_rand(), true)) . '.' . $this->icone->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {

        if (null === $this->icone) {
            return;
        }

        $this->icone->move($this->getUploadRootDir(), $this->urlIcone);
        chmod($this->getUploadRootDir(), 0755);
        unset($this->icone);
    }

    public function removeUpload($icone) {

        unlink($icone);
    }

    public function getAbsolutePath(): ?string {
        return null === $this->urlIcone ? null : $this->getUploadRootDir() . '' . $this->urlIcone;
    }

    public function getWebPath(): ?string {
        return null === $this->urlIcone ? null : $this->getUploadDir() . '' . $this->urlIcone;
    }

    public function getUploadRootDir(): ?string {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir(): ?string {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'upload/icones/';
    }

    /**
     * @ORM\PrePersist()
     * 
     */
    public function preAjout() {
        $this->rubriqueDateAjout = new \DateTime();
        //$this->medias-> = new \DateTime();

        if (null === $this->typeRubrique) {
            $this->typeRubrique = 3;
        }

        $parent0 = $this->idparent;

        if (null === $parent0) {
            $this->idparent = 0;
        } else {
            $parent_parent0 = $parent0->getIdparent();

            if ($parent_parent0 === null) {
                $this->idgrandparent = 0;
            } else {

                $idrub_parent0 = $parent_parent0->getId();

                if ($idrub_parent0 === 0) {
                    $this->idgrandparent = $parent0->getId();
                } else {
                    $this->idgrandparent = $idrub_parent0;
                }
            }
        }
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
     * Set nomRubrique
     *
     * @param string $nomRubrique
     * @return Rubrique
     */
    public function setNomRubrique(string $nomRubrique): self {
        $this->nomRubrique = $nomRubrique;

        return $this;
    }

    /**
     * Get nomRubrique
     *
     * @return string 
     */
    public function getNomRubrique(): ?string {
        return $this->nomRubrique;
    }

    /**
     * Set descriptionRubrique
     *
     * @param string $descriptionRubrique
     * @return Rubrique
     */
    public function setDescriptionRubrique(string $descriptionRubrique): self {
        $this->descriptionRubrique = $descriptionRubrique;

        return $this;
    }

    /**
     * Get descriptionRubrique
     *
     * @return string 
     */
    public function getDescriptionRubrique(): ?string {
        return $this->descriptionRubrique;
    }

    /**
     * Set rubriqueAjoutPar
     *
     * @param integer $rubriqueAjoutPar
     * @return Rubrique
     */
    public function setRubriqueAjoutPar(string $rubriqueAjoutPar): self {
        $this->rubriqueAjoutPar = $rubriqueAjoutPar;

        return $this;
    }

    /**
     * Get rubriqueAjoutPar
     *
     * @return integer 
     */
    public function getRubriqueAjoutPar(): ?string {
        return $this->rubriqueAjoutPar;
    }

    /**
     * Set rubriqueDateAjout
     *
     * @param \DateTime $rubriqueDateAjout
     * @return Rubrique
     */
    public function setRubriqueDateAjout(string $rubriqueDateAjout): self {
        $this->rubriqueDateAjout = $rubriqueDateAjout;

        return $this;
    }

    /**
     * Get rubriqueDateAjout
     *
     * @return \DateTime 
     */
    public function getRubriqueDateAjout(): ?string {
        return $this->rubriqueDateAjout;
    }

    /**
     * Set rubriqueModifPar
     *
     * @param integer $rubriqueModifPar
     * @return Rubrique
     */
    public function setRubriqueModifPar(string $rubriqueModifPar): self {
        $this->rubriqueModifPar = $rubriqueModifPar;

        return $this;
    }

    /**
     * Get rubriqueModifPar
     *
     * @return integer 
     */
    public function getRubriqueModifPar(): ?string {
        return $this->rubriqueModifPar;
    }

    /**
     * Set rubriqueDateModif
     *
     * @param \DateTime $rubriqueDateModif
     * @return Rubrique
     */
    public function setRubriqueDateModif(string $rubriqueDateModif): self {
        $this->rubriqueDateModif = $rubriqueDateModif;

        return $this;
    }

    /**
     * Get rubriqueDateModif
     *
     * @return \DateTime 
     */
    public function getRubriqueDateModif(): ?string {
        return $this->rubriqueDateModif;
    }

    /**
     * Set typeRubrique
     *
     * @param integer $typeRubrique
     * @return Rubrique
     */
    public function setTypeRubrique(string $typeRubrique): self {
        $this->typeRubrique = $typeRubrique;

        return $this;
    }

    /**
     * Get typeRubrique
     *
     * @return integer 
     */
    public function getTypeRubrique(): ?string {
        return $this->typeRubrique;
    }

    /**
     * Set idgrandparent
     *
     * @param integer $idgrandparent
     * @return Rubrique
     */
    public function setIdgrandparent(string $idgrandparent): self {
        $this->idgrandparent = $idgrandparent;

        return $this;
    }

    /**
     * Get idgrandparent
     *
     * @return integer 
     */
    public function getIdgrandparent(): ?string {
        return $this->idgrandparent;
    }

    /**
     * Set urlIcone
     *
     * @param string $urlIcone
     * @return Rubrique
     */
    public function setUrlIcone(string $urlIcone): self {
        $this->urlIcone = $urlIcone;

        return $this;
    }

    /**
     * Get urlIcone
     *
     * @return string 
     */
    public function getUrlIcone(): ?string {
        return $this->urlIcone;
    }

    /**
     * Add cadres
     *
     * @param \App\Entity\Cadre $cadres
     * @return Rubrique
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
     * Add articles
     *
     * @param \App\Entity\Article $articles
     * @return Rubrique
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
     * @return Rubrique
     */
    public function addMedia(\App\Entity\Media $medias) {
        $this->medias[] = $medias;
        $medias->setRubrique($this);
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
     * Set idparent
     *
     * @param \App\Entity\Rubrique $idparent
     * @return Rubrique
     */
    public function setIdparent(\App\Entity\Rubrique $idparent = null) {
        $this->idparent = $idparent;

        return $this;
    }

    /**
     * Get idparent
     *
     * @return \App\Entity\Rubrique 
     */
    public function getIdparent(): ?string {
        return $this->idparent;
    }

    /**
     * Set isFaq
     *
     * @param integer $isFaq
     * @return Rubrique
     */
    public function setIsFaq(string $isFaq): self {
        $this->isFaq = $isFaq;

        return $this;
    }

    /**
     * Get isFaq
     *
     * @return integer 
     */
    public function getIsFaq(): ?string {
        return $this->isFaq;
    }

    /**
     * Set typePresentation
     *
     * @param integer $typePresentation
     * @return Rubrique
     */
    public function setTypePresentation(string $typePresentation): self {
        $this->typePresentation = $typePresentation;

        return $this;
    }

    /**
     * Get typePresentation
     *
     * @return integer 
     */
    public function getTypePresentation(): ?string {
        return $this->typePresentation;
    }

}
