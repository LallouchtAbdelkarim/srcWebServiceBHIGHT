<?php

namespace App\Entity;

use App\Repository\CreanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreanceRepository::class)]
class Creance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_creance = null;


    #[ORM\ManyToOne]
    private ?Dossier $id_dossier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_echeance = null;

    #[ORM\Column]
    private ?float $total_creance = null;

    #[ORM\ManyToOne]
    private ?Portefeuille $id_ptf = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column]
    private ?float $totalRestant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE )]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column]
    private ?int $id_creance_dbi = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $id_users_id = null;

    #[ORM\ManyToOne]
    private ?DetailsTypeCreance $id_type_creance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nature_creance = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_creance = null;

    #[ORM\ManyToOne]
    private ?ParamActivite $id_activite = null;

    #[ORM\Column(nullable: true)]
    private ?int $taux_honoraire = null;

    #[ORM\Column(nullable: true)]
    private ?float $honoraire_petentiel = null;

    #[ORM\Column(nullable: true)]
    private ?float $honoraire_facture = null;

    #[ORM\Column(nullable: true)]
    private ?float $honoraire_restant = null;

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

    public function getIdDossier(): ?Dossier
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?Dossier $id_dossier): static
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

    public function getIdPtf(): ?Portefeuille
    {
        return $this->id_ptf;
    }

    public function setIdPtf(?Portefeuille $id_ptf): static
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

    public function getIdUsersId(): ?Utilisateurs
    {
        return $this->id_users_id;
    }

    public function setIdUsersId(?Utilisateurs $id_users_id): static
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

    public function getIdActivite(): ?ParamActivite
    {
        return $this->id_activite;
    }

    public function setIdActivite(?ParamActivite $id_activite): static
    {
        $this->id_activite = $id_activite;

        return $this;
    }

    public function getTauxHonoraire(): ?int
    {
        return $this->taux_honoraire;
    }

    public function setTauxHonoraire(?int $taux_honoraire): static
    {
        $this->taux_honoraire = $taux_honoraire;

        return $this;
    }

    public function getHonorairePetentiel(): ?float
    {
        return $this->honoraire_petentiel;
    }

    public function setHonorairePetentiel(?float $honoraire_petentiel): static
    {
        $this->honoraire_petentiel = $honoraire_petentiel;

        return $this;
    }

    public function getHonoraireFacture(): ?float
    {
        return $this->honoraire_facture;
    }

    public function setHonoraireFacture(?float $honoraire_facture): static
    {
        $this->honoraire_facture = $honoraire_facture;

        return $this;
    }

    public function getHonoraireRestant(): ?float
    {
        return $this->honoraire_restant;
    }

    public function setHonoraireRestant(?float $honoraire_restant): static
    {
        $this->honoraire_restant = $honoraire_restant;

        return $this;
    }
}
