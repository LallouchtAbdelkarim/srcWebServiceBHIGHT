<?php

namespace App\Entity;

use App\Repository\IntegDebiteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntegDebiteurRepository::class)]
class IntegDebiteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $params = null;

    #[ORM\Column(length: 255)]
    private ?string $values1 = null;

    #[ORM\Column(length: 255)]
    private ?string $values2 = null;

    #[ORM\Column]
    private ?int $etat_exist = null;

    #[ORM\ManyToOne]
    private ?Debiteur $id_debiteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?import $id_import = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $rapport = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParams(): ?string
    {
        return $this->params;
    }

    public function setParams(string $params): static
    {
        $this->params = $params;

        return $this;
    }

    public function getValues1(): ?string
    {
        return $this->values1;
    }

    public function setValues1(string $values1): static
    {
        $this->values1 = $values1;

        return $this;
    }

    public function getValues2(): ?string
    {
        return $this->values2;
    }

    public function setValues2(string $values2): static
    {
        $this->values2 = $values2;

        return $this;
    }

    public function getEtatExist(): ?int
    {
        return $this->etat_exist;
    }

    public function setEtatExist(int $etat_exist): static
    {
        $this->etat_exist = $etat_exist;

        return $this;
    }

    public function getIdDebiteur(): ?Debiteur
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?Debiteur $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getIdImport(): ?import
    {
        return $this->id_import;
    }

    public function setIdImport(?import $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }

    public function getRapport(): ?string
    {
        return $this->rapport;
    }

    public function setRapport(string $rapport): static
    {
        $this->rapport = $rapport;

        return $this;
    }
}
