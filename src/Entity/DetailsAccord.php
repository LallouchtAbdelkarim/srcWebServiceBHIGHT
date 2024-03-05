<?php

namespace App\Entity;

use App\Repository\DetailsAccordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsAccordRepository::class)]
class DetailsAccord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Accord $id_accord = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusDetailsAccord $id_status = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column]
    private ?float $montantPaiement = null;

    #[ORM\Column]
    private ?float $montantRestant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePrevPaiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\Column(length: 255)]
    private ?string $motif = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $id_user = null;

    #[ORM\ManyToOne]
    private ?TypePaiement $id_type_paiement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAccord(): ?Accord
    {
        return $this->id_accord;
    }

    public function setIdAccord(?Accord $id_accord): static
    {
        $this->id_accord = $id_accord;

        return $this;
    }

    public function getMontantRestant(): ?float
    {
        return $this->montant_restant;
    }

    public function setMontantRestant(float $montant_restant): static
    {
        $this->montant_restant = $montant_restant;

        return $this;
    }

    public function getIdStatus(): ?StatusDetailsAccord
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusDetailsAccord $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMontantPaiement(): ?float
    {
        return $this->montantPaiement;
    }

    public function setMontantPaiement(float $montantPaiement): static
    {
        $this->montantPaiement = $montantPaiement;

        return $this;
    }

    public function getDatePrevPaiement(): ?\DateTimeInterface
    {
        return $this->datePrevPaiement;
    }

    public function setDatePrevPaiement(\DateTimeInterface $datePrevPaiement): static
    {
        $this->datePrevPaiement = $datePrevPaiement;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(?\DateTimeInterface $datePaiement): static
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getIdUser(): ?Utilisateurs
    {
        return $this->id_user;
    }

    public function setIdUser(?Utilisateurs $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdTypePaiement(): ?TypePaiement
    {
        return $this->id_type_paiement;
    }

    public function setIdTypePaiement(?TypePaiement $id_type_paiement): static
    {
        $this->id_type_paiement = $id_type_paiement;

        return $this;
    }
}
