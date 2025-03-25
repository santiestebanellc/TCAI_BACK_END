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
    private ?dieta $dieta_id = null;

    #[ORM\ManyToOne]
    private ?tipodieta $tipo_dieta_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDietaId(): ?dieta
    {
        return $this->dieta_id;
    }

    public function setDietaId(?dieta $dieta_id): static
    {
        $this->dieta_id = $dieta_id;

        return $this;
    }

    public function getTipoDietaId(): ?tipodieta
    {
        return $this->tipo_dieta_id;
    }

    public function setTipoDietaId(?tipodieta $tipo_dieta_id): static
    {
        $this->tipo_dieta_id = $tipo_dieta_id;

        return $this;
    }
}
