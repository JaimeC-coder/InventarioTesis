<?php

namespace App\Livewire\Admin\Dashboard;

use App\Services\Chatbot\ChatbotQueryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chatbot extends Component
{
    public array $messages = [];

    public string $input = '';

    public bool $isLoading = false;

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

    private function formatResult(array $result): array
    {
        if (isset($result['error'])) {
            return ['role' => 'assistant', 'type' => 'text', 'content' => $result['error']];
        }

        if (isset($result['data'])) {
            return ['role' => 'assistant', 'type' => 'table', 'content' => $result['data']];
        }

        return ['role' => 'assistant', 'type' => 'text', 'content' => $result['reply'] ?? 'No entendí la consulta.'];
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
        $result = app(ChatbotQueryService::class)->handleUserMessage(auth()->user(), $history);
        $this->messages[] = $this->formatResult($result);
        $this->isLoading = false;
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

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.dashboard.chatbot');
    }
}
