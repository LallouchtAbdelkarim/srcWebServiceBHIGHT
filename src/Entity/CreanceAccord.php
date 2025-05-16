<?php

namespace App\Entity;

use App\Repository\CreanceAccordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreanceAccordRepository::class)]
class CreanceAccord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?accord $id_accord = null;

    #[ORM\ManyToOne]
    private ?Creance $id_creance = null;

    #[ORM\Column(nullable: true)]
    private ?float $montantAccord = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAccord(): ?accord
    {
        return $this->id_accord;
    }

    public function setIdAccord(?accord $id_accord): static
    {
        $this->id_accord = $id_accord;

        return $this;
    }

    public function getIdCreance(): ?Creance
    {
        return $this->id_creance;
    }

    public function setIdCreance(?Creance $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getMontantAccord(): ?float
    {
        return $this->montantAccord;
    }

    public function setMontantAccord(?float $montantAccord): static
    {
        $this->montantAccord = $montantAccord;

        return $this;
    }
}
