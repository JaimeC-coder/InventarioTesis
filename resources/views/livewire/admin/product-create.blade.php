<div x-data="{
    products: @entangle('products').live,
    removeProduct(index) { this.products.splice(index, 1); },

}">

    @error('products.*.codigo')
        <div id="alert-border-2"
            class="flex sm:items-center p-4 mb-4 text-sm text-fg-danger-strong bg-danger-soft border-t-4 border-danger-subtle"
            role="alert">
            <svg class="w-4 h-4 shrink-0 mt-0.5 md:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-2 text-sm ">
                A simple info alert with an <a href="#" class="font-medium underline hover:no-underline">example
                    link</a>. Give it a click if you like.
            </div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-danger-soft text-fg-danger-strong rounded focus:ring-2 focus:ring-danger-medium p-1.5 hover:bg-danger-medium inline-flex items-center justify-center h-8 w-8 shrink-0"
                data-dismiss-target="#alert-border-2" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
            </button>
        </div>
    @enderror
    <form wire:submit='saveProducts' class="space-y-4">

        <div class="grid lg:grid-cols-2 gap-4 dark:bg-gray-800">
            <x-forms.select label="Categoria" placeholder="Escribe el nombre o documento..." :async-data="['api' => route('admin.categories'), 'method' => 'POST']"
                option-label="name" option-value="uuid" wire:model="category_uuid" :disabled="$locked" />

            <div class="flex gap-4">
                <x-forms.input label="Codigo de Categoria" name="name" type="number" min="0" max="99"
                    placeholder="Ingrese el nombre del producto" wire:model="category_code" :disabled="$locked" />
                <x-forms.input label="Stock Minimo" name="stock_min" type="number" placeholder="Stock Minimo"
                    wire:model="stock_min" :disabled="$locked" />
            </div>
        </div>

        <div class="grid lg:grid-cols-5 gap-4 border-collapse lg:border lg:border-gray-200 p-4 rounded">
            <div class="grid lg:col-span-4 gap-4">
                <div class="grid grid-cols-7 gap-4">
                    <div class="grid grid-cols-3 gap-4 col-span-6">
                        <x-forms.input label="Base del producto" name="name" type="text"
                            placeholder="Ingrese el nombre del producto" wire:model="name" class="col-span-2" />
                        <x-forms.input label="Especificación (opcional)" name="name_specific" type="text"
                            placeholder="Ingrese la especificación del producto" wire:model="name_specific"
                            class="flex-1" />

                    </div>
                    <x-forms.input label="Codigo" name="code" type="number" min="1000" max="9999"
                        placeholder="Ingrese el codigo" wire:model="code" class="flex-1" :disabled="$locked" />
                </div>


                <div class="flex gap-4">
                    <x-forms.select label="Unidad de Stock" placeholder="Escribe el nombre o documento..."
                        :async-data="['api' => route('admin.units'), 'method' => 'POST']" multiselect option-label="name" option-value="uuid" wire:model="units_uuid" />
                    <x-forms.select label="Medida" placeholder="Escribe el nombre o documento..." :async-data="['api' => route('admin.measures'), 'method' => 'POST']"
                        option-label="name" option-value="uuid" wire:model="measures_uuid" multiselect />
                </div>
            </div>
            <div class="lg:grid-cols-1 gap-4  flex flex-col justify-center items-center">

                <x-button type="button" class="w-40 flex justify-center items-center mt-4 lg:mt-7"
                    wire:click="addProduct" spinner="addProduct">
                    Agregar
                </x-button>


            </div>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left table-auto">
                <caption class="caption-top px-6 py-3">
                    Productos agregados del producto base :
                    <strong>{{ $productBaseName }}</strong>
                </caption>
                <thead>
                    <tr class="border-y text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                        <th class="py-1 px-4">Codigo</th>
                        <th class="py-1 px-4">Producto</th>
                        <th class="py-1 px-4">Unidad</th>
                        <th class="py-1 px-4">Medida</th>
                        <th class="py-1 px-4">Precio de venta</th>
                        <th class="py-1 px-4">Precio de compra</th>
                        <th class="py-1 px-4"></th>
                    </tr>
                <tbody>
                    <template x-for="(product, index) in products" :key="product.id">
                        <tr class="border-b dark:border-gray-700  dark:bg-gray-500 dark:text-gray-50">
                            <td class=" hidden" x-text="product.unituuid"></td>
                            <td class=" hidden" x-text="product.measureuuid"></td>
                            <td class="py-1 px-4 w-20" x-text="product.codigo"></td>
                            <td class="py-1 px-4">
                                <x-forms.input type="text" x-model="product.name" class="w-60" />
                            </td>
                            <td class="py-1 px-4" x-text="product.unit"></td>
                            <td class="py-1 px-4" x-text="product.measure"></td>
                            <td class="w-20 "><x-forms.input type="number" x-model="product.price_sale"
                                    step="0.01" /></td>
                            <td class="w-20 "><x-forms.input type="number" x-model="product.price_purchase"
                                    step="0.01" /></td>
                            <td class="py-1 px-4">
                                <x-button type="button" x-on:click="removeProduct(index)">Eliminar</x-button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="products.length === 0">
                        <tr>
                            <td colspan="7" class="py-2 px-4 text-center">No hay productos agregados</td>
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


        <div class="flex justify-between items-center">
            <x-button type="submit" class="mt-4" spinner="saveProducts" wire:target="saveProducts"
                wire:loading.attr="disabled">
                Crear Categoria
            </x-button>

            <a href="{{ route('admin.categories.index') }}" class="ml-2">
                <x-button type="button" variant="secondary" class="mt-4">
                    Volver
                </x-button>

            </a>

        </div>

    </form>
</div>
