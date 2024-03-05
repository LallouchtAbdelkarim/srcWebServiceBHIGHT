<?php

namespace App\Entity;

use App\Repository\CompetenceProfilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetenceProfilRepository::class)]
class CompetenceProfil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\ManyToOne(inversedBy: 'competenceProfils')]
    private ?Profil $id_profil = null;

    #[ORM\ManyToOne(inversedBy: 'competenceProfils')]
    private ?Competence $id_competence = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getIdProfil(): ?Profil
    {
        return $this->id_profil;
    }

    public function setIdProfil(?Profil $id_profil): self
    {
        $this->id_profil = $id_profil;

        return $this;
    }

    public function getIdCompetence(): ?Competence
    {
        return $this->id_competence;
    }

    public function setIdCompetence(?Competence $id_competence): self
    {
        $this->id_competence = $id_competence;

        return $this;
    }
}
