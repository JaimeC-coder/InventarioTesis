<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Reportes', 'href' => route('admin.reports')],
]" :title="'Hola'">

    <x-slot name="action">
        Reportes Especificos
    </x-slot>

    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="grid grid-cols-1 gap-4 border-b pb-4 mb-4 border-gray-950">
            <livewire:admin.dashboard.reports />
        </div>



    </div>







</x-admin-layout>
