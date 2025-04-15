<?php

namespace App\Entity;

use App\Repository\DiagnosticoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagnosticoRepository::class)]
class Diagnostico
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $diagnostico = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motivo = null;

    #[ORM\ManyToOne]
    private ?Paciente $paciente_id = null;

    #[ORM\ManyToOne]
    private ?Auxiliar $auxiliar_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $toma = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiagnostico(): ?string
    {
        return $this->diagnostico;
    }

    public function setDiagnostico(?string $diagnostico): static
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(?string $motivo): static
    {
        $this->motivo = $motivo;

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

    public function getAuxiliarId(): ?Auxiliar
    {
        return $this->auxiliar_id;
    }

    public function setAuxiliarId(?Auxiliar $auxiliar_id): static
    {
        $this->auxiliar_id = $auxiliar_id;

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

    public function getToma(): ?string
    {
        return $this->toma;
    }

    public function setToma(?string $toma): static
    {
        $this->toma = $toma;

        return $this;
    }
}