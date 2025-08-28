<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Almacenes']]" :title="'Almacenes'">

    <x-slot name="action">
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary">Crear Nuevo Almacen</a>
    </x-slot>



    <livewire:admin.tables.warehouse-table />

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
