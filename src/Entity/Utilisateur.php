<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity("mail")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "utilisateur" = "Utilisateur",
 *     "eleve" = "Eleve",
 *     "secretaire" = "Secretaire",
 *     "gerant" = "Gerant",
 *     "moniteur" = "Moniteur",
 *     "admin" = "Admin",
 * })
 */
class Utilisateur implements UserInterface,\Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=50)
     * @Assert\NotBlank()
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=50)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(max=100)
     * @Assert\NotBlank()
     * @Assert\Email( message = "L'email '{{ value }}' n'est pas un email valide." )
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Length(
     *     max=10,
     *     min="10",
     *     minMessage="Le numéro de téléphone doit contenir 10 chiffres",
     *     maxMessage="Le numéro de téléphone doit contenir 10 chiffres")
     * @Assert\Type(type={"digit"},message="Le numéro de téléphone ne doit contenir que des chiffres.")
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     max=255,
     *     min="8",
     *     minMessage="Le mot de passe doit être supérieur ou égal à 8 caractères",
     *     maxMessage="Le mot de passe doit être inférieur ou égal à 50 caractères")
     * @Assert\NotBlank()
     */
    private $mdp;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="lesUtilisateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;


    public function __construct() { }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getRole()
    {
        return $this->role->getCode();
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }
    
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->mail,
            $this->mdp
        ]);
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->mail,
            $this->mdp
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getRoles(): array
    {
        $var = $this->role->getCode();

        return array($var); // We need to return an array
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials() { }

    public function getUsername(): ?string
    {
        return $this->getMail();
    }

    public function getPassword(): ?string
    {
        return $this->getMdp();
    }
}
