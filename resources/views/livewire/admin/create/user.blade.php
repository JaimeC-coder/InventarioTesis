<div>
    <form wire:submit='save' class="space-y-4">

            <div class="grid grid-cols-3 gap-4 mb-4">
                <x-forms.input label="DNI" name="document" placeholder="Escribir su DNI" wire:model.live="document" />
                <x-forms.input label="Nombre" name="name" placeholder="Escribir su nombre" wire:model.live="name" />
                <x-forms.input label="Apellido" name="lastname" placeholder="Escribir su apellido"
                    wire:model.live="lastname" />


            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <x-forms.phone label="Telefono" name="phone" placeholder="Escribir su teléfono"
                    wire:model.live="phone" />
                <x-forms.datetime-picker wire:model.live="fechaNacimiento" label="Fecha de Nacimiento"
                    placeholder="Fecha de Nacimiento" parse-format="DD-MM-YYYY " />


            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <x-forms.input label="Correo Electrónico" name="email" type="email" placeholder="Correo Electrónico"
                    wire:model.live="email" />
                <x-forms.password label="Contraseña" name="password" type="password"
                    placeholder="Escribir su contraseña" wire:model.live="password" />

            </div>



            <x-forms.select label="Rol" placeholder="Escribe el rol" :async-data="['api' => route('admin.list-roles'), 'method' => 'POST']"
                option-label="name" option-value="id" wire:model="roles_id"/>




            <div class="flex justify-between items-center">

                <a href="{{ route('admin.users.index') }}" class="ml-2">
                    <x-button type="button" variant="secondary" class="mt-4">
                        Volver
                    </x-button>

                </a>
                <x-button type="submit" class="mt-4" spinner="save" wire:target="save" wire:loading.attr="disabled"
                    :disabled="count($errors) > 0">
                    Crear Usuario
                </x-button>

            </div>

        </form>
</div>
