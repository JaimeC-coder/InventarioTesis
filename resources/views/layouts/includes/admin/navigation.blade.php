@php

    $links = [
        [
            'name' => 'Dashboard',
            'route' => 'admin.dashboard',
            'active' => request()->routeIs('admin.dashboard'),
            'icon' => 'dashboard',
        ],
        [
            'header' => 'Management',
        ],
        [
            'name' => 'E-commerce',
            'route' => 'admin.ecommerce',
            'active' => request()->routeIs('admin.ecommerce'),
            'icon' => 'ecommerce',
            // 'submenu' => [
            //     ['name' => 'Products', 'route' => 'admin.products'],
            //     // ['name' => 'Billing', 'route' => 'admin.billing'],
            //     // ['name' => 'Invoice', 'route' => 'admin.invoice'],
            // ],
        ],
        [
            'name' => 'Categories',
            'route' => 'admin.categories.index',
            'active' => request()->routeIs('admin.categories.*'),
            'icon' => 'categorie',
            'submenu' => [
                ['name' => 'Crear', 'route' => 'admin.categories.create'],
                // ['name' => 'Billing', 'route' => 'admin.billing'],
                // ['name' => 'Invoice', 'route' => 'admin.invoice'],
            ],
        ],


        ['name' => 'Users', 'route' => 'admin.users', 'active' => request()->routeIs('admin.users'), 'icon' => 'users'],

        [
            'name' => 'Products',
            'route' => 'admin.products.index',
            'active' => request()->routeIs('admin.products.*'),
            'icon' => 'products',
            'submenu' => [
                ['name' => 'Crear', 'route' => 'admin.products.create'],
            ]

        ],

        [
            'name' => 'Settings',
            'route' => 'admin.settings',
            'active' => request()->routeIs('admin.settings'),
            'icon' => 'settings',
        ],

        [
            'name' => 'Logout',
            'route' => 'admin.logout',
            'active' => request()->routeIs('admin.logout'),
            'icon' => 'logout',
        ],

        // login
        //['name' => 'Login', 'route' => 'admin.login', 'active' => request()->routeIs('admin.login'), 'icon' => 'login'],
    ];

@endphp




<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">

            @foreach ($links as $link)
                @isset($link['header'])
                    <li class="text-gray-500 uppercase dark:text-gray-400">{{ $link['header'] }}</li>
                @else
                    @if (isset($link['submenu']))
                        <li>
                            <button type="button"
                                class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                aria-controls="dropdown-{{ Str::slug($link['name']) }}"
                                data-collapse-toggle="dropdown-{{ Str::slug($link['name']) }}">
                                <x-dynamic-component :component="'icons.' . $link['icon']" />
                                <span class="flex-1 ms-3 text-left whitespace-nowrap">{{ $link['name'] }}</span>
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>
                            <ul id="dropdown-{{ Str::slug($link['name']) }}" class="hidden py-2 space-y-2">
                                @foreach ($link['submenu'] as $sub)
                                    <li>
                                        <a href="{{ route($sub['route']) }}"
                                            class="flex items-center w-full p-2 pl-11 text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                            {{ $sub['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        {{-- Elemento normal --}}
                        <li>
                            <a href="{{ route($link['route']) }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <x-dynamic-component :component="'icons.' . $link['icon']" />
                                <span class="ms-3">{{ $link['name'] }}</span>
                            </a>
                        </li>
                    @endif
                @endisset
            @endforeach

        </ul>
    </div>
</aside>
