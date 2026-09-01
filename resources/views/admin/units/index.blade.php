<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Unidades', 'href' => route('admin.units.index')],
]" :title="'Unidades'">



    <x-slot name="action">
        <a href="{{ route('admin.units.create') }}" class="btn btn-primary">Crear Nueva Unidad</a>
    </x-slot>


    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="grid grid-cols-1 gap-4 border-b pb-4 mb-4 border-gray-950">
            <livewire:admin.tables.unit-table />
            <livewire:export.pdf />

        </div>

    </div>







</x-admin-layout>
