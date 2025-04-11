<?php

namespace App\Entity;

use App\Repository\MovilizacionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovilizacionRepository::class)]
class Movilizacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sedestacion = null;

    #[ORM\Column(nullable: true)]
    private ?int $ayuda_deambulacion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ayuda_descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cambios = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $decubitos = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSedestacion(): ?string
    {
        return $this->sedestacion;
    }

    public function setSedestacion(?string $sedestacion): static
    {
        $this->sedestacion = $sedestacion;

        return $this;
    }

    public function getAyudaDeambulacion(): ?int
    {
        return $this->ayuda_deambulacion;
    }

    public function setAyudaDeambulacion(?int $ayuda_deambulacion): static
    {
        $this->ayuda_deambulacion = $ayuda_deambulacion;

        return $this;
    }

    public function getAyudaDescripcion(): ?string
    {
        return $this->ayuda_descripcion;
    }

    public function setAyudaDescripcion(?string $ayuda_descripcion): static
    {
        $this->ayuda_descripcion = $ayuda_descripcion;

        return $this;
    }

    public function getCambios(): ?string
    {
        return $this->cambios;
    }

    public function setCambios(?string $cambios): static
    {
        $this->cambios = $cambios;

        return $this;
    }

    public function getDecubitos(): ?string
    {
        return $this->decubitos;
    }

    public function setDecubitos(?string $decubitos): static
    {
        $this->decubitos = $decubitos;

        return $this;
    }
}