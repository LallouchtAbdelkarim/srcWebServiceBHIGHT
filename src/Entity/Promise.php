<?php

namespace App\Entity;

use App\Repository\PromiseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromiseRepository::class)]
class Promise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creance $id_creance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $id_user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?self $id_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreance(): ?Creance
    {
        return $this->id_creance;
    }

    public function setIdCreance(?Creance $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getIdUser(): ?Utilisateurs
    {
        return $this->id_user;
    }

    public function setIdUser(?Utilisateurs $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getIdType(): ?self
    {
        return $this->id_type;
    }

    public function setIdType(?self $id_type): static
    {
        $this->id_type = $id_type;

        return $this;
    }
}
