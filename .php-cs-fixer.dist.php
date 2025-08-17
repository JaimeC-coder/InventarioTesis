<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use App\PhpCsFixer\RemoveBlankLinesInMethodsFixer;

$finder = Finder::create()
    ->in(__DIR__) // busca en TODO el proyecto
    ->exclude('storage')   // excluye carpeta storage
    ->exclude('bootstrap') // excluye carpeta bootstrap
    ->exclude('vendor')    // excluye vendor (si quieres, ya que ahí nunca se toca)
    ->exclude('node_modules') // excluye node_modules
    ->exclude('tests')
    ->exclude('stubs')
    ->exclude('storage')
    ->notName('*.blade.php'); // excluye archivos Blade

return (new Config())
    ->registerCustomFixers([
        new RemoveBlankLinesInMethodsFixer(),
    ])
    ->setRules([
        'App/remove_blank_lines_in_methods' => true,
        '@PSR12' => true, // estándar oficial PSR-12
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'throw',
                'use',
                'curly_brace_block',
                'parenthesis_brace_block',
                'square_brace_block',
                'return',
                'continue',
                'break',
                'case',
                'default',
            ],
        ],
        'array_syntax' => ['syntax' => 'short'], // arrays cortos []
        'no_unused_imports' => true, // limpia imports
        'phpdoc_indent' => true,
        'phpdoc_align' => [
            'align' => 'vertical',
        ]

    ])
    ->setFinder($finder);
