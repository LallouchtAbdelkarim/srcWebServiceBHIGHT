<?php

namespace App\Entity;

use App\Repository\HistoAdresseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoAdresseRepository::class)]
class HistoAdresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $origine = null;

    #[ORM\Column]
    private ?int $id_status_id = null;

    #[ORM\Column]
    private ?int $id_type_adresse_id = null;
    #[ORM\Column]
    private ?int $id_debiteur_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_users_id = null;
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;
    #[ORM\Column(length: 255)]
    private ?string $adresse_complet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;
    #[ORM\Column]
    private ?bool $verifier = null;

    #[ORM\Column(length: 255)]
    private ?string $code_postal = null;

    #[ORM\Column(length: 255)]
    private ?string $province = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_action = null;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(string $origine): static
    {
        $this->origine = $origine;

        return $this;
    }

    public function getIdStatusId(): ?int
    {
        return $this->id_status_id;
    }

    public function setIdStatusId(int $id_status_id): static
    {
        $this->id_status_id = $id_status_id;

        return $this;
    }
    public function getAdresseComplet(): ?string
    {
        return $this->adresse_complet;
    }

    public function setAdresseComplet(string $adresse_complet): static
    {
        $this->adresse_complet = $adresse_complet;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getIdUsersId(): ?int
    {
        return $this->id_users_id;
    }

    public function setIdUsersId(?int $id_users_id): static
    {
        $this->id_users_id = $id_users_id;

        return $this;
    }
    public function getIdDebiteurId(): ?int
    {
        return $this->id_debiteur_id;
    }

    public function setIdDebiteurId(?int $id_debiteur_id): static
    {
        $this->id_debiteur_id = $id_debiteur_id;

        return $this;
    }
    public function getIdTypeAdresseId(): ?int
    {
        return $this->id_type_adresse_id;
    }

    public function setIdTypeAdresseId(?int $id_type_adresse_id): static
    {
        $this->id_type_adresse_id = $id_type_adresse_id;

        return $this;
    }
    public function getVerifier(): ?bool
    {
        return $this->verifier;
    }

    public function setVerifier(bool $verifier): static
    {
        $this->verifier = $verifier;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): static
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }
    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateAction(): ?\DateTimeInterface
    {
        return $this->date_action;
    }

    public function setDateAction(?\DateTimeInterface $date_action): static
    {
        $this->date_action = $date_action;

        return $this;
    }
}
