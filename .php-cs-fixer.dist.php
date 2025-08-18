<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use App\PhpCsFixer\RemoveBlankLinesInMethodsFixer;
use App\PhpCsFixer\RemoveMethodPhpDocFixer;

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
        new RemoveMethodPhpDocFixer(), // 👈 nuevo fixer
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
        'array_indentation' => true,
        'single_class_element_per_statement' => true,
        'array_syntax' => ['syntax' => 'short'], // arrays cortos []
        'no_trailing_comma_in_singleline' => true, // evita coma final en arrays de una sola línea
        'method_chaining_indentation' => true,
        'trailing_comma_in_multiline' => ['after_heredoc' => true],
        'no_unused_imports' => true, // limpia imports
        'phpdoc_indent' => true,
        'phpdoc_align' => [
            'align' => 'vertical',
        ],
        'no_empty_comment' => true,
        'class_definition' => true,
        'no_empty_phpdoc' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'single_line_empty_body' => true,

    ])
    ->setFinder($finder);
