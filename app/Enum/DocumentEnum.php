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

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case): array => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }
}
