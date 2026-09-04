<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Usuarios']]" :title="'Usuarios'">

    <x-slot name="action">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Crear Nuevo Usuario</a>
    </x-slot>




    <livewire:admin.tables.user-table />


    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
