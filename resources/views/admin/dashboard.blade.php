<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')]]" :title="'Hola'">

    <x-slot name="action">
        Bienvenido
    </x-slot>

    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="grid grid-cols-2 gap-4 border-b pb-4 mb-4 border-gray-950">
            <livewire:admin.dashboard.grafica-principal />
            <livewire:admin.dashboard.grafica-segunda />
        </div>


        <ul class="list-disc list-inside space-y-2 text-gray-700">

            <li class="text-green-500">
                mejorar todo lo que tiene que ver con apis para tener seguridad -- por probar
            </li>
            <li class="text-green-500">
                ver ese error que aparece cuando borro el listado de productos
            </li>
           
            <li class="text-green-500">
                Agregar transacciones a los save que falten
            </li>



            <li class="text-yellow-300">
                editar roles
            </li>


            <li class="text-yellow-300">
                terminar policyas para manejar perminsos
            </li>


            <li class="text-red-600">
                terminar el dashboard de compras y ventas
                -Lista de productos que ya no tiene stock por almacen
                -tabla de comparacion de ventas , compras y moviminetos
            </li>
            <li class="text-red-600">
                terminar con movimiento y transferencia de productos
            </li>


        </ul>

    </div>







</x-admin-layout>
