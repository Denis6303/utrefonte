<?php

namespace App\Entity;

use App\Repository\SondageRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;          // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;   // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer les entités liées si nécessaire
// use App\Entity\SondageOpinion;

/**
 * Classe qui va gerer les questions de sondages du site web
 *
 * @author Gautier
 */
#[ORM\Entity(repositoryClass: SondageRepository::class)]
#[ORM\Table(name: 'sondage')]
#[ORM\HasLifecycleCallbacks] // Garder si PrePersist est utilisé pour la date
class Sondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idsondage', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * La question du sondage (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'question', type: Types::TEXT)] // Changé en TEXT pour une question potentiellement longue
    #[Assert\NotBlank(message: "La question ne peut être vide.", groups: ['translatable_validation'])]
    private ?string $question = null; // Type hint ?string

    /**
     * Statut du sondage (actif/inactif).
     */
    #[ORM\Column(name: 'actif', type: Types::BOOLEAN)] // Changé en BOOLEAN
    #[Assert\NotNull]
    private ?bool $actif = true; // Initialisé dans le constructeur, type hint ?bool

    /**
     * ID de l'utilisateur ayant ajouté. Relation ManyToOne serait mieux.
     */
    #[ORM\Column(name: 'questionajoutpar', type: Types::INTEGER, nullable: true)] // Rendu nullable
    private ?int $questionAjoutPar = null; // Type hint ?int

    /**
     * Date d'ajout de la question.
     */
    #[ORM\Column(name: 'questiondateajout', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull] // Initialisé
    private ?DateTimeImmutable $questionDateAjout = null; // Type hint ?DateTimeImmutable

    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;

    // --- RELATION ---
    /**
     * Options/Opinions possibles pour ce sondage.
     * 'sondage' est la propriété dans SondageOpinion qui référence cette entité (mappedBy).
     * @var Collection<int, SondageOpinion>
     */
    #[ORM\OneToMany(mappedBy: 'sondage', targetEntity: SondageOpinion::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // Vérifiez mappedBy='sondage'
    private Collection $sondageOpinions;


    public function __construct()
    {
        $this->actif = true; // Actif par défaut
        $this->sondageOpinions = new ArrayCollection();
        // La date est gérée par PrePersist ou initialisée ici si pas de callback
        // $this->questionDateAjout = new DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function setDateOnCreate(): void
    {
        if ($this->questionDateAjout === null) {
            $this->questionDateAjout = new DateTimeImmutable();
        }
         if ($this->actif === null) { // Sécurité
             $this->actif = true;
         }
        // On pourrait définir questionAjoutPar ici si l'utilisateur est connu
    }

    // --- GETTERS & SETTERS ---

    /**
     * Get id
     *
     * @return integer|null
     */
    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    // Pas de setter pour l'ID

    /**
     * Set question
     *
     * @param string $question
     * @return Sondage
     */
    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }

    /**
     * Get question
     *
     * @return string|null
     */
    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * Vérifie si le sondage est actif.
     */
    public function isActif(): ?bool // Getter booléen
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return Sondage
     */
    public function setActif(bool $actif): self // Type paramètre corrigé en bool
    {
        $this->actif = $actif;
        return $this;
    }

    /**
     * Get actif (moins sémantique que isActif)
     * @return boolean|null
     */
    public function getActif(): ?bool // Type retour corrigé
    {
        return $this->actif;
    }


    /**
     * Set questionAjoutPar
     *
     * @param integer|null $questionAjoutPar
     * @return Sondage
     */
    public function setQuestionAjoutPar(?int $questionAjoutPar): self // Type param corrigé, accepte null
    {
        $this->questionAjoutPar = $questionAjoutPar;
        return $this;
    }

    /**
     * Get questionAjoutPar
     *
     * @return integer|null
     */
    public function getQuestionAjoutPar(): ?int // Type retour corrigé
    {
        return $this->questionAjoutPar;
    }

    /**
     * Get questionDateAjout
     *
     * @return DateTimeImmutable|null
     */
    public function getQuestionDateAjout(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->questionDateAjout;
    }

    // Setter retiré car géré par PrePersist

    // --- Gestion de la collection SondageOpinion ---

    /**
     * @return Collection<int, SondageOpinion>
     */
    public function getSondageOpinions(): Collection // Type retour corrigé
    {
        return $this->sondageOpinions;
    }

    public function addSondageOpinion(SondageOpinion $sondageOpinion): self // Type param corrigé
    {
        if (!$this->sondageOpinions->contains($sondageOpinion)) {
            $this->sondageOpinions->add($sondageOpinion);
            // Mettre à jour le côté propriétaire (ManyToOne dans SondageOpinion)
            $sondageOpinion->setSondage($this); // Assurez-vous que setSondage existe
        }
        return $this;
    }

    public function removeSondageOpinion(SondageOpinion $sondageOpinion): self // Type param corrigé
    {
        if ($this->sondageOpinions->removeElement($sondageOpinion)) {
            // Mettre le côté propriétaire à null (géré par orphanRemoval si non null)
            if ($sondageOpinion->getSondage() === $this) { // Assurez-vous que getSondage existe
                $sondageOpinion->setSondage(null);
            }
        }
        return $this;
    }

    // --- Gestionnaire de locale Gedmo ---
    public function setTranslatableLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        // Tronquer la question si elle est trop longue pour une représentation string
        $questionPreview = mb_substr($this->question ?? '', 0, 50);
        if (mb_strlen($this->question ?? '') > 50) {
            $questionPreview .= '...';
        }
        return $questionPreview ?: 'Sondage #' . $this->id;
    }
}