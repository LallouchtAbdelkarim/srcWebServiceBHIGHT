<?php

namespace App\Entity\Seg;

use App\Repository\Seg\queueDebiteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: queueDebiteurRepository::class)]
class queueDebiteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_seg = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_debiteur = null;

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

    public function getIdDebiteur(): ?int
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?int $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

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
