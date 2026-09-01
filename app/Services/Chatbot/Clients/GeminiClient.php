<?php

namespace App\Services\Chatbot\Clients;

use App\Services\Chatbot\Contracts\LlmClient;
use App\Services\Chatbot\Contracts\LlmDecision;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// app/Services/Chatbot/Clients/GeminiClient.php

class GeminiClient implements LlmClient
{
    public function decide(array $history, array $tools): LlmDecision
    {
        $response = $this->request([
            'systemInstruction' => ['parts' => [['text' => $this->systemPrompt()]]],
            'contents' => $this->mapHistoryToGeminiFormat($history),
            'tools' => [['functionDeclarations' => $this->mapTools($tools)]],
        ]);
        if (!$response) {
            return new LlmDecision(toolCall: null, text: 'Hubo un problema consultando el asistente, intenta de nuevo.');
        }

        $part = $response['candidates'][0]['content']['parts'][0] ?? null;
        if (isset($part['functionCall'])) {
            return new LlmDecision(
                toolCall: ['id' => null, 'name' => $part['functionCall']['name'], 'input' => $part['functionCall']['args'], 'thought_signature' => $part['thoughtSignature'] ?? null,],
                text: null,
            );
        }

        return new LlmDecision(toolCall: null, text: $part['text'] ?? '¿Puedes darme un poco más de detalle?');
    }

    public function respondWithToolResult(array $history, array $toolCall, array $toolResult): string
    {
        $contents = $this->mapHistoryToGeminiFormat($history);
        $modelPart = ['functionCall' => ['name' => $toolCall['name'], 'args' => $toolCall['input']]];
        if (!empty($toolCall['thought_signature'])) {
            $modelPart['thoughtSignature'] = $toolCall['thought_signature']; // se reenvía sin modificar
        }

        $contents[] = ['role' => 'model', 'parts' => [$modelPart]];
        $contents[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $toolCall['name'], 'response' => $toolResult]]]];
        $response = $this->request([
            'systemInstruction' => ['parts' => [['text' => $this->systemPrompt()]]],
            'contents' => $contents,
        ]);
        if (!$response) {
            return 'Hubo un problema generando la respuesta, intenta de nuevo.';
        }

        return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No pude generar una respuesta.';
    }

    private function mapHistoryToGeminiFormat(array $history): array
    {
        return collect($history)->map(fn($msg): array => [
            'role' => $msg['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $msg['content']]],
        ])->values()->all();
    }

    private function request(array $payload): ?array
    {
        $response = Http::timeout(30)
            ->retry(2, 200)
            ->post(
                'https://generativelanguage.googleapis.com/v1beta/models/'
                    . config('services.gemini.model')
                    . ':generateContent?key=' . config('services.gemini.key'),
                $payload
            );
        if ($response->failed()) {
            Log::error('chatbot.llm_error', [
                'provider' => 'gemini',
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json();
    }

    private function mapTools(array $tools): array
    {
        // Gemini usa "parameters" (subset de JSON Schema) en vez de "input_schema"
        return collect($tools)->map(fn($tool): array => [
            'name' => $tool['name'],
            'description' => $tool['description'],
            'parameters' => $this->normalizeSchemaTypes($tool['input_schema']),
        ])->values()->all();
    }

    private function normalizeSchemaTypes(array $schema): array
    {
        if (isset($schema['type'])) {
            $schema['type'] = strtoupper($schema['type']);
        }

        if (isset($schema['properties'])) {
            $schema['properties'] = collect($schema['properties'])
                ->map(fn($prop): array => $this->normalizeSchemaTypes($prop))
                ->all();
        }

        if (isset($schema['items'])) {
            $schema['items'] = $this->normalizeSchemaTypes($schema['items']);
        }

        return $schema;
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
