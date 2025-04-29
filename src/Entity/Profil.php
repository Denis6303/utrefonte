<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * #[ORM\Entity](repositoryClass="App\Entity\ProfilRepository")
 * #[ORM\Table(name="profil")]
 */
class Profil {

    function __construct() {
        $this->etatProfil = 1;
    }

    /**
     * @var integer $id
     * #[ORM\Column(name="idprofil", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * 
     * @var string $libProfil
     * #[ORM\Column(name="libprofil",type="string",length=70)]
     * #[Assert\NotBlank(message=" Le libellé du profil ne peut être vide ")]
     * @Assert\MinLength(3)
     */
    private $libProfil;

    /**
     * @var integer $etatProfil
     * #[ORM\Column(name="etatprofil",type="integer" )]
     *   
     */
    private $etatProfil;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set libProfil
     *
     * @param string $libProfil
     * @return Profil
     */
    public function setLibProfil(string $libProfil): self {
        $this->libProfil = $libProfil;

        return $this;
    }

    /**
     * Get libProfil
     *
     * @return string 
     */
    public function getLibProfil(): ?string {
        return $this->libProfil;
    }

    /**
     * Set etatProfil
     *
     * @param integer $etatProfil
     * @return Profil
     */
    public function setEtatProfil(string $etatProfil): self {
        $this->etatProfil = $etatProfil;

        return $this;
    }

    /**
     * Get etatProfil
     *
     * @return integer 
     */
    public function getEtatProfil(): ?string {
        return $this->etatProfil;
    }

    /* public function setTranslatableLocale(string $locale): self
      {
      $this->locale = $locale;
      } */
}
