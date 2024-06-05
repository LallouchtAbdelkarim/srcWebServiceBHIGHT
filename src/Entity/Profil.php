<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_creation = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;


    #[ORM\OneToMany(mappedBy: 'id_profil', targetEntity: Roles::class)]
    private Collection $roles;

    #[ORM\OneToMany(mappedBy: 'id_profil', targetEntity: GroupProfil::class)]
    private Collection $groupProfils;

    #[ORM\OneToMany(mappedBy: 'id_profil', targetEntity: CompetenceProfil::class)]
    private Collection $competenceProfils;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->groupProfils = new ArrayCollection();
        $this->competenceProfils = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->Date_creation;
    }

    public function setDateCreation(\DateTimeInterface $Date_creation): self
    {
        $this->Date_creation = $Date_creation;

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
     * @return Collection<int, Roles>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Roles $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->setIdProfil($this);
        }

        return $this;
    }

    public function removeRole(Roles $role): self
    {
        if ($this->roles->removeElement($role)) {
            // set the owning side to null (unless already changed)
            if ($role->getIdProfil() === $this) {
                $role->setIdProfil(null);
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
            $groupProfil->setIdProfil($this);
        }

        return $this;
    }

    public function removeGroupProfil(GroupProfil $groupProfil): self
    {
        if ($this->groupProfils->removeElement($groupProfil)) {
            // set the owning side to null (unless already changed)
            if ($groupProfil->getIdProfil() === $this) {
                $groupProfil->setIdProfil(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CompetenceProfil>
     */
    public function getCompetenceProfils(): Collection
    {
        return $this->competenceProfils;
    }

    public function addCompetenceProfil(CompetenceProfil $competenceProfil): self
    {
        if (!$this->competenceProfils->contains($competenceProfil)) {
            $this->competenceProfils->add($competenceProfil);
            $competenceProfil->setIdProfil($this);
        }

        return $this;
    }

    public function removeCompetenceProfil(CompetenceProfil $competenceProfil): self
    {
        if ($this->competenceProfils->removeElement($competenceProfil)) {
            // set the owning side to null (unless already changed)
            if ($competenceProfil->getIdProfil() === $this) {
                $competenceProfil->setIdProfil(null);
            }
        }

        return $this;
    }
}
