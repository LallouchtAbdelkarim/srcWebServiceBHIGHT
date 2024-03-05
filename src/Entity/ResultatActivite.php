<?php

namespace App\Entity;

use App\Repository\ResultatActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultatActiviteRepository::class)]
class ResultatActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activite $id_activite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_param = null;

    #[ORM\Column(length: 255)]
    private ?string $ordre = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column]
    private ?bool $skip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIdActivite(): ?Activite
    {
        return $this->id_activite;
    }

    public function setIdActivite(?Activite $id_activite): self
    {
        $this->id_activite = $id_activite;

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

    public function getOrdre(): ?string
    {
        return $this->ordre;
    }

    public function setOrdre(string $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function isSkip(): ?bool
    {
        return $this->skip;
    }

    public function setSkip(bool $skip): static
    {
        $this->skip = $skip;

        return $this;
    }
}
