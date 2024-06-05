<?php

namespace App\Entity;

use App\Repository\SystemQueueProcessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SystemQueueProcessRepository::class)]
class SystemQueueProcess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueEvent $id_queue_event = null;

    #[ORM\Column]
    private ?int $id_event = null;

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

    public function getIdEvent(): ?int
    {
        return $this->id_event;
    }

    public function setIdEvent(int $id_event): static
    {
        $this->id_event = $id_event;

        return $this;
    }
}
