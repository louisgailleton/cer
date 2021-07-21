<?php

namespace App\Entity;

use App\Repository\PlaceExamenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaceExamenRepository::class)
 */
class PlaceExamen
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPlaceAttribuee;

    /**
     * @ORM\ManyToOne(targetEntity=Moniteur::class, inversedBy="placeExamens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $moniteur;



    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPlaceDemande;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPlaceUtilise;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="placeExamens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNbPlaceAttribuee(): ?int
    {
        return $this->nbPlaceAttribuee;
    }

    public function setNbPlaceAttribuee(?int $nbPlaceAttribuee): self
    {
        $this->nbPlaceAttribuee = $nbPlaceAttribuee;

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

    public function getNbPlaceDemande(): ?int
    {
        return $this->nbPlaceDemande;
    }

    public function setNbPlaceDemande(?int $nbPlaceDemande): self
    {
        $this->nbPlaceDemande = $nbPlaceDemande;

        return $this;
    }

    public function getNbPlaceUtilise(): ?int
    {
        return $this->nbPlaceUtilise;
    }

    public function setNbPlaceUtilise(?int $nbPlaceUtilise): self
    {
        $this->nbPlaceUtilise = $nbPlaceUtilise;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }
}
