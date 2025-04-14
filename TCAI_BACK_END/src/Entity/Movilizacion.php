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

    #[ORM\Column(type: 'text', columnDefinition: 'LONGTEXT', nullable: true)]
    private ?string $sedestacion = null;

    #[ORM\Column(nullable: true)]
    private ?int $ayuda_deambulacion = null;

    #[ORM\Column(type: 'text', columnDefinition: 'LONGTEXT', nullable: true)]
    private ?string $ayuda_descripcion = null;

    #[ORM\Column(type: 'text', columnDefinition: 'LONGTEXT', nullable: true)]
    private ?string $cambios_posturales = null;

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

    public function getCambiosPosturales(): ?string
    {
        return $this->cambios_posturales;
    }

    public function setCambiosPosturales(?string $cambios_posturales): static
    {
        $this->cambios_posturales = $cambios_posturales;

        return $this;
    }
}
