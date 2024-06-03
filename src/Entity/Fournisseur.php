<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomFour = null;

    #[ORM\Column(length: 255)]
    private ?string $teleFour = null;

    #[ORM\Column(length: 255)]
    private ?string $emaiFour = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rcFour = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iceFour = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adreFour = null;

    #[ORM\Column(length: 255)]
    private ?string $prenFour = null;

    #[ORM\Column(length: 255)]
    private ?string $RaiSocFo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFour(): ?string
    {
        return $this->nomFour;
    }

    public function setNomFour(string $nomFour): static
    {
        $this->nomFour = $nomFour;

        return $this;
    }

    public function getTeleFour(): ?string
    {
        return $this->teleFour;
    }

    public function setTeleFour(string $teleFour): static
    {
        $this->teleFour = $teleFour;

        return $this;
    }

    public function getEmaiFour(): ?string
    {
        return $this->emaiFour;
    }

    public function setEmaiFour(string $emaiFour): static
    {
        $this->emaiFour = $emaiFour;

        return $this;
    }

    public function getRcFour(): ?string
    {
        return $this->rcFour;
    }

    public function setRcFour(?string $rcFour): static
    {
        $this->rcFour = $rcFour;

        return $this;
    }

    public function getIceFour(): ?string
    {
        return $this->iceFour;
    }

    public function setIceFour(?string $iceFour): static
    {
        $this->iceFour = $iceFour;

        return $this;
    }

    public function getAdreFour(): ?string
    {
        return $this->adreFour;
    }

    public function setAdreFour(?string $adreFour): static
    {
        $this->adreFour = $adreFour;

        return $this;
    }

    public function getPrenFour(): ?string
    {
        return $this->prenFour;
    }

    public function setPrenFour(string $prenFour): static
    {
        $this->prenFour = $prenFour;

        return $this;
    }

    public function getRaiSocFo(): ?string
    {
        return $this->RaiSocFo;
    }

    public function setRaiSocFo(string $RaiSocFo): static
    {
        $this->RaiSocFo = $RaiSocFo;

        return $this;
    }
}
