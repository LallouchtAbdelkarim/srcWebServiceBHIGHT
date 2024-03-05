<?php

namespace App\Entity\Customer;

use App\Repository\Customer\accordImportDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: accordImportDbiRepository::class)]
class accordImportDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_accord = null;

    #[ORM\Column]
    private ?int $id_details_accord = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $id_creance_accord = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAccord(): ?int
    {
        return $this->id_accord;
    }

    public function setIdAccord(int $id_accord): static
    {
        $this->id_accord = $id_accord;

        return $this;
    }

    public function getIdDetailsAccord(): ?int
    {
        return $this->id_details_accord;
    }

    public function setIdDetailsAccord(int $id_details_accord): static
    {
        $this->id_details_accord = $id_details_accord;

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

    public function getIdCreanceAccord(): ?int
    {
        return $this->id_creance_accord;
    }

    public function setIdCreanceAccord(int $id_creance_accord): static
    {
        $this->id_creance_accord = $id_creance_accord;

        return $this;
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
}
