<?php

namespace App\Entity;

use App\Repository\DataWorkflowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataWorkflowRepository::class)]
class DataWorkflow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $data = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private array $dataActivity = [];

    #[ORM\ManyToOne]
    private ?Workflow $id_workflow = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

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
    public function getDataActivity(): array
    {
        return $this->dataActivity;
    }

    public function setDataActivity(array $dataActivity): static
    {
        $this->dataActivity = $dataActivity;

        return $this;
    }
}
