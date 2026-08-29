<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Roles']]" :title="'Roles'">

    <x-slot name="action">
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Crear Nuevo Rol</a>
    </x-slot>




    <livewire:admin.tables.permission-table />


    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
