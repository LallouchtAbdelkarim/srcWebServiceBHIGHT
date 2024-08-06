<?php

namespace App\Entity;

use App\Repository\ActiviteAssignedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActiviteAssignedRepository::class)]
class ActiviteAssigned
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?CreanceActivite $id_creance_activite = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreanceActivite(): ?CreanceActivite
    {
        return $this->id_creance_activite;
    }

    public function setIdCreanceActivite(?CreanceActivite $id_creance_activite): static
    {
        $this->id_creance_activite = $id_creance_activite;

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
