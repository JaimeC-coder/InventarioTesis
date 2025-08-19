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
            'Elimina líneas en blanco excesivas de forma inteligente según el contexto.',
            [new CodeSample("<?php\nclass A {\n    public function foo() {\n        \n        \n        \$x = 1;\n    }\n}")]
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
        for ($index = 0; $index < $tokens->count(); $index++) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            // Encuentra el inicio del cuerpo de la función
            $openBraceIndex = $tokens->getNextTokenOfKind($index, ['{']);
            if ($openBraceIndex === null) {
                continue;
            }

            // Encuentra el final de la función
            $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openBraceIndex);
            // Determina el tipo de función
            $functionType = $this->determineFunctionType($tokens, $index);
            // Procesa según el tipo
            $this->fixBlankLinesInRange($tokens, $openBraceIndex + 1, $closeBraceIndex - 1, $functionType);
            // Salta al final de esta función
            $index = $closeBraceIndex;
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function determineFunctionType(Tokens $tokens, int $functionIndex): string
    {
        // Busca hacia atrás desde T_FUNCTION para determinar el contexto
        for ($i = $functionIndex - 1; $i >= 0; $i--) {
            $token = $tokens[$i];
            // Si encuentra 'function(' es probablemente una closure
            if ($token->equals('(')) {
                $prevToken = $tokens->getPrevMeaningfulToken($i);
                if ($prevToken !== null && $tokens[$prevToken]->isGivenKind(T_FUNCTION)) {
                    return 'closure';
                }
            }

            // Si encuentra modificadores de visibilidad, es un método de clase
            if ($token->isGivenKind([T_PUBLIC, T_PRIVATE, T_PROTECTED, T_STATIC])) {
                return 'class_method';
            }

            // Si encuentra 'class' o 'interface', es un método
            if ($token->isGivenKind([T_CLASS, T_INTERFACE, T_TRAIT])) {
                return 'class_method';
            }

            // Si encuentra tokens que indican que estamos fuera de una clase
            if ($token->isGivenKind([T_NAMESPACE, T_USE]) || $token->equals(';')) {
                break;
            }
        }

        // Por defecto, asume que es una función regular
        return 'regular_function';
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function fixBlankLinesInRange(Tokens $tokens, int $startIndex, int $endIndex, string $functionType): void
    {
        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $token = $tokens[$i];
            if (!$token->isWhitespace()) {
                continue;
            }

            $content = $token->getContent();
            $newlineCount = substr_count($content, "\n");
            // Solo actúa si hay múltiples líneas en blanco
            if ($newlineCount >= 2) {
                $newContent = $this->getNewContentBasedOnType($tokens, $i, $content, $functionType);
                $tokens[$i] = new Token([T_WHITESPACE, $newContent]);
            }
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function getNewContentBasedOnType(Tokens $tokens, int $index, string $originalContent, string $functionType): string
    {
        $lines = explode("\n", $originalContent);
        $lastLine = end($lines);
        $indentation = preg_replace('/[^ \t]/', '', $lastLine);
        switch ($functionType) {
            case 'closure':
            default:
                return "\n" . $indentation;
            case 'class_method':
                // En métodos de clase: mantener separación lógica
                return $this->handleClassMethodSpacing($tokens, $index, $indentation);
            case 'regular_function':
                // En funciones regulares: comportamiento intermedio
                return $this->handleRegularFunctionSpacing($tokens, $index, $indentation);
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function handleClassMethodSpacing(Tokens $tokens, int $index, string $indentation): string
    {
        $prevToken = $this->getPreviousNonWhitespaceToken($tokens, $index);
        $nextToken = $this->getNextNonWhitespaceToken($tokens, $index);
        // Mantener 2 líneas antes de return
        if ($nextToken && $nextToken->isGivenKind(T_RETURN)) {
            return "\n\n" . $indentation;
        }

        // Mantener 1 línea después de bloques complejos
        if ($prevToken && $prevToken->equals('}')) {
            return "\n\n" . $indentation;
        }

        // Mantener 1 línea después de assignments o method calls
        // Verifica si es una línea "compleja" mirando hacia atrás
        if ($prevToken && $prevToken->equals(';') && $this->isComplexStatement($tokens, $index)) {
            return "\n\n" . $indentation;
        }

        // Por defecto: 1 línea
        return "\n" . $indentation;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function handleRegularFunctionSpacing(Tokens $tokens, int $index, string $indentation): string
    {
        $nextToken = $this->getNextNonWhitespaceToken($tokens, $index);
        // Solo mantener separación antes de return
        if ($nextToken && $nextToken->isGivenKind(T_RETURN)) {
            return "\n\n" . $indentation;
        }

        // Para todo lo demás, 1 línea
        return "\n" . $indentation;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function isComplexStatement(Tokens $tokens, int $whitespaceIndex): bool
    {
        // Busca hacia atrás para determinar si la declaración anterior es "compleja"
        $prevIndex = $tokens->getPrevNonWhitespace($whitespaceIndex);
        if ($prevIndex === null) {
            return false;
        }

        // Cuenta tokens hacia atrás hasta encontrar el inicio de la declaración
        $tokenCount = 0;
        for ($i = $prevIndex; $i >= 0 && $tokenCount < 20; $i--) {
            $token = $tokens[$i];
            if ($token->equals(';') || $token->equals('{') || $token->equals('}')) {
                break;
            }

            if (!$token->isWhitespace() && !$token->isComment()) {
                $tokenCount++;
            }
        }

        // Si tiene más de 8 tokens, consideramos que es compleja
        return $tokenCount > 8;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function getPreviousNonWhitespaceToken(Tokens $tokens, int $index): ?Token
    {
        $prevIndex = $tokens->getPrevNonWhitespace($index);
        return $prevIndex !== null ? $tokens[$prevIndex] : null;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function getNextNonWhitespaceToken(Tokens $tokens, int $index): ?Token
    {
        $nextIndex = $tokens->getNextNonWhitespace($index);
        return $nextIndex !== null ? $tokens[$nextIndex] : null;
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
        return -10;
    }
}
