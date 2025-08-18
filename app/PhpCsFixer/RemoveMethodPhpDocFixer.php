<?php

namespace App\PhpCsFixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\CodeSample;

final class RemoveMethodPhpDocFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Elimina cualquier PHPDoc que esté directamente arriba de un método.',
            [new CodeSample("<?php\nclass A {\n    /** Some text */\n    public function foo() {}\n}")]
        );
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = 0; $index < $tokens->count(); $index++) {
            if ($tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
                $docIndex = $index;
                $nextIndex = $tokens->getNextMeaningfulToken($docIndex);
                if ($tokens[$nextIndex]->isGivenKind(T_FUNCTION)) {
                    // Elimina el docblock
                    $tokens->clearTokenAndMergeSurroundingWhitespace($docIndex);
                }
            }
        }
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return 'App/remove_method_phpdoc';
    }
}
