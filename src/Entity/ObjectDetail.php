<?php

namespace App\Entity;

use App\Repository\ObjectDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectDetailRepository::class)]
class ObjectDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ObjectWorkflow $id_object_workflow = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdObjectWorkflow(): ?ObjectWorkflow
    {
        return $this->id_object_workflow;
    }

    public function setIdObjectWorkflow(?ObjectWorkflow $id_object_workflow): static
    {
        $this->id_object_workflow = $id_object_workflow;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(?string $event): static
    {
        $this->event = $event;

        return $this;
    }
}
