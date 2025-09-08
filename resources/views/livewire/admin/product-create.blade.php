<div>
    <form wire:submit='save' class="space-y-4">

        <div class="grid lg:grid-cols-2 gap-4">
            <x-forms.select label="Categoria" placeholder="Escribe el nombre o documento..." :async-data="['api' => route('admin.categories'), 'method' => 'POST']"
                option-label="name" option-value="uuid" wire:model="category_uuid" />

            <div class="flex gap-4">
                <x-forms.input label="Codigo de Categoria" name="name" type="text"
                    placeholder="Ingrese el nombre del producto" required wire:model="category_code" />
                <x-forms.input label="Stock Minimo" name="stock_min" type="number" placeholder="Stock Minimo" required
                    wire:model="stock_min" />
            </div>
        </div>

        <div class="grid lg:grid-cols-5 gap-4 border-collapse lg:border lg:border-gray-200 p-4 rounded">
            <div class="grid lg:col-span-4 gap-4">
                <div class="flex gap-4">
                    <div class="grid grid-cols-6 gap-2">
                        <div class="col-span-5">
                            <div class="flex gap-4">
                                <x-forms.input label="Base del producto" name="name" type="text"
                                    placeholder="Ingrese el nombre del producto" required wire:model="name"
                                    class="w-7/12" />
                                <x-forms.input label="Especificación (opcional)" name="name_specific" type="text"
                                    placeholder="Ingrese la especificación del producto" wire:model="name_specific"
                                    class="flex-1" />
                            </div>
                        </div>
                        <x-forms.input label="Codigo" name="code" type="text"
                            placeholder="Ingrese el codigo" required wire:model="code"
                            class="col-span-1" />
                    </div>
                </div>

                <div class="flex gap-4">
                    <x-forms.select label="Unidad de Stock" placeholder="Escribe el nombre o documento..."
                        :async-data="['api' => route('admin.units'), 'method' => 'POST']" multiselect option-label="name" option-value="uuid" wire:model="units_uuid" />
                    <x-forms.select label="Medida" placeholder="Escribe el nombre o documento..." :async-data="['api' => route('admin.measures'), 'method' => 'POST']"
                        option-label="name" option-value="uuid" wire:model="measures_uuid" multiselect />
                </div>
            </div>
            <div class="lg:col-span-1 flex flex-col justify-center">
                <x-forms.button type="button" class="w-full mt-4 lg:mt-7" spinner="addProduct"
                    wire:click="addProduct">Agregar</x-forms.button>
            </div>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-y text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <th class="py-2 px-4">Codigo</th>
                        <th class="py-2 px-4">Producto</th>
                        <th class="py-2 px-4">Unidad</th>
                        <th class="py-2 px-4">Medida</th>
                        <th class="py-2 px-4">Precio</th>
                        <th class="py-2 px-4"></th>
                    </tr>
                <tbody>
                    <template x-for="(product, index) in products" :key="product.id">
                        <tr class="border-b dark:border-gray-700  dark:bg-gray-500 dark:text-gray-50">
                            <td class="py-1 px-4" x-text="product.code"></td>
                            <td class="py-1 px-4" x-text="product.name"></td>
                            <td class="py-1 px-4"><x-forms.input type="number" class="w-20" x-model="product.price"
                                    step="0.01" /></td>
                            <td class="py-1 px-4">
                                <x-forms.input type="number" class="w-20" x-model="product.quantity" />
                            </td>
                            <td class="py-1 px-4" x-text="(product.quantity * product.price).toFixed(2)"></td>
                            <td class="py-1 px-4">
                                <x-forms.input type="number" class="w-20" x-model="product.measure" step="0.01" />
                            </td>
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


        <div>
            <x-forms.textarea label="Descripcion" name="description" placeholder="Descripcion del producto"
                wire:model="description" />
        </div>

        <div class="">
            <x-forms.button type="submit" class="" spinner="save">Guardar</x-forms.button>
        </div>

    </form>
</div>
