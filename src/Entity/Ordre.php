<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity
 *
 * #[ORM\Table(name="ordreclient")]
 * #[ORM\Entity](repositoryClass="App\Entity\OrdreRepository")
 *
 */
class Ordre {

    function __construct() {
        //$this->etat = 0;
    }

    /**
     * @var integer $id
     * #[ORM\Id]
     * #[ORM\Column(name="idordre", type="integer")]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $nomTable
     * #[ORM\Column(name="nomtable",type="string",length=100)]
     * #[Assert\NotBlank(message=" Le libellé de la table ne peut être vide ")]
     * @Assert\MinLength(2)
     */
    private $nomTable;

    /**
     * @var string
     * #[ORM\Column(name="ordre",type="text")]
     */
    private $ordre;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Set nomTable
     *
     * @param string $nomTable
     * @return Ordre
     */
    public function setNomTable(string $nomTable): self {
        $this->nomTable = $nomTable;

        return $this;
    }

    /**
     * Get nomTable
     *
     * @return string 
     */
    public function getNomTable(): ?string {
        return $this->nomTable;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return Ordre
     */
    public function setOrdre(string $ordre): self {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre(): ?string {
        return $this->ordre;
    }

}
