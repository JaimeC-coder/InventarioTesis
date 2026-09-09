<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Unidad de Medida', 'href' => route('admin.measures.index')],
    ['name' => 'Editar'],
]" :title="'Editar Unidad de Medida'">

    <div
        class="w-full p-4  bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <livewire:admin.edit.measure :models-measure="$measure" />
    </div>

</x-admin-layout>
