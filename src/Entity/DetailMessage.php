<?php

namespace App\Entity;

use App\Repository\DetailMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailMessageRepository::class)]
class DetailMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Message $Message = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $Expediteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $Distinataire = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateEnvoi = null;

    #[ORM\Column]
    private ?bool $isRecu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateRecu = null;

    #[ORM\Column]
    private ?bool $isLu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateLu = null;

    #[ORM\Column]
    private ?bool $isDeleted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?Message
    {
        return $this->Message;
    }

    public function setMessage(?Message $Message): static
    {
        $this->Message = $Message;

        return $this;
    }

    public function getExpediteur(): ?Utilisateurs
    {
        return $this->Expediteur;
    }

    public function setExpediteur(?Utilisateurs $Expediteur): static
    {
        $this->Expediteur = $Expediteur;

        return $this;
    }

    public function getDistinataire(): ?Utilisateurs
    {
        return $this->Distinataire;
    }

    public function setDistinataire(?Utilisateurs $Distinataire): static
    {
        $this->Distinataire = $Distinataire;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->DateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $DateEnvoi): static
    {
        $this->DateEnvoi = $DateEnvoi;

        return $this;
    }

    public function isIsRecu(): ?bool
    {
        return $this->isRecu;
    }

    public function setIsRecu(bool $isRecu): static
    {
        $this->isRecu = $isRecu;

        return $this;
    }

    public function getDateRecu(): ?\DateTimeInterface
    {
        return $this->DateRecu;
    }

    public function setDateRecu(?\DateTimeInterface $DateRecu): static
    {
        $this->DateRecu = $DateRecu;

        return $this;
    }

    public function isIsLu(): ?bool
    {
        return $this->isLu;
    }

    public function setIsLu(bool $isLu): static
    {
        $this->isLu = $isLu;

        return $this;
    }

    public function getDateLu(): ?\DateTimeInterface
    {
        return $this->DateLu;
    }

    public function setDateLu(?\DateTimeInterface $DateLu): static
    {
        $this->DateLu = $DateLu;

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
