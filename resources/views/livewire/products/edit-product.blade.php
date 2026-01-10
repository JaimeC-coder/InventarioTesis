 <div>

     <div x-data="{ open: @entangle('showModalProduct') }" x-cloak>
         <div x-show="open" class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center z-50">
             <div class="bg-white p-6 rounded shadow-lg w-1/2">


                 <div wire:loading wire:target="loadProduct" class="flex justify-center items-center h-32">
                     <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
                 </div>

                 {{-- El formulario se oculta mientras se cargan los datos --}}
                 <div wire:loading.remove wire:target="loadProduct">

                     <h2 class="text-lg font-semibold mb-4">Editar : <br> {{ $name }}</h2>

                     <form wire:submit.prevent="saveProduct" class="space-y-4">
                         <div class="grid lg:grid-cols-1 ">
                             <div class="flex gap-4">
                                 <x-forms.input label="Codigo de grupo (CP)" name="name" type="text"
                                     placeholder="Ingrese el nombre del producto" required wire:model="category_code" />
                                 <x-forms.input label="Codigo" name="name" type="text"
                                     placeholder="Ingrese el codigo" required wire:model="code" />
                                 <x-forms.select label="Unidades del producto" placeholder="Cambiar unidades..."
                                     :async-data="['api' => route('admin.units'), 'method' => 'POST']" option-label="name" option-value="uuid" wire:model="unit_uuid"
                                     x-indicator />
                                 <x-forms.select label="Medida del producto" placeholder="Cambiar medida..."
                                     :async-data="['api' => route('admin.measures'), 'method' => 'POST']" option-label="name" option-value="uuid" wire:model="measure_uuid"
                                     x-indicator />

                             </div>
                             <div class="grid lg:grid-cols-2 gap-4">
                                 <x-forms.select label="Categoria del producto" placeholder="Cambiar categoria..."
                                     :async-data="['api' => route('admin.categories'), 'method' => 'POST']" option-label="name" option-value="uuid"
                                     wire:model="category_uuid" x-indicator />
                                 <x-forms.select label="Producto base" placeholder="Cambiar producto base..."
                                     :async-data="['api' => route('admin.baseProducts'), 'method' => 'POST']" option-label="name" option-value="uuid"
                                     wire:model="productBase_uuid" x-indicator />
                             </div>
                             <x-forms.input label="Nombre del producto" name="name" type="text"
                                 placeholder="Nombre del producto" required wire:model="name" />
                         </div>
                         <div class="grid lg:grid-cols-1 gap-4">
                             <div class="grid lg:grid-cols-1 gap-2 border-black p-2 rounded dark:border-white border">
                                 <label>Precio de venta general:</label>
                                 <div class="grid grid-cols-3 gap-2">
                                     <div class="mb-3">
                                         <label for="">Precio final</label>
                                         <input type="text" wire:keydown="editPrice" wire:model.defer="price_sale_regular_final"
                                             class="w-full border p-2 rounded">
                                     </div>
                                     <div class="mb-3">
                                         <span>Precio de uso</span>
                                         <input type="text" wire:keydown="editPrice" wire:model.defer="price_sale_regular"
                                             class="w-full border p-2 rounded" disabled>

                                     </div>
                                 </div>


                                 @error('price_sale_regular')
                                     <span class="text-red-500">{{ $message }}</span>
                                 @enderror
                             </div>
                             <div class="grid lg:grid-cols-1 gap-2 border-black p-2 rounded dark:border-white border">
                                 <label>Precio de venta A1:</label>
                                 <div class="grid grid-cols-3 gap-2">
                                     <div class="mb-3">
                                         <label for="">Precio final</label>
                                         <input type="text" wire:keydown="editPrice" wire:model.defer="price_sale_a1_final"
                                             class="w-full border p-2 rounded">
                                     </div>
                                     <div class="mb-3">
                                         <span>Precio de uso</span>
                                         <input type="text" wire:keydown="editPrice" wire:model.defer="price_sale_a1"
                                             class="w-full border p-2 rounded" disabled>

                                     </div>
                                 </div>
                                 @error('price_sale_a1')
                                     <span class="text-red-500">{{ $message }}</span>
                                 @enderror
                             </div>
                             <div class="grid lg:grid-cols-1 gap-2 border-black p-2 rounded dark:border-white border">
                                 <label>Precio de compra:</label>
                                 <input type="text" wire:model.defer="price_purchase"
                                     class="w-full border p-2 rounded">
                                 @error('price_purchase')
                                     <span class="text-red-500">{{ $message }}</span>
                                 @enderror
                             </div>
                         </div>

                         <div class="flex justify-end space-x-2">
                             <button type="button" @click="open = false"
                                 class="bg-gray-500 text-white px-3 py-1 rounded">
                                 Cancelar
                             </button>
                             <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">
                                 Guardar
                             </button>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 </div>
