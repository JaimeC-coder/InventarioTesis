<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Proveedores', 'href' => route('admin.suppliers.index')],
    ['name' => 'Crear'],
]" :title="'Proveedor'">

    <div
        class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <livewire:admin.create.supplier />
    </div>

</x-admin-layout>
