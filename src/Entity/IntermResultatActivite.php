<?php

namespace App\Entity;

use App\Repository\IntermResultatActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntermResultatActiviteRepository::class)]
class IntermResultatActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ResultatActivite $id_resultat = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activite $id_activite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdResultat(): ?ResultatActivite
    {
        return $this->id_resultat;
    }

    public function setIdResultat(?ResultatActivite $id_resultat): self
    {
        $this->id_resultat = $id_resultat;

        return $this;
    }

    public function getIdActivite(): ?Activite
    {
        return $this->id_activite;
    }

    public function setIdActivite(?Activite $id_activite): self
    {
        $this->id_activite = $id_activite;

        return $this;
    }
}
