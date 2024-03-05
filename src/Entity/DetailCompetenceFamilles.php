<?php

namespace App\Entity;

use App\Repository\DetailCompetenceFamillesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailCompetenceFamillesRepository::class)]
class DetailCompetenceFamilles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeParametrage $id_famille = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Competence $id_competence = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFamille(): ?TypeParametrage
    {
        return $this->id_famille;
    }

    public function setIdFamille(?TypeParametrage $id_famille): static
    {
        $this->id_famille = $id_famille;

        return $this;
    }

    public function getIdCompetence(): ?Competence
    {
        return $this->id_competence;
    }

    public function setIdCompetence(?Competence $id_competence): static
    {
        $this->id_competence = $id_competence;

        return $this;
    }
}
