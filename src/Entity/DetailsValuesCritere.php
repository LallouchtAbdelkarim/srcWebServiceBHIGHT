<?php

namespace App\Entity;

use App\Repository\DetailsValuesCritereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsValuesCritereRepository::class)]
class DetailsValuesCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamCritere $id_critere = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_parent_type_creance = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_parent_secteur_activite = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_details_type_creance_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_champ = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getIdCritere(): ?ParamCritere
    {
        return $this->id_critere;
    }

    public function setIdCritere(?ParamCritere $id_critere): static
    {
        $this->id_critere = $id_critere;

        return $this;
    }

    public function getIdParentTypeCreance(): ?int
    {
        return $this->id_parent_type_creance;
    }

    public function setIdParentTypeCreance(?int $id_parent_type_creance): static
    {
        $this->id_parent_type_creance = $id_parent_type_creance;

        return $this;
    }

    public function getIdParentSecteurActivite(): ?int
    {
        return $this->id_parent_secteur_activite;
    }

    public function setIdParentSecteurActivite(?int $id_parent_secteur_activite): static
    {
        $this->id_parent_secteur_activite = $id_parent_secteur_activite;

        return $this;
    }

    public function getIdDetailsTypeCreanceId(): ?int
    {
        return $this->id_details_type_creance_id;
    }

    public function setIdDetailsTypeCreanceId(?int $id_details_type_creance_id): static
    {
        $this->id_details_type_creance_id = $id_details_type_creance_id;

        return $this;
    }

    public function getIdChamp(): ?int
    {
        return $this->id_champ;
    }

    public function setIdChamp(?int $id_champ): static
    {
        $this->id_champ = $id_champ;

        return $this;
    }
}
