<?php

namespace App\Entity;

use App\Repository\DetailsSecteurActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsSecteurActiviteRepository::class)]
class DetailsSecteurActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SecteurActivite $id_secteur = null;

    #[ORM\Column(length: 255)]
    private ?string $activite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSecteur(): ?SecteurActivite
    {
        return $this->id_secteur;
    }

    public function setIdSecteur(?SecteurActivite $id_secteur): static
    {
        $this->id_secteur = $id_secteur;

        return $this;
    }

    public function getActivite(): ?string
    {
        return $this->activite;
    }

    public function setActivite(string $activite): static
    {
        $this->activite = $activite;

        return $this;
    }
}
