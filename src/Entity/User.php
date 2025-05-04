<?php

namespace App\Entity;

use App\Entity\Profil;
use App\Entity\MessageReponse;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'fos_user')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Please enter a username')]
    #[Assert\Length(min: 3, max: 180)]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Please enter your name')]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $nameUser = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Please enter your email')]
    #[Assert\Email(message: 'Please enter a valid email address')]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[Assert\NotBlank(message: 'Please enter your password', groups: ['Registration'])]
    #[Assert\Length(min: 6, max: 4096)]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'idprofil', referencedColumnName: 'idprofil')]
    private ?Profil $profil = null;

    #[ORM\OneToMany(targetEntity: MessageReponse::class, mappedBy: 'user')]
    private Collection $messagereponses;

    #[ORM\Column(type: 'string', length: 170, nullable: true)]
    private ?string $urlPhoto = null;

    #[Assert\File(
        maxSize: '6M',
        mimeTypes: ['image/gif', 'image/jpeg', 'image/png'],
        mimeTypesMessage: 'Please upload a valid image file'
    )]
    private ?File $photo = null;

    private string $uploadDir = 'upload/photos/';

    public function __construct()
    {
        $this->messagereponses = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
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

    public function getNameUser(): ?string
    {
        return $this->nameUser;
    }

    public function setNameUser(string $nameUser): self
    {
        $this->nameUser = $nameUser;
        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
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

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;
        return $this;
    }

    public function getPhoto(): ?File
    {
        return $this->photo;
    }

    public function setPhoto(?File $photo): self
    {
        $this->photo = $photo;
        return $this;
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

    protected function getUploadRootDir(): string
    {
        return __DIR__ . '/../../public/' . $this->getUploadDir();
    }

    protected function getUploadDir(): string
    {
        return $this->uploadDir;
    }
}
