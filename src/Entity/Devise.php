<?php

namespace App\Entity;

use App\Repository\DeviseRepository; // Importer Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File; // Importer File
use Symfony\Component\HttpFoundation\File\UploadedFile; // Importer UploadedFile
use Symfony\Component\Validator\Constraints as Assert; // Importer Assert

#[ORM\Entity(repositoryClass: DeviseRepository::class)]
#[ORM\Table(name: 'devise')]
#[ORM\HasLifecycleCallbacks] // Conserver pour la gestion de l'icône (même si déconseillé ici)
class Devise
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'iddevise', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private

    #[ORM\Column(name: 'codedevise', type: Types::STRING, length: 5, unique: true)] // Code devrait être unique
    #[Assert\NotBlank(message: "Le code devise est obligatoire.")]
    #[Assert\Length(
        min: 3, max: 5, // Longueur standard pour codes ISO (ex: EUR, USD)
        minMessage: "Le code devise doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le code devise ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $codeDevise = null;

    #[ORM\Column(name: 'libdevise', type: Types::STRING, length: 40)]
    #[Assert\NotBlank(message: "Le libellé est obligatoire.")]
    #[Assert\Length(max: 40, maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $libDevise = null;

    /**
     * Taux de change VENTE par rapport à la devise locale (ex: 1 EUR = X LOCAL).
     * Utiliser DECIMAL pour la précision monétaire.
     */
    #[ORM\Column(name: 'valdeviselocal', type: Types::DECIMAL, precision: 15, scale: 5, nullable: true)] // DECIMAL(15,5) par exemple
    #[Assert\Type(type: "numeric", message: "La valeur doit être numérique.")]
    #[Assert\PositiveOrZero(message: "La valeur doit être positive ou zéro.")]
    private ?string $valDeviseLocal = null; // Gardé en string pour correspondre à DECIMAL, mais pourrait être float

    /**
     * Taux de change ACHAT par rapport à la devise locale.
     * Utiliser DECIMAL pour la précision monétaire.
     */
    #[ORM\Column(name: 'valdeviselocalachat', type: Types::DECIMAL, precision: 15, scale: 5, nullable: true)] // Rendue nullable pour cohérence, DECIMAL
    #[Assert\Type(type: "numeric", message: "La valeur doit être numérique.")]
    #[Assert\PositiveOrZero(message: "La valeur doit être positive ou zéro.")]
    private ?string $valDeviseLocalAchat = null; // Gardé en string

    /**
     * Indique si c'est la devise locale de référence (une seule devrait l'être).
     */
    #[ORM\Column(name: 'locale', type: Types::BOOLEAN)]
    #[Assert\NotNull]
    private ?bool $siLocale = false; // Initialisé à false

    /**
     * Indique si la devise doit être affichée/utilisable.
     */
    #[ORM\Column(name: 'affiche', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $affiche = false; // Initialisé à false

    /**
     * Chemin relatif vers l'icône de la devise (drapeau).
     */
    #[ORM\Column(name: 'urlicone', type: Types::STRING, length: 255, nullable: true)] // Longueur augmentée
    private ?string $urlIcone = null;

    /**
     * Propriété temporaire pour l'upload de l'icône. Non mappée.
     * @var File|null
     */
    #[Assert\File(
        maxSize: "2M", // Taille raisonnable pour une icône
        mimeTypes: ["image/png", "image/jpeg", "image/gif", "image/svg+xml"], // Types MIME courants pour icônes/drapeaux
        mimeTypesMessage: "Format d'image invalide. Types autorisés : {{ types }}."
    )]
    private ?File $icone = null;

    /**
     * @var Collection<int, Operation> Opérations liées à cette devise.
     */
    #[ORM\OneToMany(mappedBy: 'devise', targetEntity: Operation::class)] // Vérifier 'devise' dans Operation
    private Collection $operations;

    // --- Variable temporaire pour l'ancien nom de fichier ---
    private ?string $tempIconeFilename = null;

    public function __construct()
    {
        $this->affiche = false; // Initialisation correcte
        $this->siLocale = false; // Initialisation explicite
        $this->operations = new ArrayCollection();
    }

    // --- Gestion de l'icône (Fortement recommandé d'utiliser VichUploaderBundle) ---

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function handleIconBeforeSave(): void
    {
        if ($this->icone instanceof UploadedFile) {
            // Si une icône existe déjà, stocker son nom pour suppression éventuelle
            if ($this->urlIcone) {
                $this->tempIconeFilename = $this->urlIcone;
            }
            $originalFilename = pathinfo($this->icone->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename);
            $extension = $this->icone->guessExtension() ?: $this->icone->getClientOriginalExtension();
            $this->urlIcone = $safeFilename . '-' . uniqid() . '.' . $extension;
        }
        // Gestion si on veut juste supprimer l'icône (via un champ dédié dans le form par ex.)
        // Si $this->urlIcone est null MAIS $this->tempIconeFilename ne l'est pas => suppression dans PostUpdate
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function handleIconAfterSave(): void
    {
        // Déplacer le nouveau fichier uploadé
        if ($this->icone instanceof UploadedFile) {
            try {
                $this->icone->move($this->getUploadRootDir(), $this->urlIcone);
            } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
                // Gérer l'erreur
            }
            $this->icone = null; // Nettoyer la propriété temporaire
        }

        // Supprimer l'ancienne icône si elle existait et est différente
        if ($this->tempIconeFilename && $this->tempIconeFilename !== $this->urlIcone) {
            $oldFilePath = $this->getUploadRootDir() . '/' . $this->tempIconeFilename;
            if (file_exists($oldFilePath)) {
                @unlink($oldFilePath);
            }
            $this->tempIconeFilename = null;
        }
    }

    #[ORM\PreRemove]
    public function storeIconFilenameForRemoval(): void
    {
        if ($this->urlIcone) {
            $this->tempIconeFilename = $this->getAbsolutePath(); // Stocker chemin absolu
        }
    }

    #[ORM\PostRemove]
    public function handleIconDeletionAfterRemove(): void
    {
        if ($this->tempIconeFilename && file_exists($this->tempIconeFilename)) {
            @unlink($this->tempIconeFilename);
        }
    }

    /**
     * Prépare la suppression du fichier icone lors d'une mise à jour.
     * A appeler explicitement si un formulaire permet de décocher l'image.
     */
    public function prepareRemoveIcone(): void
    {
        if ($this->urlIcone) {
            $this->tempIconeFilename = $this->urlIcone; // Stocker nom relatif pour PostUpdate
            $this->urlIcone = null;
        }
        $this->icone = null;
    }


    // --- Méthodes de chemin (Déconseillé - Injecter via service) ---

    public function getAbsolutePath(): ?string
    {
        return $this->urlIcone ? $this->getUploadRootDir() . '/' . $this->urlIcone : null;
    }

    public function getWebPath(): ?string
    {
        return $this->urlIcone ? '/' . $this->getUploadDir() . '/' . $this->urlIcone : null;
    }

    /**
     * !!! NE PAS UTILISER EN PRODUCTION SANS INJECTION DE PARAMÈTRE !!!
     */
    public function getUploadRootDir(): string
    {
        // Chemin vers le dossier public (Symfony 4+)
        return __DIR__.'/../../public/' . $this->getUploadDir();
        // Chemin Symfony < 4 (original)
        // return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir(): string
    {
        return 'upload/drapeaux'; // Relatif à public/
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeDevise(): ?string
    {
        return $this->codeDevise;
    }

    public function setCodeDevise(string $codeDevise): self
    {
        $this->codeDevise = strtoupper($codeDevise); // Mettre en majuscule
        return $this;
    }

    public function getLibDevise(): ?string
    {
        return $this->libDevise;
    }

    public function setLibDevise(string $libDevise): self
    {
        $this->libDevise = $libDevise;
        return $this;
    }

    public function getValDeviseLocal(): ?string // Retourne string car DECIMAL
    {
        return $this->valDeviseLocal;
    }

    public function setValDeviseLocal(?string $valDeviseLocal): self // Accepte null
    {
        $this->valDeviseLocal = $valDeviseLocal;
        return $this;
    }

    public function getValDeviseLocalAchat(): ?string // Retourne string car DECIMAL
    {
        return $this->valDeviseLocalAchat;
    }

    public function setValDeviseLocalAchat(?string $valDeviseLocalAchat): self // Accepte null
    {
        $this->valDeviseLocalAchat = $valDeviseLocalAchat;
        return $this;
    }

    public function isSiLocale(): ?bool // Getter booléen standard
    {
        return $this->siLocale;
    }

    public function setSiLocale(bool $siLocale): self // Type bool
    {
        $this->siLocale = $siLocale;
        return $this;
    }

    public function isAffiche(): ?bool // Getter booléen standard
    {
        return $this->affiche;
    }

    public function setAffiche(bool $affiche): self // Type bool
    {
        $this->affiche = $affiche;
        return $this;
    }

    public function getUrlIcone(): ?string
    {
        return $this->urlIcone;
    }

    // setUrlIcone est généralement interne (géré par l'upload)

    /**
     * Utilisé par les formulaires pour lier le champ d'upload.
     */
    public function setIcone(?File $icone = null): self
    {
        $this->icone = $icone;
        // La logique de préparation (stockage ancien nom) est dans PrePersist/PreUpdate
        return $this;
    }

    public function getIcone(): ?File
    {
        return $this->icone;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
            $operation->setDevise($this); // Mettre à jour l'autre côté
        }
        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // Mettre l'autre côté à null si nécessaire
            if ($operation->getDevise() === $this) {
                $operation->setDevise(null);
            }
        }
        return $this;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        return $this->libDevise . ' (' . $this->codeDevise . ')' ?? 'Devise #' . $this->id;
    }
}