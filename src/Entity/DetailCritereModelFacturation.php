<?php

namespace App\Entity;

use App\Repository\DetailCritereModelFacturationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailCritereModelFacturationRepository::class)]
class DetailCritereModelFacturation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $table_name = null;

    #[ORM\Column(length: 255)]
    private ?string $column_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeur1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeur2 = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CritereModelFacturation $idCritere = null;

    #[ORM\Column(length: 255)]
    private ?string $type_column = null;

    #[ORM\Column(length: 255)]
    private ?string $type_detail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $operator = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTableName(): ?string
    {
        return $this->table_name;
    }

    public function setTableName(string $table_name): self
    {
        $this->table_name = $table_name;

        return $this;
    }

    public function getColumnName(): ?string
    {
        return $this->column_name;
    }

    public function setColumnName(string $column_name): self
    {
        $this->column_name = $column_name;

        return $this;
    }

    public function getValeur1(): ?string
    {
        return $this->valeur1;
    }

    public function setValeur1(?string $valeur1): self
    {
        $this->valeur1 = $valeur1;

        return $this;
    }

    public function getValeur2(): ?string
    {
        return $this->valeur2;
    }

    public function setValeur2(?string $valeur2): self
    {
        $this->valeur2 = $valeur2;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getIdCritere(): ?CritereModelFacturation
    {
        return $this->idCritere;
    }

    public function setIdCritere(?CritereModelFacturation $idCritere): self
    {
        $this->idCritere = $idCritere;

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

    public function getTypeDetail(): ?string
    {
        return $this->type_detail;
    }

    public function setTypeDetail(string $type_detail): static
    {
        $this->type_detail = $type_detail;

        return $this;
    }

    public function getOperator(): ?string
    {
        return $this->operator;
    }

    public function setOperator(?string $operator): static
    {
        $this->operator = $operator;

        return $this;
    }
}
