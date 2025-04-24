<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateRegistroDto
{

    // AUXILIAR
    // public int $auxiliar;

    // PACIENTE
    // public int $paciente;

    // OBSERVACION
    #[Assert\NotBlank(message: 'La observación es obligatoria')]
    public ?string $observacion_descripcion = null;

    // DIETA
    public ?int $dieta_autonomo = null;
    public ?int $dieta_protesi = null;
    // public ?int $tipoTexturaId;
    
    // Dieta Has Tipo Dieta
    public ?int $tipo_dieta_id = null;
    
    // DRENAJE
    public ?string $drenaje_descripcion = null;

    // MOVILIZACIÓN
    public ?string $sedestacion = null;
    public ?int $ayudaDeambulacion = null;
    public ?string $ayudaDescripcion = null;
    public ?string $cambiosPosturales = null;
    
    // CONSTANTES VITALES
    public ?string $taSistolica = null;
    public ?string $taDiastolica = null;
    public ?float $frecuenciaRespiratoria = null;
    public ?float $pulso = null;
    public ?float $temperatura = null;
    public ?float $saturacionOxigeno = null;
    
    // BALANCE HIDRICO
    public ?int $diuresis = null;
    public ?string $deposicion = null;
    
    // SUEROTERAPIA
    public ?float $dosis = null;

    // HIGIENE
    public ?string $higieneDescripcion = null;
    
    // REGISTRO
    public ?string $fecha = null;
    public ?string $toma = null;
    // public ?int $auxiliarId;
    // public ?int $pacienteId;
    // public ?int $observacionId;
    // public ?int $dietaId;
    // public ?int $drenajeId;
    // public ?int $movilizacionId;
    // public ?int $constantesVitalesId;
    // public ?int $balanceHidricoId;
    // public ?int $sueroterapiaId;
    // public ?int $higieneId;
    
}