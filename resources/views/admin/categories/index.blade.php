<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Categoria']]" :title="'Categoria'">

    <x-slot name="action">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Crear Nueva Categoria</a>
    </x-slot>


    <livewire:admin.tables.category-table />

    @push('scripts')
        <script>
            let formEliminar = document.querySelectorAll('.delete-form');

            formEliminar.forEach(form => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>
