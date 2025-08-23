<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Órdenes de compra', 'href' => route('admin.purchases-orders.index')],
    ['name' => 'Crear'],
]" :title="'Órdenes de compra'">



  @livewire('admin.purchase-order-create')
</x-admin-layout>
