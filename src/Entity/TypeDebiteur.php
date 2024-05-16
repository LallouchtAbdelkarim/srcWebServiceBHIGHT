<?php

namespace App\Entity;

use App\Repository\TypeDebiteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDebiteurRepository::class)]
class TypeDebiteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Debiteur $id_debiteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creance $id_creance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailsTypeDeb $id_type = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdCreance(): ?Creance
    {
        return $this->id_creance;
    }

    public function setIdCreance(?Creance $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getIdType(): ?DetailsTypeDeb
    {
        return $this->id_type;
    }

    public function setIdType(?DetailsTypeDeb $id_type): static
    {
        $this->id_type = $id_type;

        return $this;
    }

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(?int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
