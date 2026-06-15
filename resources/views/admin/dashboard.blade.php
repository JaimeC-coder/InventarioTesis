<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Prueba', 'href' => route('admin.dashboard')],
]" :title="'Hola'">

    <x-slot name="action">
        hola mundo
    </x-slot>

    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="grid grid-cols-2 gap-4 border-b pb-4 mb-4 border-gray-950">
            <livewire:admin.dashboard.grafica-principal />
        </div>

    </div>







</x-admin-layout>
