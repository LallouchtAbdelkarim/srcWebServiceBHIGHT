<?php

namespace App\Entity;

use App\Repository\QueueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueRepository::class)]
class Queue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $priority = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private ?bool $assigned_strategy = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueGroupe $queue_groupe = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Segmentation $id_segmentation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeQueue $id_type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE , nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne]
    private ?StatusQueue $id_status = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function isAssignedStrategy(): ?bool
    {
        return $this->assigned_strategy;
    }

    public function setAssignedStrategy(bool $assigned_strategy): static
    {
        $this->assigned_strategy = $assigned_strategy;

        return $this;
    }

    public function getQueueGroupe(): ?QueueGroupe
    {
        return $this->queue_groupe;
    }

    public function setQueueGroupe(?QueueGroupe $queue_groupe): static
    {
        $this->queue_groupe = $queue_groupe;

        return $this;
    }

    public function getIdSegmentation(): ?Segmentation
    {
        return $this->id_segmentation;
    }

    public function setIdSegmentation(?Segmentation $id_segmentation): static
    {
        $this->id_segmentation = $id_segmentation;

        return $this;
    }

    public function getIdType(): ?TypeQueue
    {
        return $this->id_type;
    }

    public function setIdType(?TypeQueue $id_type): static
    {
        $this->id_type = $id_type;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getIdStatus(): ?StatusQueue
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusQueue $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }
}
