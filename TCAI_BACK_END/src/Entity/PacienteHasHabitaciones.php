<?php

namespace App\Entity;

use App\Repository\PacienteHasHabitacionesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PacienteHasHabitacionesRepository::class)]
class PacienteHasHabitaciones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\ManyToOne]
    private ?Paciente $paciente_id = null;

    #[ORM\ManyToOne]
    private ?Habitacion $habitacion_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getPacienteId(): ?Paciente
    {
        return $this->paciente_id;
    }

    public function setPacienteId(?Paciente $paciente_id): static
    {
        $this->paciente_id = $paciente_id;

        return $this;
    }

    public function getHabitacionId(): ?Habitacion
    {
        return $this->habitacion_id;
    }

    public function setHabitacionId(?Habitacion $habitacion_id): static
    {
        $this->habitacion_id = $habitacion_id;

        return $this;
    }
}
