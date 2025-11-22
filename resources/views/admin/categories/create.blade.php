<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Categoría', 'href' => route('admin.categories.index')],
    ['name' => 'Crear'],
]" :title="'Crear Categoría'">

    <div
        class="w-full p-4  bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <x-forms.input label="Nombre de la categoría" name="name" type="text" value=""
                placeholder="Ingrese su nombre" required />
            <x-forms.textarea label="Descripción" name="description" value=""
                placeholder="Ingrese una descripción" />

            <div class="flex justify-between items-center">
                <x-button type="submit" class="mt-4">
                Crear Categoria
            </x-button>

            <a href="{{ route('admin.categories.index') }}" class="ml-2">
                <x-button type="button" variant="secondary" class="mt-4">
                    Volver
                </x-button>

            </a>

            </div>

        </form>
    </div>

</x-admin-layout>
