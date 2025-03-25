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
    private ?string $descripcion = null;

    #[ORM\Column(nullable: true)]
    private ?int $sv = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sv_tipo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sv_debito = null;

    #[ORM\Column(nullable: true)]
    private ?int $sr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sr_debito = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $sng = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sng_descripcion = null;

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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getSv(): ?int
    {
        return $this->sv;
    }

    public function setSv(?int $sv): static
    {
        $this->sv = $sv;

        return $this;
    }

    public function getSvTipo(): ?string
    {
        return $this->sv_tipo;
    }

    public function setSvTipo(?string $sv_tipo): static
    {
        $this->sv_tipo = $sv_tipo;

        return $this;
    }

    public function getSvDebito(): ?string
    {
        return $this->sv_debito;
    }

    public function setSvDebito(?string $sv_debito): static
    {
        $this->sv_debito = $sv_debito;

        return $this;
    }

    public function getSr(): ?int
    {
        return $this->sr;
    }

    public function setSr(?int $sr): static
    {
        $this->sr = $sr;

        return $this;
    }

    public function getSrDebito(): ?string
    {
        return $this->sr_debito;
    }

    public function setSrDebito(?string $sr_debito): static
    {
        $this->sr_debito = $sr_debito;

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

    public function getSngDescripcion(): ?string
    {
        return $this->sng_descripcion;
    }

    public function setSngDescripcion(?string $sng_descripcion): static
    {
        $this->sng_descripcion = $sng_descripcion;

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
