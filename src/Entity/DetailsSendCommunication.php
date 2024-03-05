<?php

namespace App\Entity;

use App\Repository\DetailsSendCommunicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsSendCommunicationRepository::class)]
class DetailsSendCommunication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $step_instruction = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ObjectWorkflow $id_objet = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeSendCommunication $type = null;

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

    public function setStepInstruction(string $step_instruction): static
    {
        $this->step_instruction = $step_instruction;

        return $this;
    }
    public function getIdObjet(): ?ObjectWorkflow
    {
        return $this->id_objet;
    }

    public function setIdObjet(?ObjectWorkflow $id_objet): static
    {
        $this->id_objet = $id_objet;

        return $this;
    }

    public function getType(): ?TypeSendCommunication
    {
        return $this->type;
    }

    public function setType(?TypeSendCommunication $type): static
    {
        $this->type = $type;

        return $this;
    }
}
