<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Medidas', 'href' => route('admin.measures.index')],
]" :title="'Medidas'">

    <x-slot name="action">
        Lista de medidas
    </x-slot>

    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="grid grid-cols-1 gap-4 border-b pb-4 mb-4 border-gray-950">
            <livewire:admin.tables.measure-table />
                <livewire:export.pdf />

        </div>

    </div>







</x-admin-layout>
