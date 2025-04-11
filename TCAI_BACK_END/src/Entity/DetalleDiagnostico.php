<?php

namespace App\Entity;

use App\Repository\DetalleDiagnosticoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetalleDiagnosticoRepository::class)]
class DetalleDiagnostico
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $o2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $o2_descripcion = null;

    #[ORM\Column(nullable: true)]
    private ?int $panales = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $panales_descripcion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sv = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sng= null;

    #[ORM\ManyToOne]
    private ?diagnostico $diagnostico_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getO2(): ?int
    {
        return $this->o2;
    }

    public function setO2(?int $o2): static
    {
        $this->o2 = $o2;

        return $this;
    }

    public function getO2Descripcion(): ?string
    {
        return $this->o2_descripcion;
    }

    public function setO2Descripcion(?string $o2_descripcion): static
    {
        $this->o2_descripcion = $o2_descripcion;

        return $this;
    }

    public function getPanales(): ?int
    {
        return $this->panales;
    }

    public function setPanales(?int $panales): static
    {
        $this->panales = $panales;

        return $this;
    }

    public function getPanalesDescripcion(): ?string
    {
        return $this->panales_descripcion;
    }

    public function setPanalesDescripcion(?string $panales_descripcion): static
    {
        $this->panales_descripcion = $panales_descripcion;

        return $this;
    }

    public function getSv(): ?string
    {
        return $this->sv;
    }

    public function setSv(?string $sv): static
    {
        $this->sv = $sv;

        return $this;
    }

    public function getSr(): ?string
    {
        return $this->sr;
    }

    public function setSr(?string $sr): static
    {
        $this->sr = $sr;

        return $this;
    }

    public function getSng(): ?string
    {
        return $this->sng;
    }

    public function setSng(?string $sng): static
    {
        $this->sng = $sng;

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
}