<?php

namespace App\Entity;

use App\Repository\QueueValuesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueValuesRepository::class)]
class QueueValues
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value1 = null;

    #[ORM\Column(length: 255)]
    private ?string $value2 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueCritere $id_critere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $action = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value_view = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue1(): ?string
    {
        return $this->value1;
    }

    public function setValue1(string $value1): static
    {
        $this->value1 = $value1;

        return $this;
    }

    public function getValue2(): ?string
    {
        return $this->value2;
    }

    public function setValue2(string $value2): static
    {
        $this->value2 = $value2;

        return $this;
    }

    public function getIdCritere(): ?QueueCritere
    {
        return $this->id_critere;
    }

    public function setIdCritere(?QueueCritere $id_critere): static
    {
        $this->id_critere = $id_critere;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getValueView(): ?string
    {
        return $this->value_view;
    }

    public function setValueView(?string $value_view): static
    {
        $this->value_view = $value_view;

        return $this;
    }
}
