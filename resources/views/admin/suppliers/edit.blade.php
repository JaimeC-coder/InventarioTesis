<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Proveedores', 'href' => route('admin.suppliers.index')],
    ['name' => 'Editar'],
]" :title="'Proveedor'">

    <div
        class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <x-forms.select label="Tipo de Identidad" name="identity_uuid" :options="$identities" option-label="name"
                    option-value="uuid" placeholder="Seleccione un tipo de identidad" />
                <x-forms.input label="Número de Documento" name="document_number" type="number"
                    value="{{ old('document_number') }}" placeholder="Número de Documento" />

            </div>
            <x-forms.input label="Nombre" name="name" type="text" value="{{ old('name') }}" placeholder="Nombre"
                class="mb-4" />
            <x-forms.input label="Dirección" name="address" type="text" value="{{ old('address') }}"
                placeholder="Dirección" class="mb-4" />

            <div class="grid grid-cols-2 gap-4">
                <x-forms.input label="Teléfono" name="phone" type="text" value="{{ old('phone') }}"
                    placeholder="Teléfono" />
                <x-forms.input label="Correo Electrónico" name="email" type="email" value="{{ old('email') }}"
                    placeholder="Correo Electrónico" />
            </div>

            <x-button type="submit" class="mt-4">
                Actualizar Proveedor
            </x-button>
        </form>
    </div>


</x-admin-layout>
