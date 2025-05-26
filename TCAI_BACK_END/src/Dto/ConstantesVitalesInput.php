<?php

namespace App\Dto;

class ConstantesVitalesInput
{
    public ?int $taSistolica = null;
    public ?int $taDiastolica = null;
    public ?float $frecuenciaRespiratoria = null;
    public ?float $pulso = null;
    public ?float $temperatura = null;
    public ?float $saturacionOxigeno = null;
}
