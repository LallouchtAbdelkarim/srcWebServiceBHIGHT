<?php

namespace App\Entity\Customer;

use App\Repository\Customer\employeurDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: employeurDbiRepository::class)]
class employeurDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Employeur = null;

    #[ORM\Column(length: 255)]
    private ?string $entreprise = null;

    #[ORM\Column(length: 255)]
    private ?string $Adresse_employeur = null;

    #[ORM\Column(length: 255)]
    private ?string $poste = null;
    #[ORM\Column]
    private ?int $id_debiteur = null;

    #[ORM\Column]
    private ?int $origin_deb = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEmployeur(): ?string
    {
        return $this->Employeur;
    }

    public function setEmployeur(string $Employeur): static
    {
        $this->Employeur = $Employeur;

        return $this;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(string $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getAdresseEmployeur(): ?string
    {
        return $this->Adresse_employeur;
    }

    public function setAdresseEmployeur(string $Adresse_employeur): static
    {
        $this->Adresse_employeur = $Adresse_employeur;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): static
    {
        $this->poste = $poste;

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
    public function getOrigineDeb(): ?int
    {
        return $this->origin_deb;
    }

    public function setOrigineDeb(int $origin_deb): static
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

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(?int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }
}
