<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * #[ORM\Entity]
 * #[ORM\Entity](repositoryClass="App\Entity\DeviseRepository")
 * #[ORM\Table(name="devise")]
 * @ORM\HasLifecycleCallbacks 
 *
 */
class Devise {

    public function __construct() {
        $this->setAffiche = 0;
    }

    /**
     * #[ORM\Id]
     * #[ORM\Column(name="iddevise", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $codeDevise
     * #[ORM\Column(name="codeDevise", type="string",length=5)]
     */
    private $codeDevise;

    /**
     * @var string $libDevise
     * #[ORM\Column(name="libdevise", type="string",length=40)]
     */
    private $libDevise;

    /**
     * @var string $valDeviseLocal
     * #[ORM\Column(name="valdeviselocal", type="string", nullable=true)]
     */
    private $valDeviseLocal;
	
    /**
     * @var string $valDeviseLocalAchat
     * #[ORM\Column(name="valdeviselocalachat", type="string")]
     */
    private $valDeviseLocalAchat;

    /**
     * @var ArrayCollection Operation $operations
     * #[ORM\OneToMany(targetEntity: App\Entity\Operation::class, mappedBy="devise" )]
     * 
     */
    private $operations;

    /**
     * @var boolean $siLocale
     * #[ORM\Column(name="locale", type="boolean")]
     */
    private $siLocale;

    /**
     * @var integer $acffiche
     * #[ORM\Column(name="affiche", type="integer")]
     */
    private $affiche;

    /**
     * Get id
     *
     * @return integer 
     */

    public function getId(): ?string {
        return $this->id;
    }
    
    /**
     * @var string $urlIcone
     * #[ORM\Column(name="urlicone",type="string",length=170, nullable=true)]
     */
    private $urlIcone;

    /**
     * @Assert\File(maxSize="6000000")
     * mimeTypes = {"image/gif", "image/jpeg", "image/png"},
     * #[Assert\NotBlank()]
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
        return 'upload/drapeaux/';
    }
    

    /**
     * Set codeDevise
     *
     * @param string $codeDevise
     * @return Devise
     */
    public function setCodeDevise(string $codeDevise): self {
        $this->codeDevise = $codeDevise;

        return $this;
    }

    /**
     * Get codeDevise
     *
     * @return string 
     */
    public function getCodeDevise(): ?string {
        return $this->codeDevise;
    }

    /**
     * Set libDevise
     *
     * @param string $libDevise
     * @return Devise
     */
    public function setLibDevise(string $libDevise): self {
        $this->libDevise = $libDevise;

        return $this;
    }

    /**
     * Get libDevise
     *
     * @return string 
     */
    public function getLibDevise(): ?string {
        return $this->libDevise;
    }

    /**
     * Set valDeviseLocal
     *
     * @param string $valDeviseLocal
     * @return Devise
     */
    public function setValDeviseLocal(string $valDeviseLocal): self {
        $this->valDeviseLocal = $valDeviseLocal;

        return $this;
    }

    /**
     * Get valDeviseLocal
     *
     * @return string 
     */
    public function getValDeviseLocal(): ?string {
        return $this->valDeviseLocal;
    }

    /**
     * Add operations
     *
     * @param \App\Entity\Operation $operations
     * @return Devise
     */
    public function addOperation(\App\Entity\Operation $operations) {
        $this->operations[] = $operations;

        return $this;
    }

    /**
     * Remove operations
     *
     * @param \App\Entity\Operation $operations
     */
    public function removeOperation(\App\Entity\Operation $operations) {
        $this->operations->removeElement($operations);
    }

    /**
     * Get operations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOperations(): ?string {
        return $this->operations;
    }

    /**
     * Set siLocale
     *
     * @param boolean $siLocale
     * @return Devise
     */
    public function setSiLocale(string $siLocale): self {
        $this->siLocale = $siLocale;

        return $this;
    }

    /**
     * Get siLocale
     *
     * @return boolean 
     */
    public function getSiLocale(): ?string {
        return $this->siLocale;
    }

    /**
     * Set affiche
     *
     * @param integer $affiche
     * @return Devise
     */
    public function setAffiche(string $affiche): self {
        $this->affiche = $affiche;

        return $this;
    }

    /**
     * Get affiche
     *
     * @return integer 
     */
    public function getAffiche(): ?string {
        return $this->affiche;
    }


    /**
     * Set urlIcone
     *
     * @param string $urlIcone
     * @return Devise
     */
    public function setUrlIcone(string $urlIcone): self
    {
        $this->urlIcone = $urlIcone;
    
        return $this;
    }

    /**
     * Get urlIcone
     *
     * @return string 
     */
    public function getUrlIcone(): ?string
    {
        return $this->urlIcone;
    }

    /**
     * Set valDeviseLocalAchat
     *
     * @param string $valDeviseLocalAchat
     * @return Devise
     */
    public function setValDeviseLocalAchat(string $valDeviseLocalAchat): self
    {
        $this->valDeviseLocalAchat = $valDeviseLocalAchat;
    
        return $this;
    }

    /**
     * Get valDeviseLocalAchat
     *
     * @return string 
     */
    public function getValDeviseLocalAchat(): ?string
    {
        return $this->valDeviseLocalAchat;
    }
}
