<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Clientes', 'href' => route('admin.customers.index')],
    ['name' => 'Crear'],
]" :title="'Cliente'">

    <div
        class="w-full p-4    bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <livewire:admin.create.customer />
    </div>



</x-admin-layout>
