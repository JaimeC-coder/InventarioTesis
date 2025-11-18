<div>
    @if ($productuuid)
        <div x-data="{ open: @entangle('showModal') }">
            <div x-show="open" class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded shadow-lg w-96">
                    <h2 class="text-lg font-semibold mb-4">Editar precio de: <br>  {{$name}}</h2>

                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label>Precio de compra</label>
                            <input type="text" wire:model.defer="price_purchase" class="w-full border p-2 rounded">
                            @error('price_purchase')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Precio de venta</label>
                            <input type="text" wire:model.defer="price_sale" class="w-full border p-2 rounded">
                            @error('price_sale')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
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
    @endif
</div>
