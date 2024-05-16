<?php

namespace App\Entity;

use App\Repository\ReglePortefeuilleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReglePortefeuilleRepository::class)]
class ReglePortefeuille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $type_column = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $action = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value2 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Portefeuille $idPtf = null;

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

    public function getTypeColumn(): ?string
    {
        return $this->type_column;
    }

    public function setTypeColumn(string $type_column): static
    {
        $this->type_column = $type_column;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getValue1(): ?string
    {
        return $this->value1;
    }

    public function setValue1(?string $value1): static
    {
        $this->value1 = $value1;

        return $this;
    }

    public function getValue2(): ?string
    {
        return $this->value2;
    }

    public function setValue2(?string $value2): static
    {
        $this->value2 = $value2;

        return $this;
    }

    public function getIdPtf(): ?Portefeuille
    {
        return $this->idPtf;
    }

    public function setIdPtf(?Portefeuille $idPtf): static
    {
        $this->idPtf = $idPtf;

        return $this;
    }
}
