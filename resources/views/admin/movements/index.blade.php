<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Movimientos']]" :title="'Movimientos'">

    <x-slot name="action">
        <a href="{{ route('admin.movements.create') }}" class="btn btn-primary">Crear Nuevo Movimiento</a>
    </x-slot>



    <livewire:admin.tables.movement-table />

    <livewire:export.pdf />
    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
