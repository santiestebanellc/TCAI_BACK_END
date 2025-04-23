<?php

namespace App\Entity;

use App\Repository\PacienteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\PacienteHasHabitaciones;

#[ORM\Entity(repositoryClass: PacienteRepository::class)]
class Paciente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $num_historial = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $apellidos = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_nacimiento = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $direccion_completa = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $lengua_materna = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $antecedentes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $alergias = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $nombre_cuidador = null;

    #[ORM\Column(length: 9, nullable: true)]
    private ?string $telefono_cuidador = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_ingreso = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\OneToMany(mappedBy: 'paciente_id', targetEntity: PacienteHasHabitaciones::class)]
    private Collection $pacienteHasHabitaciones;

    #[ORM\OneToMany(mappedBy: 'paciente_id', targetEntity: Registro::class)]
    private Collection $registros;

    #[ORM\OneToMany(mappedBy: 'paciente_id', targetEntity: Diagnostico::class)]
    private Collection $diagnosticos;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumHistorial(): ?int
    {
        return $this->num_historial;
    }

    public function setNumHistorial(?int $num_historial): static
    {
        $this->num_historial = $num_historial;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): static
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento(?\DateTimeInterface $fecha_nacimiento): static
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

        return $this;
    }

    public function getDireccionCompleta(): ?string
    {
        return $this->direccion_completa;
    }

    public function setDireccionCompleta(?string $direccion_completa): static
    {
        $this->direccion_completa = $direccion_completa;

        return $this;
    }

    public function getLenguaMaterna(): ?string
    {
        return $this->lengua_materna;
    }

    public function setLenguaMaterna(?string $lengua_materna): static
    {
        $this->lengua_materna = $lengua_materna;

        return $this;
    }

    public function getAntecedentes(): ?string
    {
        return $this->antecedentes;
    }

    public function setAntecedentes(?string $antecedentes): static
    {
        $this->antecedentes = $antecedentes;

        return $this;
    }

    public function getAlergias(): ?string
    {
        return $this->alergias;
    }

    public function setAlergias(?string $alergias): static
    {
        $this->alergias = $alergias;

        return $this;
    }

    public function getNombreCuidador(): ?string
    {
        return $this->nombre_cuidador;
    }

    public function setNombreCuidador(?string $nombre_cuidador): static
    {
        $this->nombre_cuidador = $nombre_cuidador;

        return $this;
    }

    public function getTelefonoCuidador(): ?int
    {
        return $this->telefono_cuidador;
    }

    public function setTelefonoCuidador(?int $telefono_cuidador): static
    {
        $this->telefono_cuidador = $telefono_cuidador;

        return $this;
    }

    public function getFechaIngreso(): ?\DateTimeInterface
    {
        return $this->fecha_ingreso;
    }

    public function setFechaIngreso(?\DateTimeInterface $fecha_ingreso): static
    {
        $this->fecha_ingreso = $fecha_ingreso;

        return $this;
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
    public function __construct()
    {
        $this->pacienteHasHabitaciones = new ArrayCollection();
        $this->registros = new ArrayCollection();
        $this->diagnosticos = new ArrayCollection();
    }

    public function getPacienteHasHabitaciones(): Collection
    {
        return $this->pacienteHasHabitaciones;
    }

    public function getRegistros(): Collection
    {
        return $this->registros;
    }

    public function getDiagnostico(): Collection
    {
        return $this->diagnosticos;
    }
}
