<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DonneurOrdre $id_donneur_ordre_id = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroFact = null;

    #[ORM\Column(length: 255)]
    private ?string $yearFact = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    // #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    // private ?\DateTimeInterface $date_echeance = null;

    // #[ORM\Column]
    // private ?int $type = null;

    #[ORM\Column(nullable: true)]
    private ?float $total_ttc = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypePaiement $id_type_paiement = null;

    #[ORM\ManyToOne]
    private ?StatusFacture $id_status = null;

    #[ORM\ManyToOne]
    private ?RegleModelFacturation $id_model = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Creance $id_creance = null;

    // #[ORM\Column(nullable: true)]
    // private ?float $total_ttc_initial_creance = null;

    // #[ORM\Column(nullable: true)]
    // private ?float $total_ttc_restant_creance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDonneurOrdreId(): ?DonneurOrdre
    {
        return $this->id_donneur_ordre_id;
    }

    public function setIdDonneurOrdreId(?DonneurOrdre $id_donneur_ordre_id): static
    {
        $this->id_donneur_ordre_id = $id_donneur_ordre_id;

        return $this;
    }

    public function getNumeroFact(): ?string
    {
        return $this->numeroFact;
    }

    public function setNumeroFact(string $numeroFact): static
    {
        $this->numeroFact = $numeroFact;

        return $this;
    }

    public function getYearFact(): ?string
    {
        return $this->yearFact;
    }

    public function setYearFact(string $yearFact): static
    {
        $this->yearFact = $yearFact;

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

    // public function getDateEcheance(): ?\DateTimeInterface
    // {
    //     return $this->date_echeance;
    // }

    // public function setDateEcheance(?\DateTimeInterface $date_echeance): static
    // {
    //     $this->date_echeance = $date_echeance;

    //     return $this;
    // }

    // public function getType(): ?int
    // {
    //     return $this->type;
    // }

    // public function setType(int $type): static
    // {
    //     $this->type = $type;

    //     return $this;
    // }

    public function getTotalTtc(): ?float
    {
        return $this->total_ttc;
    }

    public function setTotaTtc(?float $total_ttc): static
    {
        $this->$total_ttc = $total_ttc;

        return $this;
    }

    // public function getTotalTtcInitialCreance(): ?float
    // {
    //     return $this->total_ttc_initial_creance;
    // }

    // public function setTotalTtcInitialCreance(?float $total_ttc_initial_creance): static
    // {
    //     $this->total_ttc_initial_creance = $total_ttc_initial_creance;

    //     return $this;
    // }

    // public function getTotalTtcRestantCreance(): ?float
    // {
    //     return $this->total_ttc_restant_creance;
    // }

    // public function setTotalTtcRestantCreance(?float $total_ttc_restant_creance): static
    // {
    //     $this->total_ttc_restant_creance = $total_ttc_restant_creance;

    //     return $this;
    // }

    public function getIdTypePaiement(): ?TypePaiement
    {
        return $this->id_type_paiement;
    }

    public function setIdTypePaiement(?TypePaiement $id_type_paiement): static
    {
        $this->id_type_paiement = $id_type_paiement;

        return $this;
    }

    public function getIdStatus(): ?StatusFacture
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusFacture $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }
    public function getIdModel(): ?RegleModelFacturation
    {
        return $this->id_model;
    }

    public function setIdModel(?RegleModelFacturation $id_model): static
    {
        $this->id_model = $id_model;

        return $this;
    }

    public function getIdCreance(): ?Creance
    {
        return $this->id_creance;
    }

    public function setIdCreance(?Creance $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }
}
