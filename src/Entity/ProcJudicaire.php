<?php

namespace App\Entity;

use App\Repository\ProcJudicaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProcJudicaireRepository::class)]
class ProcJudicaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_depot = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero_judicaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type_proc_judicaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_proc_dbi = null;

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

    public function getTypeProcJudicaire(): ?int
    {
        return $this->type_proc_judicaire;
    }

    public function setTypeProcJudicaire(?int $type_proc_judicaire): static
    {
        $this->type_proc_judicaire = $type_proc_judicaire;

        return $this;
    }

    public function getIdProcDbi(): ?int
    {
        return $this->id_proc_dbi;
    }

    public function setIdProcDbi(?int $id_proc_dbi): static
    {
        $this->id_proc_dbi = $id_proc_dbi;

        return $this;
    }
}
