<?php

namespace App\Entity;

use App\Repository\ObjectWorkflowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectWorkflowRepository::class)]
class ObjectWorkflow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $Uid = null;

    #[ORM\Column]
    private ?int $id_object = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne]
    private ?Workflow $id_workflow = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?EvenementWorkflow $id_event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->Uid;
    }

    public function setUid(string $Uid): static
    {
        $this->Uid = $Uid;

        return $this;
    }

    public function getIdObject(): ?int
    {
        return $this->id_object;
    }

    public function setIdObject(int $id_object): static
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

    public function getIdWorkflow(): ?Workflow
    {
        return $this->id_workflow;
    }

    public function setIdWorkflow(?Workflow $id_workflow): static
    {
        $this->id_workflow = $id_workflow;

        return $this;
    }

    public function getIdEvent(): ?EvenementWorkflow
    {
        return $this->id_event;
    }

    public function setIdEvent(?EvenementWorkflow $id_event): static
    {
        $this->id_event = $id_event;

        return $this;
    }
}
