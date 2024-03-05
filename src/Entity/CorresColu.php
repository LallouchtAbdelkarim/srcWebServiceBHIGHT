<?php

namespace App\Entity;

use App\Repository\CorresColuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CorresColuRepository::class)]
class CorresColu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $table_name = null;

    #[ORM\Column(length: 50)]
    private ?string $column_name = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column]
    private ?bool $required = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelImport $id_model_import = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $column_table = null;

    #[ORM\ManyToOne(inversedBy: 'corresColus')]
    private ?ColumnsParams $id_col_params = null;

    #[ORM\Column]
    private ?int $origine = null;

    #[ORM\Column(nullable: true)]
    private ?int $origin_champ = null;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getIdModelImport(): ?ModelImport
    {
        return $this->id_model_import;
    }

    public function setIdModelImport(?ModelImport $id_model_import): self
    {
        $this->id_model_import = $id_model_import;

        return $this;
    }

    public function getColumnTable(): ?string
    {
        return $this->column_table;
    }

    public function setColumnTable(string $column_table): self
    {
        $this->column_table = $column_table;

        return $this;
    }

    public function getIdColParams(): ?ColumnsParams
    {
        return $this->id_col_params;
    }

    public function setIdColParams(?ColumnsParams $id_col_params): static
    {
        $this->id_col_params = $id_col_params;

        return $this;
    }

    public function getOrigine(): ?int
    {
        return $this->origine;
    }

    public function setOrigine(int $origine): static
    {
        $this->origine = $origine;

        return $this;
    }

    public function getOriginChamp(): ?int
    {
        return $this->origin_champ;
    }

    public function setOriginChamp(?int $origin_champ): static
    {
        $this->origin_champ = $origin_champ;

        return $this;
    }
}
