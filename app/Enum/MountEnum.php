<?php

namespace App\Enum;

enum MountEnum: int
{
    case ENERO = 1;
    case FEBRERO = 2;
    case MARZO = 3;
    case ABRIL = 4;
    case MAYO = 5;
    case JUNIO = 6;
    case JULIO = 7;
    case AGOSTO = 8;
    case SEPTIEMBRE = 9;
    case OCTUBRE = 10;
    case NOVIEMBRE = 11;
    case DICIEMBRE = 12;

    public function label(): string
    {
        return match ($this) {
            self::ENERO => 'Enero',
            self::FEBRERO => 'Febrero',
            self::MARZO => 'Marzo',
            self::ABRIL => 'Abril',
            self::MAYO => 'Mayo',
            self::JUNIO => 'Junio',
            self::JULIO => 'Julio',
            self::AGOSTO => 'Agosto',
            self::SEPTIEMBRE => 'Septiembre',
            self::OCTUBRE => 'Octubre',
            self::NOVIEMBRE => 'Noviembre',
            self::DICIEMBRE => 'Diciembre',
        };
    }
}
