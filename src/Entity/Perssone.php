<?php

namespace App\Entity;

use App\Repository\PerssoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerssoneRepository::class)]
class Perssone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMetier(): ?string
    {
        return $this->metier;
    }

    public function setMetier(?string $metier): static
    {
        $this->metier = $metier;

        return $this;
    }
}
