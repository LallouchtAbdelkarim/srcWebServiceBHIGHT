<?php

namespace App\Entity;

use App\Repository\ProcDebiteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProcDebiteurRepository::class)]
class ProcDebiteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProcJudicaire $id_proc_judicaire = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Debiteur $id_debiteur = null;
    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProcJudicaire(): ?ProcJudicaire
    {
        return $this->id_proc_judicaire;
    }

    public function setIdProcJudicaire(?ProcJudicaire $id_proc_judicaire): static
    {
        $this->id_proc_judicaire = $id_proc_judicaire;

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
