<?php

namespace App\Entity;

use App\Repository\GarantieCreanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GarantieCreanceRepository::class)]
class GarantieCreance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?creance $id_creance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Garantie $id_garantie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreance(): ?creance
    {
        return $this->id_creance;
    }

    public function setIdCreance(?creance $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getGarantie(): ?Garantie
    {
        return $this->id_garantie;
    }

    public function setGarantie(?Garantie $id_garantie): static
    {
        $this->id_garantie = $id_garantie;

        return $this;
    }
}
