<?php

namespace App\Entity\Customer;

use App\Repository\Customer\detailCreanceDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: detailCreanceDbiRepository::class)]
class detailCreanceDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $principale = null;

    #[ORM\Column]
    private ?float $frais = null;

    #[ORM\Column]
    private ?float $interet = null;

    #[ORM\Column]
    private ?int $id_creance = null;

    #[ORM\Column]
    private ?int $origin_creance = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrincipale(): ?float
    {
        return $this->principale;
    }

    public function setPrincipale(float $principale): static
    {
        $this->principale = $principale;

        return $this;
    }

    public function getFrais(): ?float
    {
        return $this->frais;
    }

    public function setFrais(float $frais): static
    {
        $this->frais = $frais;

        return $this;
    }

    public function getInteret(): ?float
    {
        return $this->interet;
    }

    public function setInteret(float $interet): static
    {
        $this->interet = $interet;

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

    public function getOriginCreance(): ?int
    {
        return $this->origin_creance;
    }

    public function setOriginCreance(int $origin_creance): static
    {
        $this->origin_creance = $origin_creance;

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
