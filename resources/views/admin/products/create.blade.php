<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Productos', 'href' => route('admin.products.index')],
    ['name' => 'Crear'],
]" :title="'Producto'">





    <div
        class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('name') ? 'border-red-600 bg-red-500 text-red-400' : '' }}"
                    placeholder="Nombre de la categoría">
            </div>
            <div class="mb-4">
                <label for="description"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Descripción</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('description') ? 'border-red-500 bg-red-600' : '' }}">{{ old('description') }}</textarea>
            </div>
            <div class="mb-4">
                <label for="price"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Precio</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('price') ? 'border-red-500 bg-red-600' : '' }}"
                    placeholder="Precio del producto">
            </div>

            <div class="mb-4">
                <label for="category_id"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Categoría</label>
                <select id="category_id" name="category_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                    <option value="">Seleccione una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>



            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Crear
                    Categoria</button>
            </div>
        </form>
    </div>

</x-admin-layout>
