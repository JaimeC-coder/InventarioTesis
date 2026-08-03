<?php

namespace App\Enum;

enum ProductablePriceStatusEnum: string
{
    //            // $blueprint->enum('price_type', ['A1', 'GENERAL', 'QUOTE','NONE','MANUAL'])->default('NONE');

    case A1 = 'A1';
    case GENERAL = 'GENERAL';
    case QUOTE = 'QUOTE';
    case NONE = 'NONE';

    public function label(): string
    {
        return match ($this) {
            self::A1 => 'A1',
            self::GENERAL => 'General',
            self::QUOTE => 'Quote',
            self::NONE => 'None',
        };
    }

    public static function options(): array
    {
        return [
            self::A1->value => self::A1->label(),
            self::GENERAL->value => self::GENERAL->label(),
            self::QUOTE->value => self::QUOTE->label(),
            self::NONE->value => self::NONE->label(),
        ];
    }
}
