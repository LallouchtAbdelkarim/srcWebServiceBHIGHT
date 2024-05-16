<?php

namespace App\Entity\Seg;

use App\Repository\Seg\queueDossierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: queueDossierRepository::class)]
class queueDossier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_seg = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_dossier = null;

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

    public function getIdDossier(): ?int
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?int $id_dossier): static
    {
        $this->id_dossier = $id_dossier;

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
