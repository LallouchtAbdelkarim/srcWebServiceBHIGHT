<?php

namespace App\Entity;

use App\Repository\TaskAssignedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskAssignedRepository::class)]
class TaskAssigned
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $id_task = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTask(): ?Task
    {
        return $this->id_task;
    }

    public function setIdTask(?Task $id_task): static
    {
        $this->id_task = $id_task;

        return $this;
    }

    public function getIdUser(): ?Utilisateurs
    {
        return $this->id_user;
    }

    public function setIdUser(?Utilisateurs $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}
