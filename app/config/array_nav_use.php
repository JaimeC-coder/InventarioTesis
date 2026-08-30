<?php

namespace App\config;

use Illuminate\Support\Facades\Auth;

class array_nav_use
{
    /**
     * Get the navigation items for the application.
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null $user
     */
    public static function items(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return [
            [
                'header' => 'Principal',
            ],
            [
                'name' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => request()->routeIs('admin.dashboard'),
                'icon' => 'dashboard',
                'permission' => $user?->can('view-dashboard') ?? false,
            ],
            [
                'name' => 'HELLPER IA',
                'route' => 'admin.chatbot',
                'active' => request()->routeIs('admin.chatbot'),
                'icon' => 'messages',
                'permission' => $user?->can('view-Hellper') ?? false,
            ],
            [
                'header' => 'Sistema',
            ],
            [
                'name' => 'Inventario',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'warehouse',
                'permission' => ($user?->can('view-categories') && $user?->can('view-products') && $user?->can('view-warehouses') && $user?->can('view-units') && $user?->can('view-measures')) ?? false,
                'submenu' => [
                    ['name' => 'Categoria', 'route' => 'admin.categories.index', 'active' => request()->routeIs('admin.categories.index'), 'permission' => $user?->can('view-categories') ?? false],
                    ['name' => 'Productos falta los export masivos', 'route' => 'admin.products.index', 'active' => request()->routeIs('admin.products.index'), 'permission' => $user?->can('view-products') ?? false],
                    ['name' => 'Almacenes', 'route' => 'admin.warehouses.index', 'active' => request()->routeIs('admin.warehouses.index'), 'permission' => $user?->can('view-warehouses') ?? false],
                    ['name' => 'Unidades de medida', 'route' => 'admin.units.index', 'active' => request()->routeIs('admin.units.index'), 'permission' => $user?->can('view-units') ?? false],
                    ['name' => 'Unidades de envase', 'route' => 'admin.measures.index', 'active' => request()->routeIs('admin.measures.index'), 'permission' => $user?->can('view-measures') ?? false],
                ],
            ],
            [
                'name' => 'Compras',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'permission' => $user?->can('view-purchases') ?? false,
                'submenu' => [
                    ['name' => 'Proveedores', 'route' => 'admin.suppliers.index', 'active' => request()->routeIs('admin.suppliers.index'), 'permission' => $user?->can('view-suppliers') ?? false],
                    ['name' => 'Ordenes de compra ', 'route' => 'admin.purchases-orders.index', 'active' => request()->routeIs('admin.purchases-orders.index'), 'permission' => $user?->can('view-purchases-orders') ?? false],
                    ['name' => 'Compras', 'route' => 'admin.purchases.index', 'active' => request()->routeIs('admin.purchases.index'), 'permission' => $user?->can('view-purchases') ?? false],
                ],
            ],
            [
                'name' => 'Ventas',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'ecommerce',
                'permission' => $user?->can('view-sales') ?? false,
                'submenu' => [
                    ['name' => 'Clientes', 'route' => 'admin.customers.index', 'active' => request()->routeIs('admin.customers.index'), 'permission' => $user?->can('view-customers') ?? false],
                    ['name' => 'Cotizaciones', 'route' => 'admin.quotes.index', 'active' => request()->routeIs('admin.quotes.index'), 'permission' => $user?->can('view-quotes') ?? false],
                    ['name' => 'Ventas', 'route' => 'admin.sales.index', 'active' => request()->routeIs('admin.sales.index'), 'permission' => $user?->can('view-sales') ?? false],
                ],
            ],
            [
                'name' => 'Movimientos',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'permission' => $user?->can('view-movements') ?? false,
                'submenu' => [
                    ['name' => 'Entradas y Salidas', 'route' => 'admin.movements.index', 'active' => request()->routeIs('admin.movements.index'), 'permission' => $user?->can('view-movements') ?? false],
                    ['name' => 'Transferencias', 'route' => 'admin.transfers.index', 'active' => request()->routeIs('admin.transfers.index'), 'permission' => $user?->can('view-transfers') ?? false],
                ],
            ],
            [
                'name' => 'Reportes falta',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'permission' => $user?->can('view-reports') ?? false,
            ],
            ['header' => 'Configuraciones falta'],
            ['name' => 'Users', 'route' => 'admin.users.index', 'active' => request()->routeIs('admin.users.index'), 'icon' => 'users', 'permission' => $user?->can('view-users') ?? false],
            ['name' => 'Roles', 'route' => 'admin.roles.index', 'active' => request()->routeIs('admin.roles.index'), 'icon' => 'users', 'permission' => $user?->can('view-roles') ?? false],
            ['name' => 'Permisos','route' => 'admin.permissions.index','active' => request()->routeIs('admin.permissions.index'),'icon' => 'logout','permission' => $user?->can('view-permissions') ?? false,],
            ['name' => 'Settings','route' => 'admin.settings','active' => request()->routeIs('admin.settings'),'icon' => 'settings',],
        ];
    }
}
