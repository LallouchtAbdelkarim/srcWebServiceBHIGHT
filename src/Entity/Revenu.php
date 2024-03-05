<?php

namespace App\Entity;

use App\Repository\RevenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevenuRepository::class)]
class Revenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $revenu = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Debiteur $id_debiteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeRevenu $id_type_revenu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRevenu(): ?string
    {
        return $this->revenu;
    }

    public function setRevenu(string $revenu): static
    {
        $this->revenu = $revenu;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getIdDebiteur(): ?Debiteur
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?Debiteur $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getIdTypeRevenu(): ?TypeRevenu
    {
        return $this->id_type_revenu;
    }

    public function setIdTypeRevenu(?TypeRevenu $id_type_revenu): static
    {
        $this->id_type_revenu = $id_type_revenu;

        return $this;
    }
}
