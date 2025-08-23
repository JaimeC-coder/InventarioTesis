{{--
Componente de formulario mejorado con:
1. Clases personalizables para el formulario
2. Opción de footer para botones o acciones dentro del form
3. Clases preestablecidas para el form con posibilidad de override
4. Manejo automático de CSRF y métodos HTTP
5. Wire directives desde afuera (wire:submit, wire:click, etc.)
--}}

@props([
    'action' => null, // Para rutas tradicionales
    'method' => 'POST', // GET, POST, PUT, PATCH, DELETE
    'formClass' => '', // Clases adicionales para el form
    'containerClass' => '', // Clases adicionales para el contenedor del form
    'hasFooter' => true, // Si queremos mostrar el footer con acciones
    'footerInside' => false, // Si queremos el footer dentro del form o fuera
])

@php
    // Clases preestablecidas para el form que se pueden extender
    $defaultFormClasses = 'w-full';
    $formClasses = $defaultFormClasses . ($formClass ? ' ' . $formClass : '');

    // Detectar si viene algún wire desde afuera
    $hasWire = false;
    foreach ($attributes->getAttributes() as $key => $value) {
        if (str_starts_with($key, 'wire:')) {
            $hasWire = true;
            break;
        }
    }

    $isLivewire = $hasWire;

    // Métodos que necesitan method spoofing
    $needsMethodSpoofing = in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']);
    $formMethod = $needsMethodSpoofing ? 'POST' : strtoupper($method);

    // Determinar si necesita CSRF (no para GET ni para Livewire)
    $needsCsrf = !$isLivewire && strtoupper($method) !== 'GET';
@endphp

<form
    @if($action) action="{{ $action }}" @endif
    method="{{ $formMethod }}"
    class="{{ $formClasses }}"
    {{ $attributes->except(['class']) }}
>
    {{-- CSRF Token (automático para POST, PUT, PATCH, DELETE excepto Livewire) --}}
    @if($needsCsrf)
        @csrf
    @endif

    {{-- Method Spoofing (automático para PUT, PATCH, DELETE) --}}
    @if($needsMethodSpoofing)
        @method($method)
    @endif

    {{-- Contenedor principal del formulario --}}
    <div {{ $attributes->merge(['class' => 'px-4 py-5 bg-white dark:bg-gray-800 shadow ' . $containerClass]) }}>
        <div class="grid grid-cols-1 gap-4">
            {{ $form }}
        </div>

        {{-- Footer dentro del formulario (si está habilitado) --}}
        @if ($hasFooter && $footerInside && isset($actions))
            <div class="flex items-center justify-end px-0 py-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                {{ $actions }}
            </div>
        @endif
    </div>

    {{-- Footer fuera del contenedor principal (comportamiento original) --}}
    @if ($hasFooter && !$footerInside && isset($actions))
        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-800 text-end sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
            {{ $actions }}
        </div>
    @endif
</form>
