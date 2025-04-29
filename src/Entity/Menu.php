<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\MenuRepository")
 * #[ORM\Table(name="menu")]
 */
class Menu {

    function __construct() {
        $this->menuDateAjout = new \Datetime();
    }

    /**
     * @var Article $article
     * #[ORM\ManyToOne(targetEntity: App\Entity\Article::class, inversedBy="menus", cascade={"persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idarticle", referencedColumnName="idarticle")
     * })
     */
    private $article;

    /**
     * @var GroupeMenu $groupeMenu
     * #[ORM\ManyToOne(targetEntity: App\Entity\GroupeMenu::class, inversedBy="menus", cascade={ "persist"})]
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idgroupemenu", referencedColumnName="idgroupemenu")
     * })
     */
    private $groupeMenu;

    /**
     * @var integer $id
     * #[ORM\Column(name="idmenu", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var string $libMenu
     * #[ORM\Column(name="libmenu",type="string",length=50)]
     * #[Assert\NotBlank(message="Ce champ ne peut être vide")]
     * @Assert\MinLength(2)
     */
    private $libMenu;

    /**
     * @var integer $typeMenu
     * #[ORM\Column(name="typemenu",type="integer")]
     * #[Assert\NotBlank()]
     */
    private $typeMenu;

    /**
     * @var integer $idParentMenu
     * #[ORM\Column(name="idparentmenu",type="integer")]
     * #[Assert\NotBlank()]
     */
    private $idParentMenu;

    /**
     * @var string $urlExterneMenu
     * #[ORM\Column(name="urlexternemenu",type="string",length=80)]
     * @Assert\MinLength(3)
     */
    private $urlExterneMenu;

    /**
     * @var integer $menuAjoutPar
     * #[ORM\Column(name="menuajoutpar",type="integer", nullable=true)]
     * #[Assert\NotBlank()]
     */
    private $menuAjoutPar;

    /**
     * @var datetime $menuDateAjout
     * #[ORM\Column(name="menudateAjout",type="datetime")]
     * #[Assert\NotBlank()]
     */
    private $menuDateAjout;

    /**
     * @var datetime $menuDateModif
     * #[ORM\Column(name="menudatemodif",type="datetime", nullable=true)]
     */
    private $menuDateModif;

    /**
     * @var intger $menuModifPar
     * #[ORM\Column(name="menumodifpar",type="integer", nullable=true)]
     */
    private $menuModifPar;

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
     * Set libMenu
     *
     * @param string $libMenu
     * @return Menu
     */
    public function setLibMenu(string $libMenu): self {
        $this->libMenu = $libMenu;

        return $this;
    }

    /**
     * Get libMenu
     *
     * @return string 
     */
    public function getLibMenu(): ?string {
        return $this->libMenu;
    }

    /**
     * Set typeMenu
     *
     * @param integer $typeMenu
     * @return Menu
     */
    public function setTypeMenu(string $typeMenu): self {
        $this->typeMenu = $typeMenu;

        return $this;
    }

    /**
     * Get typeMenu
     *
     * @return integer 
     */
    public function getTypeMenu(): ?string {
        return $this->typeMenu;
    }

    /**
     * Set idParentMenu
     *
     * @param integer $idParentMenu
     * @return Menu
     */
    public function setIdParentMenu(string $idParentMenu): self {
        $this->idParentMenu = $idParentMenu;

        return $this;
    }

    /**
     * Get idParentMenu
     *
     * @return integer 
     */
    public function getIdParentMenu(): ?string {
        return $this->idParentMenu;
    }

    /**
     * Set urlExterneMenu
     *
     * @param string $urlExterneMenu
     * @return Menu
     */
    public function setUrlExterneMenu(string $urlExterneMenu): self {
        $this->urlExterneMenu = $urlExterneMenu;

        return $this;
    }

    /**
     * Get urlExterneMenu
     *
     * @return string 
     */
    public function getUrlExterneMenu(): ?string {
        return $this->urlExterneMenu;
    }

    /**
     * Set menuAjoutPar
     *
     * @param integer $menuAjoutPar
     * @return Menu
     */
    public function setMenuAjoutPar(string $menuAjoutPar): self {
        $this->menuAjoutPar = $menuAjoutPar;

        return $this;
    }

    /**
     * Get menuAjoutPar
     *
     * @return integer 
     */
    public function getMenuAjoutPar(): ?string {
        return $this->menuAjoutPar;
    }

    /**
     * Set menuDateAjout
     *
     * @param \DateTime $menuDateAjout
     * @return Menu
     */
    public function setMenuDateAjout(string $menuDateAjout): self {
        $this->menuDateAjout = $menuDateAjout;

        return $this;
    }

    /**
     * Get menuDateAjout
     *
     * @return \DateTime 
     */
    public function getMenuDateAjout(): ?string {
        return $this->menuDateAjout;
    }

    /**
     * Set menuDateModif
     *
     * @param \DateTime $menuDateModif
     * @return Menu
     */
    public function setMenuDateModif(string $menuDateModif): self {
        $this->menuDateModif = $menuDateModif;

        return $this;
    }

    /**
     * Get menuDateModif
     *
     * @return \DateTime 
     */
    public function getMenuDateModif(): ?string {
        return $this->menuDateModif;
    }

    /**
     * Set menuModifPar
     *
     * @param integer $menuModifPar
     * @return Menu
     */
    public function setMenuModifPar(string $menuModifPar): self {
        $this->menuModifPar = $menuModifPar;

        return $this;
    }

    /**
     * Get menuModifPar
     *
     * @return integer 
     */
    public function getMenuModifPar(): ?string {
        return $this->menuModifPar;
    }

    /**
     * Set article
     *
     * @param \App\Entity\Article $article
     * @return Menu
     */
    public function setArticle(\App\Entity\Article $article = null) {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \App\Entity\Article 
     */
    public function getArticle(): ?string {
        return $this->article;
    }

    /**
     * Set groupeMenu
     *
     * @param \App\Entity\GroupeMenu $groupeMenu
     * @return Menu
     */
    public function setGroupeMenu(\App\Entity\GroupeMenu $groupeMenu = null) {
        $this->groupeMenu = $groupeMenu;

        return $this;
    }

    /**
     * Get groupeMenu
     *
     * @return \App\Entity\GroupeMenu 
     */
    public function getGroupeMenu(): ?string {
        return $this->groupeMenu;
    }

}
