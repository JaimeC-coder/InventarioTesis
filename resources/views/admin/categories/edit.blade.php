<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Categoría', 'href' => route('admin.categories.index')],
    ['name' => 'Editar']
]" :title="'Editar Categoría'">

<div class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
<form action="{{ route('admin.categories.update', $category) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white text-left">Nombre</label>
        <input type="text" name="name" id="name" value="{{old('name', $category->name)}}"  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('name') ? 'border-red-600 bg-red-500 text-red-400'  : '' }}" placeholder="Nombre de la categoría">
    </div>
    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-white text-left">Descripción</label>
        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('description') ? 'border-red-500 bg-red-600' : '' }}">{{old('description', $category->description)}}</textarea>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Actualizar Categoria</button>
    </div>
</form>
</div>

</x-admin-layout>
