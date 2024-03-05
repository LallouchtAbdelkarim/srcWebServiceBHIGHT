<?php

namespace App\Entity\Customer;

use App\Repository\Customer\PaiementDbiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementDbiRepository::class)]
class PaiementDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_creance = null;

    #[ORM\Column]
    private ?int $id_type_paiement = null;

    #[ORM\Column(length: 255)]
    private ?string $ref = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_paiement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column]
    private ?int $id_users = null;

    #[ORM\Column]
    private ?int $id_ptf = null;

    #[ORM\Column]
    private ?int $id_details_accord = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column]
    private ?int $id_debiteur = null;

    #[ORM\Column]
    private ?int $confirmed = null;

    #[ORM\Column(length: 255)]
    private ?string $commentaire = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isUpdate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreance(): ?int
    {
        return $this->id_creance;
    }

    public function setIdCreance(int $id_creance): static
    {
        $this->id_creance = $id_creance;

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

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): static
    {
        $this->ref = $ref;

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

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(\DateTimeInterface $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

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

    public function setIdUsers(int $id_users): static
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

    public function getIdDetailsAccord(): ?int
    {
        return $this->id_details_accord;
    }

    public function setIdDetailsAccord(int $id_details_accord): static
    {
        $this->id_details_accord = $id_details_accord;

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

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

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

    public function getIdDebiteur(): ?int
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(int $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getConfirmed(): ?int
    {
        return $this->confirmed;
    }

    public function setConfirmed(int $confirmed): static
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function isIsUpdate(): ?bool
    {
        return $this->isUpdate;
    }

    public function setIsUpdate(bool $isUpdate): static
    {
        $this->isUpdate = $isUpdate;

        return $this;
    }
}
