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
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    private ?string $pays = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Avis::class)]
    private Collection $avis;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Adresse::class)]
    private Collection $adresses;

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

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Envoi::class)]
    private Collection $envois;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: HistoriqueConnexion::class)]
    private Collection $historiques;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->adresses = new ArrayCollection();
        $this->messagereponses = new ArrayCollection();
        $this->envois = new ArrayCollection();
        $this->historiques = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setUser($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            if ($avi->getUser() === $this) {
                $avi->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Adresse>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresse $adress): static
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses->add($adress);
            $adress->setUser($this);
        }

        return $this;
    }

    public function removeAdress(Adresse $adress): static
    {
        if ($this->adresses->removeElement($adress)) {
            if ($adress->getUser() === $this) {
                $adress->setUser(null);
            }
        }

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

    /**
     * @return Collection<int, Envoi>
     */
    public function getEnvois(): Collection
    {
        return $this->envois;
    }

    public function addEnvoi(Envoi $envoi): self
    {
        if (!$this->envois->contains($envoi)) {
            $this->envois->add($envoi);
            $envoi->setUtilisateur($this);
        }
        return $this;
    }

    public function removeEnvoi(Envoi $envoi): self
    {
        if ($this->envois->removeElement($envoi)) {
            // set the owning side to null (unless already changed)
            if ($envoi->getUtilisateur() === $this) {
                $envoi->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueConnexion>
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(HistoriqueConnexion $historique): self
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques->add($historique);
            $historique->setUtilisateur($this);
        }

        return $this;
    }

    public function removeHistorique(HistoriqueConnexion $historique): self
    {
        if ($this->historiques->removeElement($historique)) {
            // set the owning side to null (unless already changed)
            if ($historique->getUtilisateur() === $this) {
                $historique->setUtilisateur(null);
            }
        }

        return $this;
    }
}
