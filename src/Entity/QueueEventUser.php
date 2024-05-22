<?php

namespace App\Entity;

use App\Repository\QueueEventUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueEventUserRepository::class)]
class QueueEventUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueEvent $id_queue_event = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $id_user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusQueueEventUser $id_status = null;

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

    public function getIdUser(): ?Utilisateurs
    {
        return $this->id_user;
    }

    public function setIdUser(?Utilisateurs $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdStatus(): ?StatusQueueEventUser
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusQueueEventUser $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }
}
