<?php

namespace App\Entity\Customer;

use App\Repository\Customer\ActionImportPaiementDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionImportPaiementDbiRepository::class)]
class ActionImportPaiementDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_import = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
