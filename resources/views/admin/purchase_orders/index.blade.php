<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Ordenes de Compra']]" :title="'Ordenes de Compra'">

    <x-slot name="action">
        <a href="{{ route('admin.purchases-orders.create') }}" class="btn btn-primary">Crear Nueva Orden de Compra</a>
    </x-slot>



    <livewire:admin.tables.purchase-order-table />

    <livewire:export.pdf />
    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
