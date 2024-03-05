<?php

namespace App\Entity;

use App\Repository\EventSelectChildRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventSelectChildRepository::class)]
class EventSelectChild
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventBasedDecision $id_event_based_decision = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ObjectDetail $id_detail_check = null;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getIdEventBasedDecision(): ?EventBasedDecision
    {
        return $this->id_event_based_decision;
    }

    public function setIdEventBasedDecision(?EventBasedDecision $id_event_based_decision): static
    {
        $this->id_event_based_decision = $id_event_based_decision;

        return $this;
    }

    public function getIdDetailCheck(): ?ObjectDetail
    {
        return $this->id_detail_check;
    }

    public function setIdDetailCheck(?ObjectDetail $id_detail_check): static
    {
        $this->id_detail_check = $id_detail_check;

        return $this;
    }
}
