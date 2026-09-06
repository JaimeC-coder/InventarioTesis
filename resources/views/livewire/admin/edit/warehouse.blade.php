<div>
    <form wire:submit.prevent="save" class="space-y-4">

        <div class="grid grid-cols-2 gap-4 mb-4">
            <x-forms.input label="Nombre del Almacén" name="name" type="text" placeholder="Nombre del Almacén"
                wire:model.defer="name" />
            <x-forms.input label="Ubicación" name="location" type="text" placeholder="Ubicación"
                wire:model.defer="location" />

        </div>


        <div class="flex justify-between items-center">
            <a href="{{ route('admin.warehouses.index') }}" class="ml-2">
                <x-button type="button" variant="secondary" class="mt-4">
                    Volver
                </x-button>
            </a>


            <div class="flex items-center gap-2">
                <x-button type="button" variant="secondary" class="mt-4" wire:click="limpiar">
                    Limpiar
                </x-button>
                <x-button type="submit" class="mt-4" spinner="save" wire:target="save" wire:loading.attr="disabled"
                    :disabled="count($errors) > 0">
                    Editar Almacén
                </x-button>
            </div>

        </div>
    </form>
</div>
