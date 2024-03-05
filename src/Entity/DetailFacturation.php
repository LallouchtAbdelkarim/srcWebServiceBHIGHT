<?php

namespace App\Entity;

use App\Repository\DetailFacturationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailFacturationRepository::class)]
class DetailFacturation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $id_facture = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?RegleModelFacturation $id_regle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFacture(): ?Facture
    {
        return $this->id_facture;
    }

    public function setIdFacture(?Facture $id_facture): static
    {
        $this->id_facture = $id_facture;

        return $this;
    }

    public function getIdRegle(): ?RegleModelFacturation
    {
        return $this->id_regle;
    }

    public function setIdRegle(?RegleModelFacturation $id_regle): static
    {
        $this->id_regle = $id_regle;

        return $this;
    }
}
