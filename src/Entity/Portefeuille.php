<?php

namespace App\Entity;

use App\Repository\PortefeuilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PortefeuilleRepository::class)]
class Portefeuille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DonneurOrdre $id_donneur_ordre = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroPtf = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne]
    private ?DetailsTypeCreance $id_type_creance = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut_gestion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin_gestion = null;

    // #[ORM\ManyToOne]
    // private ?ModelFacturation $id_model_fact = null;

    #[ORM\Column(length: 255)]
    private ?string $dureeGestion = null;

    #[ORM\Column]
    private ?int $actif = null;

    #[ORM\Column]
    private ?string $type_mission = null;

    // #[ORM\OneToMany(mappedBy: 'id_ptf', targetEntity: Dossier::class)]
    // private Collection $dossiers;

    // #[ORM\OneToMany(mappedBy: 'id_ptf', targetEntity: Integration::class)]
    // private Collection $integrations;

    // #[ORM\ManyToOne]
    // private ?DetailsSecteurActivite $id_detail_secteur_activite = null;

    // public function __construct()
    // {
    //     $this->dossiers = new ArrayCollection();
    //     $this->integrations = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDonneurOrdre(): ?DonneurOrdre
    {
        return $this->id_donneur_ordre;
    }

    public function setIdDonneurOrdre(?DonneurOrdre $id_donneur_ordre): self
    {
        $this->id_donneur_ordre = $id_donneur_ordre;

        return $this;
    }

    public function getNumeroPtf(): ?string
    {
        return $this->numeroPtf;
    }

    public function setNumeroPtf(string $numeroPtf): self
    {
        $this->numeroPtf = $numeroPtf;

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

    public function getDateDebutGestion(): ?\DateTimeInterface
    {
        return $this->date_debut_gestion;
    }

    public function setDateDebutGestion(\DateTimeInterface $date_debut_gestion): self
    {
        $this->date_debut_gestion = $date_debut_gestion;

        return $this;
    }

    public function getDateFinGestion(): ?\DateTimeInterface
    {
        return $this->date_fin_gestion;
    }

    public function setDateFinGestion(\DateTimeInterface $date_fin_gestion): self
    {
        $this->date_fin_gestion = $date_fin_gestion;

        return $this;
    }

    // public function getIdModelFact(): ?ModelFacturation
    // {
    //     return $this->id_model_fact;
    // }

    // public function setIdModelFact(?ModelFacturation $id_model_fact): self
    // {
    //     $this->id_model_fact = $id_model_fact;

    //     return $this;
    // }

    public function getDureeGestion(): ?string
    {
        return $this->dureeGestion;
    }

    public function setDureeGestion(string $dureeGestion): self
    {
        $this->dureeGestion = $dureeGestion;

        return $this;
    }

    public function getActif(): ?int
    {
        return $this->actif;
    }

    public function setActif(int $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
    public function getIdTypeCreance(): ?DetailsTypeCreance
    {
        return $this->id_type_creance;
    }

    public function setIdTypeCreance(?DetailsTypeCreance $id_type_creance): static
    {
        $this->id_type_creance = $id_type_creance;

        return $this;
    }

    public function getTypeMission(): ?string
    {
        return $this->type_mission;
    }

    public function setTypeMission(string $type_mission): self
    {
        $this->type_mission = $type_mission;

        return $this;
    }

    // /**
    //  * @return Collection<int, Dossier>
    //  */
    // public function getDossiers(): Collection
    // {
    //     return $this->dossiers;
    // }

    // public function addDossier(Dossier $dossier): static
    // {
    //     if (!$this->dossiers->contains($dossier)) {
    //         $this->dossiers->add($dossier);
    //         $dossier->setIdPtf($this);
    //     }

    //     return $this;
    // }

    // public function removeDossier(Dossier $dossier): static
    // {
    //     if ($this->dossiers->removeElement($dossier)) {
    //         // set the owning side to null (unless already changed)
    //         if ($dossier->getIdPtf() === $this) {
    //             $dossier->setIdPtf(null);
    //         }
    //     }

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Integration>
    //  */
    // public function getIntegrations(): Collection
    // {
    //     return $this->integrations;
    // }

    // public function addIntegration(Integration $integration): static
    // {
    //     if (!$this->integrations->contains($integration)) {
    //         $this->integrations->add($integration);
    //         $integration->setIdPtf($this);
    //     }

    //     return $this;
    // }

    // public function removeIntegration(Integration $integration): static
    // {
    //     if ($this->integrations->removeElement($integration)) {
    //         // set the owning side to null (unless already changed)
    //         if ($integration->getIdPtf() === $this) {
    //             $integration->setIdPtf(null);
    //         }
    //     }

    //     return $this;
    // }

    // public function getIdDetailSecteurActivite(): ?DetailsSecteurActivite
    // {
    //     return $this->id_detail_secteur_activite;
    // }

    // public function setIdDetailSecteurActivite(?DetailsSecteurActivite $id_detail_secteur_activite): static
    // {
    //     $this->id_detail_secteur_activite = $id_detail_secteur_activite;

    //     return $this;
    // }
}
