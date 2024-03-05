<?php

namespace App\Entity;

use App\Repository\ClassificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassificationRepository::class)]
class Classification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $classification_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClassificationName(): ?string
    {
        return $this->classification_name;
    }

    public function setClassificationName(string $classification_name): static
    {
        $this->classification_name = $classification_name;

        return $this;
    }
}
