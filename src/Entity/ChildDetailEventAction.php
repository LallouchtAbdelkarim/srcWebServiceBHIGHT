<?php

namespace App\Entity;

use App\Repository\ChildDetailEventActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChildDetailEventActionRepository::class)]
class ChildDetailEventAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailEventAction $id_detail = null;

    #[ORM\Column]
    private ?int $id_param = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetail(): ?DetailEventAction
    {
        return $this->id_detail;
    }

    public function setIdDetail(?DetailEventAction $id_detail): static
    {
        $this->id_detail = $id_detail;

        return $this;
    }

    public function getIdParam(): ?int
    {
        return $this->id_param;
    }

    public function setIdParam(int $id_param): static
    {
        $this->id_param = $id_param;

        return $this;
    }
}
