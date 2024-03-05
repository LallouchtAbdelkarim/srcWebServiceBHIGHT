<?php

namespace App\Entity;

use App\Repository\IntermParamSousEtapRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntermParamSousEtapRepository::class)]
class IntermParamSousEtap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SousEtapActivite $id_sous_etap = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_param = null;

    #[ORM\Column]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSousEtap(): ?SousEtapActivite
    {
        return $this->id_sous_etap;
    }

    public function setIdSousEtap(?SousEtapActivite $id_sous_etap): self
    {
        $this->id_sous_etap = $id_sous_etap;

        return $this;
    }

    public function getIdParam(): ?ParamActivite
    {
        return $this->id_param;
    }

    public function setIdParam(?ParamActivite $id_param): self
    {
        $this->id_param = $id_param;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
