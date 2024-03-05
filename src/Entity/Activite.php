<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


   
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_param = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?ActiviteParent $id_parent_activite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $num_link = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

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

    public function getIdParentActivite(): ?ActiviteParent
    {
        return $this->id_parent_activite;
    }

    public function setIdParentActivite(?ActiviteParent $id_parent_activite): self
    {
        $this->id_parent_activite = $id_parent_activite;

        return $this;
    }

    public function getNumLink(): ?string
    {
        return $this->num_link;
    }

    public function setNumLink(?string $num_link): self
    {
        $this->num_link = $num_link;

        return $this;
    }

    
}
