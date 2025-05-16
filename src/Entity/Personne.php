<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    #[ORM\Column]
    private ?bool $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu_naissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\OneToMany(mappedBy: 'idPayeur', targetEntity: Accord::class)]
    private Collection $accords;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: CreanceActivite::class)]
    private Collection $creanceActivites;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    public function __construct()
    {
        $this->accords = new ArrayCollection();
        $this->creanceActivites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieu_naissance;
    }

    public function setLieuNaissance(string $lieu_naissance): static
    {
        $this->lieu_naissance = $lieu_naissance;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

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
     * @return Collection<int, Accord>
     */
    public function getAccords(): Collection
    {
        return $this->accords;
    }

    public function addAccord(Accord $accord): static
    {
        if (!$this->accords->contains($accord)) {
            $this->accords->add($accord);
            $accord->setIdPayeur($this);
        }

        return $this;
    }

    public function removeAccord(Accord $accord): static
    {
        if ($this->accords->removeElement($accord)) {
            // set the owning side to null (unless already changed)
            if ($accord->getIdPayeur() === $this) {
                $accord->setIdPayeur(null);
            }
        }

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
            $creanceActivite->setPersonne($this);
        }

        return $this;
    }

    public function removeCreanceActivite(CreanceActivite $creanceActivite): static
    {
        if ($this->creanceActivites->removeElement($creanceActivite)) {
            // set the owning side to null (unless already changed)
            if ($creanceActivite->getPersonne() === $this) {
                $creanceActivite->setPersonne(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
