<?php

namespace App\Entity;

use App\Repository\StatutSocialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatutSocialRepository::class)
 */
class StatutSocial
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=20)
     */
    private $libelleStatutSocial;

    public function getLibelleStatutSocial(): ?string
    {
        return $this->libelleStatutSocial;
    }

    public function setLibelleStatutSocial(string $libelleStatutSocial): self
    {
        $this->libelleStatutSocial = $libelleStatutSocial;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->libelleStatutSocial;
    }
}
