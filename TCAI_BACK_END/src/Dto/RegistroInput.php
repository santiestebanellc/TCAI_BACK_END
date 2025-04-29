<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegistroInput
{
    // TO DELETE
    // public ?\DateTimeInterface $fecha = null;

    // #[Assert\NotBlank]
    // public ?string $toma = null;

    // #[Assert\NotNull]
    // public ?int $pacienteId = null;

    // #[Assert\NotNull]
    // public ?int $auxiliarId = null;

    #[Assert\Valid] public ?DietaInput $dieta = null;
    #[Assert\Valid] public ?ObservacionInput $observacion = null;
    #[Assert\Valid] public ?DrenajeInput $drenaje = null;
    #[Assert\Valid] public ?HigieneInput $higiene = null;
    #[Assert\Valid] public ?ConstantesVitalesInput $constantesVitales = null;
    #[Assert\Valid] public ?MovilizacionInput $movilizacion = null;
    #[Assert\Valid] public ?SueroterapiaInput $sueroterapia = null;
    #[Assert\Valid] public ?BalanceHidricoInput $balanceHidrico = null;
}



?>