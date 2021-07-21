<?php

namespace App\Entity;

use App\Repository\PiecesjointesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PiecesjointesRepository::class)
 */
class Piecesjointes implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $typePJ;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $nomFichier;

    /**
     * @ORM\ManyToOne(targetEntity=Eleve::class, inversedBy="piecesjointes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eleve;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $nomFichierUnique;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypePJ(): ?string
    {
        return $this->typePJ;
    }

    public function setTypePJ(string $typePJ): self
    {
        $this->typePJ = $typePJ;

        return $this;
    }

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(string $nomFichier): self
    {
        $this->nomFichier = $nomFichier;

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

    public function getNomFichierUnique(): ?string
    {
        return $this->nomFichierUnique;
    }

    public function setNomFichierUnique(string $nomFichierUnique): self
    {
        $this->nomFichierUnique = $nomFichierUnique;

        return $this;
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'idEleve' => $this->eleve->getId(),
            'typePJ' => $this->typePJ,
            'nomFichier' => $this->nomFichier,
            'nomFichierUnique' => $this->nomFichierUnique,
            'etat' => $this->etat,
            'commentaire' => $this->commentaire
        );
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
