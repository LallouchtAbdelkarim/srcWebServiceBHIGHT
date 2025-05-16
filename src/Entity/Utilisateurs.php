<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
class Utilisateurs
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
    private ?string $password = null;

    #[ORM\Column( nullable: true)]
    private ?int $status = null;

    #[ORM\Column(length: 255)]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imei = null;

    #[ORM\Column(length: 255)]
    private ?string $mobile = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rayon = null;

    #[ORM\Column(length: 255)]
    private ?string $cin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    // #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    // #[ORM\JoinColumn(onDelete:"CASCADE")]
    #[ORM\ManyToOne]
    private ?Groupe $id_group = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    // #[ORM\Column]
    // private ?bool $superAdmin = null;

    #[ORM\ManyToOne]
    private ?TypeUtilisateur $id_type_user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Competence $id_competence = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?TypeUtilisateur $responsable = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Departement $id_departement = null;

    #[ORM\Column(length: 255)]
    private ?string $services = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: RecentCreance::class)]
    private Collection $recentCreances;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Teams $teams = null;

    #[ORM\OneToMany(mappedBy: 'assignedUser', targetEntity: Task::class)]
    private Collection $tasks;

    public function __construct()
    {
        $this->recentCreances = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getImei(): ?string
    {
        return $this->imei;
    }

    public function setImei(string $imei): self
    {
        $this->imei = $imei;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getRayon(): ?string
    {
        return $this->rayon;
    }

    public function setRayon(string $rayon): self
    {
        $this->rayon = $rayon;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getIdGroup(): ?Groupe
    {
        return $this->id_group;
    }

    public function setIdGroup(?Groupe $id_group): self
    {
        $this->id_group = $id_group;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

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

    // public function isSuperAdmin(): ?bool
    // {
    //     return $this->superAdmin;
    // }

    // public function setSuperAdmin(bool $superAdmin): static
    // {
    //     $this->superAdmin = $superAdmin;

    //     return $this;
    // }

    public function getIdTypeUser(): ?TypeUtilisateur
    {
        return $this->id_type_user;
    }

    public function setIdTypeUser(?TypeUtilisateur $id_type_user): static
    {
        $this->id_type_user = $id_type_user;

        return $this;
    }

    public function getIdCompetence(): ?Competence
    {
        return $this->id_competence;
    }

    public function setIdCompetence(?Competence $id_competence): static
    {
        $this->id_competence = $id_competence;

        return $this;
    }

    public function getResponsable(): ?TypeUtilisateur
    {
        return $this->responsable;
    }

    public function setResponsable(?TypeUtilisateur $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getIdDepartement(): ?Departement
    {
        return $this->id_departement;
    }

    public function setIdDepartement(?Departement $id_departement): static
    {
        $this->id_departement = $id_departement;

        return $this;
    }

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function setServices(string $services): static
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @return Collection<int, RecentCreance>
     */
    public function getRecentCreances(): Collection
    {
        return $this->recentCreances;
    }

    public function addRecentCreance(RecentCreance $recentCreance): static
    {
        if (!$this->recentCreances->contains($recentCreance)) {
            $this->recentCreances->add($recentCreance);
            $recentCreance->setUserId($this);
        }

        return $this;
    }

    public function removeRecentCreance(RecentCreance $recentCreance): static
    {
        if ($this->recentCreances->removeElement($recentCreance)) {
            // set the owning side to null (unless already changed)
            if ($recentCreance->getUserId() === $this) {
                $recentCreance->setUserId(null);
            }
        }

        return $this;
    }

    public function getTeams(): ?Teams
    {
        return $this->teams;
    }

    public function setTeams(?Teams $teams): static
    {
        $this->teams = $teams;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setAssignedUser($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getAssignedUser() === $this) {
                $task->setAssignedUser(null);
            }
        }

        return $this;
    }
}
