<?php

namespace App\Entity;

use App\Repository\MissionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissionsRepository::class)]
class Missions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailsFile $id_details_file = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusMissions $id_status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $id_users = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetailsFile(): ?DetailsFile
    {
        return $this->id_details_file;
    }

    public function setIdDetailsFile(?DetailsFile $id_details_file): static
    {
        $this->id_details_file = $id_details_file;

        return $this;
    }

    public function getIdStatus(): ?StatusMissions
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusMissions $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }

    public function getIdUsers(): ?Utilisateurs
    {
        return $this->id_users;
    }

    public function setIdUsers(?Utilisateurs $id_users): static
    {
        $this->id_users = $id_users;

        return $this;
    }
}
