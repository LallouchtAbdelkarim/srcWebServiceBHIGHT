<?php

namespace App\Entity;

use App\Repository\TypeCampagneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeCampagneRepository::class)]
class TypeCampagne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $type_campagne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeCampagne(): ?string
    {
        return $this->type_campagne;
    }

    public function setTypeCampagne(?string $type_campagne): static
    {
        $this->type_campagne = $type_campagne;

        return $this;
    }
}
