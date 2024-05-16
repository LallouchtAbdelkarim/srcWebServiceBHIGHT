<?php

namespace App\Entity\Seg;

use App\Repository\Seg\queueTelephoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: queueTelephoneRepository::class)]
class queueTelephone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_seg = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_telephone = null;

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

    public function getIdTelephone(): ?int
    {
        return $this->id_telephone;
    }

    public function setIdTelephone(?int $id_telephone): static
    {
        $this->id_telephone = $id_telephone;

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
