<?php

namespace App\Entity;

use App\Repository\TypeCreanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeCreanceRepository::class)]
class TypeCreance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeDonneur $id_type_donneur = null;

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

    public function getIdTypeDonneur(): ?TypeDonneur
    {
        return $this->id_type_donneur;
    }

    public function setIdTypeDonneur(?TypeDonneur $id_type_donneur): static
    {
        $this->id_type_donneur = $id_type_donneur;

        return $this;
    }
}

