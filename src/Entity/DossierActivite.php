<?php

namespace App\Entity;

use App\Repository\DossierActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierActiviteRepository::class)]
class DossierActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_activite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dossier $id_dossier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdActivite(): ?ParamActivite
    {
        return $this->id_activite;
    }

    public function setIdActivite(?ParamActivite $id_activite): static
    {
        $this->id_activite = $id_activite;

        return $this;
    }

    public function getIdDossier(): ?Dossier
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?Dossier $id_dossier): static
    {
        $this->id_dossier = $id_dossier;

        return $this;
    }
}
