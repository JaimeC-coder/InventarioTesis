<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Unidades', 'href' => route('admin.units.index')],
    ['name' => 'Crear'],
]" :title="'Unidad'">



    @livewire('admin.create.unit')

</x-admin-layout>
