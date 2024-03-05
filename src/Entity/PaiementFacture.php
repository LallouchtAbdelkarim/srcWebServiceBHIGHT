<?php

namespace App\Entity;

use App\Repository\PaiementFactureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementFactureRepository::class)]
class PaiementFacture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $id_facture = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Paiement $id_type_paiement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFacture(): ?Facture
    {
        return $this->id_facture;
    }

    public function setIdFacture(?Facture $id_facture): static
    {
        $this->id_facture = $id_facture;

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

    public function getIdTypePaiement(): ?Paiement
    {
        return $this->id_type_paiement;
    }

    public function setIdTypePaiement(?Paiement $id_type_paiement): static
    {
        $this->id_type_paiement = $id_type_paiement;

        return $this;
    }
}
