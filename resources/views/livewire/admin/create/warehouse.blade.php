<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    <div class="flex items-center gap-2">
        <x-button type="button" variant="secondary" class="mt-4" wire:click="limpiar">
            Limpiar
        </x-button>
        <x-button type="submit" class="mt-4" spinner="save" wire:target="save" wire:loading.attr="disabled"
            :disabled="count($errors) > 0">
            Crear Usuario
        </x-button>

    </div>
</div>
