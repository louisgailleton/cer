<?php

namespace App\Entity;

use App\Repository\EvalPreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvalPreRepository::class)
 */
class EvalPre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $permis = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $expConduite;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $ou = [];

    /**
     * @ORM\OneToOne(targetEntity=Eleve::class, cascade={"persist", "remove"})
     */
    private $eleve;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $scoreCode;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $scoreConduite;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateEvaluation;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $nbHeureTheorique;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $nbHeurePratique;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPermis(): ?array
    {
        return $this->permis;
    }

    public function setPermis(?array $permis): self
    {
        $this->permis = $permis;

        return $this;
    }

    public function getExpConduite(): ?bool
    {
        return $this->expConduite;
    }

    public function setExpConduite(bool $expConduite): self
    {
        $this->expConduite = $expConduite;

        return $this;
    }

    public function getOu(): ?array
    {
        return $this->ou;
    }

    public function setOu(?array $ou): self
    {
        $this->ou = $ou;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): self
    {
        $this->eleve = $eleve;

        return $this;
    }

    public function getScoreCode(): ?string
    {
        return $this->scoreCode;
    }

    public function setScoreCode(?string $scoreCode): self
    {
        $this->scoreCode = $scoreCode;

        return $this;
    }

    public function getScoreConduite(): ?string
    {
        return $this->scoreConduite;
    }

    public function setScoreConduite(?string $scoreConduite): self
    {
        $this->scoreConduite = $scoreConduite;

        return $this;
    }

    public function getDateEvaluation(): ?\DateTimeInterface
    {
        return $this->dateEvaluation;
    }

    public function setDateEvaluation(?\DateTimeInterface $dateEvaluation): self
    {
        $this->dateEvaluation = $dateEvaluation;

        return $this;
    }

    public function getNbHeureTheorique(): ?string
    {
        return $this->nbHeureTheorique;
    }

    public function setNbHeureTheorique(?string $nbHeureTheorique): self
    {
        $this->nbHeureTheorique = $nbHeureTheorique;

        return $this;
    }

    public function getNbHeurePratique(): ?string
    {
        return $this->nbHeurePratique;
    }

    public function setNbHeurePratique(?string $nbHeurePratique): self
    {
        $this->nbHeurePratique = $nbHeurePratique;

        return $this;
    }
}
