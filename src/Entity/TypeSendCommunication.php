<?php

namespace App\Entity;

use App\Repository\TypeSendCommunicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeSendCommunicationRepository::class)]
class TypeSendCommunication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type_communication = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeCommunication(): ?string
    {
        return $this->type_communication;
    }

    public function setTypeCommunication(string $type_communication): static
    {
        $this->type_communication = $type_communication;

        return $this;
    }
}
