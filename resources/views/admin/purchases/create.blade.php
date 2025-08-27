<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Compra', 'href' => route('admin.purchases.index')],
    ['name' => 'Crear'],
]" :title="'Compra'">



    @livewire('admin.purchases-create')

</x-admin-layout>
