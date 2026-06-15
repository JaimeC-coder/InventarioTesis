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
            self::SI => 'Sin Identidad',
            self::DNI => 'DNI',
            self::RUC => 'RUC',
            self::PASAPORTE => 'PASAPORTE',
            self::CARNET_EXTRANJERIA => 'CARNET_EXTRANJERIA',
        };
    }
}
