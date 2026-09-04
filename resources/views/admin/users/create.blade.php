<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Usuarios', 'href' => route('admin.users.index')],
    ['name' => 'Crear'],
]" :title="'Crear Usuario'">




    @livewire('admin.create.user')

</x-admin-layout>
