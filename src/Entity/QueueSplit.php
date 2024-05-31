<?php

namespace App\Entity;

use App\Repository\QueueSplitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueSplitRepository::class)]
class QueueSplit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventAction $id_event_action = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cle = null;

    #[ORM\Column(nullable: true)]
    private ?int $isChild = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    public function getIsChild(): ?int
    {
        return $this->isChild;
    }

    public function setIsChild(?int $isChild): static
    {
        $this->isChild = $isChild;

        return $this;
    }
}
