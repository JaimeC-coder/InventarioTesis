<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Almacenes', 'href' => route('admin.warehouses.index')],
    ['name' => 'Crear']
]" :title="'Almacenes'">





<div class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
<form action="{{ route('admin.warehouses.store') }}" method="POST">
    @csrf
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white text-left">Nombre</label>
        <input type="text" name="name" id="name" value="{{old('name')}}"  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('name') ? 'border-red-600 bg-red-500 text-red-400'  : '' }}" placeholder="Nombre del almacén">
    </div>
    <div class="mb-4">
        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-white text-left">Ubicación</label>
        <input type="text" name="location" id="location" value="{{old('location')}}"  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('location') ? 'border-red-600 bg-red-500 text-red-400'  : '' }}" placeholder="Ubicación del almacén">
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Crear Almacén</button>
    </div>
</form>
</div>

</x-admin-layout>
