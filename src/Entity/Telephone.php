<?php

namespace App\Entity;

use App\Repository\TelephoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TelephoneRepository::class)]
class Telephone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $origine = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?StatusTelephone $id_status = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeTel $id_type_tel = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note3 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Debiteur $id_debiteur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    #[ORM\Column(nullable: true)]
    private ?int $codeP = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_dn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\ManyToOne]
    private ?TypeSource $id_type_source = null;

    #[ORM\OneToMany(mappedBy: 'telephone', targetEntity: CreanceActivite::class)]
    private Collection $creanceActivites;

    public function __construct()
    {
        $this->creanceActivites = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(?string $origine): static
    {
        $this->origine = $origine;

        return $this;
    }
    public function getIdStatus(): ?StatusTelephone
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusTelephone $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }

    public function getIdTypeTel(): ?TypeTel
    {
        return $this->id_type_tel;
    }

    public function setIdTypeTel(?TypeTel $id_type_tel): static
    {
        $this->id_type_tel = $id_type_tel;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getNote1(): ?string
    {
        return $this->note1;
    }

    public function setNote1(?string $note1): static
    {
        $this->note1 = $note1;

        return $this;
    }

    public function getNumero2(): ?string
    {
        return $this->numero2;
    }

    public function setNumero2(?string $numero2): static
    {
        $this->numero2 = $numero2;

        return $this;
    }

    public function getStatut2(): ?string
    {
        return $this->statut2;
    }

    public function setStatut2(?string $statut2): static
    {
        $this->statut2 = $statut2;

        return $this;
    }

    public function getNote2(): ?string
    {
        return $this->note2;
    }

    public function setNote2(?string $note2): static
    {
        $this->note2 = $note2;

        return $this;
    }

    public function getNumero3(): ?string
    {
        return $this->numero3;
    }

    public function setNumero3(?string $numero3): static
    {
        $this->numero3 = $numero3;

        return $this;
    }

    public function getStatut3(): ?string
    {
        return $this->statut3;
    }

    public function setStatut3(?string $statut3): static
    {
        $this->statut3 = $statut3;

        return $this;
    }

    public function getNote3(): ?string
    {
        return $this->note3;
    }

    public function setNote3(?string $note3): static
    {
        $this->note3 = $note3;

        return $this;
    }

    public function getIdDebiteur(): ?Debiteur
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?Debiteur $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getIdIntegration(): ?int
    {
        return $this->id_integration;
    }

    public function setIdIntegration(?int $id_integration): static
    {
        $this->id_integration = $id_integration;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCodeP(): ?int
    {
        return $this->codeP;
    }

    public function setCodeP(?int $codeP): static
    {
        $this->codeP = $codeP;

        return $this;
    }

    public function getIdDn(): ?int
    {
        return $this->id_dn;
    }

    public function setIdDn(?int $id_dn): static
    {
        $this->id_dn = $id_dn;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getIdTypeSource(): ?TypeSource
    {
        return $this->id_type_source;
    }

    public function setIdTypeSource(?TypeSource $id_type_source): static
    {
        $this->id_type_source = $id_type_source;

        return $this;
    }

    /**
     * @return Collection<int, CreanceActivite>
     */
    public function getCreanceActivites(): Collection
    {
        return $this->creanceActivites;
    }

    public function addCreanceActivite(CreanceActivite $creanceActivite): static
    {
        if (!$this->creanceActivites->contains($creanceActivite)) {
            $this->creanceActivites->add($creanceActivite);
            $creanceActivite->setTelephone($this);
        }

        return $this;
    }

    public function removeCreanceActivite(CreanceActivite $creanceActivite): static
    {
        if ($this->creanceActivites->removeElement($creanceActivite)) {
            // set the owning side to null (unless already changed)
            if ($creanceActivite->getTelephone() === $this) {
                $creanceActivite->setTelephone(null);
            }
        }

        return $this;
    }

    

}
