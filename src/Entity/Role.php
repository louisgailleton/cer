<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
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
    private $code;

    /**
     * @ORM\OneToMany(targetEntity=Utilisateur::class, mappedBy="role")
     */
    private $lesUtilisateurs;

    public function __construct()
    {
        $this->lesUtilisateurs = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getLesUtilisateurs(): Collection
    {
        return $this->lesUtilisateurs;
    }

    public function addLesUtilisateur(Utilisateur $lesUtilisateur): self
    {
        if (!$this->lesUtilisateurs->contains($lesUtilisateur)) {
            $this->lesUtilisateurs[] = $lesUtilisateur;
            $lesUtilisateur->setRole($this);
        }

        return $this;
    }

    public function removeLesUtilisateur(Utilisateur $lesUtilisateur): self
    {
        if ($this->lesUtilisateurs->removeElement($lesUtilisateur)) {
            // set the owning side to null (unless already changed)
            if ($lesUtilisateur->getRole() === $this) {
                $lesUtilisateur->setRole(null);
            }
        }

        return $this;
    }
}
