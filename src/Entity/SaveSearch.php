<?php

namespace App\Entity;

use App\Repository\SaveSearchRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaveSearchRepository::class)]
class SaveSearch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $id_user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $query = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $for_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?Utilisateurs
    {
        return $this->id_user;
    }

    public function setIdUser(?Utilisateurs $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(string $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getForUser(): ?Utilisateurs
    {
        return $this->for_user;
    }

    public function setForUser(?Utilisateurs $for_user): static
    {
        $this->for_user = $for_user;

        return $this;
    }
}
