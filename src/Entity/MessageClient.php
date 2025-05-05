<?php

namespace App\Entity;

use App\Repository\MessageClientRepository; // Importer le Repository
use Doctrine\DBAL\Types\Types;             // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// ArrayCollection n'est pas utilisé ici

/**
 * Entité représentant un message client (peut-être un modèle ou un message spécifique).
 */
#[ORM\Entity(repositoryClass: MessageClientRepository::class)]
#[ORM\Table(name: 'messageclient')]
// @ORM\HasLifecycleCallbacks supprimé car pas de méthodes de callback définies
class MessageClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idmessageclient', type: Types::INTEGER)]
    private ?int $id = null; // Renommé, visibilité private, type hint ?int

    #[ORM\Column(name: 'objetmessageclient', type: Types::STRING, length: 150)] // Longueur augmentée si besoin
    #[Assert\NotBlank(message: "L'objet du message ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 150, // Correspond à la longueur ORM (ou ajustée)
        minMessage: "L'objet doit contenir au moins {{ limit }} caractères.",
        maxMessage: "L'objet ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $objetMessageClient = null; // Type hint ?string

    #[ORM\Column(name: 'contenumessageclient', type: Types::TEXT, nullable: true)] // Gardé nullable
    #[Assert\Length(
        min: 2,
        minMessage: "Le contenu doit contenir au moins {{ limit }} caractères."
        // Pas de NotBlank car nullable=true
    )]
    private ?string $contenuMessageClient = null; // Type hint ?string

    /**
     * Indique si c'est un message système (automatique).
     */
    #[ORM\Column(name: 'messagesysteme', type: Types::BOOLEAN)] // Changé en BOOLEAN, nom colonne corrigé
    #[Assert\NotNull] // Un booléen doit être défini
    private ?bool $messageSysteme = false; // Initialisé dans le constructeur, type hint ?bool

    /**
     * Relation vers les envois utilisant ce message.
     * 'messageclient' est la propriété dans Envoi qui référence cette entité (mappedBy).
     * @var Collection<int, Envoi>
     */
    #[ORM\OneToMany(mappedBy: 'messageclient', targetEntity: Envoi::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $envois; // Ajout de la propriété manquante pour la relation inverse

    public function __construct()
    {
        $this->messageSysteme = false; // Initialisé à false par défaut
        $this->envois = new ArrayCollection(); // Initialiser la collection
    }

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
     * Set objetMessageClient
     *
     * @param string $objetMessageClient
     * @return MessageClient
     */
    public function setObjetMessageClient(string $objetMessageClient): self
    {
        $this->objetMessageClient = $objetMessageClient;
        return $this;
    }

    /**
     * Get objetMessageClient
     *
     * @return string|null
     */
    public function getObjetMessageClient(): ?string
    {
        return $this->objetMessageClient;
    }

    /**
     * Set contenuMessageClient
     *
     * @param string|null $contenuMessageClient // Accepte null
     * @return MessageClient
     */
    public function setContenuMessageClient(?string $contenuMessageClient): self // Type param corrigé, accepte null
    {
        $this->contenuMessageClient = $contenuMessageClient;
        return $this;
    }

    /**
     * Get contenuMessageClient
     *
     * @return string|null
     */
    public function getContenuMessageClient(): ?string
    {
        return $this->contenuMessageClient;
    }

    /**
     * Vérifie si c'est un message système.
     */
    public function isMessageSysteme(): ?bool // Getter standard pour booléen
    {
        return $this->messageSysteme;
    }

    /**
     * Set messageSysteme
     *
     * @param boolean $messageSysteme
     * @return MessageClient
     */
    public function setMessageSysteme(bool $messageSysteme): self // Type param corrigé en bool
    {
        $this->messageSysteme = $messageSysteme;
        return $this;
    }

    /**
     * Get messageSysteme (moins sémantique que isMessageSysteme)
     *
     * @return boolean|null
     */
    public function getMessageSysteme(): ?bool // Type retour corrigé
    {
        return $this->messageSysteme;
    }

    // --- Gestion de la collection Envois ---

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
            $envoi->setMessageclient($this); // Met à jour le côté propriétaire
        }
        return $this;
    }

    public function removeEnvoi(Envoi $envoi): self
    {
        if ($this->envois->removeElement($envoi)) {
            // Met le côté propriétaire à null si la relation est nullable
            if ($envoi->getMessageclient() === $this) {
                $envoi->setMessageclient(null);
            }
        }
        return $this;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->objetMessageClient ?? 'MessageClient #' . $this->id;
    }
}