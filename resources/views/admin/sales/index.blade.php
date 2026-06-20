<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Ventas']]" :title="'Ventas'">

    <x-slot name="action">
        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary">Crear Nueva Venta</a>
    </x-slot>



    <livewire:admin.tables.sale-table />

    <livewire:export.specific-pdf />

    <livewire:export.pdf />

    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
