<?php

namespace App\Entity;

use App\Repository\DebiteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DebiteurRepository::class)]
class Debiteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $civilite = null;

    #[ORM\Column(length: 255)]
    private ?string $cin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $raison_social = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu_naissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cin_formate = null;

    #[ORM\OneToMany(mappedBy: 'id_debiteur', targetEntity: HistoriqueEmploi::class)]
    private Collection $y;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $id_debiteur_dbi = null;

    #[ORM\Column(length: 255)]
    private ?string $type_personne = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_debiteur = null;

    #[ORM\Column(length: 255)]
    private ?string $cle_identifiant = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_integration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rc = null;

    #[ORM\OneToMany(mappedBy: 'idDebiteur', targetEntity: Accord::class)]
    private Collection $accords;

    #[ORM\OneToMany(mappedBy: 'debiteur', targetEntity: CreanceActivite::class)]
    private Collection $creanceActivites;

    public function __construct()
    {
        $this->y = new ArrayCollection();
        $this->accords = new ArrayCollection();
        $this->creanceActivites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): static
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(?string $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRaisonSocial(): ?string
    {
        return $this->raison_social;
    }

    public function setRaisonSocial(?string $raison_social): static
    {
        $this->raison_social = $raison_social;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): static
    {
        $this->fax = $fax;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(?\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieu_naissance;
    }

    public function setLieuNaissance(?string $lieu_naissance): static
    {
        $this->lieu_naissance = $lieu_naissance;

        return $this;
    }

    public function getCinFormate(): ?string
    {
        return $this->cin_formate;
    }

    public function setCinFormate(?string $cin_formate): static
    {
        $this->cin_formate = $cin_formate;

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueEmploi>
     */
    public function getY(): Collection
    {
        return $this->y;
    }

    public function addY(HistoriqueEmploi $y): static
    {
        if (!$this->y->contains($y)) {
            $this->y->add($y);
            $y->setIdDebiteur($this);
        }

        return $this;
    }

    public function removeY(HistoriqueEmploi $y): static
    {
        if ($this->y->removeElement($y)) {
            // set the owning side to null (unless already changed)
            if ($y->getIdDebiteur() === $this) {
                $y->setIdDebiteur(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getIdDebiteurDbi(): ?int
    {
        return $this->id_debiteur_dbi;
    }

    public function setIdDebiteurDbi(int $id_debiteur_dbi): static
    {
        $this->id_debiteur_dbi = $id_debiteur_dbi;

        return $this;
    }

    public function getTypePersonne(): ?string
    {
        return $this->type_personne;
    }

    public function setTypePersonne(string $type_personne): static
    {
        $this->type_personne = $type_personne;

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

    public function getIdDebiteur(): ?int
    {
        return $this->id_debiteur;
    }

    public function setIdDebiteur(?int $id_debiteur): static
    {
        $this->id_debiteur = $id_debiteur;

        return $this;
    }

    public function getCleIdentifiant(): ?string
    {
        return $this->cle_identifiant;
    }

    public function setCleIdentifiant(string $cle_identifiant): static
    {
        $this->cle_identifiant = $cle_identifiant;

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

    public function getRc(): ?string
    {
        return $this->rc;
    }

    public function setRc(?string $rc): static
    {
        $this->rc = $rc;

        return $this;
    }

    /**
     * @return Collection<int, Accord>
     */
    public function getAccords(): Collection
    {
        return $this->accords;
    }

    public function addAccord(Accord $accord): static
    {
        if (!$this->accords->contains($accord)) {
            $this->accords->add($accord);
            $accord->setIdDebiteur($this);
        }

        return $this;
    }

    public function removeAccord(Accord $accord): static
    {
        if ($this->accords->removeElement($accord)) {
            // set the owning side to null (unless already changed)
            if ($accord->getIdDebiteur() === $this) {
                $accord->setIdDebiteur(null);
            }
        }

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
            $creanceActivite->setDebiteur($this);
        }

        return $this;
    }

    public function removeCreanceActivite(CreanceActivite $creanceActivite): static
    {
        if ($this->creanceActivites->removeElement($creanceActivite)) {
            // set the owning side to null (unless already changed)
            if ($creanceActivite->getDebiteur() === $this) {
                $creanceActivite->setDebiteur(null);
            }
        }

        return $this;
    }
}
