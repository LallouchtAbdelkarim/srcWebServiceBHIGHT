<?php

namespace App\Entity;

use App\Repository\RegleModelFacturationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegleModelFacturationRepository::class)]
class RegleModelFacturation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelFacturation $id_model = null;

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

    public function getIdModel(): ?ModelFacturation
    {
        return $this->id_model;
    }

    public function setIdModel(?ModelFacturation $id_model): self
    {
        $this->id_model = $id_model;

        return $this;
    }
}
