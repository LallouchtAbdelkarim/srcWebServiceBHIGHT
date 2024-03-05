<?php

namespace App\Entity;

use App\Repository\NoteWorkflowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteWorkflowRepository::class)]
class NoteWorkflow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $note_workflow = null;

    #[ORM\ManyToOne(inversedBy: 'n')]
    #[ORM\JoinColumn(nullable: false)]
    private ?workflow $id_workflow = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoteWorkflow(): ?string
    {
        return $this->note_workflow;
    }

    public function setNoteWorkflow(string $note_workflow): static
    {
        $this->note_workflow = $note_workflow;

        return $this;
    }

    public function getIdWorkflow(): ?workflow
    {
        return $this->id_workflow;
    }

    public function setIdWorkflow(?workflow $id_workflow): static
    {
        $this->id_workflow = $id_workflow;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }
}
