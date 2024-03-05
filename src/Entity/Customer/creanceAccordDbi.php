<?php

namespace App\Entity\Customer;

use App\Repository\Customer\creanceAccordDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: creanceAccordDbiRepository::class)]
class creanceAccordDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_accord = null;

    #[ORM\Column]
    private ?int $id_creance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAccord(): ?int
    {
        return $this->id_accord;
    }

    public function setIdAccord(int $id_accord): static
    {
        $this->id_accord = $id_accord;

        return $this;
    }

    public function getIdCreance(): ?int
    {
        return $this->id_creance;
    }

    public function setIdCreance(int $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }
}
