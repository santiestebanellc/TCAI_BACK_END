<?php

namespace App\Entity;

use App\Entity\PacienteHasHabitaciones;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use App\Repository\HabitacionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HabitacionRepository::class)]
class Habitacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $codigo = null;

    #[ORM\OneToMany(mappedBy: 'habitacion_id', targetEntity: PacienteHasHabitaciones::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $pacienteHasHabitaciones;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }
    public function __construct()
    {
        $this->pacienteHasHabitaciones = new ArrayCollection();
    }

    public function getPacienteHasHabitaciones(): Collection
    {
        return $this->pacienteHasHabitaciones;
    }
}
