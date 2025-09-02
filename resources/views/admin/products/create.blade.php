<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Productos', 'href' => route('admin.products.index')],
    ['name' => 'Crear'],
]" :title="'Producto'">

    <div
        class="w-full p-4  bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <x-forms.input label="Nombre" name="name" type="text" value="{{ old('name') }}" />
            <x-forms.textarea label="Descripción" name="description"   > {{ old('description') }}</x-forms.textarea>
            <x-forms.input label="Precio" name="price" type="number" value="{{ old('price') }}"   />

            <x-forms.select label="Categoría" name="category_uuid"   :options="$categories" option-label="name"
                option-value="uuid"  />

                <x-button type="submit" class="mt-4">
                    Crear Producto
                </x-button>
        </form>
    </div>

</x-admin-layout>
