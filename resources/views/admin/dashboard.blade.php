<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Prueba', 'href' => route('admin.dashboard')],
]" :title="'Hola'">

    <x-slot name="action">
        hola mundo
    </x-slot>







</x-admin-layout>
