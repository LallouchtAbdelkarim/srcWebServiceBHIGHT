<?php

namespace App\Entity;

use App\Repository\AccordNotesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccordNotesRepository::class)]
class AccordNotes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNote = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'accordNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Accord $idAccord = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDateNote(): ?\DateTimeInterface
    {
        return $this->dateNote;
    }

    public function setDateNote(?\DateTimeInterface $dateNote): static
    {
        $this->dateNote = $dateNote;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getIdAccord(): ?Accord
    {
        return $this->idAccord;
    }

    public function setIdAccord(?Accord $idAccord): static
    {
        $this->idAccord = $idAccord;

        return $this;
    }
}
