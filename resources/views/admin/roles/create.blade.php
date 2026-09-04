<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Rol', 'href' => route('admin.roles.index')],
    ['name' => 'Crear'],
]" :title="'Crear Rol'">


    @livewire('admin.create.rol')

</x-admin-layout>
