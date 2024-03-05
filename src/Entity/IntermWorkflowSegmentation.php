<?php

namespace App\Entity;

use App\Repository\IntermWorkflowSegmentationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntermWorkflowSegmentationRepository::class)]
class IntermWorkflowSegmentation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Workflow $id_workflow = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Segmentation $id_segmentaion = null;

    #[ORM\ManyToOne]
    private ?TypeWorkflowSegmentation $id_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdWorkflow(): ?Workflow
    {
        return $this->id_workflow;
    }

    public function setIdWorkflow(Workflow $id_workflow): static
    {
        $this->id_workflow = $id_workflow;

        return $this;
    }

    public function getIdSegmentaion(): ?Segmentation
    {
        return $this->id_segmentaion;
    }

    public function setIdSegmentaion(?Segmentation $id_segmentaion): static
    {
        $this->id_segmentaion = $id_segmentaion;

        return $this;
    }

    public function getIdType(): ?TypeWorkflowSegmentation
    {
        return $this->id_type;
    }

    public function setIdType(?TypeWorkflowSegmentation $id_type): static
    {
        $this->id_type = $id_type;

        return $this;
    }
}
