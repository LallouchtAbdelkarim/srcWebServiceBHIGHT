<?php

namespace App\Entity;

use App\Repository\DebiDossRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DebiDossRepository::class)]
class DebiDoss
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
    private ?Dossier $id_dossier = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

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
    public function getIdDossier(): ?Dossier
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?Dossier $id_dossier): static
    {
        $this->id_dossier = $id_dossier;

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

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(?int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }
}
