<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Prueba', 'href' => route('admin.dashboard')],
]" :title="'Hola'">

    <x-slot name="action">
        hola mundo
    </x-slot>

    <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="grid grid-cols-2 gap-4 border-b pb-4 mb-4 border-gray-950">
            <livewire:admin.dashboard.grafica-principal />
        </div>

        <ul  class="list-disc list-inside space-y-2 text-gray-700">
            <li> agregar el almacen a la orden de compra -- pendiente de hacer la migracion y probar
            </li>
            <li> al agregar el almacen hay que tener en cuenta que hay que cambiar tanto compra como venta en los respectivos valores de cotizacion y orden de compra
                -- pendiente de hacer la migracion y probar
            </li>
            <li> agregar el proveedor a la insercion masiva de producto  y al registrar producto -- pendiente de usar y revisar
            </li>
            <li>
                ver como manejamos la compra cuando aun no cambia de estado
            </li>
            <li>
                terminar el dashboard de compras y ventas
            </li>
            <li>
                terminar con movimiento y transferencia de productos
            </li>
            <li>
               configurar roles y permisos
            </li>
            <li>
                terminar policyas para manejar perminsos
            </li>

            <li>
                mandar el swal a un servicio donde se pueda reutilizar todo
            </li>
        </ul>

    </div>







</x-admin-layout>
