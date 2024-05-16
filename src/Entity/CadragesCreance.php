<?php

namespace App\Entity;

use App\Repository\CadragesCreanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CadragesCreanceRepository::class)]
class CadragesCreance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Cadrages $id_cadrage = null;

    #[ORM\ManyToOne]
    private ?Creance $id_creance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCadrage(): ?Cadrages
    {
        return $this->id_cadrage;
    }

    public function setIdCadrage(?Cadrages $id_cadrage): static
    {
        $this->id_cadrage = $id_cadrage;

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
}
