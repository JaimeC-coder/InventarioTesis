{{-- resources/views/livewire/chatbot-page.blade.php --}}

<div class="flex flex-col h-[calc(100vh-4rem)] border border-gray-200 rounded-xl overflow-hidden bg-gray-50">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200 bg-white">
        <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">Asistente de reportes</p>
            <p class="text-xs text-gray-400">{{ auth()->user()->getRoleNames()->first() }}</p>
        </div>
        <button wire:click="resetConversation"
            class="flex items-center gap-1.5 text-sm px-3 py-1.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva consulta
        </button>
    </div>

    {{-- Mensajes, centrados con ancho máximo para que no se estire feo en pantallas grandes --}}
    <div class="flex-1 overflow-y-auto px-6 py-8" x-init="$watch('$wire.messages', () => $nextTick(() => $el.scrollTop = $el.scrollHeight))">
        <div class="max-w-3xl mx-auto space-y-5">
            @foreach ($messages as $message)
                @if ($message['role'] === 'user')
                    <div class="flex justify-end">
                        <div class="bg-blue-600 text-white text-sm rounded-2xl rounded-br-sm px-4 py-2.5 max-w-[60%]">
                            {{ $message['content'] }}
                        </div>
                    </div>
                @elseif ($message['type'] === 'greeting')
                    <div class="flex justify-start">
                        <div
                            class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-5 py-4 max-w-[75%] space-y-4">
                            <p class="text-sm text-gray-800">{{ $message['content'] }}</p>

                            @foreach ($message['suggestions'] as $group => $options)
                                <div>
                                    <p class="text-[11px] uppercase tracking-wide text-gray-400 mb-2">
                                        {{ $group }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($options as $option)
                                            <button wire:click="send('{{ $option }}')"
                                                class="text-sm px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700">
                                                {{ $option }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif ($message['type'] === 'table')
                    <div class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-5 py-4 max-w-[55%]">
                        <table class="w-full text-sm">
                            @foreach ($message['content'] as $row)
                                <tr class="border-b border-gray-100 last:border-0">
                                    @foreach ($row as $label => $value)
                                        <td class="py-2 {{ $loop->last ? 'text-right font-medium' : 'text-gray-600' }}">
                                            {{ $value }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>

                        <div class="mt-3 flex gap-3 text-xs">
                            <button wire:click="exportReport('pdf')" class="text-blue-600 hover:underline">Exportar a
                                PDF</button>
                            <button wire:click="exportReport('excel')" class="text-blue-600 hover:underline">Exportar a
                                Excel</button>
                            <button wire:click="exportReport('txt')" class="text-blue-600 hover:underline">Exportar a
                                texto</button>
                        </div>
                    </div>
                @elseif ($message['type'] === 'file')
                    <div class="flex justify-start">
                        <a href="{{ $message['url'] }}"
                            class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 text-sm text-blue-600 hover:underline flex items-center gap-2 max-w-[70%]">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H8a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6v10a2 2 0 01-2 2z" />
                            </svg>
                            {{ $message['content'] }}
                        </a>
                    </div>
                @else
                    <div class="flex justify-start">
                        <div
                            class="bg-white border border-gray-100 text-gray-800 text-sm rounded-2xl rounded-bl-sm px-4 py-2.5 max-w-[70%]">
                            {{ $message['content'] }}
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($isLoading)
                <div class="flex justify-start">
                    <div
                        class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-2.5 text-sm text-gray-400">
                        Consultando...
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Input --}}
    <div class="px-6 py-5 border-t border-gray-200 bg-white">
        <form wire:submit.prevent="send" class="max-w-3xl mx-auto flex items-center gap-3">
            <input type="text" wire:model="input" placeholder="Escribe tu consulta..."
                class="flex-1 text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 focus:border-gray-400" />
            <button type="submit"
                class="w-10 h-10 rounded-full bg-gray-900 hover:bg-gray-800 flex items-center justify-center flex-shrink-0"
                aria-label="Enviar">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>
    </div>
</div>
