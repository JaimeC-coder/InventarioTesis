<?php

namespace App\config;

use Illuminate\Support\Facades\Auth;

class array_nav_use
{
    /**
     * Get the navigation items for the application.
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
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
                'name' => 'DASHBOARD',
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
                'permission' => $user?->can('view-helper') ?? false,
            ],
            [
                'name' => 'REPORTES',
                'route' => 'admin.reports',
                'active' => request()->routeIs('admin.reports'),
                'icon' => 'reports',
                'permission' => $user?->can('view-reports') ?? false,
            ],
            [
                'header' => 'Sistema',
            ],
            [
                'name' => 'Inventario',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'warehouse',
                'permission' => $user?->can('admin.categories.index') || $user?->can('admin.products.index') || $user?->can('admin.warehouses.index') || $user?->can('admin.units.index') || $user?->can('admin.measures.index'),
                'submenu' => [
                    ['name' => 'Categoria', 'route' => 'admin.categories.index', 'active' => request()->routeIs('admin.categories.index'), 'permission' => $user?->can('admin.categories.index') ?? false],
                    ['name' => 'Productos falta los export masivos', 'route' => 'admin.products.index', 'active' => request()->routeIs('admin.products.index'), 'permission' => $user?->can('admin.products.index') ?? false],
                    ['name' => 'Almacenes', 'route' => 'admin.warehouses.index', 'active' => request()->routeIs('admin.warehouses.index'), 'permission' => $user?->can('admin.warehouses.index') ?? false],
                    ['name' => 'Unidades de medida', 'route' => 'admin.units.index', 'active' => request()->routeIs('admin.units.index'), 'permission' => $user?->can('admin.units.index') ?? false],
                    ['name' => 'Unidades de almacenamiento', 'route' => 'admin.measures.index', 'active' => request()->routeIs('admin.measures.index'), 'permission' => $user?->can('admin.measures.index') ?? false],
                ],
            ],
            [
                'name' => 'Compras',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'permission' => $user?->can('admin.suppliers.index') || $user?->can('admin.purchases-orders.index') || $user?->can('admin.purchases.index'),
                'submenu' => [
                    ['name' => 'Proveedores', 'route' => 'admin.suppliers.index', 'active' => request()->routeIs('admin.suppliers.index'), 'permission' => $user?->can('admin.suppliers.index') ?? false],
                    ['name' => 'Ordenes de compra ', 'route' => 'admin.purchases-orders.index', 'active' => request()->routeIs('admin.purchases-orders.index'), 'permission' => $user?->can('admin.purchases-orders.index') ?? false],
                    ['name' => 'Compras', 'route' => 'admin.purchases.index', 'active' => request()->routeIs('admin.purchases.index'), 'permission' => $user?->can('admin.purchases.index') ?? false],
                ],
            ],
            [
                'name' => 'Ventas',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'ecommerce',
                'permission' => $user?->can('admin.sales.index') || $user?->can('admin.quotes.index') || $user?->can('admin.customers.index'),
                'submenu' => [
                    ['name' => 'Clientes', 'route' => 'admin.customers.index', 'active' => request()->routeIs('admin.customers.index'), 'permission' => $user?->can('admin.customers.index') ?? false],
                    ['name' => 'Cotizaciones', 'route' => 'admin.quotes.index', 'active' => request()->routeIs('admin.quotes.index'), 'permission' => $user?->can('admin.quotes.index') ?? false],
                    ['name' => 'Ventas', 'route' => 'admin.sales.index', 'active' => request()->routeIs('admin.sales.index'), 'permission' => $user?->can('admin.sales.index') ?? false],
                ],
            ],
            [
                'name' => 'Movimientos',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'permission' => $user?->can('admin.movements.index') || $user?->can('admin.transfers.index'),
                'submenu' => [
                    ['name' => 'Entradas y Salidas', 'route' => 'admin.movements.index', 'active' => request()->routeIs('admin.movements.index'), 'permission' => $user?->can('admin.movements.index') ?? false],
                    ['name' => 'Transferencias', 'route' => 'admin.transfers.index', 'active' => request()->routeIs('admin.transfers.index'), 'permission' => $user?->can('admin.transfers.index') ?? false],
                ],
            ],
            [
                'name' => 'Reportes falta',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'permission' => $user?->can('admin.reports.index') || $user?->can('admin.reports.purchases') || $user?->can('admin.reports.sales') || $user?->can('admin.reports.inventory'),
            ],
            ['header' => 'Configuraciones'],
            ['name' => 'Users', 'route' => 'admin.users.index', 'active' => request()->routeIs('admin.users.index'), 'icon' => 'users', 'permission' => $user?->can('admin.users.index') ?? false],
            ['name' => 'Roles', 'route' => 'admin.roles.index', 'active' => request()->routeIs('admin.roles.index'), 'icon' => 'users', 'permission' => $user?->can('admin.roles.index') ?? false],
            ['name' => 'Permisos','route' => 'admin.permissions.index','active' => request()->routeIs('admin.permissions.index'),'icon' => 'logout','permission' => $user?->can('admin.permissions.index') ?? false,],
            // ['name' => 'Settings','route' => 'admin.settings','active' => request()->routeIs('admin.settings'),'icon' => 'settings',],
        ];
    }
}
