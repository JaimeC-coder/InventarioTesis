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

        </script>
    @endpush
</x-admin-layout>
