<?php

namespace App\Entity;

use App\Repository\HistoTelephoneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoTelephoneRepository::class)]
class HistoTelephone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(length: 255)]
    private ?string $origine = null;

    #[ORM\Column]
    private ?int $id_status_id = null;

    #[ORM\Column]
    private ?int $id_type_tel_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_users_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note1 = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(nullable: true)]
    private ?int $indecatif = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_dn = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_debiteur_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setOrigine(string $origine): static
    {
        $this->origine = $origine;

        return $this;
    }

    public function getIdStatusId(): ?int
    {
        return $this->id_status_id;
    }

    public function setIdStatusId(int $id_status_id): static
    {
        $this->id_status_id = $id_status_id;

        return $this;
    }

    public function getIdTypeTelId(): ?int
    {
        return $this->id_type_tel_id;
    }

    public function setIdTypeTelId(int $id_type_tel_id): static
    {
        $this->id_type_tel_id = $id_type_tel_id;

        return $this;
    }

    public function getIdUsersId(): ?int
    {
        return $this->id_users_id;
    }

    public function setIdUsersId(?int $id_users_id): static
    {
        $this->id_users_id = $id_users_id;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getIndecatif(): ?int
    {
        return $this->indecatif;
    }

    public function setIndecatif(?int $indecatif): static
    {
        $this->indecatif = $indecatif;

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

    public function getIdDebiteurId(): ?int
    {
        return $this->id_debiteur_id;
    }

    public function setIdDebiteurId(?int $id_debiteur_id): static
    {
        $this->id_debiteur_id = $id_debiteur_id;

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
