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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypePromise $id_type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusPromise $id_status = null;

    #[ORM\ManyToOne]
    private ?Accord $idAccord = null;

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

    public function getIdType(): ?TypePromise
    {
        return $this->id_type;
    }

    public function setIdType(?TypePromise $id_type): static
    {
        $this->id_type = $id_type;

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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getIdStatus(): ?StatusPromise
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusPromise $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }

    public function getIdAccord(): ?Accord
    {
        return $this->idAccord;
    }

    public function setIdAccord(?Accord $idAccord): static
    {
        $this->idAccord = $idAccord;

        return $this;
    }
}
