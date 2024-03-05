<?php

namespace App\Entity\Customer;

use App\Repository\Customer\typeDebiteurDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: typeDebiteurDbiRepository::class)]
class typeDebiteurDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_creance_id = null;

    #[ORM\Column]
    private ?int $id_debiteur_id = null;

    #[ORM\Column]
    private ?int $origin_deb = null;

    #[ORM\Column]
    private ?int $origin_creance = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $id_integration = null;

    #[ORM\Column]
    private ?int $id_debiteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero_dossier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreanceId(): ?int
    {
        return $this->id_creance_id;
    }

    public function setIdCreanceId(int $id_creance_id): static
    {
        $this->id_creance_id = $id_creance_id;

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

    public function getOriginCreance(): ?int
    {
        return $this->origin_creance;
    }

    public function setOriginCreance(int $origin_creance): static
    {
        $this->origin_creance = $origin_creance;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }

    public function getIdDebiteur(): ?int
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(int $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getNumeroDossier(): ?string
    {
        return $this->numero_dossier;
    }

    public function setNumeroDossier(?string $numero_dossier): static
    {
        $this->numero_dossier = $numero_dossier;

        return $this;
    }
}
