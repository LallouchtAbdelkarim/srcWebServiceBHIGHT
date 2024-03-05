<?php

namespace App\Entity;

use App\Repository\ScenarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScenarioRepository::class)]
class Scenario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?workflow $id_workflow = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdWorkflow(): ?workflow
    {
        return $this->id_workflow;
    }

    public function setIdWorkflow(?workflow $id_workflow): static
    {
        $this->id_workflow = $id_workflow;

        return $this;
    }
}
