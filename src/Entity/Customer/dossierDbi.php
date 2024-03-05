<?php

namespace App\Entity\Customer;

use App\Repository\Customer\dossierDbiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: dossierDbiRepository::class)]
class dossierDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_dossier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?string $date_ouverture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin_prevesionnel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column]
    private ?int $id_users = null;

    #[ORM\Column]
    private ?int $id_ptf = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $id_qualification = null;

    #[ORM\Column]
    private ?int $id_integration = null;

    #[ORM\Column]
    private ?int $id_debiteur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDossier(): ?string
    {
        return $this->numero_dossier;
    }

    public function setNumeroDossier(string $numero_dossier): static
    {
        $this->numero_dossier = $numero_dossier;

        return $this; 
    }

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->date_ouverture;
    }

    public function setDateOuverture(?\DateTimeInterface $date_ouverture): static
    {
        $this->date_ouverture = $date_ouverture;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateFinPrevesionnel(): ?\DateTimeInterface
    {
        return $this->date_fin_prevesionnel;
    }

    public function setDateFinPrevesionnel(?\DateTimeInterface $date_fin_prevesionnel): static
    {
        $this->date_fin_prevesionnel = $date_fin_prevesionnel;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_users;
    }

    public function setIdUser(int $id_users): static
    {
        $this->id_users = $id_users;

        return $this;
    }
    public function getIdPtf(): ?int
    {
        return $this->id_ptf;
    }

    public function setIdPtf(int $id_ptf): static
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

    public function getIdImport(): ?int
    {
        return $this->id_import;
    }

    public function setIdImport(int $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }

    public function getIdQualification(): ?int
    {
        return $this->id_qualification;
    }

    public function setIdQualification(int $id_qualification): static
    {
        $this->id_qualification = $id_qualification;

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
}
