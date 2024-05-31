<?php

namespace App\Entity;

use App\Repository\AccordArchiveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccordArchiveRepository::class)]
class AccordArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_premier_paiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFinPaiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $frequence = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbrEcheanciers = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column]
    private ?int $id_users = null;

    #[ORM\Column]
    private ?int $id_type_paiement = null;

    #[ORM\Column]
    private ?int $id_status = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant_a_payer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePremierPaiement(): ?\DateTimeInterface
    {
        return $this->date_premier_paiement;
    }

    public function setDatePremierPaiement(\DateTimeInterface $date_premier_paiement): static
    {
        $this->date_premier_paiement = $date_premier_paiement;

        return $this;
    }

    public function getDateFinPaiement(): ?\DateTimeInterface
    {
        return $this->dateFinPaiement;
    }

    public function setDateFinPaiement(?\DateTimeInterface $dateFinPaiement): static
    {
        $this->dateFinPaiement = $dateFinPaiement;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getFrequence(): ?string
    {
        return $this->frequence;
    }

    public function setFrequence(?string $frequence): static
    {
        $this->frequence = $frequence;

        return $this;
    }

    public function getNbrEcheanciers(): ?int
    {
        return $this->nbrEcheanciers;
    }

    public function setNbrEcheanciers(?int $nbrEcheanciers): static
    {
        $this->nbrEcheanciers = $nbrEcheanciers;

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

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getIdUsers(): ?int
    {
        return $this->id_users;
    }

    public function setIdUsers(?int $id_users): static
    {
        $this->id_users = $id_users;

        return $this;
    }

    public function getIdTypePaiement(): ?int
    {
        return $this->id_type_paiement;
    }

    public function setIdTypePaiement(?int $id_type_paiement): static
    {
        $this->id_type_paiement = $id_type_paiement;

        return $this;
    }

    public function getIdStatus(): ?int
    {
        return $this->id_status;
    }

    public function setIdStatus(?int $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }

    public function getMontantAPayer(): ?float
    {
        return $this->montant_a_payer;
    }

    public function setMontantAPayer(?float $montant_a_payer): static
    {
        $this->montant_a_payer = $montant_a_payer;

        return $this;
    }
}
