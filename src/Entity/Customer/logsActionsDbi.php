<?php

namespace App\Entity\Customer;

use App\Repository\Customer\logsActionsDbiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: logsActionsDbiRepository::class)]
class logsActionsDbi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?actionsImportDbi $id_action = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rapport = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_champ = null;

    public function getIdChamp(): ?int
    {
        return $this->id_champ;
    }

    public function setIdChamp(?int $id_champ): static
    {
        $this->id_champ = $id_champ;

        return $this;
    }
}
