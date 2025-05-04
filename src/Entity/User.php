<?php

namespace App\Entity;

use App\Entity\Profil;
use App\Entity\MessageReponse;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'fos_user')]
#[ORM\HasLifecycleCallbacks]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nameUser', type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Please enter your name', groups: ['Registration', 'Profile'])]
    private ?string $nameUser = null;

    /**
     * Encrypted password. Must not be persisted.
     */
    #[Assert\NotBlank(message: 'Please enter your password confirm')]
    private ?string $cpassword = null;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'idprofil', referencedColumnName: 'idprofil')]
    private ?Profil $profil = null;

    #[ORM\OneToMany(targetEntity: MessageReponse::class, mappedBy: 'user')]
    private Collection $messagereponses;

    #[ORM\Column(name: 'urlphoto', type: 'string', length: 170, nullable: true)]
    private ?string $urlPhoto = null;

    #[Assert\File(maxSize: '6000000', mimeTypes: ['image/gif', 'image/jpeg', 'image/png'])]
    #[Assert\NotBlank]
    private ?File $photo = null;

    private string $uploadDir = 'upload/photos/';

    public function __construct()
    {
        $this->messagereponses = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preUpload(): void
    {
        if ($this->photo instanceof UploadedFile) {
            $this->urlPhoto = sha1(uniqid(mt_rand(), true)) . '.' . $this->photo->guessExtension();
        }
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function upload(): void
    {
        if (!$this->photo instanceof UploadedFile) {
            return;
        }

        $this->photo->move($this->getUploadRootDir(), $this->urlPhoto);
        chmod($this->getUploadRootDir(), 0755);
        unset($this->photo);
    }

    public function removeUpload(string $photo): void
    {
        if (file_exists($photo)) {
            unlink($photo);
        }
    }

    public function getAbsolutePath(): ?string
    {
        return $this->urlPhoto ? $this->getUploadRootDir() . '/' . $this->urlPhoto : null;
    }

    public function getWebPath(): ?string
    {
        return $this->urlPhoto ? $this->getUploadDir() . '/' . $this->urlPhoto : null;
    }

    public function getUploadRootDir(): string
    {
        return $this->getParameter('kernel.project_dir') . '/public/' . $this->getUploadDir();
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
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

    public function setPhoto(?File $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getPhoto(): ?File
    {
        return $this->photo;
    }
}
