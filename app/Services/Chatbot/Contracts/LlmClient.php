<?php

// app/Services/Chatbot/Contracts/LlmClient.php

namespace App\Services\Chatbot\Contracts;

interface LlmClient
{
    /**
     * Envía el mensaje del usuario + las tools disponibles, devuelve
     * la decisión del modelo (qué tool llamar, o texto directo).
     */
    public function decide(array $history, array $tools): LlmDecision;

    /**
     * Envía el resultado de la tool ejecutada, devuelve la respuesta final en texto.
     */
    public function respondWithToolResult(array $conversationHistory, array $toolCall, array $toolResult): string;
}
