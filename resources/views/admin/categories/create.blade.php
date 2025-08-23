<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Categoría', 'href' => route('admin.categories.index')],
    ['name' => 'Crear'],
]" :title="'Crear Categoría'">

    <div
        class="w-full p-4  bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <x-forms.form action="{{ route('admin.categories.store') }}" method="POST" >
            <x-slot name="form">
                <x-forms.input label="Nombre de la categoría" name="name" type="text" value=""
                    placeholder="Ingrese su nombre" required />
                <x-forms.textArea label="Descripción" name="description" value=""
                    placeholder="Ingrese una descripción" />
            </x-slot>
            <x-slot name="actions">
                <x-button type="submit">
                    Crear Categoria
                </x-button>

            </x-slot>
        </x-forms.form>
    </div>

</x-admin-layout>
