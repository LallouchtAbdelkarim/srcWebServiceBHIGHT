<?php

namespace App\Entity;

use App\Repository\DetailsApprovalStepRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsApprovalStepRepository::class)]
class DetailsApprovalStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ObjectWorkflow $id_object = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $step_instruction = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeApprovalStep $id_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdObject(): ?ObjectWorkflow
    {
        return $this->id_object;
    }

    public function setIdObject(?ObjectWorkflow $id_object): static
    {
        $this->id_object = $id_object;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getStepInstruction(): ?string
    {
        return $this->step_instruction;
    }

    public function setStepInstruction(string $step_instruction): static
    {
        $this->step_instruction = $step_instruction;

        return $this;
    }

    public function getIdType(): ?TypeApprovalStep
    {
        return $this->id_type;
    }

    public function setIdType(?TypeApprovalStep $id_type): static
    {
        $this->id_type = $id_type;

        return $this;
    }
}
