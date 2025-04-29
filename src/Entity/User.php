<?php

namespace App\Entity;

use App\Entity\Profil;
use App\Entity\MessageReponse;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'fos_user')]
#[ORM\HasLifecycleCallbacks]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    public function __construct()
    {
        $this->messagereponses = new ArrayCollection();
    }

    #[ORM\Column(name: 'nameUser', type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Please enter your name', groups: ['Registration', 'Profile'])]
    private $nameUser;

    /**
     * Encrypted password. Must not be persisted.
     */
    #[Assert\NotBlank(message: 'Please enter your password confirm')]
    private $cpassword;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'idprofil', referencedColumnName: 'idprofil')]
    private $profil;

    #[ORM\OneToMany(targetEntity: MessageReponse::class, mappedBy: 'user')]
    private $messagereponses;

    #[ORM\Column(name: 'urlphoto', type: 'string', length: 170, nullable: true)]
    private $urlPhoto;

    #[Assert\File(maxSize: '6000000', mimeTypes: ['image/gif', 'image/jpeg', 'image/png'])]
    #[Assert\NotBlank]
    public $photo;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preUpload()
    {
        if (null !== $this->photo) {
            $this->urlPhoto = sha1(uniqid(mt_rand(), true)) . '.' . $this->photo->guessExtension();
        }
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function upload()
    {
        if (null === $this->photo) {
            return;
        }

        $this->photo->move($this->getUploadRootDir(), $this->urlPhoto);
        chmod($this->getUploadRootDir(), 0755);
        unset($this->photo);
    }

    public function removeUpload($photo) {
      
            unlink($photo);
    
    }    
    
    public function getAbsolutePath(): ?string {
        return null === $this->urlPhoto ? null : $this->getUploadRootDir() . '' . $this->urlPhoto;
    }

    public function getWebPath(): ?string {
        return null === $this->urlPhoto ? null : $this->getUploadDir() . '' . $this->urlPhoto;
    }

    public function getUploadRootDir(): ?string {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir(): ?string {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'upload/photos/';
    }    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setNameUser(string $nameUser): self
    {
        $this->nameUser = $nameUser;
        return $this;
    }

    public function getNameUser(): ?string
    {
        return $this->nameUser;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;
        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function getMessagereponses(): Collection
    {
        return $this->messagereponses;
    }

    public function addMessagereponse(MessageReponse $messagereponse): self
    {
        if (!$this->messagereponses->contains($messagereponse)) {
            $this->messagereponses[] = $messagereponse;
            $messagereponse->setUser($this);
        }
        return $this;
    }

    public function removeMessagereponse(MessageReponse $messagereponse): self
    {
        if ($this->messagereponses->removeElement($messagereponse)) {
            if ($messagereponse->getUser() === $this) {
                $messagereponse->setUser(null);
            }
        }
        return $this;
    }

    public function setCpassword(string $cpassword): self
    {
        $this->cpassword = $cpassword;
        return $this;
    }

    public function getCpassword(): ?string
    {
        return $this->cpassword;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;
        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }
}
