<?php

namespace App\Entity;

use App\Repository\ChargementRepository; // Assurez-vous que ce repository existe
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables

/**
 * Entité représentant un fichier chargé (ex: relevé bancaire).
 */
#[ORM\Entity(repositoryClass: ChargementRepository::class)]
#[ORM\Table(name: 'chargement')]
#[ORM\HasLifecycleCallbacks] // Conserver les callbacks pour la gestion du fichier
class Chargement
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idchargement', type: Types::INTEGER)]
    private ?int $id = null; // Renommé en 'id' pour suivre les conventions

    /**
     * Numéro unique ou identifiant interne du fichier.
     */
    #[ORM\Column(name: 'numerofichier', type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: "Le numéro de fichier ne peut être vide.")]
    #[Assert\Length(max: 20, maxMessage: "Le numéro de fichier ne peut dépasser {{ limit }} caractères.")]
    private ?string $numeroFichier = null;

    /**
     * Nom du fichier tel qu'il est stocké sur le serveur.
     */
    #[ORM\Column(name: 'libellefichier', type: Types::STRING, length: 255)] // Augmenté la longueur pour les noms de fichiers
    #[Assert\NotBlank(message: "Le libellé du fichier ne peut être vide.")]
    #[Assert\Length(max: 255, maxMessage: "Le libellé ne peut dépasser {{ limit }} caractères.")]
    private ?string $libelleFichier = null;

    /**
     * État du chargement (ex: 0=en attente, 1=traité, 2=erreur).
     */
    #[ORM\Column(name: 'etat', type: Types::INTEGER)]
    #[Assert\NotNull]
    // #[Assert\Choice(choices: [0, 1, 2], message: "État invalide.")] // Si les états sont limités
    private ?int $etat = 0; // Valeur par défaut (ex: en attente)

    /**
     * Indicateur d'archivage (0=non archivé, 1=archivé). Utiliser BOOLEAN serait mieux.
     */
    #[ORM\Column(name: 'archive', type: Types::INTEGER, nullable: true)] // Ou Types::BOOLEAN
    private ?int $archive = 0; // Valeur par défaut

    /**
     * Type de chargement (utilité à définir).
     */
    #[ORM\Column(name: 'typechargement', type: Types::INTEGER, nullable: true)]
    private ?int $typeChargement = null;

    /**
     * Nature du chargement (utilité à définir, ex: 1=automatique, 2=manuel).
     */
    #[ORM\Column(name: 'naturechargement', type: Types::INTEGER, nullable: true)]
    private ?int $natureChargement = null;


    #[ORM\Column(name: 'datedeb', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $dateDeb = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'datefin', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $dateFin = null; // Type hint DateTimeImmutable

    #[ORM\Column(name: 'filedateajout', type: Types::DATETIME_IMMUTABLE)] // Non nullable, défini dans preUpload/constructeur
    #[Assert\NotNull] // Assurer que la date est bien définie
    private ?DateTimeImmutable $fileDateAjout = null; // Type hint DateTimeImmutable

    // --- Relation ---
    #[ORM\ManyToOne(targetEntity: TypeCompte::class, inversedBy: 'chargements')] // Assurez-vous que 'chargements' existe dans TypeCompte
    #[ORM\JoinColumn(name: 'idtypecompte', referencedColumnName: 'idtypecompte', nullable: true)] // Rendre nullable si le type de compte n'est pas obligatoire
    private ?TypeCompte $typeCompte = null;

    // --- Propriété non mappée pour l'upload ---
    /**
     * Propriété temporaire pour contenir le fichier uploadé. Ne pas mapper à la BDD.
     * @var File|null
     */
    #[Assert\File(
        maxSize: "100M", // Taille max réduite pour être réaliste (100000M = 100GB!)
        mimeTypes: ["text/plain", "application/pdf", "image/jpeg"], // Exemples de types MIME
        mimeTypesMessage: "Format de fichier invalide. Types autorisés : {{ types }}"
    )]
    private ?File $file = null;

    // --- Variable temporaire pour l'ancien nom de fichier (si nécessaire pour la suppression) ---
    private ?string $tempFilename = null;


    public function __construct()
    {
        // Initialiser la date d'ajout ici garantit qu'elle existe toujours
        $this->fileDateAjout = new DateTimeImmutable();
        $this->etat = 0; // Etat initial
        $this->archive = 0; // Non archivé par défaut
    }


    // --- Lifecycle Callbacks pour la gestion du fichier ---
    // Attention: Mettre la logique d'upload dans l'entité est déconseillé dans les applications modernes.
    // Préférez utiliser un Event Listener (Doctrine) ou un bundle comme VichUploaderBundle.
    // Ce code est conservé pour correspondre à l'original mais avec des améliorations.

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function handleFileBeforeSave(): void
    {
        // Si un nouveau fichier est uploadé
        if ($this->file instanceof UploadedFile) {
            // Mettre à jour la date (même si déjà fait dans constructeur pour PrePersist)
            $this->fileDateAjout = new DateTimeImmutable();

            // Stocker l'ancien nom de fichier pour pouvoir le supprimer plus tard si besoin
            // (Seulement si libelleFichier existe déjà - cas PreUpdate)
            if ($this->libelleFichier !== null) {
                 $this->tempFilename = $this->libelleFichier;
            }

            // Générer le nouveau nom de fichier et le numéro
            if ($this->natureChargement === 1) { // Automatique
                $dateJour = date("dmYHis"); // Ajout H:i:s pour unicité
                $nomBase = $dateJour;
                if ($this->typeChargement === 0) $nomBase .= "A";
                elseif ($this->typeChargement === 2) $nomBase .= "B";
                if ($this->typeCompte) $nomBase .= $this->typeCompte->getId(); // Assurez-vous que getId() existe

                $this->numeroFichier = $nomBase; // Utiliser le nom généré comme numéro
                $extension = $this->file->guessExtension() ?: $this->file->getClientOriginalExtension();
                $this->libelleFichier = $nomBase . '.' . $extension;
            } else { // Manuel
                $originalFilename = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
                // Optionnel: "Slugify" le nom pour éviter caractères problématiques
                $safeFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename);
                $extension = $this->file->guessExtension() ?: $this->file->getClientOriginalExtension();
                $this->libelleFichier = $safeFilename.'-'.uniqid().'.'.$extension;
                $this->numeroFichier = $this->libelleFichier; // Ou garder l'original ? A définir.
            }
        }
         // Si aucun nouveau fichier n'est uploadé, mais que le nom de fichier est null (possible?)
         // et qu'il y a un nom temporaire (signifie qu'on a demandé la suppression sans uploader de nouveau)
        elseif ($this->libelleFichier === null && $this->tempFilename !== null) {
             // On garde tempFilename pour la suppression dans handleFileAfterSave
        }
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function handleFileAfterSave(): void
    {
        // Si un fichier a été uploadé, on le déplace
        if ($this->file instanceof UploadedFile) {
            try {
                $this->file->move($this->getUploadRootDir(), $this->libelleFichier);
                // Nettoyer la propriété file car elle n'est plus nécessaire
                $this->file = null;
            } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
                // Gérer l'erreur d'upload (log, message flash, etc.)
                // Il est préférable de gérer ça dans un service/listener pour plus de contrôle
            }

            // Supprimer l'ancien fichier s'il existe et est différent du nouveau
            if ($this->tempFilename !== null && $this->tempFilename !== $this->libelleFichier) {
                $oldFilePath = $this->getUploadRootDir() . '/' . $this->tempFilename;
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath); // Utiliser @ pour ignorer les erreurs si le fichier n'existe plus
                }
                $this->tempFilename = null; // Nettoyer le nom temporaire
            }
        }
        // Si aucun fichier n'a été uploadé mais qu'on a un nom temporaire
        // (ce cas est géré par handleFileDeletionOnPreRemove ou devrait l'être via une logique explicite de suppression)
    }


    #[ORM\PreRemove]
    public function storeFilenameForRemoval(): void
    {
        // Stocker le nom du fichier pour pouvoir le supprimer après la suppression de l'entité
        if ($this->libelleFichier) {
             $this->tempFilename = $this->getAbsolutePath();
        }
    }

    #[ORM\PostRemove]
    public function handleFileDeletionAfterRemove(): void
    {
        // Supprimer le fichier physique après la suppression de l'entité
        if ($this->tempFilename !== null && file_exists($this->tempFilename)) {
            @unlink($this->tempFilename);
        }
    }

    /**
     * Méthode pour supprimer le fichier associé (si on veut le supprimer sans supprimer l'entité).
     * Devrait être appelée explicitement avant PreUpdate si nécessaire.
     */
     public function prepareRemoveFile(): void
     {
         if ($this->libelleFichier) {
            $this->tempFilename = $this->libelleFichier; // Stocker pour suppression dans PostUpdate
            $this->libelleFichier = null; // Mettre à null pour que handleFileBeforeSave le détecte
            $this->numeroFichier = null; // Potentiellement aussi mettre le numéro à null
         }
         $this->file = null; // S'assurer qu'aucun fichier n'est uploadé en même temps
     }


    // --- Méthodes utilitaires pour les chemins (à améliorer) ---
    // Il est préférable de configurer ces chemins via les paramètres Symfony (services.yaml)
    // et d'injecter le chemin de base dans un service/listener.

    /**
     * Retourne le chemin absolu vers le fichier.
     */
    public function getAbsolutePath(): ?string
    {
        return $this->libelleFichier ? $this->getUploadRootDir() . '/' . $this->libelleFichier : null;
    }

    /**
     * Retourne le chemin relatif (web) vers le fichier.
     * Attention: Fonctionne seulement si 'upload/chargement' est dans le dossier public.
     */
    public function getWebPath(): ?string
    {
        // Ce chemin est relatif au dossier 'public' de Symfony >= 4
        return $this->libelleFichier ? '/' . $this->getUploadDir() . '/' . $this->libelleFichier : null;
    }

    /**
     * Retourne le répertoire absolu où les fichiers sont stockés.
     * !!! FORTEMENT DÉCONSEILLÉ: Utilise __DIR__, dépend de la structure du projet.
     * Préférez injecter le chemin via le conteneur de services.
     */
    public function getUploadRootDir(): string
    {
        // Exemple avec paramètre de service (à définir dans services.yaml)
        // return $this->uploadDirectoryParameter; // Si injecté
        // Solution de repli (fragile) :
        return __DIR__.'/../../public/' . $this->getUploadDir(); // Symfony 4+
        // return __DIR__.'/../../../../web/' . $this->getUploadDir(); // Symfony < 4 (original)
    }

    /**
     * Retourne le sous-répertoire (relatif à public/) pour les uploads de cette entité.
     */
    protected function getUploadDir(): string
    {
        return 'upload/chargement'; // Relatif au dossier public/
    }


    // --- GETTERS & SETTERS --- (Types corrigés)

    public function getId(): ?int // Nom standardisé
    {
        return $this->id;
    }

    public function getNumeroFichier(): ?string
    {
        return $this->numeroFichier;
    }

    public function setNumeroFichier(string $numeroFichier): self
    {
        $this->numeroFichier = $numeroFichier;
        return $this;
    }

    public function getLibelleFichier(): ?string
    {
        return $this->libelleFichier;
    }

    // Ce setter ne devrait être appelé que si vous gérez le nom manuellement,
    // sinon il est défini dans handleFileBeforeSave.
    public function setLibelleFichier(string $libelleFichier): self
    {
        $this->libelleFichier = $libelleFichier;
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

    public function getArchive(): ?int // Ou ?bool
    {
        return $this->archive;
    }

     public function isArchive(): bool
    {
        return $this->archive === 1;
    }

    public function setArchive(?int $archive): self // Ou ?bool
    {
        $this->archive = $archive;
        return $this;
    }

    public function getTypeChargement(): ?int
    {
        return $this->typeChargement;
    }

    public function setTypeChargement(?int $typeChargement): self
    {
        $this->typeChargement = $typeChargement;
        return $this;
    }

    public function getNatureChargement(): ?int
    {
        return $this->natureChargement;
    }

    public function setNatureChargement(?int $natureChargement): self
    {
        $this->natureChargement = $natureChargement;
        return $this;
    }

    public function getDateDeb(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateDeb;
    }

    public function setDateDeb(?DateTimeImmutable $dateDeb): self // Type param corrigé
    {
        $this->dateDeb = $dateDeb;
        return $this;
    }

    public function getDateFin(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateFin;
    }

    public function setDateFin(?DateTimeImmutable $dateFin): self // Type param corrigé
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getFileDateAjout(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->fileDateAjout;
    }

    // Setter pour fileDateAjout retiré car géré par les callbacks/constructeur

    public function getTypeCompte(): ?TypeCompte
    {
        return $this->typeCompte;
    }

    public function setTypeCompte(?TypeCompte $typeCompte): self // Accepte null
    {
        $this->typeCompte = $typeCompte;
        return $this;
    }


    /**
     * Permet de définir le fichier (utilisé par les formulaires).
     * Déclenche potentiellement la logique dans les callbacks si différent de null.
     * @param File|null $file Le fichier uploadé ou null.
     */
    public function setFile(?File $file = null): self
    {
        $this->file = $file;
        // Si on sette le fichier, on a peut-être besoin de stocker l'ancien nom
        // si l'entité est déjà persistée et a un fichier.
        // Ceci est maintenant géré dans handleFileBeforeSave.
        // if ($this->libelleFichier) {
        //     $this->tempFilename = $this->libelleFichier;
        //     $this->libelleFichier = null; // Sera recalculé dans preUpload
        // }
        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        return $this->libelleFichier ?? $this->numeroFichier ?? 'Chargement #' . $this->id;
    }
}