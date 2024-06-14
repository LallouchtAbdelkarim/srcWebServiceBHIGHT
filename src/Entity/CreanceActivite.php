<?php

namespace App\Entity;

use App\Repository\CreanceActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreanceActiviteRepository::class)]
class CreanceActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creance $id_creance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_param_activite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreance(): ?Creance
    {
        return $this->id_creance;
    }

    public function setIdCreance(?Creance $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getIdParamActivite(): ?ParamActivite
    {
        return $this->id_param_activite;
    }

    public function setIdParamActivite(?ParamActivite $id_param_activite): static
    {
        $this->id_param_activite = $id_param_activite;

        return $this;
    }
}
