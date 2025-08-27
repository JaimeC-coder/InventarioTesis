<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Cotizaciones', 'href' => route('admin.quotes.index')],
    ['name' => 'Crear'],
]" :title="'Cotización'">



    @livewire('admin.quote-create')

</x-admin-layout>
