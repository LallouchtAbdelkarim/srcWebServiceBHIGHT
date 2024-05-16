<?php

namespace App\Entity;

use App\Repository\SousEtapRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SousEtapRepository::class)]
class SousEtap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etap $id_etap = null;

    #[ORM\Column]
    private ?int $etatApproval = null;

    #[ORM\Column]
    private ?int $orderEtap = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_param = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEtap(): ?Etap
    {
        return $this->id_etap;
    }

    public function setIdEtap(?Etap $id_etap): static
    {
        $this->id_etap = $id_etap;

        return $this;
    }

    public function getEtatApproval(): ?int
    {
        return $this->etatApproval;
    }

    public function setEtatApproval(int $etatApproval): static
    {
        $this->etatApproval = $etatApproval;

        return $this;
    }

    public function getOrderEtap(): ?int
    {
        return $this->orderEtap;
    }

    public function setOrderEtap(int $orderEtap): static
    {
        $this->orderEtap = $orderEtap;

        return $this;
    }

    public function getIdParam(): ?ParamActivite
    {
        return $this->id_param;
    }

    public function setIdParam(?ParamActivite $id_param): static
    {
        $this->id_param = $id_param;

        return $this;
    }
}
