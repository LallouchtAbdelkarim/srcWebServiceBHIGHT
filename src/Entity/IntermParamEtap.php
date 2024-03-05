<?php

namespace App\Entity;

use App\Repository\IntermParamEtapRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntermParamEtapRepository::class)]
class IntermParamEtap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?EtapActivite $id_etap = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_param_activite = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEtap(): ?EtapActivite
    {
        return $this->id_etap;
    }

    public function setIdEtap(?EtapActivite $id_etap): self
    {
        $this->id_etap = $id_etap;

        return $this;
    }

    public function getIdParamActivite(): ?ParamActivite
    {
        return $this->id_param_activite;
    }

    public function setIdParamActivite(?ParamActivite $id_param_activite): self
    {
        $this->id_param_activite = $id_param_activite;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
