<div x-data="{
    products: @entangle('products').live,
    total: @entangle('total'),
    removeProduct(index) { this.products.splice(index, 1); },
    updatePrice(product) {
        if (!product.price_type || product.price_type === 'QUOTE') {
            product.price = Number(product.price);
        } else if (product.price_type === 'GENERAL') {
            product.price = Number(product.price_a);
        } else if (product.price_type === 'A1') {
            product.price = Number(product.price_b);
        }
    },
    init() {
        this.$watch('products', (value) => {
            let sum = 0;
            value.forEach(product => { sum += product.price * product.quantity; });
            this.total = sum.toFixed(2);
        });
    }
}">
    <form wire:submit='save' class="space-y-4">
        <div class="grid lg:grid-cols-4 gap-4">
            <x-forms.native-select label="Tipo de Comprobante" wire:model="voucher_type" dark>
                <option value="">Seleccione Tipo de Comprobante</option>
                <option value="1" @if ($voucher_type === 1) selected @endif>Factura</option>
                <option value="2" @if ($voucher_type === 2) selected @endif>Boleta</option>
            </x-forms.native-select>
            <div class="grid lg:grid-cols-2 gap-2">
                <x-forms.input label="Serie" name="serie" type="text" placeholder="Ingrese el numero " required
                    wire:model="serie" disabled dark class="" />
                <x-forms.input label="Correlativo" name="correlativo" type="text" placeholder="Correlativo" required
                    wire:model="correlativo" />
            </div>
            <x-forms.input label="Fecha" name="date" type="date" required wire:model="date" />

            <x-forms.select label="Cotización" placeholder="Escribe el nombre o documento..." :async-data="['api' => route('admin.quotes'), 'method' => 'POST']"
                option-label="name" option-value="uuid" wire:model.live="quote_uuid" />

        </div>

        <div class="grid grid-cols-2 gap-4">
            <x-forms.select label="Cliente" placeholder="Escribe el nombre o documento..."
                wire:model.live="customer_uuid" :async-data="['api' => route('admin.customers'), 'method' => 'POST']" option-label="name" option-value="uuid"
                option-description="type" />
            <x-forms.select label="Almacen" placeholder="Escribe el nombre o documento..." :async-data="['api' => route('admin.warehouses'), 'method' => 'POST']"
                option-label="name" option-value="uuid" wire:model.live="warehouse_uuid" :disabled="count($products) > 0" />
        </div>


        <div class="lg:flex lg:gap-4">
            <x-forms.select label="Producto" placeholder="Buscar productos..." :async-data="['api' => route('admin.products'), 'method' => 'POST']" option-label="name"
                option-value="uuid" wire:model="product_uuid" />
            <div class="">
                <x-forms.button type="button" class="w-full mt-4 lg:mt-7" spinner="addProduct"
                    wire:click="addProduct">Agregar</x-forms.button>
            </div>

        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-y text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <th class="py-2 px-4">Producto</th>
                        <th class="py-2 px-4">Tipo precio</th>
                        <th class="py-2 px-4">Precio</th>
                        <th class="py-2 px-4">Cantidad</th>
                        <th class="py-2 px-4">Subtotal</th>
                        <th class="py-2 px-4"></th>
                    </tr>
                <tbody>
                    <template x-for="(product, index) in products" :key="product.id">
                        <tr class="border-b dark:border-gray-700  dark:bg-gray-500 dark:text-gray-50">
                            <td class="py-1 px-4" x-text="product.name"></td>
                            <td class="py-1 px-4">
                                <select x-model="product.price_type" :disabled="product.price_type === 'QUOTE'"
                                    @change="updatePrice(product)"
                                    class="border rounded px-2 py-1 text-sm">
                                    <option value="GENERAL">General</option>
                                    <option value="A1">A1</option>
                                    <option value="MANUAL">Manual</option>
                                    <option value="QUOTE">Cotización</option>
                                </select>
                            </td>
                            <td class="py-1 px-4">
                                <input type="number" step="0.01" class="w-28 border rounded px-2 py-1 text-sm"
                                    x-model.number="product.price" :disabled="product.price_type === 'QUOTE'"
                                    @input="product.price_type = 'MANUAL'" />
                            </td>
                            <td class="py-1 px-4">
                                <x-forms.input type="number" class="w-20" x-model="product.quantity" />
                            </td>
                            <td class="py-1 px-4" x-text="(product.quantity * product.price).toFixed(2)"></td>
                            <td class="py-1 px-4">
                                <x-button type="button" x-on:click="removeProduct(index)">Eliminar</x-button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="products.length === 0">
                        <tr>
                            <td colspan="5" class="py-2 px-4 text-center">No hay productos agregados</td>
                        </tr>
                    </template>
                </tbody>
                </thead>
            </table>
        </div>


        <div class="flex item-center space-x-4">
            <x-forms.input label="Observaciones" name="observation" type="text" placeholder="Observaciones"
                wire:model="observation" class="flex-1" />
        </div>
        <div class="flex items-end pt-4 justify-end text-2xl">
            Total: S/. <span x-text="total"></span>
        </div>
        <div class="flex items-end border-t pt-4 justify-end">
            <x-button type="submit" :disabled="count($errors) > 0 || count($products) === 0">Guardar</x-button>
        </div>
    </form>
</div>
