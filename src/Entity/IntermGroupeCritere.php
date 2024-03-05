<?php

namespace App\Entity;

use App\Repository\IntermGroupeCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntermGroupeCritereRepository::class)]
class IntermGroupeCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupeCritere $id_groupe = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CritereParentSeg $id_critere = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGroupe(): ?GroupeCritere
    {
        return $this->id_groupe;
    }

    public function setIdGroupe(?GroupeCritere $id_groupe): static
    {
        $this->id_groupe = $id_groupe;

        return $this;
    }

    public function getIdCritere(): ?CritereParentSeg
    {
        return $this->id_critere;
    }

    public function setIdCritere(?CritereParentSeg $id_critere): static
    {
        $this->id_critere = $id_critere;

        return $this;
    }
}
