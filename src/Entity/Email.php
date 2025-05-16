<?php

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
class Email
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Debiteur $id_debiteur = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeEmail $id_type_email = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\ManyToOne]
    private ?TypeSource $id_type_source = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusEmail $id_status_email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\OneToMany(mappedBy: 'email', targetEntity: CreanceActivite::class)]
    private Collection $creanceActivites;

    public function __construct()
    {
        $this->creanceActivites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDebiteur(): ?debiteur
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?debiteur $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIdTypeEmail(): ?TypeEmail
    {
        return $this->id_type_email;
    }

    public function setIdTypeEmail(?TypeEmail $id_type_email): static
    {
        $this->id_type_email = $id_type_email;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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

    public function getIdTypeSource(): ?TypeSource
    {
        return $this->id_type_source;
    }

    public function setIdTypeSource(?TypeSource $id_type_source): static
    {
        $this->id_type_source = $id_type_source;

        return $this;
    }

    public function getIdStatusEmail(): ?StatusEmail
    {
        return $this->id_status_email;
    }

    public function setIdStatusEmail(?StatusEmail $id_status_email): static
    {
        $this->id_status_email = $id_status_email;

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

    /**
     * @return Collection<int, CreanceActivite>
     */
    public function getCreanceActivites(): Collection
    {
        return $this->creanceActivites;
    }

    public function addCreanceActivite(CreanceActivite $creanceActivite): static
    {
        if (!$this->creanceActivites->contains($creanceActivite)) {
            $this->creanceActivites->add($creanceActivite);
            $creanceActivite->setEmail($this);
        }

        return $this;
    }

    public function removeCreanceActivite(CreanceActivite $creanceActivite): static
    {
        if ($this->creanceActivites->removeElement($creanceActivite)) {
            // set the owning side to null (unless already changed)
            if ($creanceActivite->getEmail() === $this) {
                $creanceActivite->setEmail(null);
            }
        }

        return $this;
    }
}
