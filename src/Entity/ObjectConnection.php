<?php

namespace App\Entity;

use App\Repository\ObjectConnectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectConnectionRepository::class)]
class ObjectConnection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ObjectWorkflow $id_from_object = null;

    #[ORM\ManyToOne]
    private ?ObjectWorkflow $id_to_object = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFromObject(): ?ObjectWorkflow
    {
        return $this->id_from_object;
    }

    public function setIdFromObject(?ObjectWorkflow $id_from_object): static
    {
        $this->id_from_object = $id_from_object;

        return $this;
    }

    public function getIdToObject(): ?ObjectWorkflow
    {
        return $this->id_to_object;
    }

    public function setIdToObject(?ObjectWorkflow $id_to_object): static
    {
        $this->id_to_object = $id_to_object;

        return $this;
    }
}
