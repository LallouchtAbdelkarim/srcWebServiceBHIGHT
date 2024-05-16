<?php

namespace App\Entity\Customer;

use App\Repository\Customer\procCreanceDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: procCreanceDbiRepository::class)]
class procCreanceDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_creance = null;

    #[ORM\Column]
    private ?int $origin_creance = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $id_proc = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreance(): ?int
    {
        return $this->id_creance;
    }

    public function setIdCreance(int $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getOriginCreance(): ?int
    {
        return $this->origin_creance;
    }

    public function setOriginCreance(int $origin_creance): static
    {
        $this->origin_creance = $origin_creance;

        return $this;
    }

    public function getIdImport(): ?int
    {
        return $this->id_import;
    }

    public function setIdImport(int $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }

    public function getIdProc(): ?int
    {
        return $this->id_proc;
    }

    public function setIdProc(int $id_proc): static
    {
        $this->id_proc = $id_proc;

        return $this;
    }

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(?int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }
}
