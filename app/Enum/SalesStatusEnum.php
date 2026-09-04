<?php

namespace App\Enum;

enum SalesStatusEnum: string
{
    //$blueprint->enum('status', ['REGISTRADO','CARGADO', 'RUTA','ENTREGADO'])->default('REGISTRADO');
    case REGISTRADO = 'REGISTRADO';
    case CARGADO = 'CARGADO';
    case RUTA = 'RUTA';
    case ENTREGADO = 'ENTREGADO';

    public function label(): string
    {
        return match ($this) {
            self::REGISTRADO => 'Registro',
            self::CARGADO => 'Cargado',
            self::RUTA => 'En Ruta',
            self::ENTREGADO => 'Entregado',
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
