<?php

namespace App\Entity;

use App\Repository\DrenajeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrenajeRepository::class)]
class Drenaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $debito = null;

    #[ORM\ManyToOne]
    private ?TipoDrenaje $tipo_drenaje_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebito(): ?string
    {
        return $this->debito;
    }

    public function setDebito(?string $debito): static
    {
        $this->debito = $debito;

        return $this;
    }

    public function getTipoDrenajeId(): ?TipoDrenaje
    {
        return $this->tipo_drenaje_id;
    }

    public function setTipoDrenajeId(?TipoDrenaje $tipo_drenaje_id): static
    {
        $this->tipo_drenaje_id = $tipo_drenaje_id;

        return $this;
    }
}
