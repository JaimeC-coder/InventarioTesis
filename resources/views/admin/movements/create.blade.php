<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Movimientos', 'href' => route('admin.movements.index')],
    ['name' => 'Crear'],
]" :title="'Movimiento'">



    @livewire('admin.create.movement')

</x-admin-layout>
