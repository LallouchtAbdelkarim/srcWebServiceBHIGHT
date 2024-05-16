<?php

namespace App\Entity\Customer;

use App\Repository\Customer\debiDossDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: debiDossDbiRepository::class)]
class debiDossDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_dossier_id = null;

    #[ORM\Column]
    private ?int $id_debiteur_id = null;

    #[ORM\Column]
    private ?int $origin_deb = null;

    #[ORM\Column]
    private ?int $origin_doss = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDossierId(): ?int
    {
        return $this->id_dossier_id;
    }

    public function setIdDossierId(int $id_dossier_id): static
    {
        $this->id_dossier_id = $id_dossier_id;

        return $this;
    }

    public function getIdDebiteurId(): ?int
    {
        return $this->id_debiteur_id;
    }

    public function setIdDebiteurId(int $id_debiteur_id): static
    {
        $this->id_debiteur_id = $id_debiteur_id;

        return $this;
    }

    public function getOriginDeb(): ?int
    {
        return $this->origin_deb;
    }

    public function setOriginDeb(int $origin_deb): static
    {
        $this->origin_deb = $origin_deb;

        return $this;
    }

    public function getOriginDoss(): ?int
    {
        return $this->origin_doss;
    }

    public function setOriginDoss(int $origin_doss): static
    {
        $this->origin_doss = $origin_doss;

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
