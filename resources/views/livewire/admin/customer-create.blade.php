<div>
    <form wire:submit='save' class="space-y-4">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <x-forms.select label="Tipo de Identidad" name="identity" :options="$identities" option-label="name"
                option-value="id" placeholder="Seleccione un tipo de identidad" wire:model.live="identity" />
            <x-forms.input label="Número de Documento" name="document_number" type="number"
                value="{{ old('document_number') }}" placeholder="Número de Documento" wire:model="document_number">
                <x-slot name="append">
                    <x-forms.button class="h-full" icon="magnifying-glass" rounded="rounded-r-md" primary flat
                        wire:click="generateDocumentNumber" :disabled="!$active" />
                </x-slot>
            </x-forms.input>

        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <x-forms.select label="Tipo de cliente" name="type" :options="$types" option-label="name"
                option-value="id" placeholder="Seleccione un tipo de identidad" wire:model.live="type" />

            <x-forms.input label="Nombre" name="name" type="text" value="{{ old('name') }}" placeholder="Nombre"
                wire:model.live="name" :disabled="$active" />


        </div>

        <x-forms.input label="Dirección" name="address" type="text" value="{{ old('address') }}"
            placeholder="Dirección" class="mb-4" wire:model="address" />

        <div class="grid grid-cols-2 gap-4 mb-4">
            <x-forms.input label="Teléfono" name="phone" type="text" value="{{ old('phone') }}"
                placeholder="Teléfono" wire:model="phone" />
            <x-forms.input label="Correo Electrónico" name="email" type="email" value="{{ old('email') }}"
                placeholder="Correo Electrónico" wire:model="email" />
        </div>



        <div class="flex justify-between items-center">

            <a href="{{ route('admin.customers.index') }}" class="ml-2">
                <x-button type="button" variant="secondary" class="mt-4">
                    Volver
                </x-button>

            </a>
            <x-button type="submit" class="mt-4" spinner="save" wire:target="save" wire:loading.attr="disabled"
                :disabled="count($errors) > 0">
                Crear Cliente
            </x-button>

        </div>

    </form>
</div>
