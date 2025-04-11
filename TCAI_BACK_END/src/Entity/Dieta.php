<?php

namespace App\Entity;

use App\Repository\DietaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DietaRepository::class)]
class Dieta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $autonomo = null;

    #[ORM\Column(nullable: true)]
    private ?int $protesi = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAutonomo(): ?int
    {
        return $this->autonomo;
    }

    public function setAutonomo(?int $autonomo): static
    {
        $this->autonomo = $autonomo;

        return $this;
    }

    public function getProtesi(): ?int
    {
        return $this->protesi;
    }

    public function setProtesi(?int $protesi): static
    {
        $this->protesi = $protesi;

        return $this;
    }
}