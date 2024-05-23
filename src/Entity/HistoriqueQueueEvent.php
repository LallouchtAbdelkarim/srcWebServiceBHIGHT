<?php

namespace App\Entity;

use App\Repository\HistoriqueQueueEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueQueueEventRepository::class)]
class HistoriqueQueueEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueEvent $id_queue_event = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAction = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cle = null;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDateAction(): ?\DateTimeInterface
    {
        return $this->dateAction;
    }

    public function setDateAction(?\DateTimeInterface $dateAction): static
    {
        $this->dateAction = $dateAction;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

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
}
