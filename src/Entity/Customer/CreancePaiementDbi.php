<?php

namespace App\Entity\Customer;

use App\Repository\Customer\CreancePaiementDbiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreancePaiementDbiRepository::class)]
class CreancePaiementDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_creance = null;

    #[ORM\Column]
    private ?float $oldTotalRestant = null;

    #[ORM\Column]
    private ?float $newTotalRestant = null;

    #[ORM\Column]
    private ?float $montantPaiement = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?bool $isUpdatedInThisAction = null;

    #[ORM\Column]
    private ?int $id_action = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreance(): ?int
    {
        return $this->id_creance;
    }

    public function setIdCreance(int $id_creance): static
    {
        $this->id_creance = $id_creance;

        return $this;
    }

    public function getOldTotalRestant(): ?float
    {
        return $this->oldTotalRestant;
    }

    public function setOldTotalRestant(float $oldTotalRestant): static
    {
        $this->oldTotalRestant = $oldTotalRestant;

        return $this;
    }

    public function getNewTotalRestant(): ?float
    {
        return $this->newTotalRestant;
    }

    public function setNewTotalRestant(float $newTotalRestant): static
    {
        $this->newTotalRestant = $newTotalRestant;

        return $this;
    }

    public function getMontantPaiement(): ?float
    {
        return $this->montantPaiement;
    }

    public function setMontantPaiement(float $montantPaiement): static
    {
        $this->montantPaiement = $montantPaiement;

        return $this;
    }

    public function getIdImport(): ?int
    {
        return $this->id_import;
    }

    public function setIdImport(int $id_import): static
    {
        $this->id_import = $id_import;

        return $this;
    }

    public function isIsUpdatedInThisAction(): ?bool
    {
        return $this->isUpdatedInThisAction;
    }

    public function setIsUpdatedInThisAction(bool $isUpdatedInThisAction): static
    {
        $this->isUpdatedInThisAction = $isUpdatedInThisAction;

        return $this;
    }

    public function getIdAction(): ?int
    {
        return $this->id_action;
    }

    public function setIdAction(int $id_action): static
    {
        $this->id_action = $id_action;

        return $this;
    }
}
