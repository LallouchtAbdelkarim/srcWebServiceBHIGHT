<?php

namespace App\Entity;

use App\Repository\GarantieDebiteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GarantieDebiteurRepository::class)]
class GarantieDebiteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?debiteur $id_debiteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?garantie $id_garantie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDebiteur(): ?debiteur
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?debiteur $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getIdGarantie(): ?garantie
    {
        return $this->id_garantie;
    }

    public function setIdGarantie(?garantie $id_garantie): static
    {
        $this->id_garantie = $id_garantie;

        return $this;
    }
}
