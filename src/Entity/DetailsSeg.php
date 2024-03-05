<?php

namespace App\Entity;

use App\Repository\DetailsSegRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsSegRepository::class)]
class DetailsSeg
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Segmentation $id_segment = null;

    #[ORM\Column(length: 255)]
    private ?string $id_deb = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSegment(): ?Segmentation
    {
        return $this->id_segment;
    }

    public function setIdSegment(?Segmentation $id_segment): static
    {
        $this->id_segment = $id_segment;

        return $this;
    }

    public function getIdDeb(): ?string
    {
        return $this->id_deb;
    }

    public function setIdDeb(string $id_deb): static
    {
        $this->id_deb = $id_deb;

        return $this;
    }
}
