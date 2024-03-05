<?php

namespace App\Entity;

use App\Repository\QueueGroupeCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueGroupeCritereRepository::class)]
class QueueGroupeCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $groupe = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Queue $id_queue = null;

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

    public function getIdQueue(): ?Queue
    {
        return $this->id_queue;
    }

    public function setIdQueue(?Queue $id_queue): static
    {
        $this->id_queue = $id_queue;

        return $this;
    }
}
