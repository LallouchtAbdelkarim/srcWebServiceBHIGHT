<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
class Dossier
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

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    private ?Qualification $id_qualification = null;

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    private ?Portefeuille $id_ptf = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $id_users = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_dossier_dbi = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\ManyToOne]
    private ?StatusDossierAssign $id_status_assign = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $id_user_assign = null;

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

    public function getIdQualification(): ?Qualification
    {
        return $this->id_qualification;
    }

    public function setIdQualification(?Qualification $id_qualification): static
    {
        $this->id_qualification = $id_qualification;

        return $this;
    }

    public function getIdPtf(): ?Portefeuille
    {
        return $this->id_ptf;
    }

    public function setIdPtf(?Portefeuille $id_ptf): static
    {
        $this->id_ptf = $id_ptf;

        return $this;
    }

    public function getIdUsers(): ?Utilisateurs
    {
        return $this->id_users;
    }

    public function setIdUsers(?Utilisateurs $id_users): static
    {
        $this->id_users = $id_users;

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

    public function getIdDossierDbi(): ?int
    {
        return $this->id_dossier_dbi;
    }

    public function setIdDossierDbi(?int $id_dossier_dbi): static
    {
        $this->id_dossier_dbi = $id_dossier_dbi;

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

    public function getIdStatusAssign(): ?StatusDossierAssign
    {
        return $this->id_status_assign;
    }

    public function setIdStatusAssign(?StatusDossierAssign $id_status_assign): static
    {
        $this->id_status_assign = $id_status_assign;

        return $this;
    }

    public function getIdUserAssign(): ?Utilisateurs
    {
        return $this->id_user_assign;
    }

    public function setIdUserAssign(?Utilisateurs $id_user_assign): static
    {
        $this->id_user_assign = $id_user_assign;

        return $this;
    }
}
