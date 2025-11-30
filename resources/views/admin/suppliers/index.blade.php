<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Proveedores']]" :title="'Proveedores'">

    <x-slot name="action">
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Crear Nuevo Proveedor</a>
    </x-slot>



    <livewire:admin.tables.supplier-table />

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
                            supplierId: data.supplierId
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
