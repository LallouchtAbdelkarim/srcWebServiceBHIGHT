<?php

namespace App\Entity;

use App\Repository\CreanceArchiveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreanceArchiveRepository::class)]
class CreanceArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_creance = null;


    #[ORM\Column]
    private ?int $id_dossier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_echeance = null;

    #[ORM\Column]
    private ?float $total_creance = null;

    #[ORM\Column]
    private ?int $id_ptf = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column]
    private ?float $totalRestant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE )]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column]
    private ?int $id_creance_dbi = null;

    #[ORM\Column]
    private ?int $id_users_id = null;

    #[ORM\Column]
    private ?int $id_type_creance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nature_creance = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_creance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCreance(): ?string
    {
        return $this->numero_creance;
    }

    public function setNumeroCreance(string $numero_creance): self
    {
        $this->numero_creance = $numero_creance;

        return $this;
    }

    public function getIdDossier(): ?int
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?int $id_dossier): static
    {
        $this->id_dossier = $id_dossier;

        return $this;
    }


    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(?\DateTimeInterface $date_echeance): static
    {
        $this->date_echeance = $date_echeance;

        return $this;
    }

    public function getTotalCreance(): ?float
    {
        return $this->total_creance;
    }

    public function setTotalCreance(float $total_creance): static
    {
        $this->total_creance = $total_creance;

        return $this;
    }

    public function getIdPtf(): ?int
    {
        return $this->id_ptf;
    }

    public function setIdPtf(?int $id_ptf): static
    {
        $this->id_ptf = $id_ptf;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTotalRestant(): ?float
    {
        return $this->totalRestant;
    }

    public function setTotalRestant(float $totalRestant): static
    {
        $this->totalRestant = $totalRestant;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getIdCreanceDbi(): ?int
    {
        return $this->id_creance_dbi;
    }

    public function setIdCreanceDbi(int $id_creance_dbi): static
    {
        $this->id_creance_dbi = $id_creance_dbi;

        return $this;
    }

    public function getIdUsersId(): ?int
    {
        return $this->id_users_id;
    }

    public function setIdUsersId(?int $id_users_id): static
    {
        $this->id_users_id = $id_users_id;

        return $this;
    }

    public function getIdTypeCreance(): ?DetailsTypeCreance
    {
        return $this->id_type_creance;
    }

    public function setIdTypeCreance(?DetailsTypeCreance $id_type_creance): static
    {
        $this->id_type_creance = $id_type_creance;

        return $this;
    }

    public function getNatureCreance(): ?string
    {
        return $this->nature_creance;
    }

    public function setNatureCreance(?string $nature_creance): static
    {
        $this->nature_creance = $nature_creance;

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

    public function getIdCreance(): ?int
    {
        return $this->id_creance;
    }

    public function setIdCreance(?int $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }
}
