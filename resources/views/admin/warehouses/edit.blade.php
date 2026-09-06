<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Almacenes', 'href' => route('admin.warehouses.index')],
    ['name' => 'Editar'],
]" :title="'Almacenes'">

    <div
        class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">

        <livewire:admin.edit.warehouse :models-warehouse="$warehouse" />

    </div>

</x-admin-layout>
