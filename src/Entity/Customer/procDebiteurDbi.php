<?php

namespace App\Entity\Customer;

use App\Repository\Customer\procDebiteurDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: procDebiteurDbiRepository::class)]
class procDebiteurDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_proc = null;

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

    public function getIdProc(): ?int
    {
        return $this->id_proc;
    }

    public function setOriginProc(int $origin_proc): static
    {
        $this->origin_proc = $origin_proc;

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
