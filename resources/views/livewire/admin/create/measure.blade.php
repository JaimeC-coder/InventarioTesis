<div>
    <form wire:submit='save' class="space-y-4">

        <div class="grid grid-cols gap-4 mb-4">

            <x-forms.input label="Nombre" name="name" placeholder="Escribir nombre del rol" wire:model.live="name" />
            <x-forms.input label="Abreviatura" name="abbreviation" placeholder="Escribir abreviatura"
                wire:model.live="abbreviation" />
            <x-forms.input label="Código" name="code" placeholder="Escribir código" wire:model.live="code" />
            <x-forms.native-select label="Tipo de categoría" wire:model.live="category">
                <option value="">Seleccione Tipo de Movimiento</option>
                <option value="LIQUIDO" @if ($category === 'LIQUIDO') selected @endif>Líquido</option>
                <option value="SOLIDO" @if ($category === 'SOLIDO') selected @endif>Sólido</option>
            </x-forms.native-select>
            <x-forms.input label="Descripción para producto" name="description_for_product"
                placeholder="Escribir descripción para producto" wire:model.live="description_for_product" />
        </div>


        <div class="flex justify-between items-center">

            <a href="{{ route('admin.measures.index') }}" class="ml-2">
                <x-button type="button" variant="secondary" class="mt-4">
                    Volver
                </x-button>

            </a>
            <x-button type="submit" class="mt-4" spinner="save" wire:target="save" wire:loading.attr="disabled"
                :disabled="count($errors) > 0">
                Crear Unidad de almacenamiento
            </x-button>

        </div>

    </form>
</div>
