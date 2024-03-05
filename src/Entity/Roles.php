<?php

namespace App\Entity;

use App\Repository\RolesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
class Roles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?int $status = null;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    private ?ListesRoles $id_role = null;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private ?Profil $id_profil = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getIdRole(): ?ListesRoles
    {
        return $this->id_role;
    }

    public function setIdRole(?ListesRoles $id_role): self
    {
        $this->id_role = $id_role;

        return $this;
    }

    public function getIdProfil(): ?Profil
    {
        return $this->id_profil;
    }

    public function setIdProfil(?Profil $id_profil): self
    {
        $this->id_profil = $id_profil;

        return $this;
    }
}
