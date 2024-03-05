<?php

namespace App\Entity;

use App\Repository\GarantieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GarantieRepository::class)]
class Garantie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $type_garantie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $taux = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_garantie_dbi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeGarantie(): ?string
    {
        return $this->type_garantie;
    }

    public function setTypeGarantie(?string $type_garantie): static
    {
        $this->type_garantie = $type_garantie;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->taux;
    }

    public function setTaux(?float $taux): static
    {
        $this->taux = $taux;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getIdGarantieDbi(): ?int
    {
        return $this->id_garantie_dbi;
    }

    public function setIdGarantieDbi(?int $id_garantie_dbi): static
    {
        $this->id_garantie_dbi = $id_garantie_dbi;

        return $this;
    }
}
