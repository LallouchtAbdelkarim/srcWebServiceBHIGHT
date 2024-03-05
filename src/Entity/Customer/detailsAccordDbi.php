<?php

namespace App\Entity\Customer;

use App\Repository\Customer\detailsAccordDbiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: detailsAccordDbiRepository::class)]
class detailsAccordDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_accord = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_paiement = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column]
    private ?int $id_type_paiement = null;

    #[ORM\Column]
    private ?float $montantPaiement = null;

    #[ORM\Column]
    private ?float $montantRestant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAccord(): ?int
    {
        return $this->id_accord;
    }

    public function setIdAccord(int $id_accord): static
    {
        $this->id_accord = $id_accord;

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

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePrevPaiement(\DateTimeInterface $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIdTypePaiement(): ?int
    {
        return $this->id_type_paiement;
    }

    public function setIdTypePaiement(int $id_type_paiement): static
    {
        $this->id_type_paiement = $id_type_paiement;

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

    public function getMontantRestant(): ?float
    {
        return $this->montantRestant;
    }

    public function setMontantRestant(float $montantRestant): static
    {
        $this->montantRestant = $montantRestant;

        return $this;
    }
}
