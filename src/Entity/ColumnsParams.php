<?php

namespace App\Entity;

use App\Repository\ColumnsParamsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ColumnsParamsRepository::class)]
class ColumnsParams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre_col = null;

    #[ORM\Column(length: 255)]
    private ?string $table_bdd = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $is_date = null;

    #[ORM\OneToMany(mappedBy: 'id_col_params', targetEntity: CorresColu::class)]
    private Collection $corresColus;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type_param = null;

    public function __construct()
    {
        $this->corresColus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreCol(): ?string
    {
        return $this->titre_col;
    }

    public function setTitreCol(string $titre_col): self
    {
        $this->titre_col = $titre_col;

        return $this;
    }

    public function getTableBdd(): ?string
    {
        return $this->table_bdd;
    }

    public function setTableBdd(string $table_bdd): self
    {
        $this->table_bdd = $table_bdd;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getIsDate(): ?string
    {
        return $this->is_date;
    }

    public function setIsDate(string $is_date): static
    {
        $this->is_date = $is_date;

        return $this;
    }

    /**
     * @return Collection<int, CorresColu>
     */
    public function getCorresColus(): Collection
    {
        return $this->corresColus;
    }

    public function addCorresColu(CorresColu $corresColu): static
    {
        if (!$this->corresColus->contains($corresColu)) {
            $this->corresColus->add($corresColu);
            $corresColu->setIdColParams($this);
        }

        return $this;
    }

    public function removeCorresColu(CorresColu $corresColu): static
    {
        if ($this->corresColus->removeElement($corresColu)) {
            // set the owning side to null (unless already changed)
            if ($corresColu->getIdColParams() === $this) {
                $corresColu->setIdColParams(null);
            }
        }

        return $this;
    }

    public function getTypeParam(): ?string
    {
        return $this->type_param;
    }

    public function setTypeParam(?string $type_param): static
    {
        $this->type_param = $type_param;

        return $this;
    }
}
