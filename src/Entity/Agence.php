<?php

namespace App\Entity;

use App\Repository\AgenceRepository; // Assurez-vous que ce repository existe ou créez-le
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable; // Importer pour le type hint

#[ORM\Entity(repositoryClass: AgenceRepository::class)]
#[ORM\Table(name: 'agence')]
class Agence
{
    /**
     * Clé primaire non auto-générée.
     * Le code agence doit être fourni.
     */
    #[ORM\Id]
    #[ORM\Column(name: 'codeagence', type: Types::STRING, length: 4)] // Longueur spécifique de 4 caractères
    #[Assert\NotBlank(message: "Le code agence ne peut pas être vide.")]
    #[Assert\Length(exactly: 4, exactMessage: "Le code agence doit contenir exactement {{ limit }} caractères.")]
    private ?string $codeAgence = null;

    #[ORM\Column(name: 'libagence', type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le libellé de l'agence ne peut pas être vide.")]
    #[Assert\Length(max: 100, maxMessage: "Le libellé ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $libAgence = null;

    #[ORM\Column(name: 'telagence', type: Types::STRING, length: 25, nullable: true)] // Rendu nullable si le téléphone n'est pas obligatoire
    #[Assert\Length(max: 25, maxMessage: "Le téléphone ne peut pas dépasser {{ limit }} caractères.")]
    // Ajoutez d'autres Assert comme Regex si vous voulez valider le format du téléphone
    private ?string $telAgence = null;

    #[ORM\Column(name: 'adresseagence', type: Types::TEXT)]
    #[Assert\NotBlank(message: "L'adresse de l'agence ne peut pas être vide.")]
    private ?string $adresseAgence = null;

    /**
     * État de l'agence (par exemple: 1=actif, 0=inactif).
     */
    #[ORM\Column(name: 'etatagence', type: Types::INTEGER)]
    #[Assert\NotNull(message: "L'état de l'agence doit être défini.")]
    // Optionnel : Valider les valeurs possibles (ex: 0 ou 1)
    // #[Assert\Choice(choices: [0, 1], message: "L'état doit être 0 ou 1.")]
    private ?int $etatAgence = null;

    #[ORM\Column(name: 'datecreation', type: Types::DATETIME_IMMUTABLE)] // Utilisation de DATETIME_IMMUTABLE recommandée
    #[Assert\NotNull(message: "La date de création est requise.")]
    private ?\DateTimeImmutable $dateCreation = null;

    /**
     * Indicateur de suppression logique (0=non supprimé, 1=supprimé).
     */
    #[ORM\Column(name: 'suppr', type: Types::INTEGER)] // Pourrait être Types::BOOLEAN si seulement 0/1
    #[Assert\NotNull]
    // Optionnel : Valider les valeurs possibles
    // #[Assert\Choice(choices: [0, 1], message: "La valeur de suppression doit être 0 ou 1.")]
    private ?int $suppr = null;


    public function __construct()
    {
        $this->etatAgence = 1; // Valeur par défaut pour actif
        $this->suppr = 0;      // Valeur par défaut pour non supprimé
        $this->dateCreation = new DateTimeImmutable(); // Initialiser la date de création
    }


    public function getCodeAgence(): ?string
    {
        return $this->codeAgence;
    }

    /**
     * Le code agence est la clé primaire, il ne devrait généralement pas être modifié après création.
     * Si vous permettez de le définir, assurez-vous que c'est avant la persistence initiale.
     */
    public function setCodeAgence(string $codeAgence): self
    {
        $this->codeAgence = $codeAgence;
        return $this;
    }

    public function getLibAgence(): ?string
    {
        return $this->libAgence;
    }

    public function setLibAgence(string $libAgence): self
    {
        $this->libAgence = $libAgence;
        return $this;
    }

    public function getTelAgence(): ?string
    {
        return $this->telAgence;
    }

    public function setTelAgence(?string $telAgence): self // Accepte null si nullable=true
    {
        $this->telAgence = $telAgence;
        return $this;
    }

    public function getAdresseAgence(): ?string
    {
        return $this->adresseAgence;
    }

    public function setAdresseAgence(string $adresseAgence): self
    {
        $this->adresseAgence = $adresseAgence;
        return $this;
    }

    public function getEtatAgence(): ?int // Type de retour corrigé en ?int
    {
        return $this->etatAgence;
    }

    public function setEtatAgence(int $etatAgence): self // Type de paramètre corrigé en int
    {
        $this->etatAgence = $etatAgence;
        return $this;
    }

    public function getSuppr(): ?int // Type de retour corrigé en ?int
    {
        return $this->suppr;
    }

     // Méthode sémantique pour la suppression logique
    public function isSuppr(): bool
    {
        return $this->suppr === 1;
    }

    public function setSuppr(int $suppr): self // Type de paramètre corrigé en int
    {
        $this->suppr = $suppr;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable // Type de retour corrigé
    {
        return $this->dateCreation;
    }

    /**
     * La date de création est généralement définie à la construction et ne devrait pas être modifiable.
     * Si elle doit être modifiable, la méthode est correcte. Sinon, supprimez ce setter.
     */
    public function setDateCreation(\DateTimeImmutable $dateCreation): self // Type de paramètre corrigé
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    // Méthode __toString pour affichage simple
    public function __toString(): string
    {
        return $this->libAgence ?? $this->codeAgence ?? 'Agence';
    }
}