<?php

namespace App\Entity;

use App\Repository\DetailFinancementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailFinancementRepository::class)]
class DetailFinancement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_dernier_echeance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_cloture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_aff_precontencieux = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_aff_amiable = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_transfer_ctx = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type_contrat = null;

    #[ORM\Column(length: 255)]
    private ?string $montant_echeance = null;

    #[ORM\Column(length: 255)]
    private ?string $capital_restant = null;

    #[ORM\Column(length: 255)]
    private ?string $montant_nominal = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre_impayes = null;

    #[ORM\Column(length: 255)]
    private ?string $solde_comptable = null;

    #[ORM\Column(length: 255)]
    private ?string $decaissement_depart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_arret_agios = null;

    #[ORM\Column(length: 255)]
    private ?string $ech_2 = null;

    #[ORM\Column(length: 255)]
    private ?string $ech_1 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?creance $id_creance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDernierEcheance(): ?\DateTimeInterface
    {
        return $this->date_dernier_echeance;
    }

    public function setDateDernierEcheance(\DateTimeInterface $date_dernier_echeance): static
    {
        $this->date_dernier_echeance = $date_dernier_echeance;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->date_cloture;
    }

    public function setDateCloture(?\DateTimeInterface $date_cloture): static
    {
        $this->date_cloture = $date_cloture;

        return $this;
    }

    public function getDateAffPrecontencieux(): ?\DateTimeInterface
    {
        return $this->date_aff_precontencieux;
    }

    public function setDateAffPrecontencieux(\DateTimeInterface $date_aff_precontencieux): static
    {
        $this->date_aff_precontencieux = $date_aff_precontencieux;

        return $this;
    }

    public function getDateAffAmiable(): ?\DateTimeInterface
    {
        return $this->date_aff_amiable;
    }

    public function setDateAffAmiable(\DateTimeInterface $date_aff_amiable): static
    {
        $this->date_aff_amiable = $date_aff_amiable;

        return $this;
    }

    public function getDateTransferCtx(): ?\DateTimeInterface
    {
        return $this->date_transfer_ctx;
    }

    public function setDateTransferCtx(\DateTimeInterface $date_transfer_ctx): static
    {
        $this->date_transfer_ctx = $date_transfer_ctx;

        return $this;
    }

    public function getTypeContrat(): ?string
    {
        return $this->type_contrat;
    }

    public function setTypeContrat(?string $type_contrat): static
    {
        $this->type_contrat = $type_contrat;

        return $this;
    }

    public function getMontantEcheance(): ?string
    {
        return $this->montant_echeance;
    }

    public function setMontantEcheance(string $montant_echeance): static
    {
        $this->montant_echeance = $montant_echeance;

        return $this;
    }

    public function getCapitalRestant(): ?string
    {
        return $this->capital_restant;
    }

    public function setCapitalRestant(string $capital_restant): static
    {
        $this->capital_restant = $capital_restant;

        return $this;
    }

    public function getMontantNominal(): ?string
    {
        return $this->montant_nominal;
    }

    public function setMontantNominal(string $montant_nominal): static
    {
        $this->montant_nominal = $montant_nominal;

        return $this;
    }

    public function getNombreImpayes(): ?string
    {
        return $this->nombre_impayes;
    }

    public function setNombreImpayes(string $nombre_impayes): static
    {
        $this->nombre_impayes = $nombre_impayes;

        return $this;
    }

    public function getSoldeComptable(): ?string
    {
        return $this->solde_comptable;
    }

    public function setSoldeComptable(string $solde_comptable): static
    {
        $this->solde_comptable = $solde_comptable;

        return $this;
    }

    public function getDecaissementDepart(): ?string
    {
        return $this->decaissement_depart;
    }

    public function setDecaissementDepart(string $decaissement_depart): static
    {
        $this->decaissement_depart = $decaissement_depart;

        return $this;
    }

    public function getDateArretAgios(): ?\DateTimeInterface
    {
        return $this->date_arret_agios;
    }

    public function setDateArretAgios(\DateTimeInterface $date_arret_agios): static
    {
        $this->date_arret_agios = $date_arret_agios;

        return $this;
    }

    public function getEch2(): ?string
    {
        return $this->ech_2;
    }

    public function setEch2(string $ech_2): static
    {
        $this->ech_2 = $ech_2;

        return $this;
    }

    public function getEch1(): ?string
    {
        return $this->ech_1;
    }

    public function setEch1(string $ech_1): static
    {
        $this->ech_1 = $ech_1;

        return $this;
    }

    public function getIdCreance(): ?creance
    {
        return $this->id_creance;
    }

    public function setIdCreance(?creance $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }
}
