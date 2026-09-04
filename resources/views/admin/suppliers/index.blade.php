<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Proveedores']]" :title="'Proveedores'">

    <x-slot name="action">
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Crear Nuevo Proveedor</a>
    </x-slot>



    <livewire:admin.tables.supplier-table />
    <livewire:admin.edit.supplier />

    <livewire:export.pdf />
    @push('scripts')
        <script>

        </script>
    @endpush

</x-admin-layout>
