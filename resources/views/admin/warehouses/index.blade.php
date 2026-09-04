<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Almacenes']]" :title="'Almacenes'">

    <x-slot name="action">
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary">Crear Nuevo Almacen</a>
    </x-slot>



    <livewire:admin.tables.warehouse-table />

    <livewire:admin.edit.warehouse />

    <livewire:export.pdf />


    @push('scripts')

    @endpush
</x-admin-layout>
