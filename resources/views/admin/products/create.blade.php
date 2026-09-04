<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Productos', 'href' => route('admin.products.index')],
    ['name' => 'Crear'],
]" :title="'Producto'">

    <div
        class="w-full p-4  bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        @livewire('admin.create.product')
    </div>



    @push('scripts')
        <script>
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
