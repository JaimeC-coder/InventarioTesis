<?php

namespace App\Services\Chatbot\Contracts;

class LlmDecision
{
    public function __construct(
        public readonly ?array $toolCall, // ['id' => ..., 'name' => ..., 'input' => ...] o null
        public readonly ?string $text,
    ) {
    }
}
