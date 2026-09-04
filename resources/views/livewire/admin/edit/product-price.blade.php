<div>
    @if ($productuuid)
        <div x-data="{ open: @entangle('showModal') }">
            <div x-show="open" class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded shadow-lg w-96">
                    <h2 class="text-lg font-semibold mb-4">Editar precio de: <br> {{ $name }}</h2>

                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label>Precio de compra</label>
                            <input type="text" wire:model.defer="price_purchase" class="w-full border p-2 rounded">
                            @error('price_purchase')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="grid lg:grid-cols-1 gap-2 border-black p-2 rounded dark:border-white border">
                            <label>Precio de venta general:</label>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="mb-3">
                                    <label for="">Precio final</label>
                                    <input type="text" wire:keydown="editPrice"
                                        wire:model.defer="price_sale_regular_final" class="w-full border p-2 rounded">
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
                                    <input type="text" wire:keydown="editPrice"
                                        wire:model.defer="price_sale_a1_final" class="w-full border p-2 rounded">
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
