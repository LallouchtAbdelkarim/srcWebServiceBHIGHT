<?php

namespace App\Entity;

use App\Repository\ProcCreanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProcCreanceRepository::class)]
class ProcCreance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProcJudicaire $id_proc = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creance $id_creance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProc(): ?ProcJudicaire
    {
        return $this->id_proc;
    }

    public function setIdProc(?ProcJudicaire $id_proc): static
    {
        $this->id_proc = $id_proc;

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
