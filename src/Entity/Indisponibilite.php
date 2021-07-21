<?php

namespace App\Entity;

use App\Repository\IndisponibiliteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IndisponibiliteRepository::class)
 */
class Indisponibilite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;


    /**
     * @ORM\ManyToOne(targetEntity=Moniteur::class, inversedBy="indisponibilites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $moniteur;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $preExam;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }


    public function getMoniteur(): ?Moniteur
    {
        return $this->moniteur;
    }

    public function setMoniteur(?Moniteur $moniteur): self
    {
        $this->moniteur = $moniteur;

        return $this;
    }

    public function getPreExam(): ?bool
    {
        return $this->preExam;
    }

    public function setPreExam(?bool $preExam): self
    {
        $this->preExam = $preExam;

        return $this;
    }


}
