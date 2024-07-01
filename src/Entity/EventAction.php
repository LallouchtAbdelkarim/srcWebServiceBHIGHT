<?php

namespace App\Entity;

use App\Repository\EventActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventActionRepository::class)]
class EventAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?EvenementWorkflow $id_event = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Workflow $id_workflow = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cle = null;

    #[ORM\Column(nullable: true)]
    private ?int $type = null;

    //simple event : type = 1;
    //Delay : type = 3;
    //Split flow step : type = 2;
    //Decision step : type = 4;
    //Split activity : type = 5;


    #[ORM\Column(nullable: true)]
    private ?int $delayAction = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_activity_p = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEvent(): ?EvenementWorkflow
    {
        return $this->id_event;
    }

    public function setIdEvent(?EvenementWorkflow $id_event): static
    {
        $this->id_event = $id_event;

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

    public function getCle(): ?string
    {
        return $this->cle;
    }

    public function setCle(?string $cle): static
    {
        $this->cle = $cle;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDelayAction(): ?int
    {
        return $this->delayAction;
    }

    public function setDelayAction(?int $delayAction): static
    {
        $this->delayAction = $delayAction;

        return $this;
    }

    public function getIdActivityP(): ?int
    {
        return $this->id_activity_p;
    }

    public function setIdActivityP(?int $id_activity_p): static
    {
        $this->id_activity_p = $id_activity_p;

        return $this;
    }
}
