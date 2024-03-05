<?php

namespace App\Entity\Customer;

use App\Repository\Customer\DetailsAccordPaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsAccordPaiementRepository::class)]
class DetailsAccordPaiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_detsils_accord = null;

    #[ORM\Column]
    private ?int $id_import = null;

    #[ORM\Column]
    private ?int $id_action = null;

    #[ORM\Column]
    private ?float $oldMontantPaiement = null;

    #[ORM\Column]
    private ?int $newMontantPaiement = null;

    #[ORM\Column]
    private ?float $newMontantRestant = null;

    #[ORM\Column]
    private ?float $oldMontantRestant = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetailsAccord(): ?int
    {
        return $this->id_detsils_accord;
    }

    public function setIdDetailsAccord(int $id_detsils_accord): static
    {
        $this->id_detsils_accord = $id_detsils_accord;

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

    public function getIdAction(): ?int
    {
        return $this->id_action;
    }

    public function setIdAction(int $id_action): static
    {
        $this->id_action = $id_action;

        return $this;
    }

    public function getOldMontantPaiement(): ?float
    {
        return $this->oldMontantPaiement;
    }

    public function setOldMontantPaiement(float $oldMontantPaiement): static
    {
        $this->oldMontantPaiement = $oldMontantPaiement;

        return $this;
    }

    public function getNewMontantPaiement(): ?int
    {
        return $this->newMontantPaiement;
    }

    public function setNewMontantPaiement(int $newMontantPaiement): static
    {
        $this->newMontantPaiement = $newMontantPaiement;

        return $this;
    }

    public function getNewMontantRestant(): ?float
    {
        return $this->newMontantRestant;
    }

    public function setNewMontantRestant(float $newMontantRestant): static
    {
        $this->newMontantRestant = $newMontantRestant;

        return $this;
    }

    public function getOldMontantRestant(): ?float
    {
        return $this->oldMontantRestant;
    }

    public function setOldMontantRestant(float $oldMontantRestant): static
    {
        $this->oldMontantRestant = $oldMontantRestant;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
