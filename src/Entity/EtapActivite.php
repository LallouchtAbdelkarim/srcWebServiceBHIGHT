<?php

namespace App\Entity;

use App\Repository\EtapActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtapActiviteRepository::class)]
class EtapActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: true)]
    // private ?EtatActivite $Etat_attendu = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Activite $id_activite = null;

    #[ORM\Column(nullable: true)]
    private ?int $Etat = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?ParamActivite $id_param = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etap $idEtap = null;

    // #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?ResultatActivite $id_result = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // public function getEtatAttendu(): ?EtatActivite
    // {
    //     return $this->Etat_attendu;
    // }

    // public function setEtatAttendu(?EtatActivite $Etat_attendu): self
    // {
    //     $this->Etat_attendu = $Etat_attendu;

    //     return $this;
    // }

    public function getIdActivite(): ?Activite
    {
        return $this->id_activite;
    }

    public function setIdActivite(?Activite $id_activite): self
    {
        $this->id_activite = $id_activite;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->Etat;
    }

    public function setEtat(?int $Etat): self
    {
        $this->Etat = $Etat;

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

    // public function getIdResult(): ?ResultatActivite
    // {
    //     return $this->id_result;
    // }

    // public function setIdResult(?ResultatActivite $id_result): self
    // {
    //     $this->id_result = $id_result;

    //     return $this;
    // }

    public function getIdParam(): ?ParamActivite
    {
        return $this->id_param;
    }

    public function setIdParam(?ParamActivite $id_param): self
    {
        $this->id_param = $id_param;

        return $this;
    }

    public function getIdEtap(): ?Etap
    {
        return $this->idEtap;
    }

    public function setIdEtap(?Etap $idEtap): static
    {
        $this->idEtap = $idEtap;

        return $this;
    }
}
