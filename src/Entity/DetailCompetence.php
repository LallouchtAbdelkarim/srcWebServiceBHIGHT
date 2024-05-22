<?php

namespace App\Entity;

use App\Repository\DetailCompetenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailCompetenceRepository::class)]
class DetailCompetence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Competence $id_competence = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupeCompetence $id_groupe = null;

    // #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?ParamActivite $id_param = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdGroupe(): ?GroupeCompetence
    {
        return $this->id_groupe;
    }

    public function setIdGroupe(?GroupeCompetence $id_groupe): static
    {
        $this->id_groupe = $id_groupe;

        return $this;
    }
}
