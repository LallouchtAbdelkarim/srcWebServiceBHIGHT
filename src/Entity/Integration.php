<?php

namespace App\Entity;

use App\Repository\IntegrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntegrationRepository::class)]
class Integration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_execution = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin_execution = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\ManyToOne]
    private ?Portefeuille $id_ptf = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProcessIntegration $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin_execution_1 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin_execution_2 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin_execution_3 = null;

    #[ORM\Column]
    private ?int $isMaj = null;

    #[ORM\Column(nullable: true)]
    private ?int $type = null;
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateExecution(): ?\DateTimeInterface
    {
        return $this->date_execution;
    }

    public function setDateExecution(?\DateTimeInterface $date_execution): self
    {
        $this->date_execution = $date_execution;

        return $this;
    }

    public function getDateFinExecution(): ?\DateTimeInterface
    {
        return $this->date_fin_execution;
    }

    public function setDateFinExecution(?\DateTimeInterface $date_fin_execution): self
    {
        $this->date_fin_execution = $date_fin_execution;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getIdPtf(): ?Portefeuille
    {
        return $this->id_ptf;
    }

    public function setIdPtf(?Portefeuille $id_ptf): static
    {
        $this->id_ptf = $id_ptf;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getStatus(): ?ProcessIntegration
    {
        return $this->status;
    }

    public function setStatus(?ProcessIntegration $status): static
    {
        $this->status = $status;

        return $this;
    }
    public function getDateFinExecution1(): ?\DateTimeInterface
    {
        return $this->date_fin_execution_1;
    }

    public function setDateFinExecution1(?\DateTimeInterface $date_fin_execution_1): static
    {
        $this->date_fin_execution_1 = $date_fin_execution_1;

        return $this;
    }

    public function getDateFinExecution2(): ?\DateTimeInterface
    {
        return $this->date_fin_execution_2;
    }

    public function setDateFinExecution2(?\DateTimeInterface $date_fin_execution_2): static
    {
        $this->date_fin_execution_2 = $date_fin_execution_2;

        return $this;
    }

    public function getDateFinExecution3(): ?\DateTimeInterface
    {
        return $this->date_fin_execution_3;
    }

    public function setDateFinExecution3(?\DateTimeInterface $date_fin_execution_3): static
    {
        $this->date_fin_execution_3 = $date_fin_execution_3;

        return $this;
    }

    public function getIsMaj(): ?int
    {
        return $this->isMaj;
    }

    public function setIsMaj(int $isMaj): static
    {
        $this->isMaj = $isMaj;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): static
    {
        $this->type = $type;

        return $this;
    }

    }
