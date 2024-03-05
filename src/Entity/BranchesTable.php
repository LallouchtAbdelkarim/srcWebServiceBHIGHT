<?php

namespace App\Entity;

use App\Repository\BranchesTableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BranchesTableRepository::class)]
class BranchesTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelImport $id_model_import = null;

    #[ORM\Column(length: 255)]
    private ?string $name_branche = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNameBranche(): ?string
    {
        return $this->name_branche;
    }

    public function setNameBranche(string $name_branche): self
    {
        $this->name_branche = $name_branche;

        return $this;
    }
}
