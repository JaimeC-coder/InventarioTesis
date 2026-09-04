<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Transferencias', 'href' => route('admin.transfers.index')],
    ['name' => 'Crear'],
]" :title="'Crear Transferencia'">



    @livewire('admin.create.transfer')

</x-admin-layout>
