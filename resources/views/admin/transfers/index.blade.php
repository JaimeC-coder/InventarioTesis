<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Transferencias']]" :title="'Transferencias'">

    <x-slot name="action">
        <a href="{{ route('admin.transfers.create') }}" class="btn btn-primary">Crear Nueva Transferencia</a>
    </x-slot>

    <livewire:admin.tables.transfer-table />
    <livewire:export.pdf />

</x-admin-layout>
