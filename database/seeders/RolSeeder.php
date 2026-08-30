<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1 = Role::create(['name' => 'Administrador']);
        $role2 = Role::create(['name' => 'Gerente']);
        $role3 = Role::create(['name' => 'Jefe de Almacen']);
        $role4 = Role::create(['name' => 'Jefe de abastecimiento']);
        Permission::create(['name' => 'chatbot.query.customer', 'description' => 'Consultar clientes'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'chatbot.query.product', 'description' => 'Consultar productos'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'chatbot.query.sale', 'description' => 'Consultar ventas'])->syncRoles([$role1, $role2, $role3,]);
        Permission::create(['name' => 'chatbot.query.conversion', 'description' => 'Consultar conversiones'])->syncRoles([$role1, $role2,  $role4]);
        //view-dashboard ,view-Hellper
        Permission::create(['name' => 'view-dashboard', 'description' => 'Ver dashboard'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'view-helper', 'description' => 'Ver ayuda IA'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'view-reports', 'description' => 'Ver reportes'])->syncRoles([$role1, $role2, $role3, $role4]);
        //Inventario:categories,products,warehouses,units,measures
        //*Categories
        Permission::create(['name' => 'admin.categories.index', 'description' => 'Ver categorías'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.categories.edit', 'description' => 'Editar categorías'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.categories.destroy', 'description' => 'Eliminar categorías'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.categories.create', 'description' => 'Crear categorías'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.categories.show', 'description' => 'Ver detalles de categoría'])->syncRoles([$role1, $role2, $role3]);
        //*Products
        Permission::create(['name' => 'admin.products.index', 'description' => 'Ver productos'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.products.edit', 'description' => 'Editar productos'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.products.destroy', 'description' => 'Eliminar productos'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.products.create', 'description' => 'Crear productos'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.products.show', 'description' => 'Ver detalles de producto'])->syncRoles([$role1, $role2, $role3]);
        //*Warehouses
        Permission::create(['name' => 'admin.warehouses.index', 'description' => 'Ver almacenes'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.warehouses.edit', 'description' => 'Editar almacenes'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.warehouses.destroy', 'description' => 'Eliminar almacenes'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.warehouses.create', 'description' => 'Crear almacenes'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.warehouses.show', 'description' => 'Ver detalles de almacén'])->syncRoles([$role1, $role2, $role3]);
        //*Units
        Permission::create(['name' => 'admin.units.index', 'description' => 'Ver unidades'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.units.edit', 'description' => 'Editar unidades'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.units.destroy', 'description' => 'Eliminar unidades'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.units.create', 'description' => 'Crear unidades'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.units.show', 'description' => 'Ver detalles de unidad'])->syncRoles([$role1, $role2, $role3]);
        //*Measures
        Permission::create(['name' => 'admin.measures.index', 'description' => 'Ver medidas'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.measures.edit', 'description' => 'Editar medidas'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.measures.destroy', 'description' => 'Eliminar medidas'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.measures.create', 'description' => 'Crear medidas'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'admin.measures.show', 'description' => 'Ver detalles de medida'])->syncRoles([$role1, $role2, $role3]);
        //Compras:
        //*suppliers
        Permission::create(['name' => 'admin.suppliers.index', 'description' => 'Ver proveedores'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.suppliers.edit', 'description' => 'Editar proveedores'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.suppliers.destroy', 'description' => 'Eliminar proveedores'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.suppliers.create', 'description' => 'Crear proveedores'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.suppliers.show', 'description' => 'Ver detalles de proveedor'])->syncRoles([$role1, $role2, $role4]);
        //*purchases-orders
        Permission::create(['name' => 'admin.purchases-orders.index', 'description' => 'Ver órdenes de compra'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.purchases-orders.edit', 'description' => 'Editar órdenes de compra'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.purchases-orders.destroy', 'description' => 'Eliminar órdenes de compra'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.purchases-orders.create', 'description' => 'Crear órdenes de compra'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.purchases-orders.show', 'description' => 'Ver detalles de orden de compra'])->syncRoles([$role1, $role2, $role4]);
        //*purchases
        Permission::create(['name' => 'admin.purchases.index', 'description' => 'Ver compras'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.purchases.edit', 'description' => 'Editar compras'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.purchases.destroy', 'description' => 'Eliminar compras'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.purchases.create', 'description' => 'Crear compras'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.purchases.show', 'description' => 'Ver detalles de compra'])->syncRoles([$role1, $role2, $role4]);
        //Ventas:
        //*customers
        Permission::create(['name' => 'admin.customers.index', 'description' => 'Ver clientes'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.customers.edit', 'description' => 'Editar clientes'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.customers.destroy', 'description' => 'Eliminar clientes'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.customers.create', 'description' => 'Crear clientes'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.customers.show', 'description' => 'Ver detalles de cliente'])->syncRoles([$role1, $role2, $role4]);
        //*quotes
        Permission::create(['name' => 'admin.quotes.index', 'description' => 'Ver cotizaciones'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.quotes.edit', 'description' => 'Editar cotizaciones'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.quotes.destroy', 'description' => 'Eliminar cotizaciones'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.quotes.create', 'description' => 'Crear cotizaciones'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.quotes.show', 'description' => 'Ver detalles de cotización'])->syncRoles([$role1, $role2, $role4]);
        //*sales
        Permission::create(['name' => 'admin.sales.index', 'description' => 'Ver ventas'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.sales.edit', 'description' => 'Editar ventas'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.sales.destroy', 'description' => 'Eliminar ventas'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.sales.create', 'description' => 'Crear ventas'])->syncRoles([$role1, $role2, $role4]);
        Permission::create(['name' => 'admin.sales.show', 'description' => 'Ver detalles de venta'])->syncRoles([$role1, $role2, $role4]);
        //Movimientos:
        //*movements
        Permission::create(['name' => 'admin.movements.index', 'description' => 'Ver movimientos'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'admin.movements.edit', 'description' => 'Editar movimientos'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'admin.movements.destroy', 'description' => 'Eliminar movimientos'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'admin.movements.create', 'description' => 'Crear movimientos'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'admin.movements.show', 'description' => 'Ver detalles de movimiento'])->syncRoles([$role1, $role2, $role3, $role4]);
        //*transfers
        Permission::create(['name' => 'admin.transfers.index', 'description' => 'Ver transferencias'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'admin.transfers.edit', 'description' => 'Editar transferencias'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'admin.transfers.destroy', 'description' => 'Eliminar transferencias'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'admin.transfers.create', 'description' => 'Crear transferencias'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'admin.transfers.show', 'description' => 'Ver detalles de transferencia'])->syncRoles([$role1, $role2, $role3, $role4]);
        //Ventas: Configuraciones,
        //*users
        Permission::create(['name' => 'admin.users.index', 'description' => 'Ver usuarios'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.users.edit', 'description' => 'Editar usuarios'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.users.destroy', 'description' => 'Eliminar usuarios'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.users.create', 'description' => 'Crear usuarios'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.users.show', 'description' => 'Ver detalles de usuario'])->syncRoles([$role1, $role2]);
        //*roles
        Permission::create(['name' => 'admin.roles.index', 'description' => 'Ver roles'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.roles.edit', 'description' => 'Editar roles'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.roles.destroy', 'description' => 'Eliminar roles'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.roles.create', 'description' => 'Crear roles'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.roles.show', 'description' => 'Ver detalles de role'])->syncRoles([$role1, $role2]);
        //*Permisos
        Permission::create(['name' => 'admin.permissions.index', 'description' => 'Ver permisos'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.permissions.edit', 'description' => 'Editar permisos'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.permissions.destroy', 'description' => 'Eliminar permisos'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.permissions.create', 'description' => 'Crear permisos'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'admin.permissions.show', 'description' => 'Ver detalles de permiso'])->syncRoles([$role1, $role2]);
        //otros:Reportes
    }
}
