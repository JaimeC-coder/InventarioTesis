<?php

namespace App\config;

class array_nav_use
{
    public static function items(): array
    {
        return [
            [
                'header' => 'Principal',
            ],
            [
                'name' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => request()->routeIs('admin.dashboard'),
                'icon' => 'dashboard',
            ],
            [
                'header' => 'Sistema',
            ],
            [
                'name' => 'Inventario',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'warehouse',
                'submenu' => [
                    ['name' => 'Categoria', 'route' => 'admin.categories.index'],
                    ['name' => 'Productos', 'route' => 'admin.products.index'],
                    ['name' => 'Almacenes', 'route' => 'admin.warehouses.index'],
                ],
            ],
            [
                'name' => 'Compras',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'submenu' => [
                    ['name' => 'Proveedores', 'route' => 'admin.suppliers.index'],
                    ['name' => 'Ordenes de compra ', 'route' => 'admin.purchases-orders.index'],
                    ['name' => 'Compras', 'route' => 'admin.purchases.index'],
                ],
            ],
            [
                'name' => 'Ventas',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'ecommerce',
                'submenu' => [
                    ['name' => 'Clientes', 'route' => 'admin.customers.index'],
                    ['name' => 'Cotizaciones', 'route' => 'admin.quotes.index'],
                    ['name' => 'Ventas', 'route' => 'admin.sales.index'],
                ],
            ],
            [
                'name' => 'Movimientos',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'submenu' => [
                    ['name' => 'Entradas y Salidas', 'route' => 'admin.movements.index'],
                    ['name' => 'Transferencias', 'route' => 'admin.transfers.index'],
                ],
            ],
            [
                'name' => 'Reportes falta',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
            ],
            ['header' => 'Configuraciones falta'],
            ['name' => 'Users', 'route' => 'admin.users', 'active' => request()->routeIs('admin.users'), 'icon' => 'users'],
            ['name' => 'Roles', 'route' => 'admin.users', 'active' => request()->routeIs('admin.users'), 'icon' => 'users'],
            [
                'name' => 'Settings',
                'route' => 'admin.settings',
                'active' => request()->routeIs('admin.settings'),
                'icon' => 'settings',
            ],
            [
                'name' => 'Permisos',
                'route' => 'admin.logout',
                'active' => request()->routeIs('admin.logout'),
                'icon' => 'logout',
            ],
        ];
    }
}
