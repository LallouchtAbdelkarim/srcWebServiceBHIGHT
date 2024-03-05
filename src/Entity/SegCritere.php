<?php

namespace App\Entity;

use App\Repository\SegCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SegCritereRepository::class)]
class SegCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SegGroupeCritere $id_groupe = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;
    #[ORM\Column(length: 255)]
    private ?string $critere = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGroupe(): ?SegGroupeCritere
    {
        return $this->id_groupe;
    }

    public function setIdGroupe(?SegGroupeCritere $id_groupe): static
    {
        $this->id_groupe = $id_groupe;

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
    public function getCritere(): ?string
    {
        return $this->critere;
    }

    public function setCritere(string $critere): static
    {
        $this->critere = $critere;

        return $this;
    }
}
