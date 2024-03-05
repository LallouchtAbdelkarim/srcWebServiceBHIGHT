<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\OneToMany(mappedBy: 'id_group', targetEntity: Utilisateurs::class)]
    private Collection $utilisateurs;

    #[ORM\OneToMany(mappedBy: 'id_group', targetEntity: GroupProfil::class)]
    private Collection $groupProfils;

    

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->groupProfils = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
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

    /**
     * @return Collection<int, Utilisateurs>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateurs $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setIdGroup($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateurs $utilisateur): self
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getIdGroup() === $this) {
                $utilisateur->setIdGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupProfil>
     */
    public function getGroupProfils(): Collection
    {
        return $this->groupProfils;
    }

    public function addGroupProfil(GroupProfil $groupProfil): self
    {
        if (!$this->groupProfils->contains($groupProfil)) {
            $this->groupProfils->add($groupProfil);
            $groupProfil->setIdGroup($this);
        }

        return $this;
    }

    public function removeGroupProfil(GroupProfil $groupProfil): self
    {
        if ($this->groupProfils->removeElement($groupProfil)) {
            // set the owning side to null (unless already changed)
            if ($groupProfil->getIdGroup() === $this) {
                $groupProfil->setIdGroup(null);
            }
        }

        return $this;
    }

    
}
