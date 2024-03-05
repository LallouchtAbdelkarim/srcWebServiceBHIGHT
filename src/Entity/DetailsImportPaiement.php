<?php

namespace App\Entity;

use App\Repository\DetailsImportPaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsImportPaiementRepository::class)]
class DetailsImportPaiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ImportPaiement $id_import = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusDetImpP $status = null;

    #[ORM\Column]
    private ?int $ordre = null;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getStatus(): ?StatusDetImpP
    {
        return $this->status;
    }

    public function setStatus(?StatusDetImpP $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }
}
