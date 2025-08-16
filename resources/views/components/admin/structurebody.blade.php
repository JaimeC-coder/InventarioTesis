@props(['breadcrumbs' => []])
<div class="p-4 sm:ml-64">
    <div class="mt-14">


        <div class="mt-14 flex items-center">
            @include('layouts.includes.admin.breadcrumb')
            @isset($action)
                <div class="ml-auto">
                    {{ $action }}
                </div>
            @endisset
        </div>

        <div class="p-4 rounded-lg dark:border-gray-700  dark:bg-gray-800 ">

            {{ $slot }}
        </div>
    </div>
</div>
