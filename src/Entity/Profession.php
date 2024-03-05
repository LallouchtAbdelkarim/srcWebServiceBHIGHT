<?php

namespace App\Entity;

use App\Repository\ProfessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfessionRepository::class)]
class Profession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Classification $id_classification = null;

    #[ORM\Column(length: 255)]
    private ?string $Profession = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClassification(): ?Classification
    {
        return $this->id_classification;
    }

    public function setIdClassification(?Classification $id_classification): static
    {
        $this->id_classification = $id_classification;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->Profession;
    }

    public function setProfession(string $Profession): static
    {
        $this->Profession = $Profession;

        return $this;
    }
}
