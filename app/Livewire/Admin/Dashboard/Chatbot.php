<?php

namespace App\Livewire\Admin\Dashboard;

use App\Services\Chatbot\ChatbotQueryService;
use App\Services\Chatbot\ReportExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class Chatbot extends Component
{
    public array $messages = [];

    public string $input = '';

    public bool $isLoading = false;

    public array $lastReportData = [];

    public string $lastReportTitle = '';

    public function mount(): void
    {
        $this->resetConversation();
    }

    public function resetConversation(): void
    {
        $this->messages = [[
            'role' => 'assistant',
            'type' => 'greeting',
            'content' => '¡Hola! ¿En qué puedo ayudarte? Elige una opción o escribe tu pregunta.',
            'suggestions' => $this->suggestions,
        ]];
    }

    public function send(?string $quickPrompt = null): void
    {
        $text = $quickPrompt ?? trim($this->input);
        if ($text === '') {
            return;
        }

        $this->messages[] = ['role' => 'user', 'type' => 'text', 'content' => $text];
        $this->input = '';
        $this->isLoading = true;
        // Construye el historial solo con mensajes de texto reales (sin el greeting con botones)
        $history = collect($this->messages)
            ->filter(fn($m): bool => in_array($m['type'], ['text', 'table']))
            ->map(fn($m): array => ['role' => $m['role'], 'content' => is_array($m['content']) ? json_encode($m['content']) : $m['content']])
            ->values()
            ->all();
        $result = app(ChatbotQueryService::class)->handleUserMessage(Auth::user(), $history, ['title' => $this->lastReportTitle, 'data' => $this->lastReportData]);
        $this->messages[] = $this->formatResult($result);
        $this->isLoading = false;
    }

    private function formatResult(array $result): array
    {
        if (isset($result['error'])) {
            return ['role' => 'assistant', 'type' => 'text', 'content' => $result['error']];
        }

        if (isset($result['file'])) {
            $downloadUrl = URL::temporarySignedRoute(
                'admin.chatbot.download',
                now()->addMinutes(15), // el link deja de funcionar después de 15 min
                ['filename' => basename($result['path'])]
            );

            return [
                'role' => 'assistant',
                'type' => 'file',
                'content' => 'Tu reporte está listo.',
                'url' => $downloadUrl,
            ];
        }

        if (isset($result['data'])) {
            // Se guarda para que un futuro "exportar" tenga de dónde sacar los datos
            $this->lastReportData = $result['data'];
            $this->lastReportTitle = $result['label'] ?? 'Reporte';

            return ['role' => 'assistant', 'type' => 'table', 'content' => $result['data']];
        }

        return ['role' => 'assistant', 'type' => 'text', 'content' => $result['reply'] ?? 'No entendí la consulta.'];
    }

    public function getSuggestionsProperty(): array
    {
        $user = Auth::user();
        $groups = [];
        if ($user->can('chatbot.query.customer')) {
            $groups['Clientes'] = ['Top clientes del mes', 'Clientes con más compras'];
        }

        if ($user->can('chatbot.query.product')) {
            $groups['Productos'] = ['Productos más vendidos', 'Productos con menos ventas', 'Stock actual'];
        }

        if ($user->can('chatbot.query.conversion')) {
            $groups['Conversión'] = ['Cotización a venta'];
        }

        return $groups;
    }

    public function exportReport(string $format): void
    {
        if ($this->lastReportData === []) {
            return;
        }

        $result = app(ReportExportService::class)->export($format, $this->lastReportTitle, $this->lastReportData);
        $labels = ['pdf' => 'PDF', 'excel' => 'Excel', 'txt' => 'archivo de texto'];

        $downloadUrl = URL::temporarySignedRoute(
            'admin.chatbot.download',
            now()->addMinutes(15), // el link deja de funcionar después de 15 min
            ['filename' => basename($result['path'])]
        );




        $this->messages[] = [
            'role' => 'assistant',
            'type' => 'file',
            'content' => sprintf('Tu reporte en %s está listo.', $labels[$format]),
            'url' => $downloadUrl,
        ];
    }

    // public function exportReport(string $format): void
    // {
    //     $this->send("Exportar el último reporte a {$format}"); // reutiliza el mismo flujo, pasa por exportLastResult
    // }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.dashboard.chatbot');
    }
}
