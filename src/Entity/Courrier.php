<?php

namespace App\Entity;

use App\Repository\CourrierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourrierRepository::class)]
class Courrier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    private ?string $bar_code = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelCourier $id_models = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?dossier $id_dossier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Adresse $id_adresse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

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

    public function getBarCode(): ?string
    {
        return $this->bar_code;
    }

    public function setBarCode(string $bar_code): static
    {
        $this->bar_code = $bar_code;

        return $this;
    }

    public function getIdModels(): ?ModelCourier
    {
        return $this->id_models;
    }

    public function setIdModels(?ModelCourier $id_models): static
    {
        $this->id_models = $id_models;

        return $this;
    }

    public function getIdDossier(): ?dossier
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?dossier $id_dossier): static
    {
        $this->id_dossier = $id_dossier;

        return $this;
    }

    public function getIdAdresse(): ?Adresse
    {
        return $this->id_adresse;
    }

    public function setIdAdresse(?Adresse $id_adresse): static
    {
        $this->id_adresse = $id_adresse;

        return $this;
    }
}
