<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\GroupeMenuRepository")
 * #[ORM\Table(name="groupemenu")]
 * 
 */
class GroupeMenu {

    function __construct() {
        
    }

    /**
     * @var Menu $menus
     * #[ORM\OneToMany(targetEntity: App\Entity\Menu::class, mappedBy="groupeMenu" )]
     * 
     */
    private $menus;

    /**
     * @var integer $id
     * #[ORM\Column(name="idgroupemenu", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var string $libGroupeMenu
     * #[ORM\Column(name="libgroupemenu",type="string",length=50)]
     * #[Assert\NotBlank(message=" Le libellé du groupe menu ne peut être vide ")]
     * @Assert\MinLength(3)
     */
    private $libGroupeMenu;

    /**
     * @Gedmo\Translatable
     * @var text $commentaireGroupeMenu
     * #[ORM\Column(name="commentaireGroupeMenu" , type="text", nullable=true)]  
     */
    private $commentaireGroupeMenu;

    /**
     * @var string $visibileteGroupeMenu
     * #[ORM\Column(name="visibileteGroupeMenu",type="string",length=255)]
     * #[Assert\NotBlank(message=" ce champ ne peut être vide ")]
     * @Assert\MinLength(5)
     */
    private $visibileteGroupeMenu;

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
     * Set libGroupeMenu
     *
     * @param string $libGroupeMenu
     * @return GroupeMenu
     */
    public function setLibGroupeMenu(string $libGroupeMenu): self {
        $this->libGroupeMenu = $libGroupeMenu;

        return $this;
    }

    /**
     * Get libGroupeMenu
     *
     * @return string 
     */
    public function getLibGroupeMenu(): ?string {
        return $this->libGroupeMenu;
    }

    /**
     * Add menus
     *
     * @param \App\Entity\Menu $menus
     * @return GroupeMenu
     */
    public function addMenu(\App\Entity\Menu $menus) {
        $this->menus[] = $menus;

        return $this;
    }

    /**
     * Remove menus
     *
     * @param \App\Entity\Menu $menus
     */
    public function removeMenu(\App\Entity\Menu $menus) {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenus(): ?string {
        return $this->menus;
    }

    /**
     * Set commentaireGroupeMenu
     *
     * @param string $commentaireGroupeMenu
     * @return GroupeMenu
     */
    public function setCommentaireGroupeMenu(string $commentaireGroupeMenu): self {
        $this->commentaireGroupeMenu = $commentaireGroupeMenu;

        return $this;
    }

    /**
     * Get commentaireGroupeMenu
     *
     * @return string 
     */
    public function getCommentaireGroupeMenu(): ?string {
        return $this->commentaireGroupeMenu;
    }

    /**
     * Set visibileteGroupeMenu
     *
     * @param string $visibileteGroupeMenu
     * @return GroupeMenu
     */
    public function setVisibileteGroupeMenu(string $visibileteGroupeMenu): self {
        $this->visibileteGroupeMenu = $visibileteGroupeMenu;

        return $this;
    }

    /**
     * Get visibileteGroupeMenu
     *
     * @return string 
     */
    public function getVisibileteGroupeMenu(): ?string {
        return $this->visibileteGroupeMenu;
    }

}
