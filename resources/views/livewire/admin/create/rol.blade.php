<div>
     <form wire:submit='save' class="space-y-4">

            <div class="grid grid-cols gap-4 mb-4">

                <x-forms.input label="Nombre" name="name" placeholder="Escribir nombre del rol" wire:model.live="name" />

            </div>
            <div class="grid grid-cols-3 gap-4 mb-4">
                @foreach ($allPermissions as $permission)
                  <div class="flex items-center">
                        <input type="checkbox" id="permission-{{ $permission->id }}" value="{{ $permission->id }}"
                            wire:model.live="selectedPermissions"
                            class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                        <label for="permission-{{ $permission->id }}"
                            class="ml-2 block text-sm leading-5 text-gray-700">{{ $permission->description }}</label>
                    </div>
                @endforeach


            </div>

            <div class="flex justify-between items-center">

                <a href="{{ route('admin.users.index') }}" class="ml-2">
                    <x-button type="button" variant="secondary" class="mt-4">
                        Volver
                    </x-button>

                </a>
                <x-button type="submit" class="mt-4" spinner="save" wire:target="save" wire:loading.attr="disabled"
                    :disabled="count($errors) > 0">
                    Crear Rol
                </x-button>

            </div>

        </form>
</div>
