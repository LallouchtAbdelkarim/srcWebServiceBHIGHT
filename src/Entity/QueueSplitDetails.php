<?php

namespace App\Entity;

use App\Repository\QueueSplitDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueSplitDetailsRepository::class)]
class QueueSplitDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueSplit $id_queue_split = null;

    #[ORM\Column]
    private ?int $id_queue_detail = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdQueueDetail(): ?int
    {
        return $this->id_queue_detail;
    }

    public function setIdQueueDetail(int $id_queue_detail): static
    {
        $this->id_queue_detail = $id_queue_detail;

        return $this;
    }
}
