<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Squelettepage
 *
 * #[ORM\Table(name="squelettepage")]
 * #[ORM\Entity](repositoryClass="App\Entity\SquelettepageRepository")
 */
class Squelettepage
{
    /**
     * @var integer
     *
     * #[ORM\Column(name="id", type="integer")]
     * #[ORM\Id]
     * #[ORM\GeneratedValue](strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * #[ORM\Column(name="page", type="string", length=255)]
     */
    private $page;

    /**
     * @var string
     *
     * #[ORM\Column(name="pageurl", type="string", length=255)]
     */
    private $pageurl;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId(): ?string
    {
        return $this->id;
    }

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
     * @return string 
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
     * @return string 
     */
    public function getPageurl(): ?string
    {
        return $this->pageurl;
    }
}
