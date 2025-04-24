<?php

namespace App\Entity;

use App\Repository\ConstantesVitalesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConstantesVitalesRepository::class)]
class ConstantesVitales
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $ta_sistolica = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $ta_diastolica = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 1, nullable: true)]
    private ?string $frecuencia_respiratoria = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 1, nullable: true)]
    private ?string $pulso = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 1, nullable: true)]
    private ?string $temperatura = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 1, nullable: true)]
    private ?string $saturacion_oxigeno = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaSistolica(): ?string
    {
        return $this->ta_sistolica;
    }

    public function setTaSistolica(?string $ta_sistolica): static
    {
        $this->ta_sistolica = $ta_sistolica;

        return $this;
    }

    public function getTaDiastolica(): ?string
    {
        return $this->ta_diastolica;
    }

    public function setTaDiastolica(?string $ta_diastolica): static
    {
        $this->ta_diastolica = $ta_diastolica;

        return $this;
    }

    public function getFrecuenciaRespiratoria(): ?string
    {
        return $this->frecuencia_respiratoria;
    }

    public function setFrecuenciaRespiratoria(?string $frecuencia_respiratoria): static
    {
        $this->frecuencia_respiratoria = $frecuencia_respiratoria;

        return $this;
    }

    public function getPulso(): ?string
    {
        return $this->pulso;
    }

    public function setPulso(?string $pulso): static
    {
        $this->pulso = $pulso;

        return $this;
    }

    public function getTemperatura(): ?string
    {
        return $this->temperatura;
    }

    public function setTemperatura(?string $temperatura): static
    {
        $this->temperatura = $temperatura;

        return $this;
    }

    public function getSaturacionOxigeno(): ?string
    {
        return $this->saturacion_oxigeno;
    }

    public function setSaturacionOxigeno(?string $saturacion_oxigeno): static
    {
        $this->saturacion_oxigeno = $saturacion_oxigeno;

        return $this;
    }
}