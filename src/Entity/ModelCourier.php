<?php

namespace App\Entity;

use App\Repository\ModelCourierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModelCourierRepository::class)]
class ModelCourier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 4000)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    private ?string $objet = null;

    #[ORM\ManyToOne]
    private ?BackgroundCourrier $idBackground = null;

    #[ORM\ManyToOne]
    private ?Header $id_header = null;

    #[ORM\ManyToOne]
    private ?Footer $id_footer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getIdBackground(): ?BackgroundCourrier
    {
        return $this->idBackground;
    }

    public function setIdBackground(?BackgroundCourrier $idBackground): static
    {
        $this->idBackground = $idBackground;

        return $this;
    }

    public function getIdHeader(): ?Header
    {
        return $this->id_header;
    }

    public function setIdHeader(?Header $id_header): static
    {
        $this->id_header = $id_header;

        return $this;
    }

    public function getIdFooter(): ?Footer
    {
        return $this->id_footer;
    }

    public function setIdFooter(?Footer $id_footer): static
    {
        $this->id_footer = $id_footer;

        return $this;
    }
}
