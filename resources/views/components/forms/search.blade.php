@props([
    'label' => null,
    'placeholder' => 'Buscar...',
    'name' => 'search_id',
    'endpoint' => null,
    'minQueryLength' => 2,
    'required' => false,
])

<div x-data="searchComponent('{{ $endpoint }}', {{ $minQueryLength }})" @click.away="closeResults" class="relative mb-4" x-cloak>
    @if ($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    @endif

    <div class="relative ">
        <input type="text" id="{{ $name }}" placeholder="{{ $placeholder }}" x-model="search"
            @input.debounce.300ms="fetchResults" @keydown.enter.prevent="selectCurrentResult"
            @keydown.arrow-up.prevent="focusPrevious" @keydown.arrow-down.prevent="focusNext"
            @focus="showResultsList = true" autocomplete="off"
            class=" border text-sm rounded-lg block w-full p-2.5 bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:placeholder-gray-400">


        <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>
    </div>

    <div x-show="showResultsList" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-200 max-h-60 overflow-y-auto dark:bg-gray-800 dark:border-gray-700">

        <template x-if="results.length > 0">
            <ul class="py-1" x-ref="resultsList">
                <template x-for="(item, index) in results" :key="item.id || index">
                    <li @click="select(item)" @mouseenter="focusedIndex = index"
                        :class="{ 'bg-blue-100 dark:bg-blue-700': focusedIndex === index }"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-700 transition-colors duration-150 ease-in-out dark:text-white"
                        x-text="item.name" :id="'result-' + index"></li>
                </template>
            </ul>
        </template>
        <template x-if="results.length === 0 && search.length >= minQueryLength && !loading">
            <div class="px-4 py-2 text-gray-500 dark:text-gray-400">
                No se encontraron resultados.
            </div>
        </template>
    </div>
    {{-- Si el id esta bloquedado cambiar aqui para obtener el valor del id o uuid --}}
    <input type="hidden" name="{{ $name }}" :value="selected ? selected.uuid : ''">

</div>


@once
    <script>
        function searchComponent(endpoint, minQueryLength) {
            return {
                endpoint: endpoint,
                minQueryLength: minQueryLength,
                search: '',
                results: [],
                selected: {
                    id: '',
                    name: ''
                },
                loading: false,
                focusedIndex: -1,
                showResultsList: false,

                fetchResults() {
                    if (this.search.length < this.minQueryLength) {
                        this.results = [];
                        this.loading = false;
                        this.focusedIndex = -1;
                        this.showResultsList = false;
                        return;
                    }

                    this.loading = true;
                    this.showResultsList = true;

                    fetch(this.endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                search: this.search
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            console.log('Suppliers fetched', response);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Suppliers fetched 1', data);
                            this.results = Array.isArray(data) ? data : [];
                            this.loading = false;
                            this.focusedIndex = -1;
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                            this.results = [];
                            this.loading = false;
                        });
                },

                select(item) {
                    console.log('Selected item:', item);
                    this.selected = item;
                    this.search = item.name;
                    this.results = [];
                    this.focusedIndex = -1;
                    this.closeResults();
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
                    }
                },

                scrollIntoView() {
                    this.$nextTick(() => {
                        if (!this.$refs.resultsList) return;
                        const focusedElement = this.$refs.resultsList.querySelector(`#result-${this.focusedIndex}`);
                        if (focusedElement) {
                            focusedElement.scrollIntoView({
                                block: 'nearest',
                                behavior: 'smooth'
                            });
                        }
                    });
                },

                closeResults() {
                    this.showResultsList = false;
                    if (!this.selected) {
                        this.search = '';
                    } else {
                        this.search = this.selected.name;
                    }
                    this.results = [];
                    this.focusedIndex = -1;
                }
            }
        }
    </script>
@endonce
