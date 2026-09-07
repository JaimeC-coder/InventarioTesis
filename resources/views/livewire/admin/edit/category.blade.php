<div>
    <form wire:submit.prevent="save" class="space-y-4">


        <div class="grid grid-cols gap-4 mb-4">

            <x-forms.input label="Nombre de la categoría" name="name" type="text" placeholder="Nombre de la categoría"
                wire:model.live="name" />
            <x-forms.input label="Descripción" name="description" type="text" placeholder="Descripción"
                wire:model.live="description" />
            <x-forms.input label="Código" name="codigo" type="number" placeholder="Código" wire:model="codigo" />


        </div>

        <div class="flex justify-between items-center">

            <a href="{{ route('admin.categories.index') }}" class="ml-2">
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
                    Crear Categoría
                </x-button>
            </div>

        </div>




    </form>
</div>
