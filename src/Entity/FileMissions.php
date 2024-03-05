<?php

namespace App\Entity;

use App\Repository\FileMissionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileMissionsRepository::class)]
class FileMissions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?ModelMissions $id_model = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeMissions $id_type_missions = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $id_users = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getIdModel(): ?ModelMissions
    {
        return $this->id_model;
    }

    public function setIdModel(?ModelMissions $id_model): static
    {
        $this->id_model = $id_model;

        return $this;
    }

    public function getIdTypeMissions(): ?TypeMissions
    {
        return $this->id_type_missions;
    }

    public function setIdTypeMissions(?TypeMissions $id_type_missions): static
    {
        $this->id_type_missions = $id_type_missions;

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

    public function getIdUsers(): ?Utilisateurs
    {
        return $this->id_users;
    }

    public function setIdUsers(?Utilisateurs $id_users): static
    {
        $this->id_users = $id_users;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }
}
