<?php

namespace App\Entity;

use App\Repository\EleveRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EleveRepository::class)
 * @UniqueEntity("mail")
 */
class Eleve extends Utilisateur implements \JsonSerializable
{
    const LUNETTE = [
        'Non' => 'Non',
        'Oui' => 'Oui'
    ];

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=100)
     */
    private $autrePrenoms;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $nomUsage;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateNaiss;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $telParent;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     * @Assert\Email( message = "L'email '{{ value }}' n'est pas un email valide." )
     */
    private $mailParent;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=100)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Length(max=5, min="5", minMessage="Le code postal doit contenir 5 chiffres")
     * @Assert\Type(
     *     type={"digit"},
     *     message="Le code postal ne doit contenir que des chiffres.")
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $paysNaiss;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $depNaiss;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $villeNaiss;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     * @Assert\Length(max=3, min="3")
     */
    private $lunette;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $statutSocial;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=100)
     */
    private $lycee;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lyceeAutre;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $nomSociete;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $metier;

    /**
     * @ORM\OneToMany(targetEntity=Piecesjointes::class, mappedBy="eleve")
     */
    private $piecesjointes;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="eleves")
     */
    private $agence;

    /**
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="eleve", orphanRemoval=true)
     */
    private $appointments;

    /**
     * @ORM\OneToMany(targetEntity=Disponibilite::class, mappedBy="eleve", orphanRemoval=true)
     */
    private $disponibilites;

    /**
     * @ORM\Column(type="float")
     */
    private $compteurHeure = 35;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $heurePrevue;

    /**
     * @ORM\ManyToOne(targetEntity=ShortList::class, inversedBy="eleves")
     */
    private $shortList;

    /**
     * @ORM\ManyToMany(targetEntity=Examen::class, inversedBy="eleves")
     */
    private $examens;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="eleves")
     */
    private $lieu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $examen_Reussi;

    /**
     * @ORM\OneToOne(targetEntity=Moniteur::class, inversedBy="eleve", cascade={"persist", "remove"})
     */
    private $soumis_par;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $contratSigne;

    /**
     * @ORM\OneToOne(targetEntity=EvalPre::class, cascade={"persist", "remove"})
     */
    private $evalPre;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $etatDossier;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $formulaireInscription;

    /**
     * @ORM\ManyToOne(targetEntity=Forfait::class, inversedBy="eleves")
     */
    private $forfait;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $neph;

    /**
     * @ORM\OneToMany(targetEntity=Panier::class, mappedBy="eleve")
     */
    private $paniers;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="eleve")
     */
    private $commandes;

    /**
     * @ORM\ManyToOne(targetEntity=PorteOuverte::class, inversedBy="eleve")
     */
    private $porteOuverte;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $commentairePJ;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPersonnePorteOuverte;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $porteOuverteAnnule;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $presentJourneeInfo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaireEPHOTO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaireCNI;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaireJUSTIFDOM;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaireATTESHEB;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaireJDC;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaireAUTREP;

    public function __construct()
    {
        $this->piecesjointes = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->disponibilites = new ArrayCollection();
        $this->examens = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    /**
     * @return Collection|Disponibilite[]
     */
    public function getDisponibilite(): Collection
    {
        return $this->disponibilite;
    }

    public function addDisponibilite(Disponibilite $disponibilite): self
    {
        if (!$this->disponibilite->contains($disponibilite)) {
            $this->disponibilite[] = $disponibilite;
            $disponibilite->setEleve($this);
        }

        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): self
    {
        if ($this->disponibilite->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getEleve() === $this) {
                $disponibilite->setEleve(null);
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
            $appointment->setEleve($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getEleve() === $this) {
                $appointment->setEleve(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Disponibilite[]
     */
    public function getDisponibilites(): Collection
    {
        return $this->disponibilites;
    }

    public function getDateNaiss(): ?\DateTimeInterface
    {
        return $this->dateNaiss;
    }

    public function setDateNaiss(\DateTimeInterface $dateNaiss): self
    {
        $this->dateNaiss = $dateNaiss;

        return $this;
    }

    public function getTelParent(): ?string
    {
        return $this->telParent;
    }

    public function setTelParent(?string $telParent): self
    {
        $this->telParent = $telParent;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getPaysNaiss(): ?string
    {
        return $this->paysNaiss;
    }

    public function setPaysNaiss(string $paysNaiss): self
    {
        $this->paysNaiss = $paysNaiss;

        return $this;
    }

    public function getDepNaiss(): ?string
    {
        return $this->depNaiss;
    }

    public function setDepNaiss(string $depNaiss): self
    {
        $this->depNaiss = $depNaiss;

        return $this;
    }

    public function getVilleNaiss(): ?string
    {
        return $this->villeNaiss;
    }

    public function setVilleNaiss(string $villeNaiss): self
    {
        $this->villeNaiss = $villeNaiss;

        return $this;
    }

    public function getLunette(): ?string
    {
        return $this->lunette;
    }

    public function setLunette(string $lunette): self
    {
        $this->lunette = $lunette;

        return $this;
    }

    public function getStatutSocial(): ?string
    {
        return $this->statutSocial;
    }

    public function setStatutSocial(string $statutSocial): self
    {
        $this->statutSocial = $statutSocial;

        return $this;
    }

    public function getLycee(): ?string
    {
        return $this->lycee;
    }

    public function setLycee(?string $lycee): self
    {
        $this->lycee = $lycee;

        return $this;
    }

    public function getNomSociete(): ?string
    {
        return $this->nomSociete;
    }

    public function setNomSociete(?string $nomSociete): self
    {
        $this->nomSociete = $nomSociete;

        return $this;
    }

    public function getMetier(): ?string
    {
        return $this->metier;
    }

    public function setMetier(?string $metier): self
    {
        $this->metier = $metier;

        return $this;
    }

    /**
     * @return Collection|Piecesjointes[]
     */
    public function getPiecesjointes(): Collection
    {
        return $this->piecesjointes;
    }

    public function addPiecesjointe(Piecesjointes $piecesjointe): self
    {
        if (!$this->piecesjointes->contains($piecesjointe)) {
            $this->piecesjointes[] = $piecesjointe;
            $piecesjointe->setEleve($this);
        }

        return $this;
    }

    public function removePiecesjointe(Piecesjointes $piecesjointe): self
    {
        if ($this->piecesjointes->removeElement($piecesjointe)) {
            // set the owning side to null (unless already changed)
            if ($piecesjointe->getEleve() === $this) {
                $piecesjointe->setEleve(null);
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

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getLyceeAutre(): ?string
    {
        return $this->lyceeAutre;
    }

    public function setLyceeAutre(?string $lyceeAutre): self
    {
        $this->lyceeAutre = $lyceeAutre;

        return $this;
    }

    public function getCompteurHeure(): ?float
    {
        return $this->compteurHeure;
    }

    public function setCompteurHeure(float $compteurHeure): self
    {
        $this->compteurHeure = $compteurHeure;

        return $this;
    }

    public function getHeurePrevue(): ?string
    {
        return $this->heurePrevue;
    }

    public function setHeurePrevue(?string $heurePrevue): self
    {
        $this->heurePrevue = $heurePrevue;

        return $this;
    }
    public function getHAPoser()
    {
        $start = new DateTime();
        $heurePrevue =  $this->heurePrevue;
        $hPose = 0;
        $rdvs = $this->appointments;
        foreach ($rdvs as $rdv) {
            $start = $rdv->getStart()->format('Y-m-d H:i:s');
            $end = $rdv->getEnd()->format('Y-m-d H:i:s');
            $start = strtotime($start);
            $end = strtotime($end);
            $hPose = abs($end - $start) / (60 * 60);
        }
        return $heurePrevue - $hPose;
    }

    public function getShortList(): ?ShortList
    {
        return $this->shortList;
    }

    public function setShortList(?ShortList $shortList): self
    {
        $this->shortList = $shortList;

        return $this;
    }

    public function getNomUsage(): ?string
    {
        return $this->nomUsage;
    }

    public function setNomUsage(?string $nomUsage): self
    {
        $this->nomUsage = $nomUsage;

        return $this;
    }
    public function getLieuExamen(): ?Lieu
    {
        return $this->lieuExamen;
    }

    public function setLieuExamen(?Lieu $lieuExamen): self
    {
        $this->lieuExamen = $lieuExamen;

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
        }

        return $this;
    }

    public function removeExamen(Examen $examen): self
    {
        $this->examens->removeElement($examen);

        return $this;
    }

    public function getAutrePrenoms(): ?string
    {
        return $this->autrePrenoms;
    }

    public function setAutrePrenoms(?string $autrePrenoms): self
    {
        $this->autrePrenoms = $autrePrenoms;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExamenReussi()
    {
        return $this->examen_Reussi;
    }

    /**
     * @param mixed $examen_Reussi
     */
    public function setExamenReussi($examen_Reussi): void
    {
        $this->examen_Reussi = $examen_Reussi;
    }

    public function getContratSigne(): ?string
    {
        return $this->contratSigne;
    }

    public function setContratSigne(?string $contratSigne): self
    {
        $this->contratSigne = $contratSigne;

        return $this;
    }

    public function getEvalPre(): ?EvalPre
    {
        return $this->evalPre;
    }

    public function setEvalPre(?EvalPre $evalPre): self
    {
        $this->evalPre = $evalPre;

        return $this;
    }

    public function getEtatDossier(): ?string
    {
        return $this->etatDossier;
    }

    public function setEtatDossier(?string $etatDossier): self
    {
        $this->etatDossier = $etatDossier;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getFormulaireInscription(): ?string
    {
        return $this->formulaireInscription;
    }

    public function setFormulaireInscription(?string $formulaireInscription): self
    {
        $this->formulaireInscription = $formulaireInscription;

        return $this;
    }

    public function getForfait(): ?Forfait
    {
        return $this->forfait;
    }

    public function setForfait(?Forfait $forfait): self
    {
        $this->forfait = $forfait;

        return $this;
    }

    public function getNeph(): ?string
    {
        return $this->neph;
    }

    public function setNeph(?string $neph): self
    {
        $this->neph = $neph;

        return $this;
    }

    /**
     * @return Collection|Panier[]
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
            $panier->setEleve($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getEleve() === $this) {
                $panier->setEleve(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setEleve($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getEleve() === $this) {
                $commande->setEleve(null);
            }
        }

        return $this;
    }

    public function getPorteOuverte(): ?PorteOuverte
    {
        return $this->porteOuverte;
    }

    public function setPorteOuverte(?PorteOuverte $porteOuverte): self
    {
        $this->porteOuverte = $porteOuverte;

        return $this;
    }

    public function getAge()
    {
        /*$dateNaiss = $this->getDateNaiss()->format('d/m/Y');
        $dateNaiss = explode("-" , $dateNaiss);
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $dateNaiss[0], $dateNaiss[1], $dateNaiss[2]))) > date("md")
            ? ((date("Y") - $dateNaiss[2]) - 1)
            : (date("Y") - $dateNaiss[2]));
        return $age;*/
        $dateOfBirth = $this->getDateNaiss()->format('Y-m-d');
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        return $diff->format('%y');
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'prenom' => $this->getPrenom(),
            'nom' => $this->getNom(),
            'mail' => $this->getMail(),
            'autrePrenoms' => $this->autrePrenoms,
            'nomUsage' => $this->nomUsage,
            'dateNaiss' => $this->dateNaiss,
            'telephone' => $this->getTelephone(),
            'telephoneParent' => $this->telParent,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'cp' => $this->cp,
            'paysNaiss' => $this->paysNaiss,
            'depNaiss' => $this->depNaiss,
            'villeNaiss' => $this->villeNaiss,
            'statutSocial' => $this->statutSocial,
            'lunette' => $this->lunette,
            'lycee' => $this->lycee,
            'lyceeAutre' => $this->lyceeAutre,
            'metier' => $this->metier,
            'nomSociete' => $this->nomSociete,
            'commentairePJ' => $this->commentairePJ
        );
    }

    public function getCommentairePJ(): ?string
    {
        return $this->commentairePJ;
    }

    public function setCommentairePJ(?string $commentairePJ): self
    {
        $this->commentairePJ = $commentairePJ;

        return $this;
    }

    public function getNbPersonnePorteOuverte(): ?string
    {
        return $this->nbPersonnePorteOuverte;
    }

    public function setNbPersonnePorteOuverte(?string $nbPersonnePorteOuverte): self
    {
        $this->nbPersonnePorteOuverte = $nbPersonnePorteOuverte;

        return $this;
    }

    public function getMailParent(): ?string
    {
        return $this->mailParent;
    }

    public function setMailParent(?string $mailParent): self
    {
        $this->mailParent = $mailParent;

        return $this;
    }

    public function getPorteOuverteAnnule(): ?string
    {
        return $this->porteOuverteAnnule;
    }

    public function setPorteOuverteAnnule(?string $porteOuverteAnnule): self
    {
        $this->porteOuverteAnnule = $porteOuverteAnnule;

        return $this;
    }

    public function getPresentJourneeInfo(): ?string
    {
        return $this->presentJourneeInfo;
    }

    public function setPresentJourneeInfo(?string $presentJourneeInfo): self
    {
        $this->presentJourneeInfo = $presentJourneeInfo;

        return $this;
    }

    public function getCommentaireEPHOTO(): ?string
    {
        return $this->commentaireEPHOTO;
    }

    public function setCommentaireEPHOTO(?string $commentaireEPHOTO): self
    {
        $this->commentaireEPHOTO = $commentaireEPHOTO;

        return $this;
    }

    public function getCommentaireCNI(): ?string
    {
        return $this->commentaireCNI;
    }

    public function setCommentaireCNI(?string $commentaireCNI): self
    {
        $this->commentaireCNI = $commentaireCNI;

        return $this;
    }

    public function getCommentaireJUSTIFDOM(): ?string
    {
        return $this->commentaireJUSTIFDOM;
    }

    public function setCommentaireJUSTIFDOM(?string $commentaireJUSTIFDOM): self
    {
        $this->commentaireJUSTIFDOM = $commentaireJUSTIFDOM;

        return $this;
    }

    public function getCommentaireATTESHEB(): ?string
    {
        return $this->commentaireATTESHEB;
    }

    public function setCommentaireATTESHEB(?string $commentaireATTESHEB): self
    {
        $this->commentaireATTESHEB = $commentaireATTESHEB;

        return $this;
    }

    public function getCommentaireJDC(): ?string
    {
        return $this->commentaireJDC;
    }

    public function setCommentaireJDC(?string $commentaireJDC): self
    {
        $this->commentaireJDC = $commentaireJDC;

        return $this;
    }

    public function getCommentaireAUTREP(): ?string
    {
        return $this->commentaireAUTREP;
    }

    public function setCommentaireAUTREP(?string $commentaireAUTREP): self
    {
        $this->commentaireAUTREP = $commentaireAUTREP;

        return $this;
    }
}
