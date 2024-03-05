<?php

namespace App\Entity;

use App\Repository\ActionsImportPaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionsImportPaiementRepository::class)]
class ActionsImportPaiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ImportPaiement $id_import = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdImport(): ?ImportPaiement
    {
        return $this->id_import;
    }

    public function setIdImport(?ImportPaiement $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }
}
