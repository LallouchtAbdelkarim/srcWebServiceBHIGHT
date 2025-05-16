<?php

namespace App\Entity;

use App\Repository\HistoriqueTimerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueTimerRepository::class)]
class HistoriqueTimer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueTimers')]
    private ?Dossier $idDossier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDossier(): ?Dossier
    {
        return $this->idDossier;
    }

    public function setIdDossier(?Dossier $idDossier): static
    {
        $this->idDossier = $idDossier;

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

    public function getTimer(): ?\DateTimeInterface
    {
        return $this->timer;
    }

    public function setTimer(\DateTimeInterface $timer): static
    {
        $this->timer = $timer;

        return $this;
    }
}
