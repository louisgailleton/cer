<?php

namespace App\Entity;

use App\Repository\GerantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GerantRepository::class)
 */
class Gerant extends Utilisateur
{

    /**
     * @ORM\OneToMany(targetEntity=Agence::class, mappedBy="gerant")
     */
    private $agences;

    public function __construct()
    {
        $this->agences = new ArrayCollection();
    }

    /**
     * @return Collection|Agence[]
     */
    public function getAgences(): Collection
    {
        return $this->agences;
    }

    public function addAgence(Agence $agence): self
    {
        if (!$this->agences->contains($agence)) {
            $this->agences[] = $agence;
            $agence->setGerant($this);
        }

        return $this;
    }

    public function removeAgence(Agence $agence): self
    {
        if ($this->agences->removeElement($agence)) {
            // set the owning side to null (unless already changed)
            if ($agence->getGerant() === $this) {
                $agence->setGerant(null);
            }
        }

        return $this;
    }

}
