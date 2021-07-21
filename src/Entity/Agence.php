<?php

namespace App\Entity;

use App\Repository\AgenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 */
class Agence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nomAgence;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $adresseAgence;

    /**
     * @ORM\ManyToMany(targetEntity=Forfait::class, mappedBy="agence")
     */
    private $forfaits;

    /**
     * @ORM\OneToMany(targetEntity=Eleve::class, mappedBy="agence")
     */
    private $eleves;

    /**
     * @ORM\ManyToOne(targetEntity=Gerant::class, inversedBy="agences")
     */
    private $gerant;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $demande;

    /**
     * @ORM\OneToMany(targetEntity=Moniteur::class, mappedBy="agence")
     */
    private $moniteurs;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $villeAgence;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $telephoneAgence;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     */
    private $SIRET;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $numAgrement;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $numDeclarActivite;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nomAssurance;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $numPoliceAssurance;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nomFdGarantie;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $numFdGarantie;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montantFdGarantie;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nomInterloc;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $prenomInterloc;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $telInterloc;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mailInterloc;

    /**
     * @ORM\OneToMany(targetEntity=Secretaire::class, mappedBy="agence")
     */
    private $secretaires;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu_agence;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_agence;


    public function __construct()
    {
        $this->forfaits = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        $this->moniteurs = new ArrayCollection();
        $this->contenuForfaits = new ArrayCollection();
        $this->secretaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {   
        $this->id = $id;

        return $this;
    }

    public function getNomAgence(): ?string
    {
        return $this->nomAgence;
    }

    public function setNomAgence(string $nomAgence): self
    {
        $this->nomAgence = $nomAgence;

        return $this;
    }

    public function getAdresseAgence(): ?string
    {
        return $this->adresseAgence;
    }

    public function setAdresseAgence(string $adresseAgence): self
    {
        $this->adresseAgence = $adresseAgence;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->nomAgence;
    }

    /**
     * @return Collection|Forfait[]
     */
    public function getForfaits(): Collection
    {
        return $this->forfaits;
    }

    public function addForfait(Forfait $forfait): self
    {
        if (!$this->forfaits->contains($forfait)) {
            $this->forfaits[] = $forfait;
            $forfait->addAgence($this);
        }

        return $this;
    }

    public function removeForfait(Forfait $forfait): self
    {
        if ($this->forfaits->removeElement($forfait)) {
            $forfait->removeAgence($this);
        }

        return $this;
    }

    /**
     * @return Collection|Eleve[]
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addEleve(Eleve $eleve): self
    {
        if (!$this->eleves->contains($eleve)) {
            $this->eleves[] = $eleve;
            $eleve->setAgence($this);
        }

        return $this;
    }

    public function removeEleve(Eleve $eleve): self
    {
        if ($this->eleves->removeElement($eleve)) {
            // set the owning side to null (unless already changed)
            if ($eleve->getAgence() === $this) {
                $eleve->setAgence(null);
            }
        }

        return $this;
    }

    public function getGerant(): ?Gerant
    {
        return $this->gerant;
    }

    public function setGerant(?Gerant $gerant): self
    {
        $this->gerant = $gerant;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDemande(): ?string
    {
        return $this->demande;
    }

    public function setDemande(?string $demande): self
    {
        $this->demande = $demande;

        return $this;
    }

    /**
     * @return Collection|Moniteur[]
     */
    public function getMoniteurs(): Collection
    {
        return $this->moniteurs;
    }

    public function addMoniteur(Moniteur $moniteur): self
    {
        if (!$this->moniteurs->contains($moniteur)) {
            $this->moniteurs[] = $moniteur;
            $moniteur->setAgence($this);
        }

        return $this;
    }

    public function removeMoniteur(Moniteur $moniteur): self
    {
        if ($this->moniteurs->removeElement($moniteur)) {
            // set the owning side to null (unless already changed)
            if ($moniteur->getAgence() === $this) {
                $moniteur->setAgence(null);
            }
        }

        return $this;
    }

    public function getVilleAgence(): ?string
    {
        return $this->villeAgence;
    }

    public function setVilleAgence(?string $villeAgence): self
    {
        $this->villeAgence = $villeAgence;

        return $this;
    }

    public function getTelephoneAgence(): ?string
    {
        return $this->telephoneAgence;
    }

    public function setTelephoneAgence(?string $telephoneAgence): self
    {
        $this->telephoneAgence = $telephoneAgence;

        return $this;
    }

    public function getSIRET(): ?string
    {
        return $this->SIRET;
    }

    public function setSIRET(?string $SIRET): self
    {
        $this->SIRET = $SIRET;

        return $this;
    }

    public function getNumAgrement(): ?string
    {
        return $this->numAgrement;
    }

    public function setNumAgrement(?string $numAgrement): self
    {
        $this->numAgrement = $numAgrement;

        return $this;
    }

    public function getNumDeclarActivite(): ?string
    {
        return $this->numDeclarActivite;
    }

    public function setNumDeclarActivite(?string $numDeclarActivite): self
    {
        $this->numDeclarActivite = $numDeclarActivite;

        return $this;
    }

    public function getNomAssurance(): ?string
    {
        return $this->nomAssurance;
    }

    public function setNomAssurance(?string $nomAssurance): self
    {
        $this->nomAssurance = $nomAssurance;

        return $this;
    }

    public function getNumPoliceAssurance(): ?string
    {
        return $this->numPoliceAssurance;
    }

    public function setNumPoliceAssurance(?string $numPoliceAssurance): self
    {
        $this->numPoliceAssurance = $numPoliceAssurance;

        return $this;
    }

    public function getNomFdGarantie(): ?string
    {
        return $this->nomFdGarantie;
    }

    public function setNomFdGarantie(?string $nomFdGarantie): self
    {
        $this->nomFdGarantie = $nomFdGarantie;

        return $this;
    }

    public function getNumFdGarantie(): ?string
    {
        return $this->numFdGarantie;
    }

    public function setNumFdGarantie(?string $numFdGarantie): self
    {
        $this->numFdGarantie = $numFdGarantie;

        return $this;
    }

    public function getMontantFdGarantie(): ?float
    {
        return $this->montantFdGarantie;
    }

    public function setMontantFdGarantie(?float $montantFdGarantie): self
    {
        $this->montantFdGarantie = $montantFdGarantie;

        return $this;
    }

    public function getNomInterloc(): ?string
    {
        return $this->nomInterloc;
    }

    public function setNomInterloc(?string $nomInterloc): self
    {
        $this->nomInterloc = $nomInterloc;

        return $this;
    }

    public function getPrenomInterloc(): ?string
    {
        return $this->prenomInterloc;
    }

    public function setPrenomInterloc(?string $prenomInterloc): self
    {
        $this->prenomInterloc = $prenomInterloc;

        return $this;
    }

    public function getTelInterloc(): ?string
    {
        return $this->telInterloc;
    }

    public function setTelInterloc(?string $telInterloc): self
    {
        $this->telInterloc = $telInterloc;

        return $this;
    }

    public function getMailInterloc(): ?string
    {
        return $this->mailInterloc;
    }

    public function setMailInterloc(?string $mailInterloc): self
    {
        $this->mailInterloc = $mailInterloc;

        return $this;
    }

    /**
     * @return Collection|Secretaire[]
     */
    public function getSecretaires(): Collection
    {
        return $this->secretaires;
    }

    public function addSecretaire(Secretaire $secretaire): self
    {
        if (!$this->secretaires->contains($secretaire)) {
            $this->secretaires[] = $secretaire;
            $secretaire->setAgence($this);
        }

        return $this;
    }

    public function removeSecretaire(Secretaire $secretaire): self
    {
        if ($this->secretaires->removeElement($secretaire)) {
            // set the owning side to null (unless already changed)
            if ($secretaire->getAgence() === $this) {
                $secretaire->setAgence(null);
            }
        }

        return $this;
    }

    public function getLieuAgence(): ?string
    {
        return $this->lieu_agence;
    }

    public function setLieuAgence(?string $lieu_agence): self
    {
        $this->lieu_agence = $lieu_agence;

        return $this;
    }

    public function getImageAgence(): ?string
    {
        return $this->image_agence;
    }

    public function setImageAgence(string $image_agence): self
    {
        $this->image_agence = $image_agence;

        return $this;
    }

}
