<?php

namespace App\Entity;

use App\Repository\DetailModelAffichageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailModelAffichageRepository::class)]
class DetailModelAffichage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelAffichage $id_model_affichage = null;

    #[ORM\Column(length: 255)]
    public ?string $table_name = null;

    #[ORM\Column(length: 255)]
    public ?string $champ_name = null;

    #[ORM\Column]
    private ?int $length = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(length: 255)]
    public ?string $type_creance = null;

    #[ORM\Column(length: 255)]
    public ?string $type_champ = null;

    #[ORM\Column]
    private ?bool $Required = null;

    #[ORM\OneToMany(mappedBy: 'champs_id', targetEntity: Champs::class)]
    private Collection $champs;

    public function __construct()
    {
        $this->champs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdModelAffichage(): ?ModelAffichage
    {
        return $this->id_model_affichage;
    }

    public function setIdModelAffichage(?ModelAffichage $id_model_affichage): self
    {
        $this->id_model_affichage = $id_model_affichage;

        return $this;
    }

    public function getTableName(): ?string
    {
        return $this->table_name;
    }

    public function setTableName(string $table_name): self
    {
        $this->table_name = $table_name;

        return $this;
    }

    public function getChampName(): ?string
    {
        return $this->champ_name;
    }

    public function setChampName(string $champ_name): self
    {
        $this->champ_name = $champ_name;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function getTypeCreance(): ?string
    {
        return $this->type_creance;
    }

    public function setTypeCreance(string $type_creance): self
    {
        $this->type_creance = $type_creance;

        return $this;
    }

    public function getTypeChamp(): ?string
    {
        return $this->type_champ;
    }

    public function setTypeChamp(string $type_champ): self
    {
        $this->type_champ = $type_champ;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->Required;
    } 

    public function setRequired(bool $Required): self
    {
        $this->Required = $Required;
        return $this;
    }

    /**
     * @return Collection<int, Champs>
     */
    public function getChamps(): Collection
    {
        return $this->champs;
    }

    public function addChamp(Champs $champ): self
    {
        if (!$this->champs->contains($champ)) {
            $this->champs->add($champ);
            $champ->setChampsId($this);
        }

        return $this;
    }

    public function removeChamp(Champs $champ): self
    {
        if ($this->champs->removeElement($champ)) {
            // set the owning side to null (unless already changed)
            if ($champ->getChampsId() === $this) {
                $champ->setChampsId(null);
            }
        }

        return $this;
    }


}
