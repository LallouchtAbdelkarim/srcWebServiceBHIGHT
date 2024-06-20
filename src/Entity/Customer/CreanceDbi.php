<?php

namespace App\Entity\Customer;

use App\Repository\Customer\CreanceDbiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreanceDbiRepository::class)]
class CreanceDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_creance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_echeance = null;

    #[ORM\Column]
    private ?float $total_creance = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(length: 255)]
    private ?string $type_creance = null;

    #[ORM\Column]
    private ?float $totalRestant = null;

    #[ORM\Column]
    private ?int $id_ptf_id = null;

    #[ORM\Column]
    private ?int $id_dossier = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $origine_doss = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nature_creance = null;

    #[ORM\Column]
    private ?int $id_integration = null;

    #[ORM\Column]
    private ?int $id_debiteur = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_creance_import = null;

    #[ORM\Column(nullable: true)]
    private ?int $isExist = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_creance_exist = null;
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
    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTypeCreance(): ?string
    {
        return $this->type_creance;
    }

    public function setTypeCreance(string $type_creance): static
    {
        $this->type_creance = $type_creance;

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

    public function getIdPtfId(): ?int
    {
        return $this->id_ptf_id;
    }

    public function setIdPtfId(int $id_ptf_id): static
    {
        $this->id_ptf_id = $id_ptf_id;

        return $this;
    }

    public function getIdDossier(): ?int
    {
        return $this->id_dossier;
    }

    public function setIdDossier(int $id_dossier): static
    {
        $this->id_dossier = $id_dossier;

        return $this;
    }

    public function getIdImport(): ?int
    {
        return $this->id_import;
    }

    public function setIdImport(int $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }

    public function getOrigineDoss(): ?int
    {
        return $this->origine_doss;
    }

    public function setOrigineDoss(int $origine_doss): static
    {
        $this->origine_doss = $origine_doss;

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

    public function getIdCreanceImport(): ?int
    {
        return $this->id_creance_import;
    }

    public function setIdCreanceImport(?int $id_creance_import): static
    {
        $this->id_creance_import = $id_creance_import;

        return $this;
    }

    public function getIsExist(): ?int
    {
        return $this->isExist;
    }

    public function setIsExist(?int $isExist): static
    {
        $this->isExist = $isExist;

        return $this;
    }

    public function getIdCreanceExist(): ?int
    {
        return $this->id_creance_exist;
    }

    public function setIdCreanceExist(?int $id_creance_exist): static
    {
        $this->id_creance_exist = $id_creance_exist;

        return $this;
    }
}
