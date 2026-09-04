<?php

namespace App\Enum;

enum KardexTypeEnum: string
{
    //Entrada ,Salida ,Traslado
    case ENTRADA = 'ENTRADA';
    case SALIDA = 'SALIDA';
    case TRASLADO = 'TRASLADO';
    case TRASLADO_IGS = 'Traslado-IGS'; // salida del almacén origen
    case TRASLADO_IGD = 'Traslado-IGD'; // ingreso al almacén destino
    case OTROS = 'Otros';

    public function label(): string
    {
        return match ($this) {
            self::ENTRADA => 'Entrada',
            self::SALIDA => 'Salida',
            self::TRASLADO => 'Traslado',
            self::TRASLADO_IGS => 'Traslado-IGS',
            self::TRASLADO_IGD => 'Traslado-IGD',
            self::OTROS => 'Otros',
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
