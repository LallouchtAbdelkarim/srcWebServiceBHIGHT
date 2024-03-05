<?php

namespace App\Entity;

use App\Repository\SegmentationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SegmentationRepository::class)]
class Segmentation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_segment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?StatusSeg $id_status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $cle_identifiant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $entities = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSegment(): ?string
    {
        return $this->nom_segment;
    }

    public function setNomSegment(string $nom_segment): static
    {
        $this->nom_segment = $nom_segment;

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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getIdStatus(): ?StatusSeg
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusSeg $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCleIdentifiant(): ?string
    {
        return $this->cle_identifiant;
    }

    public function setCleIdentifiant(string $cle_identifiant): static
    {
        $this->cle_identifiant = $cle_identifiant;

        return $this;
    }

    public function getEntities(): ?string
    {
        return $this->entities;
    }

    public function setEntities(?string $entities): static
    {
        $this->entities = $entities;

        return $this;
    }
}
