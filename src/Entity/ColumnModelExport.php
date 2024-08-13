<?php

namespace App\Entity;

use App\Repository\ColumnModelExportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ColumnModelExportRepository::class)]
class ColumnModelExport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?ModelExport $id_model = null;

    #[ORM\Column(length: 255)]
    private ?string $column_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $entity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdModel(): ?ModelExport
    {
        return $this->id_model;
    }

    public function setIdModel(?ModelExport $id_model): static
    {
        $this->id_model = $id_model;

        return $this;
    }

    public function getColumnName(): ?string
    {
        return $this->column_name;
    }

    public function setColumnName(string $column_name): static
    {
        $this->column_name = $column_name;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): static
    {
        $this->entity = $entity;

        return $this;
    }
}
