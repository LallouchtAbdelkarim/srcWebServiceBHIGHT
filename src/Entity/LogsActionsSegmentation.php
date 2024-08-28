<?php

namespace App\Entity;

use App\Repository\LogsActionsSegmentationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogsActionsSegmentationRepository::class)]
class LogsActionsSegmentation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_segmentation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logs = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_action = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSegmentation(): ?int
    {
        return $this->id_segmentation;
    }

    public function setIdSegmentation(?int $id_segmentation): static
    {
        $this->id_segmentation = $id_segmentation;

        return $this;
    }

    public function getLogs(): ?string
    {
        return $this->logs;
    }

    public function setLogs(?string $logs): static
    {
        $this->logs = $logs;

        return $this;
    }

    public function getDateAction(): ?\DateTimeInterface
    {
        return $this->date_action;
    }

    public function setDateAction(?\DateTimeInterface $date_action): static
    {
        $this->date_action = $date_action;

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
}
