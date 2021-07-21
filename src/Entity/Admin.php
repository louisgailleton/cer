<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin extends Utilisateur
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
    private $mdpSecondaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMdpSecondaire(): ?string
    {
        return $this->mdpSecondaire;
    }

    public function setMdpSecondaire(string $mdpSecondaire): self
    {
        $this->mdpSecondaire = $mdpSecondaire;

        return $this;
    }
}
