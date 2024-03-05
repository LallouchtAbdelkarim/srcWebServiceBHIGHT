<?php

namespace App\Entity;

use App\Repository\CourrierCampagneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourrierCampagneRepository::class)]
class CourrierCampagne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $etat_retour = null;

    #[ORM\ManyToOne]
    private ?dossier $id_dossier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?debiteur $id_debiteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?courrier $id_courrier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtatRetour(): ?int
    {
        return $this->etat_retour;
    }

    public function setEtatRetour(int $etat_retour): static
    {
        $this->etat_retour = $etat_retour;

        return $this;
    }

    public function getIdDossier(): ?dossier
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?dossier $id_dossier): static
    {
        $this->id_dossier = $id_dossier;

        return $this;
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

    public function getIdCourrier(): ?courrier
    {
        return $this->id_courrier;
    }

    public function setIdCourrier(?courrier $id_courrier): static
    {
        $this->id_courrier = $id_courrier;

        return $this;
    }
}
