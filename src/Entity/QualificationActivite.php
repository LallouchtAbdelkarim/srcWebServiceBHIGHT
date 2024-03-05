<?php

namespace App\Entity;

use App\Repository\QualificationActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QualificationActiviteRepository::class)]
class QualificationActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?ParamActivite $id_param_activite = null;

    #[ORM\Column(length: 255)]
    private ?string $qualification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdParamActivite(): ?ParamActivite
    {
        return $this->id_param_activite;
    }

    public function setIdParamActivite(?ParamActivite $id_param_activite): static
    {
        $this->id_param_activite = $id_param_activite;

        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): static
    {
        $this->qualification = $qualification;

        return $this;
    }
}
