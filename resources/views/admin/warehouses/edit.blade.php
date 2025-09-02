<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Almacenes', 'href' => route('admin.warehouses.index')],
    ['name' => 'Editar'],
]" :title="'Almacenes'">

    <div
        class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <form action="{{ route('admin.warehouses.update', $warehouse) }}" method="POST">
            @csrf
            @method('PUT')
            <x-forms.input label="Nombre del Almacén" name="name" type="text" value="{{ old('name', $warehouse->name) }}"
                placeholder="Nombre del Almacén" />
            <x-forms.input label="Ubicación del Almacén" name="location" type="text" value="{{ old('location', $warehouse->location) }}"
                placeholder="Ubicación del Almacén" />
            <x-button type="submit" class="mt-4">
                Actualizar Almacén
            </x-button>
        </form>
    </div>

</x-admin-layout>
