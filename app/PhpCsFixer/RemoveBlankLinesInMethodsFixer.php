<?php

namespace App\PhpCsFixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

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
        $insideMethod = false;
        for ($index = 0; $index < $tokens->count(); $index++) {
            $token = $tokens[$index];
            if ($token->isGivenKind(T_FUNCTION)) {
                $insideMethod = true;
            }

            if ($insideMethod && $token->equals('}')) {
                $insideMethod = false;
            }

            if ($insideMethod && $token->isWhitespace() && substr_count($token->getContent(), "\n") > 1) {
                $tokens[$index] = new Token([T_WHITESPACE, "\n"]);
            }
        }
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return 'App/remove_blank_lines_in_methods';
    }

    public function getPriority(): int
    {
        return -10; // corre antes que class_attributes_separation
    }
}
