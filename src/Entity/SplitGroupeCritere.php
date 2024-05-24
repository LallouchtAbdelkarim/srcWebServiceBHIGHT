<?php

namespace App\Entity;

use App\Repository\SplitGroupeCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SplitGroupeCritereRepository::class)]
class SplitGroupeCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $groupe = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueSplit $id_queue_split = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(string $groupe): static
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getIdQueueSplit(): ?QueueSplit
    {
        return $this->id_queue_split;
    }

    public function setIdQueueSplit(?QueueSplit $id_queue_split): static
    {
        $this->id_queue_split = $id_queue_split;

        return $this;
    }
}
