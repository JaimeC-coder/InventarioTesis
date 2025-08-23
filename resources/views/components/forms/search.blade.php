@props([
    'label' => null,
    'placeholder' => 'Buscar...',
    'name' => 'search_id',
    'endpoint' => null,
    'minQueryLength' => 2,
    'required' => false,
    'debounce' => 300,
    'searchField' => 'name',
    'valueField' => 'uuid',
    'noResultsText' => 'No se encontraron resultados.',
    'initialValue' => null,
    'initialDisplay' => null,
    'additionalClasses' => ''
])

@php
    // Detectar si es Livewire por los atributos wire:
    $wireAttributes = $attributes->whereStartsWith('wire:');
    $isLivewire = $wireAttributes->isNotEmpty();
    $componentId = 'search_' . uniqid();

    // Separar atributos wire: de otros atributos
    $otherAttributes = $attributes->whereDoesntStartWith('wire:')->except(['class', 'style']);
@endphp

<div
    x-data="livewireSearchComponent({
        endpoint: '{{ $endpoint }}',
        minQueryLength: {{ $minQueryLength }},
        debounce: {{ $debounce }},
        searchField: '{{ $searchField }}',
        valueField: '{{ $valueField }}',
        isLivewire: {{ $isLivewire ? 'true' : 'false' }},
        initialValue: {{ $initialValue ? json_encode($initialValue) : 'null' }},
        initialDisplay: '{{ $initialDisplay }}',
        componentId: '{{ $componentId }}',
        name: '{{ $name }}'
    })"
    @click.away="closeResults"
    class="relative mb-4 {{$additionalClasses}}"
    x-cloak

    {{-- Solo usar wire:ignore si NO es Livewire --}}
    @if(!$isLivewire) wire:ignore @endif
>
    @if ($label)
        <label for="{{ $componentId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    {{-- Input visible para búsqueda --}}
    <div class="relative">
        <input
            type="text"
            id="{{ $componentId }}"
            placeholder="{{ $placeholder }}"
            x-model="search"
            @input="handleInput"
            @keydown.enter.prevent="selectCurrentResult"
            @keydown.arrow-up.prevent="focusPrevious"
            @keydown.arrow-down.prevent="focusNext"
            @keydown.escape="closeResults"
            @focus="handleFocus"
            autocomplete="off"
            class="border text-sm rounded-lg block w-full p-2.5 bg-gray-50 dark:bg-gray-700 dark:placeholder-gray-400 transition-all duration-200
                @error($name) border-red-500 focus:ring-red-500 focus:border-red-500 text-red-900
                @else border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
                @enderror"
            :class="{ 'ring-2 ring-blue-500 border-blue-500': showResultsList }"
        >

        {{-- Clear button --}}
        <button
            x-show="search.length > 0"
            x-transition
            @click="clearSearch"
            type="button"
            class="absolute inset-y-0 right-8 flex items-center pr-2 text-gray-400 hover:text-gray-600"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Loader --}}
        <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    {{-- Dropdown de resultados --}}
    <div
        x-show="showResultsList && (results.length > 0 || (search.length >= minQueryLength && !loading && results.length === 0))"
        x-transition
        class="absolute z-50 mt-1 w-full bg-white shadow-xl rounded-lg border border-gray-200 max-h-80 overflow-y-auto dark:bg-gray-800 dark:border-gray-700"
        style="display: none;"
    >
        {{-- Loading --}}
        <div x-show="loading" class="px-4 py-3 text-center">
            <span class="text-sm text-gray-600">Buscando...</span>
        </div>

        {{-- Results --}}
        <template x-if="results.length > 0 && !loading">
            <ul class="py-1" x-ref="resultsList">
                <template x-for="(item, index) in results" :key="getItemValue(item) || index">
                    <li
                        @click="select(item)"
                        @mouseenter="focusedIndex = index"
                        :class="{ 'bg-blue-50 text-blue-700': focusedIndex === index }"
                        class="px-4 py-3 cursor-pointer hover:bg-gray-50 transition-colors duration-150 border-b border-gray-100 last:border-b-0"
                        :id="'result-' + index"
                    >
                        <div class="font-medium" x-text="getItemDisplay(item)"></div>
                        <div x-show="item.description" class="text-xs text-gray-500 mt-1" x-text="item.description"></div>
                    </li>
                </template>
            </ul>
        </template>

        {{-- No results --}}
        <template x-if="results.length === 0 && search.length >= minQueryLength && !loading">
            <div class="px-4 py-3 text-center text-gray-500">
                <p class="text-sm">{{ $noResultsText }}</p>
            </div>
        </template>
    </div>

    {{-- ✅ HIDDEN INPUT CON REACTIVIDAD CORREGIDA --}}
    <input
        type="hidden"
        name="{{ $name }}"
        x-model="selectedValue"
        {{-- SOLO aplicar los wire:model que se pasaron como props --}}
        {{ $wireAttributes }}
        {{ $required ? 'required' : '' }}
        x-ref="hiddenInput"
        {{ $otherAttributes }}
    >

    {{-- Error messages --}}
    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>

