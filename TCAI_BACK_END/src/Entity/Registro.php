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
    private ?Auxiliar $auxiliar_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $toma = null;

    #[ORM\ManyToOne]
    private ?Paciente $paciente_id = null;

    #[ORM\OneToOne]
    private ?Observacion $observacion_id = null;

    #[ORM\OneToOne]
    private ?Dieta $dieta_id = null;

    #[ORM\OneToOne]
    private ?Drenaje $drenaje_id = null;

    #[ORM\OneToOne]
    private ?Movilizacion $movilizacion_id = null;

    #[ORM\OneToOne]
    private ?ConstantesVitales $constantes_vitales_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?BalanceHidrico $balance_hidrico_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Sueroterapia $sueroterapia_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Higiene $higiene_id = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPacienteId(): ?Paciente
    {
        return $this->paciente_id;
    }

    public function setPacienteId(?Paciente $paciente_id): static
    {
        $this->paciente_id = $paciente_id;

        return $this;
    }

    public function getObservacion(): ?Observacion
    {
        return $this->observacion_id;
    }

    public function setObservacion(?Observacion $observacion_id): static
    {
        $this->observacion_id = $observacion_id;

        return $this;
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

    public function getDrenajeId(): ?Drenaje
    {
        return $this->drenaje_id;
    }

    public function setDrenajeId(?Drenaje $drenaje_id): static
    {
        $this->drenaje_id = $drenaje_id;

        return $this;
    }

    public function getMovilizacionId(): ?Movilizacion
    {
        return $this->movilizacion_id;
    }

    public function setMovilizacionId(?Movilizacion $movilizacion_id): static
    {
        $this->movilizacion_id = $movilizacion_id;

        return $this;
    }

    public function getConstantesVitalesId(): ?ConstantesVitales
    {
        return $this->constantes_vitales_id;
    }

    public function setConstantesVitalesId(?ConstantesVitales $constantes_vitales_id): static
    {
        $this->constantes_vitales_id = $constantes_vitales_id;

        return $this;
    }

    public function getBalanceHidricoId(): ?BalanceHidrico
    {
        return $this->balance_hidrico_id;
    }

    public function setBalanceHidricoId(?BalanceHidrico $balance_hidrico_id): static
    {
        $this->balance_hidrico_id = $balance_hidrico_id;

        return $this;
    }

    public function getSueroterapiaId(): ?Sueroterapia
    {
        return $this->sueroterapia_id;
    }

    public function setSueroterapiaId(?Sueroterapia $sueroterapia_id): static
    {
        $this->sueroterapia_id = $sueroterapia_id;

        return $this;
    }

    public function getHigieneId(): ?Higiene
    {
        return $this->higiene_id;
    }

    public function setHigieneId(?Higiene $higiene_id): static
    {
        $this->higiene_id = $higiene_id;

        return $this;
    }
}
