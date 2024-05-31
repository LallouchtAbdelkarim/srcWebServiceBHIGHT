<?php

namespace App\Entity;

use App\Repository\DetailCreanceArchiveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailCreanceArchiveRepository::class)]
class DetailCreanceArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_creance = null;

    #[ORM\Column(length: 255)]
    private ?string $interet = null;

    #[ORM\Column]
    private ?float $principale = null;

    #[ORM\Column(nullable: true)]
    private ?float $frais = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreance(): ?int
    {
        return $this->id_creance;
    }

    public function setIdCreance(?int $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getInteret(): ?string
    {
        return $this->interet;
    }

    public function setInteret(string $interet): static
    {
        $this->interet = $interet;

        return $this;
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

    public function setFrais(?float $frais): static
    {
        $this->frais = $frais;

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

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }


}
