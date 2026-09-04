<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Ventas', 'href' => route('admin.sales.index')],
    ['name' => 'Crear'],
]" :title="'Ventas'">



    @livewire('admin.create.sale')

</x-admin-layout>
