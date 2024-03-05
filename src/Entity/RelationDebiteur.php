<?php

namespace App\Entity;

use App\Repository\RelationDebiteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RelationDebiteurRepository::class)]
class RelationDebiteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Debiteur $id_debiteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $id_personne = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Relation $id_relation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDebiteur(): ?Debiteur
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?Debiteur $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getIdPersonne(): ?Personne
    {
        return $this->id_personne;
    }

    public function setIdPersonne(?Personne $id_personne): static
    {
        $this->id_personne = $id_personne;

        return $this;
    }

    public function getIdRelation(): ?Relation
    {
        return $this->id_relation;
    }

    public function setIdRelation(?Relation $id_relation): static
    {
        $this->id_relation = $id_relation;

        return $this;
    }
}