<script>
function livewireSearchComponent(config) {
    return {
        // Config
        endpoint: config.endpoint,
        minQueryLength: config.minQueryLength,
        debounce: config.debounce,
        searchField: config.searchField,
        valueField: config.valueField,
        isLivewire: config.isLivewire,
        componentId: config.componentId,
        name: config.name,

        // State
        search: config.initialDisplay || '',
        results: [],
        selected: config.initialValue || null,
        selectedValue: config.initialValue ? (config.initialValue[config.valueField] || config.initialValue.id || '') : '',
        loading: false,
        focusedIndex: -1,
        showResultsList: false,
        debounceTimer: null,
        abortController: null,
        initialized: false,

        init() {
            // Configurar valor inicial
            if (config.initialValue) {
                this.selected = config.initialValue;
                this.search = this.getItemDisplay(config.initialValue);
                this.selectedValue = this.getItemValue(config.initialValue);
            }

            // ✅ SOLUCIÓN MEJORADA PARA LIVEWIRE
            if (this.isLivewire) {
                this.$nextTick(() => {
                    this.setupLivewireIntegration();
                });
            }

            // Event listeners personalizados
            this.setupEventListeners();

            this.initialized = true;
        },

        setupLivewireIntegration() {
            const hiddenInput = this.$refs.hiddenInput;
            if (!hiddenInput) return;

            // ✅ Watcher para cambios en selectedValue -> notificar a Livewire
            this.$watch('selectedValue', (newValue, oldValue) => {
                if (!this.initialized) return;

                if (newValue !== oldValue) {
                    // Esperar un tick para asegurar que el DOM se actualice
                    this.$nextTick(() => {
                        // Disparar eventos para que Livewire detecte el cambio
                        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));

                        // También disparar evento personalizado
                        this.$dispatch('value-changed', {
                            name: this.name,
                            value: newValue,
                            item: this.selected
                        });
                    });
                }
            });

            // ✅ Listener para cambios externos desde Livewire
            // Usar MutationObserver para detectar cambios en el value del input
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        const newValue = hiddenInput.value || hiddenInput.getAttribute('value');
                        if (newValue !== this.selectedValue && this.initialized) {
                            this.handleExternalChange(newValue);
                        }
                    }
                });
            });

            observer.observe(hiddenInput, {
                attributes: true,
                attributeFilter: ['value']
            });

            // También escuchar eventos de input directamente
            hiddenInput.addEventListener('input', (e) => {
                const newValue = e.target.value;
                if (newValue !== this.selectedValue && this.initialized) {
                    this.handleExternalChange(newValue);
                }
            });

            // Verificar valor inicial después de que Livewire se inicialice
            setTimeout(() => {
                const currentValue = hiddenInput.value || hiddenInput.getAttribute('value');
                if (currentValue && currentValue !== this.selectedValue) {
                    this.handleExternalChange(currentValue);
                }
            }, 100);
        },

        setupEventListeners() {
            // Event listeners personalizados
            this.$el.addEventListener('reset-search', () => this.clearSearch());
            this.$el.addEventListener('set-value', (event) => {
                if (event.detail) {
                    this.select(event.detail);
                }
            });

            // Listener para resets del formulario
            const form = this.$el.closest('form');
            if (form) {
                form.addEventListener('reset', () => {
                    setTimeout(() => this.clearSearch(), 0);
                });
            }
        },

        async handleExternalChange(newValue) {
            if (!newValue || newValue === this.selectedValue) return;
            // Si el nuevo valor está vacío, limpiar
            if (!newValue.trim()) {
                this.clearSearch();
                return;
            }

            // Si ya tenemos el objeto correcto, solo actualizar el valor
            if (this.selected && this.getItemValue(this.selected) === newValue) {
                this.selectedValue = newValue;
                return;
            }

            // Buscar el objeto completo
            await this.fetchItemByValue(newValue);
        },

        handleInput() {
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }

            if (this.abortController) {
                this.abortController.abort();
            }

            // Si cambió el texto, limpiar selección
            if (this.selected && this.search !== this.getItemDisplay(this.selected)) {
                this.selected = null;
                this.selectedValue = '';
            }

            this.debounceTimer = setTimeout(() => {
                this.fetchResults();
            }, this.debounce);
        },

        handleFocus() {
            if (this.search.length >= this.minQueryLength && this.results.length > 0) {
                this.showResultsList = true;
            }
        },

        async fetchResults() {
            if (this.search.length < this.minQueryLength) {
                this.results = [];
                this.loading = false;
                this.focusedIndex = -1;
                this.showResultsList = false;
                return;
            }

            this.loading = true;
            this.showResultsList = true;
            this.abortController = new AbortController();

            try {
                const response = await fetch(this.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        search: this.search
                    }),
                    signal: this.abortController.signal
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();
                this.results = Array.isArray(data) ? data : (data.data || []);
                this.focusedIndex = -1;

            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Error fetching search results:', error);
                    this.$dispatch('search-error', { error: error.message });
                }
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchItemByValue(value) {
            if (!this.endpoint || !value) return;

            try {
                const response = await fetch(this.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        get_by_value: value,
                        value_field: this.valueField
                    })
                });

                if (response.ok) {
                    const data = await response.json();

                    if (data) {
                        // Temporalmente deshabilitar la inicialización para evitar loops
                        const wasInitialized = this.initialized;
                        this.initialized = false;

                        this.selected = data;
                        this.search = this.getItemDisplay(data);
                        this.selectedValue = value;

                        this.initialized = wasInitialized;
                    }
                } else {
                    console.warn('No se encontró item con valor:', value);
                }
            } catch (error) {
                console.error('Error fetching item by value:', error);
            }
        },

        select(item) {
            this.selected = item;
            this.search = this.getItemDisplay(item);
            this.selectedValue = this.getItemValue(item);
            this.results = [];
            this.focusedIndex = -1;
            this.closeResults();

            // Disparar eventos
            this.$dispatch('item-selected', { item, value: this.selectedValue });

            // Para validación de formularios
            this.$nextTick(() => {
                const hiddenInput = this.$refs.hiddenInput;
                if (hiddenInput) {
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        },

        selectCurrentResult() {
            if (this.focusedIndex !== -1 && this.results[this.focusedIndex]) {
                this.select(this.results[this.focusedIndex]);
            }
        },

        focusNext() {
            if (this.focusedIndex < this.results.length - 1) {
                this.focusedIndex++;
                this.scrollIntoView();
            }
        },

        focusPrevious() {
            if (this.focusedIndex > 0) {
                this.focusedIndex--;
                this.scrollIntoView();
            } else if (this.focusedIndex === -1 && this.results.length > 0) {
                this.focusedIndex = this.results.length - 1;
                this.scrollIntoView();
            }
        },

        scrollIntoView() {
            this.$nextTick(() => {
                if (!this.$refs.resultsList) return;
                const focusedElement = this.$refs.resultsList.querySelector(`#result-${this.focusedIndex}`);
                if (focusedElement) {
                    focusedElement.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            });
        },

        clearSearch() {
            this.search = '';
            this.selected = null;
            this.selectedValue = '';
            this.results = [];
            this.focusedIndex = -1;
            this.closeResults();
            this.$dispatch('search-cleared');
        },

        closeResults() {
            this.showResultsList = false;
            this.focusedIndex = -1;
        },

        getItemDisplay(item) {
            if (!item) return '';
            return item[this.searchField] || item.name || item.title || item.label || '';
        },

        getItemValue(item) {
            if (!item) return '';
            return item[this.valueField] || item.uuid || item.id || '';
        }
    }
}
</script>
