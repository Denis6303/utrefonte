<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="typecadre")]
 * #[ORM\Entity](repositoryClass="App\Entity\TypeCadreRepository")
 *
 */
class TypeCadre {

    function __construct() {
        
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idtypecadre", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection Cadre $cadres
     * #[ORM\OneToMany(targetEntity: App\Entity\Cadre::class, mappedBy="typeCadre" )]
     * 
     */
    private $cadres;

    /**
     * @var string $libtypecadre
     * #[ORM\Column(name="libTypeCadre",type="string",length=100)]
     * #[Assert\NotBlank(message="Le Libellé du type de cadre ne peut être vide! ")]
     * @Assert\MinLength(2)
     */
    private $libTypeCadre;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libTypeCadre
     *
     * @param string $libTypeCadre
     * @return TypeCadre
     */
    public function setLibTypeCadre(string $libTypeCadre): self {
        $this->libTypeCadre = $libTypeCadre;

        return $this;
    }

    /**
     * Get libTypeCadre
     *
     * @return string 
     */
    public function getLibTypeCadre(): ?string {
        return $this->libTypeCadre;
    }

    /**
     * Add cadres
     *
     * @param \App\Entity\Cadre $cadres
     * @return TypeCadre
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

}
