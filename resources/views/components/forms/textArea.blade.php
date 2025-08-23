@props([
    'label',
    'name',
    'id' => null, // ID personalizado, si no se pasa usa el name
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'rows' => 4, // Altura del textarea
    'cols' => null, // Ancho del textarea (opcional)
    'maxlength' => null, // Límite de caracteres
    'state' => null, // null = auto-detect from errors, 'success', 'error' para forzar estado
    'message' => null, // Mensaje personalizado, si es null usa errores de Laravel
    'showCounter' => false, // Mostrar contador de caracteres
])

@php
    // Si no se pasa ID, usar el name como ID
    $inputId = $id ?? $name;

    // Auto-detectar estado desde errores de Laravel si no se especifica
    $hasError = $errors->has($name);
    $errorMessage = $hasError ? $errors->first($name) : null;

    // Determinar el estado final
    if ($state === null) {
        $finalState = $hasError ? 'error' : 'default';
    } else {
        $finalState = $state;
    }

    // Mensaje final a mostrar
    $finalMessage = $message ?? $errorMessage;

    // Configuración de clases según el estado
    $stateClasses = [
        'default' => [
            'label' => 'text-gray-900 dark:text-white',
            'textarea' =>
                'bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:placeholder-gray-400',
            'message' => 'text-gray-600 dark:text-gray-400',
        ],
        'success' => [
            'label' => 'text-green-700 dark:text-green-500',
            'textarea' =>
                'bg-green-50 border-green-500 text-green-900 dark:text-green-400 placeholder-green-700 dark:placeholder-green-500 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-green-500',
            'message' => 'text-green-600 dark:text-green-500',
        ],
        'error' => [
            'label' => 'text-red-700 dark:text-red-500',
            'textarea' =>
                'bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500',
            'message' => 'text-red-600 dark:text-red-500',
        ],
    ];

    $currentState = $stateClasses[$finalState] ?? $stateClasses['default'];
@endphp

<div class="mb-4">
    {{-- Label --}}
    <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium {{ $currentState['label'] }}">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
        @if ($showCounter && $maxlength)
            <span class="float-right text-xs text-gray-500 dark:text-gray-400">
                <span id="{{ $inputId }}_counter">0</span>/{{ $maxlength }}
            </span>
        @endif
    </label>

    {{-- TextArea --}}
    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($cols) cols="{{ $cols }}" @endif
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        class="border text-sm rounded-lg block w-full p-2.5 resize-y {{ $currentState['textarea'] }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class']) }}
    >{{ old($name, $value) }}</textarea>

    {{-- Mensaje de error/éxito --}}
    @if ($finalMessage)
        <p class="mt-2 text-sm {{ $currentState['message'] }}">
            @if ($finalState === 'success')
                <span class="font-medium">¡Bien hecho!</span>
            @elseif($finalState === 'error')
                <span class="font-medium">Error:</span>
            @endif
            {{ $finalMessage }}
        </p>
    @endif
</div>

{{-- JavaScript para contador de caracteres --}}
@if($showCounter && $maxlength)
@once
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar contador de caracteres
    function updateCharacterCounter(textarea) {
        const counterId = textarea.id + '_counter';
        const counter = document.getElementById(counterId);
        if (counter) {
            counter.textContent = textarea.value.length;
        }
    }

    // Aplicar contador a todos los textareas con maxlength
    document.querySelectorAll('textarea[maxlength]').forEach(function(textarea) {
        // Actualizar contador inicialmente
        updateCharacterCounter(textarea);

        // Actualizar contador mientras se escribe
        textarea.addEventListener('input', function() {
            updateCharacterCounter(this);
        });

        // Actualizar contador al pegar contenido
        textarea.addEventListener('paste', function() {
            setTimeout(() => {
                updateCharacterCounter(this);
            }, 0);
        });
    });
});
</script>
@endonce
@endif
