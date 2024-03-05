<?php

namespace App\Entity;

use App\Repository\DebiteurDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DebiteurDetailRepository::class)]
class DebiteurDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?dossier $id_dossier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Debiteur $id_debiteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeDebiteur $id_type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creance $id_creance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDossier(): ?dossier
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?dossier $id_dossier): static
    {
        $this->id_dossier = $id_dossier;

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

    public function getIdType(): ?TypeDebiteur
    {
        return $this->id_type;
    }

    public function setIdType(?TypeDebiteur $id_type): static
    {
        $this->id_type = $id_type;

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
}
