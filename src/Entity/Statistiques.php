<?php

namespace App\Entity;

use App\Repository\StatistiquesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatistiquesRepository::class)]
class Statistiques
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $creance_non_success = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $creance_success = null;

    #[ORM\Column(length: 255)]
    private ?string $nbr_process_groupe = null;

    #[ORM\Column(length: 255)]
    private ?string $nbr_process_by_user = null;

    #[ORM\Column(length: 255)]
    private ?string $nbr_paiement = null;

    #[ORM\Column(length: 255)]
    private ?string $nbr_actions = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nbr_accord_terminee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nbr_accord_echeance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nbr_total_creance = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_users = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreanceNonSuccess(): ?string
    {
        return $this->creance_non_success;
    }

    public function setCreanceNonSuccess(string $creance_non_success): static
    {
        $this->creance_non_success = $creance_non_success;

        return $this;
    }

    public function getCreanceSuccess(): ?string
    {
        return $this->creance_success;
    }

    public function setCreanceSuccess(?string $creance_success): static
    {
        $this->creance_success = $creance_success;

        return $this;
    }

    public function getNbrProcessGroupe(): ?string
    {
        return $this->nbr_process_groupe;
    }

    public function setNbrProcessGroupe(string $nbr_process_groupe): static
    {
        $this->nbr_process_groupe = $nbr_process_groupe;

        return $this;
    }

    public function getNbrProcessByUser(): ?string
    {
        return $this->nbr_process_by_user;
    }

    public function setNbrProcessByUser(string $nbr_process_by_user): static
    {
        $this->nbr_process_by_user = $nbr_process_by_user;

        return $this;
    }

    public function getNbrPaiement(): ?string
    {
        return $this->nbr_paiement;
    }

    public function setNbrPaiement(string $nbr_paiement): static
    {
        $this->nbr_paiement = $nbr_paiement;

        return $this;
    }

    public function getNbrActions(): ?string
    {
        return $this->nbr_actions;
    }

    public function setNbrActions(string $nbr_actions): static
    {
        $this->nbr_actions = $nbr_actions;

        return $this;
    }

    public function getNbrAccordTerminee(): ?string
    {
        return $this->nbr_accord_terminee;
    }

    public function setNbrAccordTerminee(?string $nbr_accord_terminee): static
    {
        $this->nbr_accord_terminee = $nbr_accord_terminee;

        return $this;
    }

    public function getNbrAccordEcheance(): ?string
    {
        return $this->nbr_accord_echeance;
    }

    public function setNbrAccordEcheance(?string $nbr_accord_echeance): static
    {
        $this->nbr_accord_echeance = $nbr_accord_echeance;

        return $this;
    }

    public function getNbrTotalCreance(): ?string
    {
        return $this->nbr_total_creance;
    }

    public function setNbrTotalCreance(?string $nbr_total_creance): static
    {
        $this->nbr_total_creance = $nbr_total_creance;

        return $this;
    }

    public function getIdUsers(): ?int
    {
        return $this->id_users;
    }

    public function setIdUsers(?int $id_users): static
    {
        $this->id_users = $id_users;

        return $this;
    }
}
