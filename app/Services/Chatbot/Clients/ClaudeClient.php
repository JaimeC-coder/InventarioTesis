<?php

namespace App\Services\Chatbot\Clients;

use App\Services\Chatbot\Contracts\LlmClient;
use App\Services\Chatbot\Contracts\LlmDecision;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeClient implements LlmClient
{
    public function decide(array $history, array $tools): LlmDecision
    {
        $response = $this->request([
            'system' => $this->systemPrompt(),
            'messages' => $history,
            'tools' => $tools,
        ]);
        if (!$response) {
            return new LlmDecision(toolCall: null, text: 'Hubo un problema consultando el asistente, intenta de nuevo.');
        }

        $toolBlock = collect($response['content'] ?? [])->firstWhere('type', 'tool_use');
        $textBlock = collect($response['content'] ?? [])->firstWhere('type', 'text');
        if ($toolBlock) {
            return new LlmDecision(
                toolCall: ['id' => $toolBlock['id'], 'name' => $toolBlock['name'], 'input' => $toolBlock['input']],
                text: null,
            );
        }

        if (!$textBlock) {
            // Esto solo debería pasar si la API cambió de forma o hay un error inesperado.
            Log::warning('chatbot.empty_response', ['raw' => $response]);
        }

        return new LlmDecision(toolCall: null, text: $textBlock['text'] ?? null);
    }

    public function respondWithToolResult(array $conversationHistory, array $toolCall, array $toolResult): string
    {
        $response = $this->request([
            'messages' => [
                ['role' => 'user', 'content' => collect($conversationHistory)->pluck('content')->implode("\n")],
                ['role' => 'assistant', 'content' => [[
                    'type' => 'tool_use',
                    'id' => $toolCall['id'],
                    'name' => $toolCall['name'],
                    'input' => $toolCall['input'],
                ]]],
                ['role' => 'user', 'content' => [[
                    'type' => 'tool_result',
                    'tool_use_id' => $toolCall['id'],
                    'content' => json_encode($toolResult),
                ]]],
            ],
        ]);
        if (!$response) {
            return 'Hubo un problema generando la respuesta, intenta de nuevo.';
        }

        return collect($response['content'] ?? [])->firstWhere('type', 'text')['text'] ?? 'No pude generar una respuesta.';
    }

    private function request(array $payload): ?array
    {
        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
        ])
            ->timeout(30)
            ->retry(2, 200)
            ->post('https://api.anthropic.com/v1/messages', array_merge([
                'model' => config('services.anthropic.model'),
                'max_tokens' => 1024,
            ], $payload));
        if ($response->failed()) {
            Log::error('chatbot.llm_error', [
                'provider' => 'claude',
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json();
    }

    private function systemPrompt(): string
    {
        return <<<TEXT
                    Eres el asistente de reportes de un sistema de ventas e inventario. Tu tono es cercano,
                    claro y directo — como un colega que conoce bien los números de la empresa, no como un
                    bot corporativo.

                    Puedes conversar con el usuario sobre cualquier tema general (clima, cultura general,
                    charla casual, etc.) usando tu conocimiento normal, sin necesidad de ninguna herramienta.

                    Reglas para temas del SISTEMA (ventas, clientes, productos, inventario, conversión):
                    - SIEMPRE usa la herramienta queryMetric para cualquier pregunta sobre datos del negocio.
                    NUNCA respondas con cifras o reportes desde tu propio conocimiento — solo desde lo que
                    te devuelva la herramienta.
                    - Si el usuario no tiene permiso para ese reporte, la herramienta te lo indicará; en ese
                    caso explícale con amabilidad que no tiene acceso a esa información, sin dar detalles
                    técnicos de por qué.
                    - Si no tienes claro qué reporte pidió, pregunta para aclarar en vez de adivinar.

                    Para todo lo demás (charla casual, preguntas generales, temas que no son del negocio):
                    - Responde normalmente, sin usar herramientas, como lo haría cualquier asistente conversacional.

                    Nunca mezcles ambos mundos: si estás hablando del clima y el usuario pregunta algo del
                    negocio a la mitad, cambia al modo de reporte y usa la herramienta como corresponde.
                    TEXT;
    }
}
