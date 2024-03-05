<?php

namespace App\Entity;

use App\Repository\DetailsFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsFileRepository::class)]
class DetailsFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?FileMissions $id_file_missions = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_dossier = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?bool $isInMissions = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFileMissions(): ?FileMissions
    {
        return $this->id_file_missions;
    }

    public function setIdFileMissions(?FileMissions $id_file_missions): static
    {
        $this->id_file_missions = $id_file_missions;

        return $this;
    }

    public function getNumeroDossier(): ?string
    {
        return $this->numero_dossier;
    }

    public function setNumeroDossier(string $numero_dossier): static
    {
        $this->numero_dossier = $numero_dossier;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function isIsInMissions(): ?bool
    {
        return $this->isInMissions;
    }

    public function setIsInMissions(bool $isInMissions): static
    {
        $this->isInMissions = $isInMissions;

        return $this;
    }
}
