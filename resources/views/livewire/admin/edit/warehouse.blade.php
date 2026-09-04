<div>
    @if ($warehouseId)
        <div x-data="{ open: @entangle('showModal') }">
            <div x-show="open" class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded shadow-lg w-96">
                    <h2 class="text-lg font-semibold mb-4">Editar Almacén</h2>

                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" wire:model.defer="name" class="w-full border p-2 rounded">
                            @error('name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Ubicación</label>
                            <textarea wire:model.defer="location" class="w-full border p-2 rounded"></textarea>
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

