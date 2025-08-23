<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Prueba', 'href' => route('admin.dashboard')],
]" :title="'Hola'">

    <x-slot name="action">
        hola mundo
    </x-slot>

    {{-- Form nuevo inicio --}}

    <x-forms.form method="POST" action="{{ route('prueba') }}" class="space-y-6">

        <x-slot name="form">
            <div class="grid grid-cols-3 gap-3">
                <x-forms.input label="Nombre" name="name" type="text" value="" placeholder="Ingrese su nombre"
                    required />
                <x-forms.input label="Nombre" name="name1" type="number" value=""
                    placeholder="Ingrese el numero " required />
                <x-forms.input label="Nombre" name="name2" type="email" value=""
                    placeholder="Ingrese su correo" required />
                <x-forms.input label="Nombre" name="name3" type="password" value=""
                    placeholder="Ingrese su contraseña" required />
                <x-forms.search label="Proveedor" placeholder="Escribe el nombre o documento..."
                    endpoint="{{ route('admin.suppliers') }}" name="supplier_id" required />
                <x-forms.input label="Nombre" name="name4" type="date" value=""
                    placeholder="Ingrese su fecha de nacimiento" required />
                <x-button>Guardar</x-button>
            </div>
        </x-slot>
    </x-forms.form>

    {{-- Form nuevo fin  --}}





</x-admin-layout>
