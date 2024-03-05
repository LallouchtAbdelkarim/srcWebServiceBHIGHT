<?php

namespace App\Entity\Customer;

use App\Repository\Customer\emploiDbiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: emploiDbiRepository::class)]
class emploiDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDernierSalaire = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $id_status_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_empl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $salaire = null;

    #[ORM\Column(length: 255)]
    private ?string $profession_id = null;

    #[ORM\Column]
    private ?int $id_debiteur = null;

    #[ORM\Column]
    private ?int $origin_deb = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $id_integration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDateDernierSalaire(): ?\DateTimeInterface
    {
        return $this->dateDernierSalaire;
    }

    public function setDateDernierSalaire(?\DateTimeInterface $dateDernierSalaire): static
    {
        $this->dateDernierSalaire = $dateDernierSalaire;

        return $this;
    }

    public function getIdStatus(): ?string
    {
        return $this->id_status_id;
    }

    public function setIdStatus(?string $id_status_id): static
    {
        $this->id_status_id = $id_status_id;

        return $this;
    }

    public function getNomEmpl(): ?string
    {
        return $this->nom_empl;
    }

    public function setNomEmpl(?string $nom_empl): static
    {
        $this->nom_empl = $nom_empl;

        return $this;
    }

    public function getSalaire(): ?string
    {
        return $this->salaire;
    }

    public function setSalaire(?string $salaire): static
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession_id;
    }

    public function setProfession(string $profession_id): static
    {
        $this->profession_id = $profession_id;

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
    public function getOrigineDeb(): ?int
    {
        return $this->origin_deb;
    }

    public function setOrigineDeb(int $origin_deb): static
    {
        $this->origin_deb = $origin_deb;

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

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }
}
