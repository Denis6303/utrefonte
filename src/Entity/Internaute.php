<?php

namespace App\Entity;

use App\Repository\InternauteRepository; // Importer le Repository
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;           // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Utiliser les objets immuables
// Importer les entités liées si nécessaire
// use App\Entity\Message;
// use App\Entity\Pays;

/**
 * Entité représentant un internaute (visiteur, contact, etc.).
 */
#[ORM\Entity(repositoryClass: InternauteRepository::class)]
#[ORM\Table(name: 'internaute')]
class Internaute
{
    /**
     * Email utilisé comme identifiant primaire.
     */
    #[ORM\Id]
    #[ORM\Column(name: 'mailinternaute', type: Types::STRING, length: 180)] // Longueur standard pour email, name conservé
    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    #[Assert\Length(max: 180)]
    private ?string $mailInternaute = null;

    #[ORM\Column(name: 'nom', type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(name: 'prenom', type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
     #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le prénom ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $prenom = null;

    /**
     * Numéro de téléphone.
     */
    #[ORM\Column(name: 'tel', type: Types::STRING, length: 30, nullable: true)] // Rendu nullable, longueur augmentée
    #[Assert\Length(max: 30, maxMessage: "Le téléphone ne doit pas dépasser {{ limit }} caractères.")]
    // Optionnel : #[Assert\Regex(pattern: "/^\+?[0-9\s\-\(\)]{7,}$/", message: "Format de téléphone invalide.")]
    private ?string $tel = null;

    /**
     * Adresse postale.
     */
    #[ORM\Column(name: 'adresse', type: Types::STRING, length: 255, nullable: true)] // Rendu nullable, longueur augmentée
    #[Assert\Length(max: 255, maxMessage: "L'adresse ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $adresse = null;

    /**
     * Type d'internaute (ex: 0=visiteur, 1=contact, ...).
     */
    #[ORM\Column(name: 'typeinternaute', type: Types::INTEGER)]
    #[Assert\NotNull]
    // Optionnel : #[Assert\Choice(choices: [0, 1, 2], message: "Type d'internaute invalide.")]
    private ?int $typeInternaute = 0; // Initialisé dans le constructeur

    #[ORM\Column(name: 'dateinscription', type: Types::DATETIME_IMMUTABLE)] // Changé en DATETIME_IMMUTABLE
    #[Assert\NotNull] // Assurer que la date est définie
    private ?DateTimeImmutable $dateInscription = null; // Changé en DateTimeImmutable

    /**
     * État de l'internaute (ex: 0=inactif, 1=actif, 2=bloqué).
     */
    #[ORM\Column(name: 'etat', type: Types::INTEGER)]
    #[Assert\NotNull]
    // Optionnel : #[Assert\Choice(choices: [0, 1, 2], message: "État invalide.")]
    private ?int $etat = 1; // Initialisé à 1 (actif) par défaut

    // --- RELATIONS ---

    /**
     * Messages envoyés par cet internaute.
     * 'internaute' est la propriété dans Message qui référence cette entité (mappedBy).
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'internaute', targetEntity: Message::class, cascade: ['persist', 'remove'], orphanRemoval: true)] // Vérifiez mappedBy='internaute'
    private Collection $messages;

    /**
     * Pays de l'internaute.
     */
    #[ORM\ManyToOne(targetEntity: Pays::class, inversedBy: 'internautes')] // Ajout de inversedBy (vérifiez nom 'internautes' dans Pays)
    #[ORM\JoinColumn(name: 'idpays', referencedColumnName: 'idpays', nullable: true)] // Gardé nullable
    private ?Pays $pays = null;


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->typeInternaute = 0; // Valeur par défaut (ex: visiteur)
        $this->dateInscription = new DateTimeImmutable(); // Date d'inscription par défaut
        $this->etat = 1; // Actif par défaut
    }

    // --- GETTERS & SETTERS ---

    public function getMailInternaute(): ?string
    {
        return $this->mailInternaute;
    }

    // Le mail est l'ID, ne devrait pas être modifiable après création
    // public function setMailInternaute(string $mailInternaute): self
    // {
    //     $this->mailInternaute = $mailInternaute;
    //     return $this;
    // }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self // Accepte null
    {
        $this->tel = $tel;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self // Accepte null
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getTypeInternaute(): ?int
    {
        return $this->typeInternaute;
    }

    public function setTypeInternaute(int $typeInternaute): self
    {
        $this->typeInternaute = $typeInternaute;
        return $this;
    }

    public function getDateInscription(): ?DateTimeImmutable // Type retour corrigé
    {
        return $this->dateInscription;
    }

    // Setter pour dateInscription retiré (défini à la construction)

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    // --- Gestion de la collection Messages ---

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection // Type retour corrigé
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self // Type param corrigé
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            // Mettre à jour le côté propriétaire (ManyToOne dans Message)
            $message->setInternaute($this); // Assurez-vous que setInternaute existe dans Message
        }
        return $this;
    }

    public function removeMessage(Message $message): self // Type param corrigé
    {
        if ($this->messages->removeElement($message)) {
            // Mettre le côté propriétaire à null (si la relation est nullable dans Message)
            if ($message->getInternaute() === $this) { // Assurez-vous que getInternaute existe
                $message->setInternaute(null);
            }
        }
        return $this;
    }

    // --- Gestion de la relation Pays ---

    public function getPays(): ?Pays // Type retour corrigé
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self // Type param corrigé
    {
        $this->pays = $pays;
        return $this;
    }

     // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return trim($this->prenom . ' ' . $this->nom) ?: $this->mailInternaute ?? 'Internaute Inconnu';
    }
}