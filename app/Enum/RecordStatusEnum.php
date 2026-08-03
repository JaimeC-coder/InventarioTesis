<?php

namespace App\Enum;

enum RecordStatusEnum: string
{
    //Entrada ,Salida ,Traslado
    case ENTRADA = 'ENTRADA';
    case SALIDA = 'SALIDA';
    case TRASLADO = 'TRASLADO';

    public function label(): string
    {
        return match ($this) {
            self::ENTRADA => 'Entrada',
            self::SALIDA => 'Salida',
            self::TRASLADO => 'Traslado',
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
