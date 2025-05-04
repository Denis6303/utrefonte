<?php

namespace App\Entity;

use App\Repository\MediaRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;      // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // Importer Gedmo
use Symfony\Component\HttpFoundation\File\File; // Importer File
use Symfony\Component\HttpFoundation\File\UploadedFile; // Importer UploadedFile
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables

/**
 * Entité représentant un Média (image, document, etc.).
 */
#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\Table(name: 'media')]
#[ORM\HasLifecycleCallbacks] // Conserver pour la gestion du fichier
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idmedia', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * Type de média (ex: 1=image, 2=document, 3=lien externe/vidéo).
     */
    #[ORM\Column(name: 'typemedia', type: Types::INTEGER)]
    #[Assert\NotNull]
    // #[Assert\Choice(choices: [1, 2, 3], message: "Type de média invalide.")] // Si limité
    private ?int $typeMedia = null;

    /**
     * URL ou chemin du fichier principal (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'urlmedia', type: Types::STRING, length: 255)] // Longueur par défaut si non spécifiée
    #[Assert\Length(max: 255)]
    // NotBlank ne s'applique pas ici car défini par l'upload ou manuellement
    private ?string $urlMedia = null;

    /**
     * URL ou chemin de la miniature ou première version (traduisible?).
     * @var string|null
     */
    #[Gedmo\Translatable] // À confirmer si ce champ doit être traduisible
    #[ORM\Column(name: 'urlfistmedia', type: Types::STRING, length: 255, nullable: true)] // Rendu nullable
    #[Assert\Length(max: 255)]
    private ?string $urlFistMedia = null;

    /**
     * Variable temporaire utilisée pendant l'upload pour stocker le nom généré.
     * Pas une colonne ORM.
     */
    #[ORM\Column(name: 'urlvariable', type: Types::STRING, length: 255, nullable: true)] // Rendu nullable
    #[Assert\Length(max: 255)]
    private ?string $urlVariable = null;

    #[ORM\Column(name: 'positionmedia', type: Types::INTEGER, nullable: true)] // Rendu nullable
    private ?int $positionMedia = 1; // Valeur par défaut

    /**
     * Nom du média (titre, légende) (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'nommedia', type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $nomMedia = null;

    /**
     * Description du média (alt text, etc.) (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'descriptionMedia', type: Types::TEXT, nullable: true)] // Type TEXT, nom de colonne corrigé
    private ?string $descriptionMedia = null;

    /**
     * Indique si c'est l'image d'illustration principale.
     */
    #[ORM\Column(name: 'illustreImgMedia', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $illustreImgMedia = false; // Défaut

    /**
     * Indicateur d'ajout/modification (0=ajout, 1=modif?). Utiliser les dates est mieux.
     */
    #[ORM\Column(name: 'ajoutmodifmedia', type: Types::INTEGER)] // Gardé INTEGER, mais utilité limitée
    #[Assert\NotNull]
    private ?int $ajoutmodifMedia = 0; // Initialisé dans le constructeur

    /**
     * ID de l'utilisateur ayant ajouté. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'mediaajoutpar', type: Types::INTEGER)]
    #[Assert\NotNull] // Si toujours requis
    private ?int $mediaAjoutPar = null;

    #[ORM\Column(name: 'mediadateajout', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull]
    private ?DateTimeImmutable $mediaDateAjout = null; // Sera défini dans PrePersist

    /**
     * ID de l'utilisateur ayant modifié. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'mediamodifpar', type: Types::INTEGER, nullable: true)]
    private ?int $mediaModifPar = null;

    #[ORM\Column(name: 'mediadatemodif', type: Types::DATETIME_IMMUTABLE, nullable: true)] // Changé en DATETIME_IMMUTABLE
    private ?DateTimeImmutable $mediaDateModif = null; // Sera défini dans PreUpdate


    // --- Relations ---

    /**
     * Articles auxquels ce média est associé (Owning side).
     * @var Collection<int, Article>
     */
    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'medias', cascade: ['persist'])] // mappedBy (Inverse side)
    private Collection $articles;

    #[ORM\ManyToOne(targetEntity: Dimension::class, inversedBy: 'medias', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'iddimension', referencedColumnName: 'iddimension', nullable: true)] // Rendu nullable
    private ?Dimension $dimension = null;

    #[ORM\ManyToOne(targetEntity: Rubrique::class, inversedBy: 'medias', cascade: ['persist', 'merge'])]
    #[ORM\JoinColumn(name: 'idrubrique', referencedColumnName: 'idrubrique', nullable: true)] // Rendu nullable
    private ?Rubrique $rubrique = null;

    #[ORM\ManyToOne(targetEntity: Cadre::class, inversedBy: 'medias', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idcadre', referencedColumnName: 'idcadre', nullable: true)] // Rendu nullable
    private ?Cadre $cadre = null;

    #[ORM\ManyToOne(targetEntity: NatureDoc::class, inversedBy: 'medias', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idnaturedoc', referencedColumnName: 'idnaturedoc', nullable: true)] // Rendu nullable
    private ?NatureDoc $natureDoc = null;


    // --- Propriété non mappée pour l'upload ---
    /**
     * @var File|null
     */
    #[Assert\File(
        maxSize: "2M",
        mimeTypes: ["image/gif", "image/jpeg", "image/png", "application/pdf"], // Correction mime type PDF
        mimeTypesMessage: "Format de fichier invalide ({{ type }}). Types autorisés : {{ types }}"
    )]
    // NotBlank n'est pertinent que dans certains contextes (ex: ajout)
    // Placé ici pour la validation du formulaire, mais l'upload est optionnel pour l'entité elle-même
    private ?File $file = null;


    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;

     // --- Variable temporaire pour l'ancien nom de fichier ---
    private ?string $tempFilename = null;


    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->ajoutmodifMedia = 0; // Ajout par défaut
        $this->illustreImgMedia = false; // Non illustrant par défaut
        $this->positionMedia = 1; // Position 1 par défaut
        $this->articles = new ArrayCollection();
        // Les dates sont gérées par les callbacks
    }


    // --- Lifecycle Callbacks pour la gestion du fichier ---
    // !! Rappel: La gestion de l'upload dans l'entité est DÉCONSEILLÉE !!
    // !! Préférez un Listener Doctrine ou VichUploaderBundle !!

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function handleFileBeforeSave(): void
    {
         // Définir les dates
        if ($this->mediaDateAjout === null) { // Seulement à la création
            $this->mediaDateAjout = new DateTimeImmutable();
        }
        $this->mediaDateModif = new DateTimeImmutable(); // Toujours à la mise à jour

        // Gérer le fichier SEULEMENT s'il y en a un de nouveau
        if ($this->file instanceof UploadedFile) {

            // Stocker l'ancien nom si mise à jour
            if ($this->id !== null && $this->urlMedia !== null) {
                $this->tempFilename = $this->urlMedia; // Stocker l'ancien chemin/nom
            }

            // Générer un nom unique
            $extension = $this->file->guessExtension() ?: $this->file->getClientOriginalExtension();
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME));
            $this->urlMedia = $safeFilename . '-' . uniqid() . '.' . $extension;

             // Mise à jour conditionnelle basée sur l'ancienne logique (ajoutmodifMedia)
             // Cette logique est complexe et pourrait être simplifiée ou externalisée.
             // if ($this->ajoutmodifMedia == 0 || $this->ajoutmodifMedia == 2) {
             //    $this->urlVariable = $this->urlMedia; // urlVariable semble redondant avec urlMedia
             // }

            // Logique urlFistMedia semble liée à la PREMIERE version du fichier. Complexe à gérer ici.
            // if (strpos($this->urlMedia, '/') === false) { // Test peu fiable
            //    $this->urlFistMedia = $this->urlMedia;
            // }
        }
        // Si pas de nouveau fichier, mais qu'on a demandé la suppression (via prepareRemoveFile par ex.)
        elseif ($this->urlMedia === null && $this->tempFilename !== null) {
             // Ne rien faire ici, la suppression se fera dans PostUpdate/PostRemove
        }
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function handleFileAfterSave(): void
    {
        // Si un fichier a été préparé pour l'upload (urlMedia a été défini dans PrePersist/PreUpdate)
        // ET que la propriété $file contient le fichier uploadé
        if ($this->file instanceof UploadedFile && $this->urlMedia !== null) {
            $targetDirectory = $this->getUploadRootDir(); // Obtenir le dossier cible
            if ($targetDirectory) { // Vérifier que le dossier est valide
                 try {
                    $this->file->move($targetDirectory, $this->urlMedia);
                    // Optionnel: Gérer les permissions si nécessaire
                    // @chmod($targetDirectory . '/' . $this->urlMedia, 0644);
                 } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
                     // Log l'erreur
                     // error_log("Erreur upload Media ID ".$this->id.": ".$e->getMessage());
                 }
            }
            // Nettoyer la propriété file
            $this->file = null;
        }

        // Supprimer l'ancien fichier si un nouveau a été uploadé
        if ($this->tempFilename !== null) {
            $oldFilePath = $this->getUploadRootDir() . '/' . $this->tempFilename;
             if (file_exists($oldFilePath) && $this->tempFilename !== $this->urlMedia) {
                @unlink($oldFilePath);
            }
            $this->tempFilename = null; // Nettoyer
        }
    }

    #[ORM\PreRemove]
    public function storeFilenameForRemoval(): void
    {
        if ($this->urlMedia) {
             $this->tempFilename = $this->getAbsolutePath(); // Stocker chemin absolu
        }
    }

    #[ORM\PostRemove]
    public function handleFileDeletionAfterRemove(): void
    {
        if ($this->tempFilename !== null && file_exists($this->tempFilename)) {
            @unlink($this->tempFilename);
        }
    }

    /**
     * Prépare la suppression du fichier associé sans supprimer l'entité.
     * Appeler avant de flush().
     */
    public function prepareRemoveFile(): void
    {
        if ($this->urlMedia) {
           $this->tempFilename = $this->urlMedia; // Stocker pour suppression dans PostUpdate
           $this->urlMedia = null;
           $this->urlFistMedia = null; // Potentiellement réinitialiser aussi
           // Réinitialiser d'autres champs liés au fichier si nécessaire
        }
        $this->file = null; // S'assurer qu'aucun nouveau fichier n'est traité
    }


    // --- Méthodes de chemin (Déconseillé - utiliser des paramètres injectés) ---

    public function getAbsolutePath(): ?string
    {
        return $this->urlMedia ? $this->getUploadRootDir() . '/' . $this->urlMedia : null;
    }

    public function getWebPath(): ?string
    {
        // Relatif au dossier public/
        return $this->urlMedia ? '/' . $this->getUploadDirBasedOnContext() . '/' . $this->urlMedia : null;
    }

    /**
     * !!! FORTEMENT DÉCONSEILLÉ: Dépend de la structure du projet. !!!
     * À remplacer par un paramètre injecté (%kernel.project_dir%/public/...)
     */
    public function getUploadRootDir(): ?string
    {
        $dir = $this->getUploadDirBasedOnContext();
        if (!$dir) return null;
        // Assumer Symfony 4+ structure avec /public
        return __DIR__.'/../../public/' . $dir;
         // Chemin original Symfony < 4 :
        // return __DIR__.'/../../../../web/' . $dir;
    }

    /**
     * Détermine le répertoire d'upload basé sur le contexte.
     * !! Logique complexe ici, sujette aux erreurs et difficile à maintenir !!
     * !! Préférer une approche où le chemin est défini explicitement ou via config !!
     */
    protected function getUploadDirBasedOnContext(): ?string
    {
        if ($this->rubrique !== null) {
            return 'upload/rubriques'; // Upload lié à une rubrique
        } elseif ($this->cadre !== null) {
             return 'upload/cadres'; // Upload lié à un cadre
        } elseif ($this->articles->count() > 0 || $this->illustreImgMedia) { // Lié à un article
            if ($this->typeMedia == 1) { // Image
                return 'upload/articles/images';
            } elseif ($this->typeMedia == 2) { // Document
                return 'upload/articles/docs';
            } else { // Autre type lié à article (ou image principale)
                 return 'upload/articles';
            }
        } else {
            // Cas par défaut ou inconnu?
             return 'upload/medias'; // Un dossier générique?
        }
        // L'ancienne logique avec positionMedia=3 n'est pas claire
    }

    // --- GETTERS & SETTERS --- (Types corrigés)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeMedia(): ?int
    {
        return $this->typeMedia;
    }

    public function setTypeMedia(int $typeMedia): self
    {
        $this->typeMedia = $typeMedia;
        return $this;
    }

    public function getUrlMedia(): ?string
    {
        return $this->urlMedia;
    }

    public function setUrlMedia(?string $urlMedia): self // Accepte null
    {
        $this->urlMedia = $urlMedia;
        return $this;
    }

     public function getUrlFistMedia(): ?string { return $this->urlFistMedia; }
     public function setUrlFistMedia(?string $d): self { $this->urlFistMedia = $d; return $this; }
     public function getUrlVariable(): ?string { return $this->urlVariable; }
     public function setUrlVariable(?string $d): self { $this->urlVariable = $d; return $this; }


    public function getPositionMedia(): ?int
    {
        return $this->positionMedia;
    }

    public function setPositionMedia(?int $positionMedia): self // Accepte null
    {
        $this->positionMedia = $positionMedia;
        return $this;
    }

    public function getAjoutmodifMedia(): ?int
    {
        return $this->ajoutmodifMedia;
    }

    public function setAjoutmodifMedia(int $ajoutmodifMedia): self
    {
        $this->ajoutmodifMedia = $ajoutmodifMedia;
        return $this;
    }

    public function getNomMedia(): ?string
    {
        return $this->nomMedia;
    }

    public function setNomMedia(?string $nomMedia): self // Accepte null
    {
        $this->nomMedia = $nomMedia;
        return $this;
    }

    public function getDescriptionMedia(): ?string
    {
        return $this->descriptionMedia;
    }

    public function setDescriptionMedia(?string $descriptionMedia): self // Accepte null
    {
        $this->descriptionMedia = $descriptionMedia;
        return $this;
    }

    public function isIllustreImgMedia(): ?bool // Getter booléen
    {
        return $this->illustreImgMedia;
    }

    public function setIllustreImgMedia(bool $illustreImgMedia): self // Setter booléen
    {
        $this->illustreImgMedia = $illustreImgMedia;
        return $this;
    }

    public function getMediaAjoutPar(): ?int { return $this->mediaAjoutPar; }
    public function setMediaAjoutPar(int $id): self { $this->mediaAjoutPar = $id; return $this; }
    public function getMediaDateAjout(): ?DateTimeImmutable { return $this->mediaDateAjout; }
    // Setter date ajout retiré
    public function getMediaModifPar(): ?int { return $this->mediaModifPar; }
    public function setMediaModifPar(?int $id): self { $this->mediaModifPar = $id; return $this; } // Accepte null
    public function getMediaDateModif(): ?DateTimeImmutable { return $this->mediaDateModif; }
     // Setter date modif retiré

    // --- Collections & Relations ---

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addMedia($this); // Assumer owning side sur Article
        }
        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
             $article->removeMedia($this); // Assumer owning side sur Article
        }
        return $this;
    }

    public function getDimension(): ?Dimension { return $this->dimension; }
    public function setDimension(?Dimension $d): self { $this->dimension = $d; return $this; }
    public function getRubrique(): ?Rubrique { return $this->rubrique; }
    public function setRubrique(?Rubrique $r): self { $this->rubrique = $r; return $this; }
    public function getCadre(): ?Cadre { return $this->cadre; }
    public function setCadre(?Cadre $c): self { $this->cadre = $c; return $this; }
    public function getNatureDoc(): ?NatureDoc { return $this->natureDoc; }
    public function setNatureDoc(?NatureDoc $n): self { $this->natureDoc = $n; return $this; }

    // --- Fichier & Locale ---
    public function setFile(?File $file = null): self
    {
        $this->file = $file;
        // Gérer l'ancien nom pour suppression si l'entité est déjà persistée
        if ($file && $this->id !== null && $this->urlMedia !== null) {
            $this->tempFilename = $this->urlMedia;
        }
        return $this;
    }
    public function getFile(): ?File { return $this->file; }
    public function setTranslatableLocale(string $locale): self { $this->locale = $locale; return $this;}


     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->nomMedia ?? $this->urlMedia ?? 'Media #' . $this->id;
    }
}