<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Categoría', 'href' => route('admin.categories.index')],
    ['name' => 'Editar'],
]" :title="'Editar Categoría'">

    <div class="w-full p-4  bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')


            <x-forms.input label="Nombre de la categoría" name="name" type="text" value="{{ old('name', $category->name) }}"
                placeholder="Ingrese su nombre" required />
            <x-forms.textarea label="Descripción" name="description" placeholder="Ingrese una descripción" required>
                {{ old('description', $category->description) }}
            </x-forms.textarea>

            <x-forms.button type="submit" primary class="mt-4">
                Actualizar Categoria
            </x-forms.button>


        </form>
    </div>

</x-admin-layout>
