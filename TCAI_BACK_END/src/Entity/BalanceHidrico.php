<?php

namespace App\Entity;

use App\Repository\BalanceHidricoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BalanceHidricoRepository::class)]
class BalanceHidrico
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $diuresis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $deposicion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiuresis(): ?int
    {
        return $this->diuresis;
    }

    public function setDiuresis(?int $diuresis): static
    {
        $this->diuresis = $diuresis;

        return $this;
    }

    public function getDeposicion(): ?string
    {
        return $this->deposicion;
    }

    public function setDeposicion(?string $deposicion): static
    {
        $this->deposicion = $deposicion;

        return $this;
    }
}
