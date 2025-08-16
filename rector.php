<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

// Reglas puntuales
use Rector\DeadCode\Rector\Use_\RemoveUnusedImportsRector;
use Rector\CodingStyle\Rector\Namespace_\ImportFullyQualifiedNamesRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;

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
        SetList::TYPE_DECLARATION,
    ]);

    // Reglas específicas convenientes
    $rectorConfig->rules([
        RemoveUnusedImportsRector::class,            // borra use no usados
        ImportFullyQualifiedNamesRector::class,      // organiza imports
        InlineConstructorDefaultToPropertyRector::class, // promociona props en el ctor (PHP 8)
        TypedPropertyFromAssignsRector::class,       // infiere tipos en propiedades
        SimplifyIfReturnBoolRector::class,           // simplifica ifs que devuelven bool
    ]);

    // (Opcional) Si instalas rector/rector-laravel, puedes activar sets de Laravel:
    // use RectorLaravel\Set\LaravelSetList;
    // $rectorConfig->sets([LaravelSetList::LARAVEL_90 /* o el que corresponda */]);
};
