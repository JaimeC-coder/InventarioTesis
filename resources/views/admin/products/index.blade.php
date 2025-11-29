<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Productos']]" :title="'Productos'">

    <x-slot name="action">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Crear Nuevo Producto</a>
    </x-slot>



    <livewire:admin.tables.product-table />



    <livewire:products.edit-price />
    <livewire:products.edit-product />
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
                            categoryId: data.categoryId
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
