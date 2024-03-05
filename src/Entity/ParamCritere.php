<?php

namespace App\Entity;

use App\Repository\ParamCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParamCritereRepository::class)]
class ParamCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $critere = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamGroupeCritere $id_groupe_critere = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCritere(): ?string
    {
        return $this->critere;
    }

    public function setCritere(string $critere): static
    {
        $this->critere = $critere;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getIdGroupeCritere(): ?ParamGroupeCritere
    {
        return $this->id_groupe_critere;
    }

    public function setIdGroupeCritere(?ParamGroupeCritere $id_groupe_critere): static
    {
        $this->id_groupe_critere = $id_groupe_critere;

        return $this;
    }
}
