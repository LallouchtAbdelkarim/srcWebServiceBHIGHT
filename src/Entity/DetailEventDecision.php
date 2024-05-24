<?php

namespace App\Entity;

use App\Repository\DetailEventDecisionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailEventDecisionRepository::class)]
class DetailEventDecision
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueEvent $id_queue_event = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_decision = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cle = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_step = null;

    #[ORM\Column(nullable: true)]
    private ?int $isAction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdQueueEvent(): ?QueueEvent
    {
        return $this->id_queue_event;
    }

    public function setIdQueueEvent(?QueueEvent $id_queue_event): static
    {
        $this->id_queue_event = $id_queue_event;

        return $this;
    }

    public function getIdDecision(): ?int
    {
        return $this->id_decision;
    }

    public function setIdDecision(?int $id_decision): static
    {
        $this->id_decision = $id_decision;

        return $this;
    }

    public function getCle(): ?string
    {
        return $this->cle;
    }

    public function setCle(?string $cle): static
    {
        $this->cle = $cle;

        return $this;
    }

    public function getIdStep(): ?int
    {
        return $this->id_step;
    }

    public function setIdStep(?int $id_step): static
    {
        $this->id_step = $id_step;

        return $this;
    }

    public function getIsAction(): ?int
    {
        return $this->isAction;
    }

    public function setIsAction(?int $isAction): static
    {
        $this->isAction = $isAction;

        return $this;
    }
}
