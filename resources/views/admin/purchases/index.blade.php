<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Compras']]" :title="'Compras'">

    <x-slot name="action">
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">Crear Nueva Compra</a>
    </x-slot>



    <livewire:admin.tables.purchase-table />
    <livewire:export.specific-pdf />

    <livewire:export.pdf />
    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
