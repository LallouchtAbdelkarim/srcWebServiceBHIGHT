<?php

namespace App\Entity;

use App\Repository\DetailsAppelCustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsAppelCustomerRepository::class)]
class DetailsAppelCustomer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeAgent $id_type_agent = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ObjectWorkflow $id_objet = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $step_instruction = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeAppel $id_type_appel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTypeAgent(): ?TypeAgent
    {
        return $this->id_type_agent;
    }

    public function setIdTypeAgent(?TypeAgent $id_type_agent): static
    {
        $this->id_type_agent = $id_type_agent;

        return $this;
    }

    public function getIdObjet(): ?ObjectWorkflow
    {
        return $this->id_objet;
    }

    public function setIdObjet(?ObjectWorkflow $id_objet): static
    {
        $this->id_objet = $id_objet;

        return $this;
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

    public function getStepInstruction(): ?string
    {
        return $this->step_instruction;
    }

    public function setStepInstruction(string $step_instruction): static
    {
        $this->step_instruction = $step_instruction;

        return $this;
    }

    public function getIdTypeAppel(): ?TypeAppel
    {
        return $this->id_type_appel;
    }

    public function setIdTypeAppel(?TypeAppel $id_type_appel): static
    {
        $this->id_type_appel = $id_type_appel;

        return $this;
    }
}
