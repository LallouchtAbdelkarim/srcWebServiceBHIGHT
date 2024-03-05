<?php

namespace App\Entity;

use App\Repository\ParamActiviteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParamActiviteRepository::class)]
class ParamActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $code_type = null;

    #[ORM\ManyToOne(inversedBy: 'paramActivites')]
    private ?TypeParametrage $id_branche = null;

    #[ORM\OneToMany(mappedBy: 'id_param_activite', targetEntity: QualificationParam::class)]
    private Collection $qualificationParams;

    #[ORM\Column]
    private ?int $typeActivite = null;

    #[ORM\Column(nullable: true)]
    private ?int $activite_p = null;

    public function __construct()
    {
        $this->qualificationParams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCodeType(): ?string
    {
        return $this->code_type;
    }

    public function setCodeType(string $code_type): self
    {
        $this->code_type = $code_type;

        return $this;
    }

    public function getIdBranche(): ?TypeParametrage
    {
        return $this->id_branche;
    }

    public function setIdBranche(?TypeParametrage $id_branche): self
    {
        $this->id_branche = $id_branche;

        return $this;
    }

    /**
     * @return Collection<int, QualificationParam>
     */
    public function getQualificationParams(): Collection
    {
        return $this->qualificationParams;
    }

    public function addQualificationParam(QualificationParam $qualificationParam): static
    {
        if (!$this->qualificationParams->contains($qualificationParam)) {
            $this->qualificationParams->add($qualificationParam);
            $qualificationParam->setIdParamActivite($this);
        }

        return $this;
    }

    public function removeQualificationParam(QualificationParam $qualificationParam): static
    {
        if ($this->qualificationParams->removeElement($qualificationParam)) {
            // set the owning side to null (unless already changed)
            if ($qualificationParam->getIdParamActivite() === $this) {
                $qualificationParam->setIdParamActivite(null);
            }
        }

        return $this;
    }

    public function getTypeActivite(): ?int
    {
        return $this->typeActivite;
    }

    public function setTypeActivite(int $typeActivite): static
    {
        $this->typeActivite = $typeActivite;

        return $this;
    }

    public function getActiviteP(): ?int
    {
        return $this->activite_p;
    }

    public function setActiviteP(?int $activite_p): static
    {
        $this->activite_p = $activite_p;

        return $this;
    }
}
