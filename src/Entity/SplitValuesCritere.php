<?php

namespace App\Entity;

use App\Repository\SplitValuesCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SplitValuesCritereRepository::class)]
class SplitValuesCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value1 = null;

    #[ORM\Column(length: 255)]
    private ?string $value2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue1(): ?string
    {
        return $this->value1;
    }

    public function setValue1(string $value1): static
    {
        $this->value1 = $value1;

        return $this;
    }

    public function getValue2(): ?string
    {
        return $this->value2;
    }

    public function setValue2(string $value2): static
    {
        $this->value2 = $value2;

        return $this;
    }
}
