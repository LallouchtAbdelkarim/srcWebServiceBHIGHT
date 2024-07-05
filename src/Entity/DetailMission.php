<?php

namespace App\Entity;

use App\Repository\DetailMissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailMissionRepository::class)]
class DetailMission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?DetailsFile $id_detail_file = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Missions $id_mission = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetailFile(): ?DetailsFile
    {
        return $this->id_detail_file;
    }

    public function setIdDetailFile(?DetailsFile $id_detail_file): static
    {
        $this->id_detail_file = $id_detail_file;

        return $this;
    }

    public function getIdMission(): ?Missions
    {
        return $this->id_mission;
    }

    public function setIdMission(?Missions $id_mission): static
    {
        $this->id_mission = $id_mission;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
