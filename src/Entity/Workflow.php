<?php

namespace App\Entity;

use App\Repository\WorkflowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkflowRepository::class)]
class Workflow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $id_user = null;

    #[ORM\OneToMany(mappedBy: 'id_workflow', targetEntity: NoteWorkflow::class)]
    private Collection $n;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_start = null;

    #[ORM\ManyToOne]
    private ?StatusWorkflow $id_status = null;

    public function __construct()
    {
        $this->n = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getIdUser(): ?Utilisateurs
    {
        return $this->id_user;
    }

    public function setIdUser(?Utilisateurs $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

   
    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(?\DateTimeInterface $date_start): static
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getIdStatus(): ?StatusWorkflow
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusWorkflow $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }
}
