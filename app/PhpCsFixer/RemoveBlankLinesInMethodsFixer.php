<?php

namespace App\PhpCsFixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\CodeSample;

final class RemoveBlankLinesInMethodsFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Elimina líneas en blanco dentro de los métodos.',
            [new CodeSample("<?php\nclass A {\n    public function foo() {\n        \n        \$x = 1;\n    }\n}")]
        );
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $token = $tokens[$index];
            if ($token->isWhitespace() && substr_count($token->getContent(), "\n") > 1) {
                // Deja solo un salto de línea
                $tokens[$index] = new Token([T_WHITESPACE, "\n"]);
            }
        }
    }    public function isRisky(): bool
    {
        return false;
    }
    public function getName(): string
    {
        return 'App/remove_blank_lines_in_methods';
    }
}
