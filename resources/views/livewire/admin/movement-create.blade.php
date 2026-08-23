<div x-data="{
    products: @entangle('products').live,
    total: @entangle('total'),
    removeProduct(index) { this.products.splice(index, 1); },
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
            <x-forms.native-select label="Tipo de movimiento" wire:model.live="type">
                <option value="">Seleccione Tipo de Movimiento</option>
                <option value="1" @if ($type === 1) selected @endif>Ingreso</option>
                <option value="2" @if ($type === 2) selected @endif>Salida</option>
            </x-forms.native-select>

            <x-forms.input label="Serie" name="serie" type="text" placeholder="Ingrese el numero " required
                wire:model="serie" disabled dark class="" />
            <x-forms.input label="Correlativo" name="correlativo" type="text" placeholder="Correlativo" required
                wire:model="correlativo" />
            <x-forms.input label="Fecha" name="date" type="date" required wire:model="date" />

        </div>

        <div class="grid grid-cols-2 gap-4">
            <x-forms.select label="Almacen" placeholder="Escribe el nombre o documento..." :async-data="['api' => route('admin.warehouses'), 'method' => 'POST']"
                option-label="name" option-value="uuid" wire:model="warehouse_uuid" :disabled="count($products) > 0" />
            <x-forms.select label="Motivo" placeholder="Escribe el nombre o documento..." :async-data="['api' => route('admin.reasons'), 'method' => 'POST', 'params' => ['type' => $type]]"
                option-label="name" option-value="uuid" wire:model="reason_uuid" :disabled="count($products) > 0"/>
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
                        <th class="py-2 px-4">Precio</th>
                        <th class="py-2 px-4">Cantidad</th>
                        <th class="py-2 px-4">Subtotal</th>
                        <th class="py-2 px-4"></th>
                    </tr>
                <tbody>
                    <template x-for="(product, index) in products" :key="product.id">
                        <tr class="border-b dark:border-gray-700  dark:bg-gray-500 dark:text-gray-50">
                            <td class="py-1 px-4" x-text="product.name"></td>
                            <td class="py-1 px-4"><x-forms.input type="number" class="w-20" x-model="product.price"
                                    step="0.01" /></td>
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
                observation class="flex-1" />
        </div>
        <div class="flex items-end pt-4 justify-end text-2xl">
            Total: S/. <span x-text="total"></span>
        </div>

        <div class="flex justify-between items-center">

            <a href="{{ route('admin.movements.index') }}" class="ml-2">
                <x-button type="button" variant="secondary" class="mt-4">
                    Volver
                </x-button>

            </a>
            <x-button type="submit" class="mt-4" spinner="save" wire:target="save" wire:loading.attr="disabled"
                :disabled="count($errors) > 0 || count($products) === 0">
                Crear Movimiento
            </x-button>

        </div>

    </form>
</div>
