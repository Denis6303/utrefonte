<?php

namespace App\Entity;

use App\Entity\ProfilClient;
use App\Entity\MessageReponse;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'iduser', type: 'integer')]
    private ?int $idUser = null;

    #[ORM\Column(name: 'username', type: 'string', length: 50)]
    #[Assert\NotBlank]
    private ?string $username = null;

    #[ORM\Column(name: 'password', type: 'string', length: 50)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(name: 'salt', type: 'string', length: 32)]
    private ?string $salt = null;

    #[ORM\Column(name: 'email', type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private ?bool $isActive = null;

    #[ORM\Column(name: 'roles', type: 'json')]
    private array $roles = [];

    #[ORM\OneToOne(targetEntity: Abonne::class)]
    #[ORM\JoinColumn(name: 'idabonne', referencedColumnName: 'idabonne')]
    private ?Abonne $abonne = null;

    #[ORM\ManyToOne(targetEntity: ProfilClient::class)]
    #[ORM\JoinColumn(name: 'idprofil', referencedColumnName: 'idprofil')]
    private ?ProfilClient $profil = null;

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
        $this->salt = md5(uniqid('', true));
        $this->isActive = true;
        $this->roles = ['ROLE_USER'];
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

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getAbonne(): ?Abonne
    {
        return $this->abonne;
    }

    public function setAbonne(?Abonne $abonne): self
    {
        $this->abonne = $abonne;
        return $this;
    }

    public function getProfil(): ?ProfilClient
    {
        return $this->profil;
    }

    public function setProfil(?ProfilClient $profil): self
    {
        $this->profil = $profil;
        return $this;
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

    public function eraseCredentials(): void
    {
    }
}
