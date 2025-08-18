<?php

use App\PhpCsFixer\RemoveBlankLinesInMethodsFixer;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->exclude('node_modules')
    ->exclude('storage')
    ->exclude('bootstrap/cache')
    ->exclude('tests')
    ->notName('*.blade.php');

return (new Config())
    ->registerCustomFixers([
        new RemoveBlankLinesInMethodsFixer(),
    ])
    ->setRules([
        '@PSR12' => true,

        // 🔥 SOLUCIÓN: Sobrescribir la regla de definición de clases
        'class_definition' => [
            'single_line' => true,
            'single_item_single_line' => true,
            'multi_line_extends_each_single_line' => false,
            'space_before_parenthesis' => false, // ← CLAVE: sin espacio antes del paréntesis
        ],

        // Mantener `()` en clases anónimas
        'new_with_parentheses' => [
            'anonymous_class' => true,
        ],

        // 🔧 Tu fixer custom
        'App/remove_blank_lines_in_methods' => true,

        // Arrays estilo Laravel
        'array_syntax' => ['syntax' => 'short'],
        'array_indentation' => true,
        'trailing_comma_in_multiline' => ['after_heredoc' => true],

        // Imports
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],

        // Espaciado en clases (Laravel style)
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'property' => 'one',
                'method' => 'one',
                'trait_import' => 'one',
            ],
        ],

        // Saltos de línea innecesarios
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'use',
                'return',
                'throw',
            ],
        ],

        // Espaciado en funciones
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],

        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],

        // Comentarios y PHPDoc
        'phpdoc_trim' => true,
        'phpdoc_indent' => true,
        'phpdoc_align' => ['align' => 'vertical'],
        'phpdoc_separation' => false,
        'no_empty_phpdoc' => true,

        // Misceláneo
        'single_quote' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'method_chaining_indentation' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(true);
