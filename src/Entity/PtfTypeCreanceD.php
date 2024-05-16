<?php

namespace App\Entity;

use App\Repository\PtfTypeCreanceDRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PtfTypeCreanceDRepository::class)]
class PtfTypeCreanceD
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PorteFeuille $id_ptf = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailsTypeCreance $id_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPtf(): ?PorteFeuille
    {
        return $this->id_ptf;
    }

    public function setIdPtf(?PorteFeuille $id_ptf): static
    {
        $this->id_ptf = $id_ptf;

        return $this;
    }

    public function getIdType(): ?DetailsTypeCreance
    {
        return $this->id_type;
    }

    public function setIdType(?DetailsTypeCreance $id_type): static
    {
        $this->id_type = $id_type;

        return $this;
    }
}
