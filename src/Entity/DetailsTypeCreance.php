<?php

namespace App\Entity;

use App\Repository\DetailsTypeCreanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsTypeCreanceRepository::class)]
class DetailsTypeCreance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCreance $id_type_creance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getIdTypeCreance(): ?TypeCreance
    {
        return $this->id_type_creance;
    }

    public function setIdTypeCreance(?TypeCreance $id_type_creance): static
    {
        $this->id_type_creance = $id_type_creance;

        return $this;
    }
}
