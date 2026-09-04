<?php

namespace App\Enum;

enum PurchasesStatusEnum: string
{
    case PENDIENTE = 'PENDIENTE';
    case REGISTRADO = 'REGISTRADO';
    case PEDIDO = 'PEDIDO';
    case RECIBIDO = 'RECIBIDO';
    case ANULADO = 'ANULADO';

    public function label(): string
    {
        return match ($this) {
            self::PENDIENTE => 'PENDIENTE',
            self::REGISTRADO => 'REGISTRADO',
            self::PEDIDO => 'PEDIDO',
            self::RECIBIDO => 'RECIBIDO',
            self::ANULADO => 'ANULADO',
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
