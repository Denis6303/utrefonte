<?php

namespace App\Entity;

use App\Repository\AbonneRepository; // Assurez-vous que le namespace du Repository est correct
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; // Import Type Hints
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface; // Interface pour le hachage de mot de passe
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AbonneRepository::class)]
#[ORM\Table(name: 'abonne')]
class Abonne implements UserInterface, PasswordAuthenticatedUserInterface // Ajout de PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut, pas besoin de le spécifier
    #[ORM\Column(name: 'idabonne', type: Types::INTEGER)]
    private ?int $id = null; // Nom de propriété standardisé en 'id'

    #[ORM\Column(name: 'username', type: Types::STRING, length: 50, unique: true)] // Username doit être unique pour UserInterface
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private ?string $username = null;

    #[ORM\Column(name: 'nomprenom', type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $nomPrenom = null;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 180, unique: true)] // Longueur standard pour email, doit être unique
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    #[ORM\Column(name: 'telabonne', type: Types::STRING, length: 16, nullable: true)]
    #[Assert\Length(max: 16)]
    private ?string $telAbonne = null;

    #[ORM\Column(name: 'adresseabonne', type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $adresseAbonne = null;

    #[ORM\Column(name: 'celabonne', type: Types::STRING, length: 16)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 16)]
    private ?string $celAbonne = null;

    /**
     * Mot de passe généré (si utilisé pour une raison spécifique, sinon pourrait être supprimé).
     * Le nom 'genpsswd' dans la DB est conservé.
     */
    #[ORM\Column(name: 'genpsswd', type: Types::STRING, length: 16, nullable: true)] // Rendu nullable, car son utilité n'est pas claire
    #[Assert\Length(max: 16)]
    private ?string $genPsswd = null;

    /**
     * Statut de l'abonné (par exemple: 0=inactif, 1=actif, 2=suspendu).
     * Le nom 'etatabonne' dans la DB est conservé.
     */
    #[ORM\Column(name: 'etatabonne', type: Types::INTEGER)]
    #[Assert\NotNull] // Utiliser NotNull pour les types non-chaîne si une valeur est toujours attendue
    private ?int $etat = null; // Nom de propriété clarifié en 'etat'

    #[ORM\Column(name: 'attempt', type: Types::INTEGER)]
    #[Assert\NotNull]
    private ?int $attempt = null;

    #[ORM\Column(name: 'suppr', type: Types::INTEGER)] // Probablement un booléen (0/1) pour 'supprimé' ?
    #[Assert\NotNull]
    private ?int $suppr = null;

    /**
     * Radical de l'abonné (utilité non claire, mais conservé).
     * Le nom 'radicalabonne' dans la DB est conservé.
     */
    #[ORM\Column(name: 'radicalabonne', type: Types::STRING, length: 8, nullable: true)] // Rendu nullable
    #[Assert\Length(max: 8)]
    private ?string $radicalAbonne = null;

    /**
     * @deprecated Le sel n'est plus géré manuellement avec les encodeurs modernes (depuis Symfony 5.3).
     * Il est inclus dans le hash du mot de passe. Cette propriété peut être supprimée si
     * vous utilisez un PasswordHasher moderne (e.g., BcryptPasswordHasher, Argon2iPasswordHasher).
     * Si vous devez la conserver pour des raisons de compatibilité ascendante, laissez-la,
     * mais elle ne sera pas utilisée par le système de sécurité moderne.
     */
    #[ORM\Column(name: 'salt', type: Types::STRING, length: 32, nullable: true)]
    private ?string $salt = null;

    /**
     * Le mot de passe haché.
     */
    #[ORM\Column(name: 'password', type: Types::STRING)]
    private ?string $password = null;

    /**
     * Code de contrôle (utilité non claire, mais conservé).
     * Le nom 'controlecode' dans la DB est conservé.
     */
    #[ORM\Column(name: 'controlecode', type: Types::INTEGER, nullable: true)]
    private ?int $controleCode = null;

    /**
     * Champ de confirmation de mot de passe (non mappé à la BDD).
     * Utilisé uniquement pour la validation dans les formulaires.
     * Ne pas ajouter #[ORM\Column] ici.
     */
    //#[Assert\NotBlank(groups: ['Registration'])] // Exemple : Valider seulement à l'inscription
    //#[Assert\EqualTo(propertyPath: 'password', message: 'Les mots de passe ne correspondent pas.', groups: ['Registration'])] // Exemple
    private ?string $plainPassword = null; // Renommé pour clarté (plainPassword ou cpassword)


    /**
     * L'ancien champ cpassword persisté est problématique.
     * Si vous en avez absolument besoin, gardez-le, sinon supprimez-le.
     * Il est TRES inhabituel de stocker un mot de passe de confirmation.
     */
    // #[ORM\Column(name: 'cpassword', type: Types::STRING, nullable: true)]
    // private ?string $cpassword = null;


    /**
     * @deprecated Remplacé par profilClient ci-dessous pour plus de clarté. Si 'profil'
     * est bien le ProfilClient, migrez les données et supprimez cette propriété.
     * Sinon, ajustez le nom et la cible.
     */
    #[ORM\ManyToOne(targetEntity: ProfilClient::class)]
    #[ORM\JoinColumn(name: 'idprofil', referencedColumnName: 'idprofil', nullable: true)] // Supposant que idprofil existe sur ProfilClient
    private ?ProfilClient $profil = null;

    #[ORM\OneToMany(mappedBy: 'abonne', targetEntity: Compte::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $comptes;

    #[ORM\OneToMany(mappedBy: 'abonne', targetEntity: Envoi::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // mappedBy doit correspondre au nom de la propriété dans Envoi
    private Collection $envois;

    #[ORM\OneToMany(mappedBy: 'abonne', targetEntity: HistoriqueConnexion::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // mappedBy doit correspondre au nom de la propriété dans HistoriqueConnexion
    private Collection $historiques;

    /**
     * Le nom 'codebase' dans la DB est conservé.
     */
    #[ORM\Column(name: 'codebase', type: Types::STRING, length: 16, nullable: true)] // Rendu nullable
    #[Assert\Length(max: 16)]
    private ?string $codeBase = null;

    /**
     * Le nom 'codeop' dans la DB est conservé.
     */
    #[ORM\Column(name: 'codeop', type: Types::STRING, length: 16, nullable: true)] // Rendu nullable
    #[Assert\Length(max: 16)]
    private ?string $codeOp = null;

    /**
     * Relation réflexive pour un parent Abonne.
     */
    #[ORM\ManyToOne(targetEntity: self::class)] // Utiliser self::class pour les relations réflexives
    #[ORM\JoinColumn(name: 'idabonneparent', referencedColumnName: 'idabonne', nullable: true)]
    private ?Abonne $parent = null; // Nom de propriété clarifié en 'parent'

    /**
     * Comptes parents (utilité non claire - stocker des IDs sérialisés ? Préférer une relation ManyToMany si possible).
     * Le nom 'compteparents' dans la DB est conservé.
     */
    #[ORM\Column(name: 'compteparents', type: Types::TEXT, nullable: true)]
    private ?string $compteParents = null;

    #[ORM\Column(name: 'dateinscription', type: Types::DATETIME_IMMUTABLE)] // Préférer DATETIME_IMMUTABLE
    #[Assert\NotNull]
    private ?\DateTimeImmutable $dateInscription = null;

    #[ORM\ManyToOne(targetEntity: Internaute::class, inversedBy: 'abonnes')]
    #[ORM\JoinColumn(name: 'idinternaute', referencedColumnName: 'idinternaute', nullable: true)] // Rendu nullable si un abonné peut ne pas avoir d'internaute lié
    private ?Internaute $internaute = null;

    #[ORM\ManyToOne(targetEntity: ProfilClient::class)] // Potentiellement redondant avec $profil ? Vérifier la logique métier.
    #[ORM\JoinColumn(name: 'idprofilclient', referencedColumnName: 'idprofilclient', nullable: true)] // Supposant que idprofilclient existe sur ProfilClient
    private ?ProfilClient $profilClient = null;

    // Pas de propriété `isActive` définie, mais utilisée dans le constructeur. A ajouter si nécessaire.
    // #[ORM\Column(type: Types::BOOLEAN)]
    // private bool $isActive = true;

    public function __construct()
    {
        // $this->isActive = true; // A décommenter si la propriété isActive est ajoutée
        $this->attempt = 0;
        $this->suppr = 0; // 0 signifie probablement 'non supprimé'
        $this->controleCode = 0;
        //$this->genPsswd = ""; // Initialisé via propriété nullable
        $this->comptes = new ArrayCollection();
        $this->envois = new ArrayCollection();
        $this->historiques = new ArrayCollection();
        $this->dateInscription = new \DateTimeImmutable(); // Utiliser DateTimeImmutable
        $this->etat = 0; // 0 signifie probablement 'inactif' par défaut
        // Le salt n'a plus besoin d'être généré ici avec les encodeurs modernes
        // $this->salt = md5(uniqid(null, true));
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

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        // Garantit qu'il y a toujours au moins ROLE_USER
        $roles = ['ROLE_USER'];

        // Ajouter d'autres rôles basés sur le profil, par exemple
        // if ($this->profilClient && $this->profilClient->getRoleName()) { // Supposant une méthode getRoleName()
        //     $roles[] = $this->profilClient->getRoleName();
        // }

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Utilisé pour effacer les données sensibles temporaires, comme le plainPassword
        $this->plainPassword = null;
    }

    /**
     * Retourne le mot de passe en clair temporaire (utilisé par les formulaires).
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        // Ne mettez PAS à jour $this->password ici. Le hachage doit être fait
        // par un listener ou dans le contrôleur/service avant de persister.
        return $this;
    }


    public function getNomPrenom(): ?string
    {
        return $this->nomPrenom;
    }

    public function setNomPrenom(string $nomPrenom): self
    {
        $this->nomPrenom = $nomPrenom;
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

    public function getTelAbonne(): ?string
    {
        return $this->telAbonne;
    }

    public function setTelAbonne(?string $telAbonne): self
    {
        $this->telAbonne = $telAbonne;
        return $this;
    }

    public function getAdresseAbonne(): ?string
    {
        return $this->adresseAbonne;
    }

    public function setAdresseAbonne(string $adresseAbonne): self
    {
        $this->adresseAbonne = $adresseAbonne;
        return $this;
    }

    public function getCelAbonne(): ?string
    {
        return $this->celAbonne;
    }

    public function setCelAbonne(string $celAbonne): self
    {
        $this->celAbonne = $celAbonne;
        return $this;
    }

    public function getGenPsswd(): ?string
    {
        return $this->genPsswd;
    }

    public function setGenPsswd(?string $genPsswd): self
    {
        $this->genPsswd = $genPsswd;
        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function getAttempt(): ?int
    {
        return $this->attempt;
    }

    public function setAttempt(int $attempt): self
    {
        $this->attempt = $attempt;
        return $this;
    }

    public function getSuppr(): ?int
    {
        return $this->suppr;
    }

    public function setSuppr(int $suppr): self
    {
        $this->suppr = $suppr;
        return $this;
    }

    // Méthode isSuppr() pour plus de clarté si suppr est un booléen (0/1)
    public function isSuppr(): bool
    {
        return $this->suppr === 1;
    }

    public function getRadicalAbonne(): ?string
    {
        return $this->radicalAbonne;
    }

    public function setRadicalAbonne(?string $radicalAbonne): self
    {
        $this->radicalAbonne = $radicalAbonne;
        return $this;
    }

    /**
     * @deprecated Voir commentaire sur la propriété $salt
     */
    public function getSalt(): ?string
    {
        // Retourne null car le sel est géré par le hasher
        return null;
        // Si vous devez conserver l'ancien sel : return $this->salt;
    }

    /**
     * @deprecated Voir commentaire sur la propriété $salt
     */
    public function setSalt(?string $salt): self
    {
        // Ne fait rien car le sel est géré par le hasher
        // Si vous devez conserver l'ancien sel : $this->salt = $salt;
        return $this;
    }

    public function getControleCode(): ?int
    {
        return $this->controleCode;
    }

    public function setControleCode(?int $controleCode): self
    {
        $this->controleCode = $controleCode;
        return $this;
    }

    /**
     * @deprecated Voir commentaire sur la propriété $profil
     */
    public function getProfil(): ?ProfilClient
    {
        return $this->profil;
    }

    /**
     * @deprecated Voir commentaire sur la propriété $profil
     */
    public function setProfil(?ProfilClient $profil): self
    {
        $this->profil = $profil;
        return $this;
    }

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes->add($compte);
            $compte->setAbonne($this);
        }
        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getAbonne() === $this) {
                $compte->setAbonne(null);
            }
        }
        return $this;
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
            $envoi->setAbonne($this);
        }
        return $this;
    }

    public function removeEnvoi(Envoi $envoi): self
    {
        if ($this->envois->removeElement($envoi)) {
            // set the owning side to null (unless already changed)
            if ($envoi->getAbonne() === $this) {
                $envoi->setAbonne(null);
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
            $historique->setAbonne($this);
        }
        return $this;
    }

    public function removeHistorique(HistoriqueConnexion $historique): self
    {
        if ($this->historiques->removeElement($historique)) {
            // set the owning side to null (unless already changed)
            if ($historique->getAbonne() === $this) {
                $historique->setAbonne(null);
            }
        }
        return $this;
    }

    public function getCodeBase(): ?string
    {
        return $this->codeBase;
    }

    public function setCodeBase(?string $codeBase): self
    {
        $this->codeBase = $codeBase;
        return $this;
    }

    public function getCodeOp(): ?string
    {
        return $this->codeOp;
    }

    public function setCodeOp(?string $codeOp): self
    {
        $this->codeOp = $codeOp;
        return $this;
    }

    public function getParent(): ?self // Utiliser self pour le type hint
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self // Utiliser self pour le type hint
    {
        $this->parent = $parent;
        return $this;
    }

    public function getCompteParents(): ?string
    {
        return $this->compteParents;
    }

    public function setCompteParents(?string $compteParents): self
    {
        $this->compteParents = $compteParents;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeImmutable
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeImmutable $dateInscription): self
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    public function getInternaute(): ?Internaute
    {
        return $this->internaute;
    }

    public function setInternaute(?Internaute $internaute): self
    {
        $this->internaute = $internaute;
        return $this;
    }

    public function getProfilClient(): ?ProfilClient
    {
        return $this->profilClient;
    }

    public function setProfilClient(?ProfilClient $profilClient): self
    {
        $this->profilClient = $profilClient;
        return $this;
    }

    // Si la propriété isActive est ajoutée:
    // public function isActive(): bool
    // {
    //     return $this->isActive;
    // }
    //
    // public function setIsActive(bool $isActive): self
    // {
    //     $this->isActive = $isActive;
    //     return $this;
    // }

    // On pourrait ajouter une méthode __toString pour faciliter l'affichage
    public function __toString(): string
    {
        return $this->getNomPrenom() ?? $this->getUsername() ?? 'Abonne #' . $this->getId();
    }
}