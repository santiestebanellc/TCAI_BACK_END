<?php

namespace App\Entity;

use App\Repository\HigieneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HigieneRepository::class)]
class Higiene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?TipoHigiene $tipo = null;

    #[ORM\Column(type: Types::TEXT, columnDefinition: 'LONGTEXT', nullable: true)]
    private ?string $descripcion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?TipoHigiene
    {
        return $this->tipo;
    }

    public function setTipo(?TipoHigiene $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
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
}
