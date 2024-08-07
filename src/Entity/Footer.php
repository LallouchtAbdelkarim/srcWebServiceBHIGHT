<?php

namespace App\Entity;

use App\Repository\FooterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FooterRepository::class)]
class Footer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $messsage = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMesssage(): ?string
    {
        return $this->messsage;
    }

    public function setMesssage(string $messsage): static
    {
        $this->messsage = $messsage;

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
}
