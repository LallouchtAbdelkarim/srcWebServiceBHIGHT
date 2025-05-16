<?php

namespace App\Entity;

use App\Repository\AccordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccordRepository::class)]
class Accord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_premier_paiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFinPaiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $frequence = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbrEcheanciers = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $id_users = null;

    #[ORM\ManyToOne]
    private ?TypePaiement $id_type_paiement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusAccord $id_status = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant_a_payer = null;

    #[ORM\OneToMany(mappedBy: 'idAccord', targetEntity: AccordNotes::class, orphanRemoval: true)]
    private Collection $accordNotes;

    #[ORM\OneToMany(mappedBy: 'idAccord', targetEntity: AccordPj::class)]
    private Collection $url;

    #[ORM\Column(nullable: true)]
    private ?float $montantDeBase = null;

    #[ORM\Column(nullable: true)]
    private ?float $feeAdmin = null;

    #[ORM\Column(nullable: true)]
    private ?float $feeInstallment = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $interets = null;

    #[ORM\Column(nullable: true)]
    private ?float $remise = null;

    #[ORM\Column(nullable: true)]
    private ?float $accompte = null;

    #[ORM\ManyToOne(inversedBy: 'accords')]
    private ?Debiteur $idDebiteur = null;

    #[ORM\ManyToOne(inversedBy: 'accords')]
    private ?Personne $idPayeur = null;

    public function __construct()
    {
        $this->accordNotes = new ArrayCollection();
        $this->url = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePremierPaiement(): ?\DateTimeInterface
    {
        return $this->date_premier_paiement;
    }

    public function setDatePremierPaiement(\DateTimeInterface $date_premier_paiement): static
    {
        $this->date_premier_paiement = $date_premier_paiement;

        return $this;
    }

    public function getDateFinPaiement(): ?\DateTimeInterface
    {
        return $this->dateFinPaiement;
    }

    public function setDateFinPaiement(?\DateTimeInterface $dateFinPaiement): static
    {
        $this->dateFinPaiement = $dateFinPaiement;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getFrequence(): ?string
    {
        return $this->frequence;
    }

    public function setFrequence(?string $frequence): static
    {
        $this->frequence = $frequence;

        return $this;
    }

    public function getNbrEcheanciers(): ?int
    {
        return $this->nbrEcheanciers;
    }

    public function setNbrEcheanciers(?int $nbrEcheanciers): static
    {
        $this->nbrEcheanciers = $nbrEcheanciers;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getIdUsers(): ?Utilisateurs
    {
        return $this->id_users;
    }

    public function setIdUsers(?Utilisateurs $id_users): static
    {
        $this->id_users = $id_users;

        return $this;
    }

    public function getIdTypePaiement(): ?TypePaiement
    {
        return $this->id_type_paiement;
    }

    public function setIdTypePaiement(?TypePaiement $id_type_paiement): static
    {
        $this->id_type_paiement = $id_type_paiement;

        return $this;
    }

    public function getIdStatus(): ?StatusAccord
    {
        return $this->id_status;
    }

    public function setIdStatus(?StatusAccord $id_status): static
    {
        $this->id_status = $id_status;

        return $this;
    }

    public function getMontantAPayer(): ?float
    {
        return $this->montant_a_payer;
    }

    public function setMontantAPayer(?float $montant_a_payer): static
    {
        $this->montant_a_payer = $montant_a_payer;

        return $this;
    }

    /**
     * @return Collection<int, AccordNotes>
     */
    public function getAccordNotes(): Collection
    {
        return $this->accordNotes;
    }

    public function addAccordNote(AccordNotes $accordNote): static
    {
        if (!$this->accordNotes->contains($accordNote)) {
            $this->accordNotes->add($accordNote);
            $accordNote->setIdAccord($this);
        }

        return $this;
    }

    public function removeAccordNote(AccordNotes $accordNote): static
    {
        if ($this->accordNotes->removeElement($accordNote)) {
            // set the owning side to null (unless already changed)
            if ($accordNote->getIdAccord() === $this) {
                $accordNote->setIdAccord(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AccordPj>
     */
    public function getUrl(): Collection
    {
        return $this->url;
    }

    public function addUrl(AccordPj $url): static
    {
        if (!$this->url->contains($url)) {
            $this->url->add($url);
            $url->setIdAccord($this);
        }

        return $this;
    }

    public function removeUrl(AccordPj $url): static
    {
        if ($this->url->removeElement($url)) {
            // set the owning side to null (unless already changed)
            if ($url->getIdAccord() === $this) {
                $url->setIdAccord(null);
            }
        }

        return $this;
    }

    public function getMontantDeBase(): ?float
    {
        return $this->montantDeBase;
    }

    public function setMontantDeBase(?float $montantDeBase): static
    {
        $this->montantDeBase = $montantDeBase;

        return $this;
    }

    public function getFeeAdmin(): ?float
    {
        return $this->feeAdmin;
    }

    public function setFeeAdmin(?float $feeAdmin): static
    {
        $this->feeAdmin = $feeAdmin;

        return $this;
    }

    public function getFeeInstallment(): ?float
    {
        return $this->feeInstallment;
    }

    public function setFeeInstallment(?float $feeInstallment): static
    {
        $this->feeInstallment = $feeInstallment;

        return $this;
    }

    public function getInterets(): ?string
    {
        return $this->interets;
    }

    public function setInterets(?string $interets): static
    {
        $this->interets = $interets;

        return $this;
    }

    public function getRemise(): ?float
    {
        return $this->remise;
    }

    public function setRemise(?float $remise): static
    {
        $this->remise = $remise;

        return $this;
    }

    public function getAccompte(): ?float
    {
        return $this->accompte;
    }

    public function setAccompte(?float $accompte): static
    {
        $this->accompte = $accompte;

        return $this;
    }

    public function getIdDebiteur(): ?Debiteur
    {
        return $this->idDebiteur;
    }

    public function setIdDebiteur(?Debiteur $idDebiteur): static
    {
        $this->idDebiteur = $idDebiteur;

        return $this;
    }

    public function getIdPayeur(): ?Personne
    {
        return $this->idPayeur;
    }

    public function setIdPayeur(?Personne $idPayeur): static
    {
        $this->idPayeur = $idPayeur;

        return $this;
    }
}
