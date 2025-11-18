<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Productos', 'href' => route('admin.products.index')],
    ['name' => 'Crear'],
]" :title="'Producto'">

    <div
        class="w-full p-4  bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        @livewire('admin.product-create')
    </div>

</x-admin-layout>
