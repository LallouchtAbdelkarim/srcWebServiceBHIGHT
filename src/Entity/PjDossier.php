<?php

namespace App\Entity;

use App\Repository\PjDossierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PjDossierRepository::class)]
class PjDossier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Dossier $id_dossier_id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDossierId(): ?Dossier
    {
        return $this->id_dossier_id;
    }

    public function setIdDossierId(?Dossier $id_dossier_id): static
    {
        $this->id_dossier_id = $id_dossier_id;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
