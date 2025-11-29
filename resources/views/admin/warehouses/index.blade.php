<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Almacenes']]" :title="'Almacenes'">

    <x-slot name="action">
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary">Crear Nuevo Almacen</a>
    </x-slot>



    <livewire:admin.tables.warehouse-table />

    <livewire:warehouses.edit-modal />

    <livewire:export.pdf />


    @push('scripts')
        <script>
            // Escucha del evento Livewire
            window.addEventListener('swal:confirmDelete', event => {

                const data = event.detail[0];
                Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: data.icon,
                    showCancelButton: true,
                    confirmButtonText: data.confirmButtonText,
                    cancelButtonText: data.cancelButtonText,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteConfirmed', {
                            warehouseId: data.warehouseId
                        });
                    }
                });
            });

            window.addEventListener('swal:success', event => {
                const data = event.detail[0];
                Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: data.icon,
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endpush
</x-admin-layout>
