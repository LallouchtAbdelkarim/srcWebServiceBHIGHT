<?php

namespace App\Entity;

use App\Repository\ImportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportRepository::class)]
class Import
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_execution = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE ,nullable: true)]
    private ?\DateTimeInterface $date_fin_execution = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Integration $id_integration = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelImport $id_model = null;

    #[ORM\Column]
    private ?int $order_import = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbr_lignes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    public function setDateFinExecution(\DateTimeInterface $date_fin_execution): self
    {
        $this->date_fin_execution = $date_fin_execution;

        return $this;
    }

    public function getIdIntegration(): ?Integration
    {
        return $this->id_integration;
    }

    public function setIdIntegration(?Integration $id_integration): self
    {
        $this->id_integration = $id_integration;

        return $this;
    }

    public function getIdModel(): ?ModelImport
    {
        return $this->id_model;
    }

    public function setIdModel(?ModelImport $id_model): self
    {
        $this->id_model = $id_model;

        return $this;
    }

    public function getOrderImport(): ?int
    {
        return $this->order_import;
    }

    public function setOrderImport(int $order_import): static
    {
        $this->order_import = $order_import;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNbrLignes(): ?int
    {
        return $this->nbr_lignes;
    }

    public function setNbrLignes(?int $nbr_lignes): static
    {
        $this->nbr_lignes = $nbr_lignes;

        return $this;
    }
}
