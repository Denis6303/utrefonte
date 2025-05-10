<?php

namespace App\Entity;

use App\Repository\SquelettepageRepository; // Importer le Repository (nom de classe corrigé)
use Doctrine\DBAL\Types\Types;            // Importer Types
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant la structure ou le modèle d'une page.
 */
#[ORM\Entity(repositoryClass: SquelettepageRepository::class)] // Nom Repository corrigé
#[ORM\Table(name: 'squelettepage')]
// Pas besoin de @ORM\HasLifecycleCallbacks si non utilisé
class Squelettepage
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // strategy: 'AUTO' est la valeur par défaut
    #[ORM\Column(type: Types::INTEGER)] // name="id" est la valeur par défaut
    private ?int $id = null; // Visibilité private, type hint ?int

    /**
     * Nom ou titre de la page/modèle.
     * @var string|null
     */
    #[ORM\Column(type: Types::STRING, length: 255)] // name="page" est la valeur par défaut
    #[Assert\NotBlank(message: "Le nom de la page ne peut être vide.")]
    #[Assert\Length(
        max: 255, // Correspond à la longueur ORM
        maxMessage: "Le nom de la page ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $page = null; // Type hint ?string

    /**
     * URL, chemin relatif ou nom de route associé à la page/modèle.
     * @var string|null
     */
    #[ORM\Column(type: Types::STRING, length: 255)] // name="pageurl" est la valeur par défaut
    #[Assert\NotBlank(message: "L'URL/Chemin de la page ne peut être vide.")]
    #[Assert\Length(
        max: 255, // Correspond à la longueur ORM
        maxMessage: "L'URL/Chemin ne doit pas dépasser {{ limit }} caractères."
    )]
    // Optionnel: Ajouter Assert\Url si c'est toujours une URL complète,
    // ou Assert\Regex si ça doit suivre un pattern spécifique (ex: commencer par /).
    // #[Assert\Url(message: "L'URL '{{ value }}' n'est pas une URL valide.")]
    // #[Assert\Regex(pattern: "/^\/[\w\/-]*$/", message: "Le chemin doit commencer par / et ne contenir que des caractères alphanumériques, / ou -.")]
    private ?string $pageurl = null; // Type hint ?string

    // Constructeur vide supprimé

    /**
     * Get id
     *
     * @return integer|null
     */
    public function getId(): ?int // Type retour corrigé
    {
        return $this->id;
    }

    // Pas de setter pour l'ID auto-généré

    /**
     * Set page
     *
     * @param string $page
     * @return Squelettepage
     */
    public function setPage(string $page): self
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get page
     *
     * @return string|null
     */
    public function getPage(): ?string
    {
        return $this->page;
    }

    /**
     * Set pageurl
     *
     * @param string $pageurl
     * @return Squelettepage
     */
    public function setPageurl(string $pageurl): self
    {
        $this->pageurl = $pageurl;
        return $this;
    }

    /**
     * Get pageurl
     *
     * @return string|null
     */
    public function getPageurl(): ?string
    {
        return $this->pageurl;
    }

    // --- Méthode __toString ---
    public function __toString(): string
    {
        // Fournit une représentation textuelle simple de l'objet
        return $this->page ?? 'SquelettePage #' . $this->id;
    }
}