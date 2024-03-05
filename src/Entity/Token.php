<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token implements UserInterface
{
	
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 3000)]
    private $token;

    // #[ORM\Column(type: 'integer', length: 180, unique: true)]
    // private $codeclie;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $userIdent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
    // public function getCodeClie(): ?int
    // {
    //     return $this->codeclie;
    // }

    // public function setCodeClie(string $codeclie): self
    // {
    //     $this->codeclie = $codeclie;

    //     return $this;
    // }

   

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->token;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdent(): ?Utilisateurs
    {
        return $this->userIdent;
    }

    public function setUserIdent(?Utilisateurs $userIdent): static
    {
        $this->userIdent = $userIdent;

        return $this;
    }
}