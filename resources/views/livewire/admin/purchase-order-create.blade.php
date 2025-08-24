<div>
    <form wire:submit='save' class="space-y-4">
        <div class="grid grid-cols-4 gap-4">
            <div class="md-4">
                <label for="supplier_id" class="text-gray-700 dark:text-white">Tipo de Comprobante</label>
                <select name="voucher_type" id="voucher_type" wire:model="voucher_type"
                    class="form-select block w-full mt-1">
                    <option value="">Seleccione Tipo de Comprobante</option>
                    <option value="1" @if ($voucher_type === 1) selected @endif>Factura</option>
                    <option value="2" @if ($voucher_type === 2) selected @endif>Boleta</option>
                </select>
            </div>
            <x-forms.input label="Nombre" name="serie" type="text" placeholder="Ingrese el numero " required
                wire:model="serie" disabled="true" />
            <x-forms.input label="Correlativo" name="correlativo" type="text" placeholder="Correlativo" required
                wire:model="correlativo" />
            <x-forms.input label="Fecha" name="date" type="date" required wire:model="date" />

        </div>
        <x-forms.search label="Proveedor" placeholder="Escribe el nombre o documento..."
            endpoint="{{ route('admin.suppliers') }}" name="supplier_id" required wire:model="supplier_id" />

        <div class="flex gap-4">
            <x-forms.search name="product_id" label="Producto" placeholder="Buscar productos..." additionalClasses="flex-1"
                endpoint="{{ route('admin.products') }}" wire:model="product_id" :livewire="true"
                searchField="name" valueField="uuid" required />
            <div class="">
                <x-button type="button" class="mt-8" wire:click="addProduct">Agregar</x-button>
            </div>

        </div>

        <table class="w-full text-sm text-left">
            <thead>
                <tr class=" text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <th class="py-2 px-4">Producto</th>
                    <th class="py-2 px-4">Precio</th>
                    <th class="py-2 px-4">Cantidad</th>
                    <th class="py-2 px-4">Subtotal</th>
                    <th class="py-2 px-4">Acciones</th>
                </tr>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b dark:border-gray-700  dark:bg-gray-500 dark:text-gray-50">
                        <td class="py-2 px-4">{{ $product['name'] }}</td>
                        <td class="py-2 px-4">{{ $product['price'] }}</td>
                        <td class="py-2 px-4">{{ $product['quantity'] }}</td>
                        <td class="py-2 px-4">{{ $product['subtotal'] }}</td>
                        <td class="py-2 px-4">
                            <x-button type="button" wire:click="removeProduct({{ $product['id'] }})">Eliminar</x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-2 px-4 text-center bg-gray-400 dark:bg-white">No hay productos
                            agregados.</td>
                    </tr>
                @endforelse
            </tbody>
            </thead>
        </table>

    </form>
</div>
