<?php

namespace App\Entity;

use App\Repository\SousDetailGroupeCompetenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SousDetailGroupeCompetenceRepository::class)]
class SousDetailGroupeCompetence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailGroupeCompetence $id_detail_groupe_competence = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_param = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetailGroupeCompetence(): ?DetailGroupeCompetence
    {
        return $this->id_detail_groupe_competence;
    }

    public function setIdDetailGroupeCompetence(?DetailGroupeCompetence $id_detail_groupe_competence): static
    {
        $this->id_detail_groupe_competence = $id_detail_groupe_competence;

        return $this;
    }

    public function getIdParam(): ?ParamActivite
    {
        return $this->id_param;
    }

    public function setIdParam(?ParamActivite $id_param): static
    {
        $this->id_param = $id_param;

        return $this;
    }
}
