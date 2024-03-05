<?php

namespace App\Entity\Customer;

use App\Repository\Customer\ChampsDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChampsDbiRepository::class)]
class ChampsDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_details_model = null;

    #[ORM\Column(length: 255)]
    private ?string $colum_name = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $id_champ = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetailsModel(): ?int
    {
        return $this->id_details_model;
    }

    public function setIdDetailsModel(int $id_details_model): static
    {
        $this->id_details_model = $id_details_model;

        return $this;
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

    public function getIdImport(): ?int
    {
        return $this->id_import;
    }

    public function setIdImport(int $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }

    public function getIdChamp(): ?int
    {
        return $this->id_champ;
    }

    public function setIdChamp(int $id_champ): static
    {
        $this->id_champ = $id_champ;

        return $this;
    }
}
