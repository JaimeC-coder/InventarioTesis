<?php

namespace App\Enum;

enum PurchasesStatusEnum: string
{
    case REGISTRADO = 'REGISTRADO';
    case PEDIDO = 'PEDIDO';
    case RECIBIDO = 'RECIBIDO';

    public function label(): string
    {
        return match ($this) {
            self::REGISTRADO => 'Registro',
            self::PEDIDO => 'Pedido',
            self::RECIBIDO => 'Recibido',
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
