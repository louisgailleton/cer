<?php

namespace App\Entity;

use App\Repository\ForfaitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ForfaitRepository::class)
 */
class Forfait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $libelleforfait;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $prix;

    /**
     * @ORM\ManyToMany(targetEntity=Agence::class, inversedBy="forfaits")
     */
    private $agence;

    /**
     * @ORM\OneToMany(targetEntity=Eleve::class, mappedBy="forfait")
     */
    private $eleves;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contenuForfait;

    public function __construct()
    {
        $this->agence = new ArrayCollection();
        $this->contenuForfaits = new ArrayCollection();
        $this->eleves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleforfait(): ?string
    {
        return $this->libelleforfait;
    }

    public function setLibelleforfait(string $libelleforfait): self
    {
        $this->libelleforfait = $libelleforfait;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->libelleforfait;
    }

    /**
     * @return Collection|Agence[]
     */
    public function getAgence(): Collection
    {
        return $this->agence;
    }

    public function addAgence(Agence $agence): self
    {
        if (!$this->agence->contains($agence)) {
            $this->agence[] = $agence;
        }

        return $this;
    }

    public function removeAgence(Agence $agence): self
    {
        $this->agence->removeElement($agence);

        return $this;
    }

    public function affichageInfos() {
        return $this->libelleforfait . " - " . $this->prix . "€";
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
            $elefe->setForfait($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): self
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getForfait() === $this) {
                $elefe->setForfait(null);
            }
        }

        return $this;
    }

    public function getContenuForfait(): ?string
    {
        return $this->contenuForfait;
    }

    public function setContenuForfait(?string $contenuForfait): self
    {
        $this->contenuForfait = $contenuForfait;

        return $this;
    }

    public function getListeContenu(){
        if (!$this->getContenuForfait()) {
            return null;
        }
        $liste = "<ul>";
        $listeContenu = explode("\n", $this->getContenuForfait());
        foreach ($listeContenu as $contenu){
            $liste .= "<li>" . $contenu . "</li>";
        }
        $liste .= "</ul>";
        return $liste;
    }
}
