<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Proveedores', 'href' => route('admin.suppliers.index')],
    ['name' => 'Crear'],
]" :title="'Proveedor'">





    <div
        class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <form action="{{ route('admin.suppliers.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="identity_id"
                        class="block text-sm font-medium text-gray-700 dark:text-white text-left">Tipo de Identidad</label>
                    <select id="identity_id" name="identity_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="">Seleccione un tipo de identidad</option>
                        @foreach ($identities as $identity)
                            <option value="{{ $identity->id }}"
                                {{ old('identity_id') == $identity->id ? 'selected' : '' }}>
                                {{ $identity->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="document_number"
                        class="block text-sm font-medium text-gray-700 dark:text-white text-left">Número del Documento</label>
                    <input type="number" name="document_number" id="document_number" value="{{ old('document_number') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('document_number') ? 'border-red-500 bg-red-600' : '' }}"
                        placeholder="Numero del Documento">
                </div>

            </div>
            <div class="mb-4">
                <label for="name"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('name') ? 'border-red-600 bg-red-500 text-red-400' : '' }}"
                    placeholder="Nombre del proveedor">
            </div>
            <div class="mb-4">
                <label for="address"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Dirección</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('address') ? 'border-red-600 bg-red-500 text-red-400' : '' }}"
                    placeholder="Dirección del proveedor">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="phone"
                        class="block text-sm font-medium text-gray-700 dark:text-white text-left">Teléfono</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('phone') ? 'border-red-600 bg-red-500 text-red-400' : '' }}"
                        placeholder="Teléfono del proveedor">
                </div>
                <div class="mb-4">
                    <label for="email"
                        class="block text-sm font-medium text-gray-700 dark:text-white text-left">Correo Electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('email') ? 'border-red-600 bg-red-500 text-red-400' : '' }}"
                        placeholder="Correo Electrónico del proveedor">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Crear
                    Proveedor</button>
            </div>
        </form>
    </div>

</x-admin-layout>
