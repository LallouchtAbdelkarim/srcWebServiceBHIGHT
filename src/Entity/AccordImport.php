<?php

namespace App\Entity;

use App\Repository\AccordImportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccordImportRepository::class)]
class AccordImport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Accord $id_accord = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailsAccord $id_details_accord = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ImportPaiement $id_import = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CreanceAccord $id_creance_accord = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAccord(): ?Accord
    {
        return $this->id_accord;
    }

    public function setIdAccord(?Accord $id_accord): static
    {
        $this->id_accord = $id_accord;

        return $this;
    }

    public function getIdDetailsAccord(): ?DetailsAccord
    {
        return $this->id_details_accord;
    }

    public function setIdDetailsAccord(?DetailsAccord $id_details_accord): static
    {
        $this->id_details_accord = $id_details_accord;

        return $this;
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

    public function getIdCreanceAccord(): ?CreanceAccord
    {
        return $this->id_creance_accord;
    }

    public function setIdCreanceAccord(?CreanceAccord $id_creance_accord): static
    {
        $this->id_creance_accord = $id_creance_accord;

        return $this;
    }
}
