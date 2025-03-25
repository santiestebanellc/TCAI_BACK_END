<?php

namespace App\Entity;

use App\Repository\RegistroRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistroRepository::class)]
class Registro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?auxiliar $auxiliar_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\ManyToOne]
    private ?paciente $paciente_id = null;

    #[ORM\ManyToOne]
    private ?tipohigiene $tipo_higiene_id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observaciones = null;

    #[ORM\ManyToOne]
    private ?dieta $dieta_id = null;

    #[ORM\ManyToOne]
    private ?drenaje $drenaje_id = null;

    #[ORM\ManyToOne]
    private ?movilizacion $movilizacion_id = null;

    #[ORM\ManyToOne]
    private ?diagnostico $diagnostico_id = null;

    #[ORM\ManyToOne]
    private ?constantesvitales $constantes_vitales_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuxiliarId(): ?auxiliar
    {
        return $this->auxiliar_id;
    }

    public function setAuxiliarId(?auxiliar $auxiliar_id): static
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

    public function getPacienteId(): ?paciente
    {
        return $this->paciente_id;
    }

    public function setPacienteId(?paciente $paciente_id): static
    {
        $this->paciente_id = $paciente_id;

        return $this;
    }

    public function getTipoHigieneId(): ?tipohigiene
    {
        return $this->tipo_higiene_id;
    }

    public function setTipoHigieneId(?tipohigiene $tipo_higiene_id): static
    {
        $this->tipo_higiene_id = $tipo_higiene_id;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): static
    {
        $this->observaciones = $observaciones;

        return $this;
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

    public function getDrenajeId(): ?drenaje
    {
        return $this->drenaje_id;
    }

    public function setDrenajeId(?drenaje $drenaje_id): static
    {
        $this->drenaje_id = $drenaje_id;

        return $this;
    }

    public function getMovilizacionId(): ?movilizacion
    {
        return $this->movilizacion_id;
    }

    public function setMovilizacionId(?movilizacion $movilizacion_id): static
    {
        $this->movilizacion_id = $movilizacion_id;

        return $this;
    }

    public function getDiagnosticoId(): ?diagnostico
    {
        return $this->diagnostico_id;
    }

    public function setDiagnosticoId(?diagnostico $diagnostico_id): static
    {
        $this->diagnostico_id = $diagnostico_id;

        return $this;
    }

    public function getConstantesVitalesId(): ?constantesvitales
    {
        return $this->constantes_vitales_id;
    }

    public function setConstantesVitalesId(?constantesvitales $constantes_vitales_id): static
    {
        $this->constantes_vitales_id = $constantes_vitales_id;

        return $this;
    }
}
