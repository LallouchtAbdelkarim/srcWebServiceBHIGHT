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
    private ?int $id_decision_step = null;

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

    public function getIdDecisionStep(): ?int
    {
        return $this->id_decision_step;
    }

    public function setIdDecisionStep(?int $id_decision_step): static
    {
        $this->id_decision_step = $id_decision_step;

        return $this;
    }
}
