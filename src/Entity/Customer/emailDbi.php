<?php

namespace App\Entity\Customer;

use App\Repository\Customer\emailDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: emailDbiRepository::class)]
class emailDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_debiteur = null;

    #[ORM\Column(nullable: true)]
    private ?int $origin_deb = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $id_email_import = null;

    #[ORM\Column]
    private ?int $id_integration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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

    public function getOriginDeb(): ?int
    {
        return $this->origin_deb;
    }

    public function setOriginDeb(?int $origin_deb): static
    {
        $this->origin_deb = $origin_deb;

        return $this;
    }

    public function getIdEmailImport(): ?string
    {
        return $this->id_email_import;
    }

    public function setIdEmailImport(?string $id_email_import): static
    {
        $this->id_email_import = $id_email_import;

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
}
