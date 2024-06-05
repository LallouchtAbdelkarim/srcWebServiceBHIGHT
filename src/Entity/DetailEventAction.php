<?php

namespace App\Entity;

use App\Repository\DetailEventActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailEventActionRepository::class)]
class DetailEventAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventAction $id_event_action = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_resultat = null;

    #[ORM\Column(length: 255)]
    private ?string $nomSplit = null;

    #[ORM\Column(nullable: true)]
    private ?int $isAllQualification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEventAction(): ?EventAction
    {
        return $this->id_event_action;
    }

    public function setIdEventAction(?EventAction $id_event_action): static
    {
        $this->id_event_action = $id_event_action;

        return $this;
    }

    public function getIdResultat(): ?int
    {
        return $this->id_resultat;
    }

    public function setIdResultat(?int $id_resultat): static
    {
        $this->id_resultat = $id_resultat;
        return $this;
    }

    public function getNomSplit(): ?string
    {
        return $this->nomSplit;
    }

    public function setNomSplit(string $nomSplit): static
    {
        $this->nomSplit = $nomSplit;

        return $this;
    }

    public function getIsAllQualification(): ?int
    {
        return $this->isAllQualification;
    }

    public function setIsAllQualification(?int $isAllQualification): static
    {
        $this->isAllQualification = $isAllQualification;

        return $this;
    }
}
