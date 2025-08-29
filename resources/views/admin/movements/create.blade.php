<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Movimientos', 'href' => route('admin.movements.index')],
    ['name' => 'Crear'],
]" :title="'Movimiento'">



    @livewire('admin.movement-create')

</x-admin-layout>
