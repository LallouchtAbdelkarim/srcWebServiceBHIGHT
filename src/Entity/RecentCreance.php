<?php

namespace App\Entity;

use App\Repository\RecentCreanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecentCreanceRepository::class)]
class RecentCreance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creance $creance_id = null;

    #[ORM\ManyToOne(inversedBy: 'recentCreances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getCreanceId(): ?Creance
    {
        return $this->creance_id;
    }

    public function setCreanceId(?Creance $creance_id): static
    {
        $this->creance_id = $creance_id;

        return $this;
    }

    public function getUserId(): ?Utilisateurs
    {
        return $this->user_id;
    }

    public function setUserId(?Utilisateurs $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }
}
