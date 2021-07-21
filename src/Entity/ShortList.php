<?php

namespace App\Entity;

use App\Repository\ShortListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShortListRepository::class)
 */
class ShortList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\OneToOne(targetEntity=Moniteur::class, cascade={"persist", "remove"})
     */
    private $moniteurs;

    /**
     * @ORM\OneToMany(targetEntity=Eleve::class, mappedBy="shortList")
     */
    private $eleves;




    public function __construct()
    {
        $this->eleves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getMoniteurs(): ?Moniteur
    {
        return $this->moniteurs;
    }

    public function setMoniteurs(?Moniteur $moniteurs): self
    {
        $this->moniteurs = $moniteurs;

        return $this;
    }

 

  

    /**
     * @return Collection|Lieu[]
     */
    public function getLieux(): Collection
    {
        return $this->lieux;
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
            $elefe->setShortList($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): self
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getShortList() === $this) {
                $elefe->setShortList(null);
            }
        }

        return $this;
    }

   

}
