<?php

namespace App\Entity;

use App\Repository\ParamGroupeCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParamGroupeCritereRepository::class)]
class ParamGroupeCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre_groupe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreGroupe(): ?string
    {
        return $this->titre_groupe;
    }

    public function setTitreGroupe(string $titre_groupe): static
    {
        $this->titre_groupe = $titre_groupe;

        return $this;
    }
}
