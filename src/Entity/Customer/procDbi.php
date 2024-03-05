<?php

namespace App\Entity\Customer;

use App\Repository\Customer\procDbiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: procDbiRepository::class)]
class procDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_depot = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero_judicaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type_proc_judicaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->date_depot;
    }

    public function setDateDepot(?\DateTimeInterface $date_depot): static
    {
        $this->date_depot = $date_depot;

        return $this;
    }

    public function getNumeroJudicaire(): ?string
    {
        return $this->numero_judicaire;
    }

    public function setNumeroJudicaire(?string $numero_judicaire): static
    {
        $this->numero_judicaire = $numero_judicaire;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeProcJudicaire(): ?string
    {
        return $this->type_proc_judicaire;
    }

    public function setTypeProcJudicaire(?string $type_proc_judicaire): static
    {
        $this->type_proc_judicaire = $type_proc_judicaire;

        return $this;
    }
    public function getIdImport(): ?int
    {
        return $this->id_import;
    }

    public function setIdImport(int $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(?int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }
}
