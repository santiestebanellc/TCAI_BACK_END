<?php

namespace App\Controller;

final class HospitalUtils
{
    // Calcula la toma (Mañana, Tarde, Noche) según la fecha
    public static function calcularToma(\DateTimeInterface $fecha): string
    {
        $hora = (int) $fecha->format('H');

        if ($hora >= 6 && $hora < 14) {
            return 'M'; // Mañana
        } elseif ($hora >= 14 && $hora < 22) {
            return 'T'; // Tarde
        } else {
            return 'N'; // Noche
        }
    }

    public static function calcularEdad(\DateTimeInterface $fechaNacimiento): int
    {
        $hoy = new \DateTime();
        $edad = $hoy->diff($fechaNacimiento);
        return $edad->y;
    }
}
