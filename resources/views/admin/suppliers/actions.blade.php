<div class="flex gap-2">

    <a href="{{ route('admin.suppliers.edit', $supplier) }}">
    <x-button class="bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
        Editar

        </x-button>
    </a>

    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="inline delete-form">
        @csrf
        @method('DELETE')
        <x-button class="bg-red-600 text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600">
            Eliminar
        </x-button>

    </form>
</div>
