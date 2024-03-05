<?php

namespace App\Entity;

use App\Repository\ImportTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportTypeRepository::class)]
class ImportType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tableBdd = null;

    #[ORM\Column(length: 255)]
    private ?string $champs = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelImport $id_model = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_col = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTableBdd(): ?string
    {
        return $this->tableBdd;
    }

    public function setTableBdd(string $tableBdd): static
    {
        $this->tableBdd = $tableBdd;

        return $this;
    }

    public function getChamps(): ?string
    {
        return $this->champs;
    }

    public function setChamps(string $champs): static
    {
        $this->champs = $champs;

        return $this;
    }

    public function getIdModel(): ?ModelImport
    {
        return $this->id_model;
    }

    public function setIdModel(?ModelImport $id_model): static
    {
        $this->id_model = $id_model;

        return $this;
    }

    public function getNomCol(): ?string
    {
        return $this->nom_col;
    }

    public function setNomCol(string $nom_col): static
    {
        $this->nom_col = $nom_col;

        return $this;
    }
}
