<?php

namespace App\Entity;

use App\Repository\PaiementAccordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementAccordRepository::class)]
class PaiementAccord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Paiement $id_paiement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailsAccord $id_details_accord = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPaiement(): ?Paiement
    {
        return $this->id_paiement;
    }

    public function setIdPaiement(?Paiement $id_paiement): static
    {
        $this->id_paiement = $id_paiement;

        return $this;
    }

    public function getIdDetailsAccord(): ?DetailsAccord
    {
        return $this->id_details_accord;
    }

    public function setIdDetailsAccord(?DetailsAccord $id_details_accord): static
    {
        $this->id_details_accord = $id_details_accord;

        return $this;
    }
}
