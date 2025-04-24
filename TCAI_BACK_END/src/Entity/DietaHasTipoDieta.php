<?php

namespace App\Entity;

use App\Repository\DietaHasTipoDietaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DietaHasTipoDietaRepository::class)]
class DietaHasTipoDieta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Dieta $dieta_id = null;

    #[ORM\ManyToOne]
    private ?TipoDieta $tipo_dieta_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDietaId(): ?Dieta
    {
        return $this->dieta_id;
    }

    public function setDietaId(?Dieta $dieta_id): static
    {
        $this->dieta_id = $dieta_id;

        return $this;
    }

    public function getTipoDietaId(): ?TipoDieta
    {
        return $this->tipo_dieta_id;
    }

    public function setTipoDietaId(?TipoDieta $tipo_dieta_id): static
    {
        $this->tipo_dieta_id = $tipo_dieta_id;

        return $this;
    }
}
