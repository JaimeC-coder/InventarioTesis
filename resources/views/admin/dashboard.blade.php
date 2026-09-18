<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')]]" :title="'Hola'">

    <x-slot name="action">
        Bienvenido
    </x-slot>

    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="grid grid-cols-3 gap-4 border-b pb-4 mb-4 border-gray-950">
            <livewire:admin.dashboard.grafica-cuarta />
            <livewire:admin.dashboard.grafica-principal />
        </div>
        <div class="grid grid-cols-2 gap-4 border-b pb-4 mb-4 border-gray-950">
            <livewire:admin.dashboard.grafica-segunda />
            <livewire:admin.dashboard.grafica-tercera />
        </div>

        <ul class="list-disc list-inside space-y-2 text-gray-700">


            <li class="text-red-600">
                hacer funcionar el boton de crear orden de compra de la tabla del dashboard
            </li>
            <li class="text-red-600">
                terminar con movimiento y transferencia de productos
            </li>

            <li class="text-yellow-300">
                terminar policyas para manejar perminsos
            </li>


            <li class="text-green-500">
                ver si cambiamos como traer los iconos
            </li>



        </ul>

    </div>







</x-admin-layout>
