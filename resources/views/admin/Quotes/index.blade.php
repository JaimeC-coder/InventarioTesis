<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Cotizaciones']]" :title="'Cotizaciones'">

    <x-slot name="action">
        <a href="{{ route('admin.quotes.create') }}" class="btn btn-primary">Crear Nueva Cotización</a>
    </x-slot>



    <livewire:admin.tables.quote-table />
    <livewire:export.pdf />

    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
