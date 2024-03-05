<?php

namespace App\Entity;

use App\Repository\LogsActionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogsActionsRepository::class)]
class LogsActions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActionsImport $id_action = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rapport = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAction(): ?ActionsImport
    {
        return $this->id_action;
    }

    public function setIdAction(?ActionsImport $id_action): static
    {
        $this->id_action = $id_action;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getRapport(): ?string
    {
        return $this->rapport;
    }

    public function setRapport(?string $rapport): static
    {
        $this->rapport = $rapport;

        return $this;
    }
}
