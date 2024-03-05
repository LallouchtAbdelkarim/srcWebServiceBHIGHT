<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?TypeUtilisateur $id_type_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTypeUser(): ?TypeUtilisateur
    {
        return $this->id_type_user;
    }

    public function setIdTypeUser(?TypeUtilisateur $id_type_user): static
    {
        $this->id_type_user = $id_type_user;

        return $this;
    }
}
