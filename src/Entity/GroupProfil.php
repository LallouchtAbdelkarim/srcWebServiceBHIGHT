<?php

namespace App\Entity;

use App\Repository\GroupProfilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupProfilRepository::class)]
class GroupProfil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'groupProfils')]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private ?Groupe $id_group = null;

    #[ORM\ManyToOne(inversedBy: 'groupProfils')]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private ?Profil $id_profil = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGroup(): ?Groupe
    {
        return $this->id_group;
    }

    public function setIdGroup(?Groupe $id_group): self
    {
        $this->id_group = $id_group;

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
