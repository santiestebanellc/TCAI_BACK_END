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

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $toma = null;

    #[ORM\ManyToOne]
    private ?paciente $paciente_id = null;

    #[ORM\OneToOne]
    private ?observacion $observacion_id = null;

    #[ORM\OneToOne]
    private ?dieta $dieta_id = null;

    #[ORM\OneToOne]
    private ?drenaje $drenaje_id = null;

    #[ORM\OneToOne]
    private ?movilizacion $movilizacion_id = null;

    #[ORM\OneToOne]
    private ?constantesvitales $constantes_vitales_id = null;

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

    public function getToma(): ?string
    {
        return $this->toma;
    }

    public function setToma(?string $toma): static
    {
        $this->toma = $toma;

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

    public function getObservacion(): ?observacion
    {
        return $this->observacion_id;
    }

    public function setObservacion(?observacion $observacion_id): static
    {
        $this->observacion_id = $observacion_id;

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

    public function getConstantesVitalesId(): ?constantesvitales
    {
        return $this->constantes_vitales_id;
    }

    public function setConstantesVitalesId(?constantesvitales $constantes_vitales_id): static
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
