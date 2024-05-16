<?php

namespace App\Entity\Seg;

use App\Repository\Seg\queueAdresseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: queueAdresseRepository::class)]
class queueAdresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_seg = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_adresse = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_queue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSeg(): ?int
    {
        return $this->id_seg;
    }

    public function setIdSeg(int $id_seg): static
    {
        $this->id_seg = $id_seg;

        return $this;
    }

    public function getIdAdresse(): ?int
    {
        return $this->id_adresse;
    }

    public function setIdAdresse(?int $id_adresse): static
    {
        $this->id_adresse = $id_adresse;

        return $this;
    }

    public function getIdQueue(): ?int
    {
        return $this->id_queue;
    }

    public function setIdQueue(?int $id_queue): static
    {
        $this->id_queue = $id_queue;

        return $this;
    }
}
