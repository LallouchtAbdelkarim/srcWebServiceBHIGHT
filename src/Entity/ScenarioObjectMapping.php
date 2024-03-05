<?php

namespace App\Entity;

use App\Repository\ScenarioObjectMappingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScenarioObjectMappingRepository::class)]
class ScenarioObjectMapping
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?scenario $id_scenario = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ObjectWorkflow $id_object = null;

    #[ORM\Column(length: 255)]
    private ?string $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdScenario(): ?scenario
    {
        return $this->id_scenario;
    }

    public function setIdScenario(?scenario $id_scenario): static
    {
        $this->id_scenario = $id_scenario;

        return $this;
    }

    public function getIdObject(): ?ObjectWorkflow
    {
        return $this->id_object;
    }

    public function setIdObject(?ObjectWorkflow $id_object): static
    {
        $this->id_object = $id_object;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }
}
