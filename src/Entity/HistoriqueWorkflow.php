<?php

namespace App\Entity;

use App\Repository\HistoriqueWorkflowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueWorkflowRepository::class)]
class HistoriqueWorkflow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $historique = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Workflow $id_workflow = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHistorique(): ?string
    {
        return $this->historique;
    }

    public function setHistorique(string $historique): static
    {
        $this->historique = $historique;

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
}
