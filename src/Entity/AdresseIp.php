<?php

namespace App\Entity;

use App\Repository\AdresseIpRepository; // Assurez-vous que ce repository existe ou créez-le
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Supprimer les imports non utilisés: ArrayCollection, Gedmo\*, Translatable

/**
 * Entité pour stocker l'adresse IP associée à une réponse de sondage d'opinion.
 * Le nom de table 'adresseip' est conservé.
 */
#[ORM\Entity(repositoryClass: AdresseIpRepository::class)] // Référence au bon repository
#[ORM\Table(name: 'adresseip')]
class AdresseIp
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(name: 'idip', type: Types::INTEGER)] // Conserve le nom de colonne 'idip'
    private ?int $id = null;

    /**
     * L'adresse IP enregistrée.
     * ATTENTION: Le nom de colonne original était 'question', ce qui semble incorrect.
     * Il a été changé pour 'ip'. Vérifiez si c'est correct pour votre base de données.
     * Si la colonne s'appelle bien 'question', remplacez name: 'ip' par name: 'question'.
     */
    #[ORM\Column(name: 'ip', type: Types::STRING, length: 45)] // Longueur suffisante pour IPv4 et IPv6
    #[Assert\NotBlank]
    #[Assert\Ip] // Valide que la chaîne est une adresse IP (v4 ou v6)
    private ?string $ip = null;

    #[ORM\ManyToOne(targetEntity: SondageOpinion::class, inversedBy: 'adresseips', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'idopinion', referencedColumnName: 'idopinion', nullable: false)] // Assurez-vous que 'idopinion' est la clé primaire de SondageOpinion et que la relation ne doit pas être nulle
    #[Assert\NotNull] // Si la relation est obligatoire
    private ?SondageOpinion $sondageopinion = null;

    // Le constructeur vide peut être supprimé s'il ne fait rien
    // function __construct() {
    // }

    public function getId(): ?int // Type de retour corrigé en ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function getSondageopinion(): ?SondageOpinion // Type de retour corrigé en ?SondageOpinion
    {
        return $this->sondageopinion;
    }

    // Le paramètre peut être typé directement, pas besoin de = null si la relation est obligatoire (nullable: false)
    // Si la relation PEUT être nulle (nullable: true dans JoinColumn), alors gardez ?SondageOpinion
    public function setSondageopinion(?SondageOpinion $sondageopinion): self
    {
        $this->sondageopinion = $sondageopinion;
        return $this;
    }

    // Optionnel: Ajouter une méthode __toString pour un affichage facile
    public function __toString(): string
    {
        return $this->ip ?? 'Adresse IP #' . $this->id;
    }
}