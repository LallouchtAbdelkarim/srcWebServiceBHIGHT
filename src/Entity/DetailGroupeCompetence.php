<?php

namespace App\Entity;

use App\Repository\DetailGroupeCompetenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailGroupeCompetenceRepository::class)]
class DetailGroupeCompetence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupeCompetence $id_groupe = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?ActiviteParent $id_activite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeParametrage $idFamille = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdActivite(): ?ActiviteParent
    {
        return $this->id_activite;
    }

    public function setIdActivite(?ActiviteParent $id_activite): static
    {
        $this->id_activite = $id_activite;

        return $this;
    }

    public function getIdFamille(): ?TypeParametrage
    {
        return $this->idFamille;
    }

    public function setIdFamille(?TypeParametrage $idFamille): static
    {
        $this->idFamille = $idFamille;

        return $this;
    }
}
