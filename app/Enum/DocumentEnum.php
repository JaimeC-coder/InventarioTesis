<?php

namespace App\Enum;

enum DocumentEnum: String
{
    case SI = 'S.I.';
    case DNI = 'DNI';
    case RUC = 'RUC';
    case PASAPORTE = 'PASAPORTE';
    case CARNET_EXTRANJERIA = 'CARNET_EXTRANJERIA';

    public function label(): string
    {
        return match ($this) {
            self::SI => 'SIN IDENTIDAD',
            self::DNI => 'DNI',
            self::RUC => 'R.U.C.',
            self::PASAPORTE => 'PASAPORTE',
            self::CARNET_EXTRANJERIA => 'CARNET DE EXTRANJERIA',
        };
    }
}
