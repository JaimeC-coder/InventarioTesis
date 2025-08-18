<?php

declare(strict_types=1);
use Rector\Config\RectorConfig;
// Reglas puntuales
// Sets generales
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/routes',
        __DIR__ . '/database',
        __DIR__ . '/config',
        __DIR__ . '/tests',
    ]);
    // Evita tocar Blade, vendor, storage, etc.
    $rectorConfig->skip([
        __DIR__ . '/vendor/*',
        __DIR__ . '/storage/*',
        __DIR__ . '/bootstrap/*',
        __DIR__ . '/resources/views/*',
    ]);
    // Conjuntos recomendados (ajusta a tu versión de PHP)
    $rectorConfig->sets([
        SetList::PHP_82,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::NAMING,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,      // cambia if anidados por return temprano
    ]);
    // Opcional: ignora vendor/ y storage/ , tests/
    $rectorConfig->skip([
        __DIR__ . '/vendor/*',
        __DIR__ . '/storage/*',
        __DIR__ . '/tests/*',
    ]);
    // (Opcional) Si instalas rector/rector-laravel, puedes activar sets de Laravel:
    // use RectorLaravel\Set\LaravelSetList;
    // $rectorConfig->sets([LaravelSetList::LARAVEL_90 /* o el que corresponda */]);
};
