<?php

namespace App\Entity;

use App\Repository\SueroterapiaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SueroterapiaRepository::class)]
class Sueroterapia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 1, nullable: true)]
    private ?string $dosis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDosis(): ?int
    {
        return $this->dosis;
    }

    public function setDosis(?string $dosis): static
    {
        $this->dosis = $dosis;

        return $this;
    }
}