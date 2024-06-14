<?php

namespace App\Entity;

use App\Repository\HistoriqueEmailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueEmailRepository::class)]
class HistoriqueEmail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_action = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_debiteur = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column]
    private ?int $id_type_email = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column]
    private ?int $id_type_source = null;

    #[ORM\Column]
    private ?int $id_status_email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAction(): ?\DateTimeInterface
    {
        return $this->date_action;
    }

    public function setDateAction(?\DateTimeInterface $date_action): static
    {
        $this->date_action = $date_action;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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

    public function getIdTypeEmail(): ?int
    {
        return $this->id_type_email;
    }

    public function setIdTypeEmail(?int $id_type_email): static
    {
        $this->id_type_email = $id_type_email;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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

    public function getIdTypeSource(): ?int
    {
        return $this->id_type_source;
    }

    public function setIdTypeSource(?int $id_type_source): static
    {
        $this->id_type_source = $id_type_source;

        return $this;
    }

    public function getIdStatusEmail(): ?int
    {
        return $this->id_status_email;
    }

    public function setIdStatusEmail(?int $id_status_email): static
    {
        $this->id_status_email = $id_status_email;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }
}
