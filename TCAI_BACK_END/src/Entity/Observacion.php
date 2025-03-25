<?php

namespace App\Entity;

use App\Repository\ObservacionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObservacionRepository::class)]
class Observacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\ManyToOne]
    private ?paciente $paciente_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getPacienteId(): ?paciente
    {
        return $this->paciente_id;
    }

    public function setPacienteId(?paciente $paciente_id): static
    {
        $this->paciente_id = $paciente_id;

        return $this;
    }
}
