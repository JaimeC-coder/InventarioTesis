<x-admin-layout :breadcrumbs="[['name' => 'Dashboard', 'href' => route('admin.dashboard')], ['name' => 'Roles']]" :title="'Roles'">


    <livewire:admin.tables.permission-table />


    @push('scripts')
        <script>

        </script>
    @endpush
</x-admin-layout>
