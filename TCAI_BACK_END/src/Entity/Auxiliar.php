<?php

namespace App\Entity;

use App\Repository\AuxiliarRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuxiliarRepository::class)]
class Auxiliar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['auxiliar:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['auxiliar:read'])]
    private ?string $num_trabajador = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['auxiliar:read'])]
    private ?string $nombre = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Groups(['auxiliar:read'])]
    private ?string $apellidos = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contraseña = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumTrabajador(): ?string
    {
        return $this->num_trabajador;
    }

    public function setNumTrabajador(?string $num_trabajador): static
    {
        $this->num_trabajador = $num_trabajador;

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

    public function getContraseña(): ?string
    {
        return $this->contraseña;
    }

    public function setContraseña(?string $contraseña): static
    {
        $this->contraseña = $contraseña;

        return $this;
    }
}
