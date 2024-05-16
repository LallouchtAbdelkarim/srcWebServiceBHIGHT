<?php

namespace App\Entity\Customer;

use App\Repository\Customer\adresseDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: adresseDbiRepository::class)]
class adresseDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_debiteur = null;


    #[ORM\Column(length: 255)]
    private ?string $adresse_complet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column]
    private ?int  $id_type_adresse = null;

    #[ORM\Column]
    private ?int $verifier = null;

    #[ORM\Column(length: 255)]
    private ?string $code_postal = null;

    #[ORM\Column(length: 255)]
    private ?string $province = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 255)]
    private ?string $origine = null;

    #[ORM\Column]
    private ?int $origin_deb = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column(length: 255)]
    private ?string $type_adresse = null;

    #[ORM\Column]
    private ?int $id_integration = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_adresse_import = null;

    public function getId(): ?int
    {
        return $this->id;
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
    
    public function getAdresseComplet(): ?string
    {
        return $this->adresse_complet;
    }

    public function setAdresseComplet(string $adresse_complet): static
    {
        $this->adresse_complet = $adresse_complet;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

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

    public function getIdTypeAdresse(): ?static
    {
        return $this->id_type_adresse;
    }

    public function setIdTypeAdresse(?int $id_type_adresse): static
    {
        $this->id_type_adresse = $id_type_adresse;

        return $this;
    }

    public function getVerifier(): ?int
    {
        return $this->verifier;
    }

    public function setVerifier(int $verifier): static
    {
        $this->verifier = $verifier;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): static
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }
    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(string $origine): static
    {
        $this->origine = $origine;

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

    public function getIdImport(): ?int
    {
        return $this->id_import;
    }

    public function setIdImport(int $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }

    public function getTypeAdresse(): ?string
    {
        return $this->type_adresse;
    }

    public function setTypeAdresse(string $type_adresse): static
    {
        $this->type_adresse = $type_adresse;

        return $this;
    }

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }

    public function getIdAdresseImport(): ?int
    {
        return $this->id_adresse_import;
    }

    public function setIdAdresseImport(?int $id_adresse_import): static
    {
        $this->id_adresse_import = $id_adresse_import;

        return $this;
    }
}
