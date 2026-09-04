<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Clientes']]" :title="'Clientes'">

    <x-slot name="action">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">Crear Nuevo Cliente</a>
    </x-slot>



    <livewire:admin.tables.customer-table />

    <livewire:export.pdf />

    @push('scripts')

    @endpush
</x-admin-layout>
