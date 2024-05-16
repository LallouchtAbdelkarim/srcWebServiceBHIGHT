<?php

namespace App\Entity\Customer;

use App\Repository\Customer\GarantieCreanceDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GarantieCreanceDbiRepository::class)]
class GarantieCreanceDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_garantie = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $id_creance = null;

    #[ORM\Column]
    private ?int $origin_deb = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGarantie(): ?int
    {
        return $this->id_garantie;
    }

    public function setOriginGarantie(int $origin_garantie): static
    {
        $this->origin_garantie = $origin_garantie;

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

    public function getIdCreance(): ?int
    {
        return $this->id_creance;
    }

    public function setIdCreance(int $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getOriginDeb(): ?int
    {
        return $this->origin_deb;
    }

    public function setOriginDeb(int $origin_deb): static
    {
        $this->origin_deb = $origin_deb;

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
