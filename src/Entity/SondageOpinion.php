<?php

namespace App\Entity;

use App\Repository\SondageOpinionRepository; // Importer le Repository (nom de classe corrigé)
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;               // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;        // Importer Gedmo
use Symfony\Component\Validator\Constraints as Assert;
// Importer les entités liées si nécessaire
// use App\Entity\Sondage;
// use App\Entity\AdresseIp;

/**
 * Classe qui va gerer les opinions/options de réponse sur les sondages du site web
 *
 * @author Gautier
 */
#[ORM\Entity(repositoryClass: SondageOpinionRepository::class)] // Nom Repository corrigé
#[ORM\Table(name: 'sondageopinion')]
// Pas de @ORM\HasLifecycleCallbacks si non utilisé
class SondageOpinion
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idopinion', type: Types::INTEGER)]
    private ?int $id = null; // Renommé pour suivre les conventions

    /**
     * L'option de réponse (traduisible).
     * @var string|null
     */
    #[Gedmo\Translatable]
    #[ORM\Column(name: 'reponse', type: Types::STRING, length: 255)] // Longueur par défaut si non spécifiée
    #[Assert\NotBlank(message: "L'option de réponse ne peut être vide.", groups: ['translatable_validation'])]
    #[Assert\Length(max: 255)]
    private ?string $reponse = null; // Type hint ?string

    /**
     * Compteur du nombre de fois où cette option a été choisie.
     */
    #[ORM\Column(name: 'nbreponse', type: Types::INTEGER)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: "Le nombre de réponses doit être positif ou zéro.")]
    private ?int $nbReponse = 0; // Initialisé, Type hint ?int

    /**
     * Locale utilisée pour les traductions Gedmo (non mappée).
     * @var string|null
     */
    #[Gedmo\Locale]
    private ?string $locale = null;

    // --- RELATIONS ---

    /**
     * Le sondage auquel cette option de réponse appartient.
     * Supposons qu'une option DOIT appartenir à un sondage.
     * 'sondageOpinions' est la propriété dans Sondage qui référence cette entité (inversedBy).
     */
    // Correction typo: sondageOpinnions -> sondageOpinions. VÉRIFIEZ DANS Sondage.
    #[ORM\ManyToOne(targetEntity: Sondage::class, inversedBy: 'sondageOpinions', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idsondage', referencedColumnName: 'idsondage', nullable: false)] // Rendu non nullable
    #[Assert\NotNull(message: "Le sondage associé est obligatoire.")]
    private ?Sondage $sondage = null; // Type hint ?Sondage

    /**
     * Adresses IP ayant voté pour cette opinion (si cette logique est utilisée).
     * 'sondageopinion' est la propriété dans AdresseIp qui référence cette entité (mappedBy).
     * @var Collection<int, AdresseIp>
     */
    #[ORM\OneToMany(mappedBy: 'sondageopinion', targetEntity: AdresseIp::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // Vérifiez mappedBy='sondageopinion'
    private Collection $adresseips;


    public function __construct()
    {
        $this->nbReponse = 0; // Initialiser le compteur
        $this->adresseips = new ArrayCollection();
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
     * Set reponse
     *
     * @param string $reponse
     * @return SondageOpinion
     */
    public function setReponse(string $reponse): self
    {
        $this->reponse = $reponse;
        return $this;
    }

    /**
     * Get reponse
     *
     * @return string|null
     */
    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    /**
     * Set nbReponse
     *
     * @param integer $nbReponse
     * @return SondageOpinion
     */
    public function setNbReponse(int $nbReponse): self // Type param corrigé en int
    {
        $this->nbReponse = $nbReponse;
        return $this;
    }

    /**
     * Get nbReponse
     *
     * @return integer|null
     */
    public function getNbReponse(): ?int // Type retour corrigé
    {
        return $this->nbReponse;
    }

    /**
     * Incrémente le compteur de réponses.
     */
    public function incrementNbReponse(): self
    {
        $this->nbReponse++;
        return $this;
    }


    /**
     * Set sondage
     *
     * @param Sondage $sondage Le sondage associé (non nullable)
     * @return SondageOpinion
     */
    public function setSondage(Sondage $sondage): self // Param non nullable, type corrigé
    {
        $this->sondage = $sondage;
        return $this;
    }

    /**
     * Get sondage
     *
     * @return Sondage|null
     */
    public function getSondage(): ?Sondage // Type retour corrigé
    {
        return $this->sondage;
    }


    // --- Gestion de la collection AdresseIp ---

    /**
     * @return Collection<int, AdresseIp>
     */
    public function getAdresseips(): Collection // Type retour corrigé
    {
        return $this->adresseips;
    }

    public function addAdresseip(AdresseIp $adresseIp): self // Type param corrigé, nom param singulier
    {
        if (!$this->adresseips->contains($adresseIp)) {
            $this->adresseips->add($adresseIp);
            // Mettre à jour le côté propriétaire (ManyToOne dans AdresseIp)
            $adresseIp->setSondageopinion($this); // Assurez-vous que setSondageopinion existe
        }
        return $this;
    }

    public function removeAdresseip(AdresseIp $adresseIp): self // Type param corrigé, nom param singulier
    {
        if ($this->adresseips->removeElement($adresseIp)) {
            // Mettre le côté propriétaire à null (géré par orphanRemoval si non null)
            if ($adresseIp->getSondageopinion() === $this) { // Assurez-vous que getSondageopinion existe
                $adresseIp->setSondageopinion(null);
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
        return $this->reponse ?? 'Opinion #' . $this->id;
    }
}