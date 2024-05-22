<?php

namespace App\Entity;

use App\Repository\QueueEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueEventRepository::class)]
class QueueEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?EventAction $id_event_action = null;

    #[ORM\Column]
    private ?int $id_queue_detail = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatutQueueEvent $id_statut = null;

    #[ORM\Column]
    private ?int $statut_workflow = null;

    #[ORM\Column]
    private ?int $type = null;
    /* Statut 0 : default
       Statut 1 : terminée
    */

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

    public function getIdQueueDetail(): ?int
    {
        return $this->id_queue_detail;
    }

    public function setIdQueueDetail(int $id_queue_detail): static
    {
        $this->id_queue_detail = $id_queue_detail;

        return $this;
    }

    public function getIdStatut(): ?StatutQueueEvent
    {
        return $this->id_statut;
    }

    public function setIdStatut(?StatutQueueEvent $id_statut): static
    {
        $this->id_statut = $id_statut;

        return $this;
    }

    public function getStatutWorkflow(): ?int
    {
        return $this->statut_workflow;
    }

    public function setStatutWorkflow(int $statut_workflow): static
    {
        $this->statut_workflow = $statut_workflow;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }
}
