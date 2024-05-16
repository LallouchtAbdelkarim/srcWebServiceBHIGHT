<?php

namespace App\Entity\Customer;

use App\Repository\Customer\telephoneDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: telephoneDbiRepository::class)]
class telephoneDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $origin_deb = null;

    #[ORM\Column]
    private ?int $id_import = null;
    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $origine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $id_type_tel = null;

    #[ORM\Column]
    private ?int $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note3 = null;

    #[ORM\Column]
    private ?int $id_debiteur = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_telephone_import = null;

    #[ORM\Column(nullable: true)]
    private ?int $codeP = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_dn = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat_histo = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginDeb(): ?int
    {
        return $this->origin_deb;
    }

    public function setOriginDeb(int $origin_deb): static
    {
        $this->origin_deb = $origin_deb;

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
    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(?string $origine): static
    {
        $this->origine = $origine;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }


    public function getIdTypeTel(): ?int
    {
        return $this->id_type_tel;
    }

    public function setIdTypeTel(?int $id_type_tel): static
    {
        $this->id_type_tel = $id_type_tel;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getNote1(): ?string
    {
        return $this->note1;
    }

    public function setNote1(?string $note1): static
    {
        $this->note1 = $note1;

        return $this;
    }

    public function getNumero2(): ?string
    {
        return $this->numero2;
    }

    public function setNumero2(?string $numero2): static
    {
        $this->numero2 = $numero2;

        return $this;
    }

    public function getStatut2(): ?string
    {
        return $this->statut2;
    }

    public function setStatut2(?string $statut2): static
    {
        $this->statut2 = $statut2;

        return $this;
    }

    public function getNote2(): ?string
    {
        return $this->note2;
    }

    public function setNote2(?string $note2): static
    {
        $this->note2 = $note2;

        return $this;
    }

    public function getNumero3(): ?string
    {
        return $this->numero3;
    }

    public function setNumero3(?string $numero3): static
    {
        $this->numero3 = $numero3;

        return $this;
    }

    public function getStatut3(): ?string
    {
        return $this->statut3;
    }

    public function setStatut3(?string $statut3): static
    {
        $this->statut3 = $statut3;

        return $this;
    }

    public function getNote3(): ?string
    {
        return $this->note3;
    }

    public function setNote3(?string $note3): static
    {
        $this->note3 = $note3;

        return $this;
    }

    public function getIdDebiteur(): ?int
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?int $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

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

    public function getIdTelephoneImport(): ?int
    {
        return $this->id_telephone_import;
    }

    public function setIdTelephoneImport(?int $id_telephone_import): static
    {
        $this->id_telephone_import = $id_telephone_import;

        return $this;
    }

    public function getCodeP(): ?int
    {
        return $this->codeP;
    }

    public function setCodeP(?int $codeP): static
    {
        $this->codeP = $codeP;

        return $this;
    }

    public function getIdDn(): ?int
    {
        return $this->id_dn;
    }

    public function setIdDn(?int $id_dn): static
    {
        $this->id_dn = $id_dn;

        return $this;
    }

    public function getEtatHisto(): ?int
    {
        return $this->etat_histo;
    }

    public function setEtatHisto(?int $etat_histo): static
    {
        $this->etat_histo = $etat_histo;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
