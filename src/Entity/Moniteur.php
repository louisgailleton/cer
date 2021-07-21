<?php

namespace App\Entity;

use App\Repository\MoniteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MoniteurRepository::class)
 */
class Moniteur extends Utilisateur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Lieu::class, mappedBy="moniteur", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $lieus;



    /**
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="moniteur", orphanRemoval=true)
     */
    private $appointments;

    /**
     * @ORM\OneToMany(targetEntity=Indisponibilite::class, mappedBy="moniteur", orphanRemoval=true)
     */
    private $indisponibilites;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity=IndispoType::class, mappedBy="moniteur", orphanRemoval=true)
     */
    private $indispoTypes;

    /**
     * @ORM\OneToMany(targetEntity=PlaceExamen::class, mappedBy="moniteur")
     */
    private $placeExamens;

    /**
     * @ORM\OneToMany(targetEntity=Examen::class, mappedBy="moniteur")
     */
    private $examens;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="moniteurs")
     */
    private $agence;

    /**
     * @ORM\OneToOne(targetEntity=Eleve::class, mappedBy="soumis_par", cascade={"persist", "remove"})
     */
    private $eleve;



 





    public function __construct()
    {
        $this->lieus = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->indisponibilites = new ArrayCollection();
        $this->indispoTypes = new ArrayCollection();
        $this->placeExamens = new ArrayCollection();
        $this->examens = new ArrayCollection();
    }




    // public function getRoles()
    // {
    //     return ['ROLE_MONITEUR'];
    // }

    public function getSalt()
    {
        return null;
    }




    /**
     * @return Collection|Lieu[]
     */
    public function getLieus(): Collection
    {
        return $this->lieus;
    }

    public function addLieu(Lieu $lieu): self
    {
        if (!$this->lieus->contains($lieu)) {
            $this->lieus[] = $lieu;
            $lieu->setMoniteur($this);
        }

        return $this;
    }

    public function removeLieu(Lieu $lieu): self
    {
        if ($this->lieus->removeElement($lieu)) {
            // set the owning side to null (unless already changed)
            if ($lieu->getMoniteur() === $this) {
                $lieu->setMoniteur(null);
            }
        }

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
            $appointment->setMoniteur($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getMoniteur() === $this) {
                $appointment->setMoniteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Indisponibilite[]
     */
    public function getIndisponibilites(): Collection
    {
        return $this->indisponibilites;
    }

    public function addIndisponibilite(Indisponibilite $indisponibilite): self
    {
        if (!$this->indisponibilites->contains($indisponibilite)) {
            $this->indisponibilites[] = $indisponibilite;
            $indisponibilite->setMoniteur($this);
        }

        return $this;
    }

    public function removeIndisponibilite(Indisponibilite $indisponibilite): self
    {
        if ($this->indisponibilites->removeElement($indisponibilite)) {
            // set the owning side to null (unless already changed)
            if ($indisponibilite->getMoniteur() === $this) {
                $indisponibilite->setMoniteur(null);
            }
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }


    /**
     * @return Collection|IndispoType[]
     */
    public function getIndispoTypes(): Collection
    {
        return $this->indispoTypes;
    }

    public function addIndispoType(IndispoType $indispoType): self
    {
        if (!$this->indispoTypes->contains($indispoType)) {
            $this->indispoTypes[] = $indispoType;
            $indispoType->setMoniteur($this);
        }

        return $this;
    }

    public function removeIndispoType(IndispoType $indispoType): self
    {
        if ($this->indispoTypes->removeElement($indispoType)) {
            // set the owning side to null (unless already changed)
            if ($indispoType->getMoniteur() === $this) {
                $indispoType->setMoniteur(null);
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
            $placeExamen->setMoniteur($this);
        }

        return $this;
    }

    public function removePlaceExamen(PlaceExamen $placeExamen): self
    {
        if ($this->placeExamens->removeElement($placeExamen)) {
            // set the owning side to null (unless already changed)
            if ($placeExamen->getMoniteur() === $this) {
                $placeExamen->setMoniteur(null);
            }
        }

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
            $examen->setMoniteur($this);
        }

        return $this;
    }

    public function removeExamen(Examen $examen): self
    {
        if ($this->examens->removeElement($examen)) {
            // set the owning side to null (unless already changed)
            if ($examen->getMoniteur() === $this) {
                $examen->setMoniteur(null);
            }
        }

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): self
    {
        // unset the owning side of the relation if necessary
        if ($eleve === null && $this->eleve !== null) {
            $this->eleve->setSoumisPar(null);
        }

        // set the owning side of the relation if necessary
        if ($eleve !== null && $eleve->getSoumisPar() !== $this) {
            $eleve->setSoumisPar($this);
        }

        $this->eleve = $eleve;

        return $this;
    }



}
