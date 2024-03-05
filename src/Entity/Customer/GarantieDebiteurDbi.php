<?php

namespace App\Entity\Customer;

use App\Repository\Customer\GarantieDebiteurDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GarantieDebiteurDbiRepository::class)]
class GarantieDebiteurDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_garantie = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $id_debiteur = null;

    #[ORM\Column]
    private ?int $origin_deb = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGarantie(): ?int
    {
        return $this->id_garantie;
    }

    public function setOriginGarantie(int $origin_garantie): static
    {
        $this->origin_garantie = $origin_garantie;

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

    public function getIdDebiteur(): ?int
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(int $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
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
}
