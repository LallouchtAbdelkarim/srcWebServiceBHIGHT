<?php

namespace App\Entity\Customer;

use App\Repository\Customer\PaiementAccordDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementAccordDbiRepository::class)]
class PaiementAccordDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_paiement = null;

    #[ORM\Column]
    private ?int $id_details_accord = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPaiement(): ?int
    {
        return $this->id_paiement;
    }

    public function setIdPaiement(int $id_paiement): static
    {
        $this->id_paiement = $id_paiement;

        return $this;
    }

    public function getIdDetailsAccord(): ?int
    {
        return $this->id_details_accord;
    }

    public function setIdDetailsAccord(int $id_details_accord): static
    {
        $this->id_details_accord = $id_details_accord;

        return $this;
    }
}
