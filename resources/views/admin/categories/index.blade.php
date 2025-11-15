<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Categoria']]" :title="'Categoria'">

    <x-slot name="action">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Crear Nueva Categoria</a>
    </x-slot>




    <livewire:admin.tables.category-table />


    <livewire:categories.edit-modal />


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
