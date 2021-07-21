<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\ManyToOne(targetEntity=Moniteur::class, inversedBy="lieus")
     * @ORM\JoinColumn(nullable=true)
     */
    private $moniteur;

    /**
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="lieu")
     */
    private $appointments;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rue;

    /**
     * @ORM\OneToMany(targetEntity=Examen::class, mappedBy="lieu")
     */
    private $examens;

    /**
     * @ORM\Column(type="boolean")
     */
    private $examen;

    /**
     * @ORM\OneToMany(targetEntity=Eleve::class, mappedBy="lieu")
     */
    private $eleves;

    /**
     * @ORM\OneToMany(targetEntity=PlaceExamen::class, mappedBy="lieu")
     */
    private $placeExamens;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->examens = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        $this->placeExamens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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

    /**
     * @return Collection|Appointment[]
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments[] = $appointment;
            $appointment->setLieu($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getLieu() === $this) {
                $appointment->setLieu(null);
            }
        }

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    /**
     * @return Collection|Examen[]
     */
    public function getExamens(): Collection
    {
        return $this->examens;
    }

    public function addExamen(Examen $examen): self
    {
        if (!$this->examens->contains($examen)) {
            $this->examens[] = $examen;
            $examen->setLieu($this);
        }

        return $this;
    }

    public function removeExamen(Examen $examen): self
    {
        if ($this->examens->removeElement($examen)) {
            // set the owning side to null (unless already changed)
            if ($examen->getLieu() === $this) {
                $examen->setLieu(null);
            }
        }

        return $this;
    }

    public function getExamen()
    {
        return $this->examen;
    }

    public function setExamen($examen): self
    {
        $this->examen = $examen;

        return $this;
    }

    /**
     * @return Collection|Eleve[]
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): self
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves[] = $elefe;
            $elefe->setLieu($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): self
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getLieu() === $this) {
                $elefe->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlaceExamen[]
     */
    public function getPlaceExamens(): Collection
    {
        return $this->placeExamens;
    }

    public function addPlaceExamen(PlaceExamen $placeExamen): self
    {
        if (!$this->placeExamens->contains($placeExamen)) {
            $this->placeExamens[] = $placeExamen;
            $placeExamen->setLieu($this);
        }

        return $this;
    }

    public function removePlaceExamen(PlaceExamen $placeExamen): self
    {
        if ($this->placeExamens->removeElement($placeExamen)) {
            // set the owning side to null (unless already changed)
            if ($placeExamen->getLieu() === $this) {
                $placeExamen->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->libelle;
    }
}
