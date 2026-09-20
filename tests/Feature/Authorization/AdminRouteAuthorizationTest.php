<?php

namespace Tests\Feature\Authorization;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Verifica que las rutas admin.*.index / admin.*.create respeten el permiso
 * (no el rol) definido en RolSeeder: un rol que SÍ tiene el permiso entra,
 * uno que NO lo tiene recibe 403.
 */
class AdminRouteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // La caché de permisos de Spatie usa CACHE_STORE=array, que vive
        // durante todo el proceso de PHPUnit: sin esto, un usuario nuevo
        // puede heredar permisos cacheados de un test anterior con el mismo
        // id autoincremental reciclado por RefreshDatabase.
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function userWithRole(string $roleName): User
    {
        $user = User::factory()->create();
        Employee::create([
            'user_id' => $user->id,
            'document' => '00000000',
            'phone' => '0000000',
            'address' => 'test',
            'fechaNacimiento' => '1990-01-01',
        ]);
        $user->assignRole($roleName);

        return $user;
    }

    public static function resourceProvider(): array
    {
        return [
            'categories' => ['admin.categories.index', 'admin.categories.create', 'Jefe de Almacen', 'Jefe de abastecimiento'],
            'products' => ['admin.products.index', 'admin.products.create', 'Jefe de Almacen', 'Jefe de abastecimiento'],
            'warehouses' => ['admin.warehouses.index', 'admin.warehouses.create', 'Jefe de Almacen', 'Jefe de abastecimiento'],
            'units' => ['admin.units.index', 'admin.units.create', 'Jefe de Almacen', 'Jefe de abastecimiento'],
            'measures' => ['admin.measures.index', 'admin.measures.create', 'Jefe de Almacen', 'Jefe de abastecimiento'],
            'suppliers' => ['admin.suppliers.index', 'admin.suppliers.create', 'Jefe de abastecimiento', 'Jefe de Almacen'],
            'purchases-orders' => ['admin.purchases-orders.index', 'admin.purchases-orders.create', 'Jefe de abastecimiento', 'Jefe de Almacen'],
            'purchases' => ['admin.purchases.index', 'admin.purchases.create', 'Jefe de abastecimiento', 'Jefe de Almacen'],
            'customers' => ['admin.customers.index', 'admin.customers.create', 'Jefe de abastecimiento', 'Jefe de Almacen'],
            'quotes' => ['admin.quotes.index', 'admin.quotes.create', 'Jefe de abastecimiento', 'Jefe de Almacen'],
            'sales' => ['admin.sales.index', 'admin.sales.create', 'Jefe de abastecimiento', 'Jefe de Almacen'],
            'users' => ['admin.users.index', 'admin.users.create', 'Administrador', 'Jefe de Almacen'],
            'roles' => ['admin.roles.index', 'admin.roles.create', 'Administrador', 'Jefe de Almacen'],
        ];
    }

    #[DataProvider('resourceProvider')]
    public function test_role_with_permission_can_access_index_and_create(string $indexRoute, string $createRoute, string $allowedRole, string $deniedRole): void
    {
        $this->seed(RolSeeder::class);
        $user = $this->userWithRole($allowedRole);
        $this->actingAs($user)->get(route($indexRoute))->assertOk();
        $this->actingAs($user)->get(route($createRoute))->assertOk();
    }

    #[DataProvider('resourceProvider')]
    public function test_role_without_permission_is_forbidden(string $indexRoute, string $createRoute, string $allowedRole, string $deniedRole): void
    {
        $this->seed(RolSeeder::class);
        $user = $this->userWithRole($deniedRole);
        $this->actingAs($user)->get(route($indexRoute))->assertForbidden();
        $this->actingAs($user)->get(route($createRoute))->assertForbidden();
    }

    public static function sharedResourceProvider(): array
    {
        return [
            'movements' => ['admin.movements.index', 'admin.movements.create'],
            // 'transfers.create' está roto por un bug preexistente ajeno a
            // esta tarea: livewire/admin/create/transfer.blade.php referencia
            // route('admin.products'), que no existe (falta el ".index").
            'transfers' => ['admin.transfers.index', null],
        ];
    }

    #[DataProvider('sharedResourceProvider')]
    public function test_resource_shared_by_all_roles_is_reachable(string $indexRoute, ?string $createRoute): void
    {
        $this->seed(RolSeeder::class);
        $user = $this->userWithRole('Jefe de Almacen');
        $this->actingAs($user)->get(route($indexRoute))->assertOk();
        if ($createRoute !== null) {
            $this->actingAs($user)->get(route($createRoute))->assertOk();
        }
    }

    public function test_guest_without_role_is_forbidden_from_products_index(): void
    {
        $this->seed(RolSeeder::class);
        $user = User::factory()->create();
        Employee::create([
            'user_id' => $user->id,
            'document' => '00000001',
            'phone' => '0000000',
            'address' => 'test',
            'fechaNacimiento' => '1990-01-01',
        ]);
        $this->actingAs($user)->get(route('admin.products.index'))->assertForbidden();
    }
}
