<?php

namespace App\Entity;

use App\Repository\TypeParametrageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeParametrageRepository::class)]
class TypeParametrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\OneToMany(mappedBy: 'id_branche', targetEntity: ParamActivite::class)]
    private Collection $paramActivites;

    public function __construct()
    {
        $this->paramActivites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

//     /**
//      * @return Collection<int, ParamActivite>
//      */
//     public function getParamActivites(): Collection
//     {
//         return $this->paramActivites;
//     }

//     public function addParamActivite(ParamActivite $paramActivite): self
//     {
//         if (!$this->paramActivites->contains($paramActivite)) {
//             $this->paramActivites->add($paramActivite);
//             $paramActivite->setIdBranche($this);
//         }

//         return $this;
//     }

//     public function removeParamActivite(ParamActivite $paramActivite): self
//     {
//         if ($this->paramActivites->removeElement($paramActivite)) {
//             // set the owning side to null (unless already changed)
//             if ($paramActivite->getIdBranche() === $this) {
//                 $paramActivite->setIdBranche(null);
//             }
//         }

//         return $this;
//     }
}
