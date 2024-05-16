<?php

namespace App\Entity;

use App\Repository\ChampsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChampsRepository::class)]
class Champs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $colum_name = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    private ?string $table_name = null;

    #[ORM\Column(length: 255)]
    private ?string $form = null;
   
    #[ORM\Column(nullable: true)]
    private ?int $champs_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_details_model = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColumName(): ?string
    {
        return $this->colum_name;
    }

    public function setColumName(string $colum_name): self
    {
        $this->colum_name = $colum_name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
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

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function setForm(?string $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getChampsId(): ?int
    {
        return $this->champs_id;
    }

    public function setChampsId(?int $champs_id): static
    {
        $this->champs = $champs_id;

        return $this;
    }

    public function getIdDetailsModel(): ?int
    {
        return $this->id_details_model;
    }

    public function setIdDetailsModel(?int $id_details_model): static
    {
        $this->id_details_model = $id_details_model;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
}
