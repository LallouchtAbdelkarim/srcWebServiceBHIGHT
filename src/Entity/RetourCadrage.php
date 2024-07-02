<?php

namespace App\Entity;

use App\Repository\RetourCadrageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RetourCadrageRepository::class)]
class RetourCadrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?HistoriqueDemandeCadrage $id_demande = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbrDebiteurs = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDemande(): ?HistoriqueDemandeCadrage
    {
        return $this->id_demande;
    }

    public function setIdDemande(?HistoriqueDemandeCadrage $id_demande): static
    {
        $this->id_demande = $id_demande;

        return $this;
    }

    public function getNbrDebiteurs(): ?int
    {
        return $this->nbrDebiteurs;
    }

    public function setNbrDebiteurs(?int $nbrDebiteurs): static
    {
        $this->nbrDebiteurs = $nbrDebiteurs;

        return $this;
    }
}
