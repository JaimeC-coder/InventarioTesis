<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Medidas', 'href' => route('admin.measures.index')],
    ['name' => 'Crear'],
]" :title="'Medida'">



    @livewire('admin.create.measure')

</x-admin-layout>
