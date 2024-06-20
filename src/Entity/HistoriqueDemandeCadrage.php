<?php

namespace App\Entity;

use App\Repository\HistoriqueDemandeCadrageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueDemandeCadrageRepository::class)]
class HistoriqueDemandeCadrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeCadrage = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbrDebiteurs = null;

    #[ORM\ManyToOne]
    private ?Portefeuille $idPtf = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeCadrage(): ?string
    {
        return $this->typeCadrage;
    }

    public function setTypeCadrage(?string $typeCadrage): static
    {
        $this->typeCadrage = $typeCadrage;

        return $this;
    }

    public function getNbrDebiteurs(): ?int
    {
        return $this->nbrDebiteurs;
    }

    public function setNbrDebiteurs(?int $nbrDebiteurs): static
    {
        $this->nbrDebiteurs = $nbrDebiteurs;

        return $this;
    }

    public function getIdPtf(): ?Portefeuille
    {
        return $this->idPtf;
    }

    public function setIdPtf(?Portefeuille $idPtf): static
    {
        $this->idPtf = $idPtf;

        return $this;
    }
}
