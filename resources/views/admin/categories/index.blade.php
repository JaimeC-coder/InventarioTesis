<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Categoria']]" :title="'Categoria'">

    <x-slot name="action">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Crear Nueva Categoria</a>
    </x-slot>




    <livewire:admin.tables.category-table />


    <livewire:admin.edit.category />

    <livewire:export.pdf />


    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
