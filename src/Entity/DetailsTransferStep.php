<?php

namespace App\Entity;

use App\Repository\DetailsTransferStepRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsTransferStepRepository::class)]
class DetailsTransferStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $step_instruction = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setStepInstruction(?string $step_instruction): static
    {
        $this->step_instruction = $step_instruction;

        return $this;
    }
}
