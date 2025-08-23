@props([
    'label',
    'name',
    'id' => null, // ID personalizado, si no se pasa usa el name
    'type' => 'text', // 'text', 'number', 'date', 'email', 'password'
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'state' => null, // null = auto-detect from errors, 'success', 'error' para forzar estado
    'message' => null, // Mensaje personalizado, si es null usa errores de Laravel
])

@php
    // Si no se pasa ID, usar el name como ID
    $inputId = $id ?? $name;
    $inputType = $type === 'number' || $type === 'tel' ? 'text' : $type;
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
            'input' =>
                'bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:placeholder-gray-400',
            'message' => 'text-gray-600 dark:text-gray-400',
        ],
        'success' => [
            'label' => 'text-green-700 dark:text-green-500',
            'input' =>
                'bg-green-50 border-green-500 text-green-900 dark:text-green-400 placeholder-green-700 dark:placeholder-green-500 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-green-500',
            'message' => 'text-green-600 dark:text-green-500',
        ],
        'error' => [
            'label' => 'text-red-700 dark:text-red-500',
            'input' =>
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
    </label>

    {{-- Input --}}
    <input type="{{ $inputType }}" id="{{ $inputId }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        data-input-type="{{ $type }}" autocomplete="false"
        class="border text-sm rounded-lg block w-full p-2.5 {{ $currentState['input'] }}"
        placeholder="{{ $placeholder }}"  {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class']) }} />

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

{{-- JavaScript para validación de teclado --}}
@once
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para validar input numérico
            function validateNumericInput(e) {
                // Permitir: backspace, delete, tab, escape, enter
                if ([8, 9, 27, 13, 46].includes(e.keyCode) ||
                    // Permitir: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey) ||
                    (e.keyCode === 67 && e.ctrlKey) ||
                    (e.keyCode === 86 && e.ctrlKey) ||
                    (e.keyCode === 88 && e.ctrlKey) ||
                    // Permitir: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                // Asegurar que es un número y detener si no lo es
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            }

            // Función para validar input de fecha
            function validateDateInput(e) {
                // Permitir: números, guiones y barras
                const allowedKeys = [8, 9, 27, 13, 46, 35, 36, 37, 38, 39, 40]; // teclas especiales
                const isNumber = (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105);
                const isDash = e.keyCode === 189 || e.keyCode === 109; // guión
                const isSlash = e.keyCode === 191; // barra

                if (!allowedKeys.includes(e.keyCode) && !isNumber && !isDash && !isSlash && !e.ctrlKey) {
                    e.preventDefault();
                }
            }

            // Aplicar validaciones según el tipo
            document.querySelectorAll('input[data-input-type]').forEach(function(input) {
                const inputType = input.getAttribute('data-input-type');

                if (inputType === 'number') {
                    input.addEventListener('keydown', validateNumericInput);

                    // También validar al pegar contenido
                    input.addEventListener('paste', function(e) {
                        setTimeout(function() {
                            input.value = input.value.replace(/[^0-9]/g, '');
                        }, 0);
                    });
                }


            });
        });
    </script>
@endonce
