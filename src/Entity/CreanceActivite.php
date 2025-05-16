<?php

namespace App\Entity;

use App\Repository\CreanceActiviteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreanceActiviteRepository::class)]
class CreanceActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Creance $id_creance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParamActivite $id_param_activite = null;

    #[ORM\Column(nullable: true)]
    private ?int $assigned_type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $createdBy = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'creanceActivites')]
    private ?ParamActivite $id_param_parent = null;

    #[ORM\Column]
    private ?int $typeActivite = null;

    #[ORM\ManyToOne(inversedBy: 'creanceActivites')]
    private ?Debiteur $debiteur = null;

    #[ORM\ManyToOne(inversedBy: 'creanceActivites')]
    private ?Personne $personne = null;

    #[ORM\ManyToOne(inversedBy: 'creanceActivites')]
    private ?Telephone $telephone = null;

    #[ORM\ManyToOne(inversedBy: 'creanceActivites')]
    private ?Email $email = null;

    #[ORM\ManyToOne(inversedBy: 'creanceActivites')]
    private ?Adresse $adresse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreance(): ?Creance
    {
        return $this->id_creance;
    }

    public function setIdCreance(?Creance $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getIdParamActivite(): ?ParamActivite
    {
        return $this->id_param_activite;
    }

    public function setIdParamActivite(?ParamActivite $id_param_activite): static
    {
        $this->id_param_activite = $id_param_activite;

        return $this;
    }

    public function getAssignedType(): ?int
    {
        return $this->assigned_type;
    }

    public function setAssignedType(?int $assigned_type): static
    {
        $this->assigned_type = $assigned_type;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }
    public function getCreatedBy(): ?Utilisateurs
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Utilisateurs $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getIdParamParent(): ?ParamActivite
    {
        return $this->id_param_parent;
    }

    public function setIdParamParent(?ParamActivite $id_param_parent): static
    {
        $this->id_param_parent = $id_param_parent;

        return $this;
    }

    public function getTypeActivite(): ?int
    {
        return $this->typeActivite;
    }

    public function setTypeActivite(int $typeActivite): static
    {
        $this->typeActivite = $typeActivite;

        return $this;
    }

    public function getDebiteur(): ?Debiteur
    {
        return $this->debiteur;
    }

    public function setDebiteur(?Debiteur $debiteur): static
    {
        $this->debiteur = $debiteur;

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): static
    {
        $this->personne = $personne;

        return $this;
    }

    public function getTelephone(): ?Telephone
    {
        return $this->telephone;
    }

    public function setTelephone(?Telephone $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function setEmail(?Email $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }
}
