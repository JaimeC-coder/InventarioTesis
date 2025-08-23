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
                    ['name' => 'Compras Falta', 'route' => 'admin.warehouses.index'],
                ],
            ],
            [
                'name' => 'Ventas falta',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'ecommerce',
                'submenu' => [
                    ['name' => 'Proveedores', 'route' => 'admin.suppliers.index'],
                    ['name' => 'Ordenes de compra ', 'route' => 'admin.purchases-orders.index'],
                    ['name' => 'Compras Falta', 'route' => 'admin.warehouses.index'],
                ],
            ],
            [
                'name' => 'Movimientos falta',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'submenu' => [
                    ['name' => 'Proveedores', 'route' => 'admin.suppliers.index'],
                    ['name' => 'Ordenes de compra ', 'route' => 'admin.purchases-orders.index'],
                    ['name' => 'Compras Falta', 'route' => 'admin.warehouses.index'],
                ],
            ],
            [
                'name' => 'Reportes falta',
                'route' => 'admin.ecommerce',
                'active' => request()->routeIs('admin.ecommerce'),
                'icon' => 'customers',
                'submenu' => [
                    ['name' => 'Proveedores', 'route' => 'admin.suppliers.index'],
                    ['name' => 'Ordenes de compra ', 'route' => 'admin.purchases-orders.index'],
                    ['name' => 'Compras Falta', 'route' => 'admin.warehouses.index'],
                ],
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
                'name' => 'Logout',
                'route' => 'admin.logout',
                'active' => request()->routeIs('admin.logout'),
                'icon' => 'logout',
            ],
        ];
    }
}
