<?php

namespace Tests\Feature\Authorization;

use App\Livewire\Admin\Create\Unit as CreateUnit;
use App\Livewire\Admin\Create\User as CreateUser;
use App\Livewire\Admin\Edit\Unit as EditUnit;
use App\Livewire\Admin\Tables\ProductTable;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A diferencia de AdminRouteAuthorizationTest (que cubre las páginas), esto
 * cubre la mutación real: el save()/delete() de Livewire, invocado por su
 * propio canal AJAX y no por la ruta del resource controller (que está vacía).
 */
class AdminMutationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
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

    public function test_create_unit_save_is_forbidden_without_permission(): void
    {
        $this->seed(RolSeeder::class);
        // Ningun rol de RolSeeder carece de admin.units.create salvo que se cree
        // un usuario sin rol: sin permisos en absoluto.
        $this->userWithRole('Jefe de abastecimiento');
        // Jefe de abastecimiento SÍ tiene admin.units.create en el seeder actual,
        // así que probamos con un usuario sin ningún rol asignado.
        $bareUser = User::factory()->create();
        Employee::create([
            'user_id' => $bareUser->id,
            'document' => '00000002',
            'phone' => '0000000',
            'address' => 'test',
            'fechaNacimiento' => '1990-01-01',
        ]);
        // Livewire::test() convierte una AuthorizationException lanzada dentro
        // del componente en una respuesta 403 real (no la re-lanza como
        // excepción PHP), así que se verifica con assertForbidden().
        Livewire::actingAs($bareUser)
            ->test(CreateUnit::class)
            ->set('name', 'Caja')
            ->set('abbreviation', 'CJ')
            ->set('code', 'CJ01')
            ->call('save')
            ->assertForbidden();
    }

    public function test_create_unit_save_succeeds_with_permission(): void
    {
        $this->seed(RolSeeder::class);
        $user = $this->userWithRole('Administrador');
        Livewire::actingAs($user)
            ->test(CreateUnit::class)
            ->set('name', 'Caja')
            ->set('abbreviation', 'CJ')
            ->set('code', 'CJ01')
            ->call('save');
        $this->assertDatabaseHas('units', ['name' => 'Caja']);
    }

    public function test_edit_unit_mount_is_forbidden_without_permission(): void
    {
        $this->seed(RolSeeder::class);
        $unit = Unit::create(['name' => 'Docena', 'abbreviation' => 'DOC', 'code' => 'DOC01']);
        $bareUser = User::factory()->create();
        Employee::create([
            'user_id' => $bareUser->id,
            'document' => '00000003',
            'phone' => '0000000',
            'address' => 'test',
            'fechaNacimiento' => '1990-01-01',
        ]);
        Livewire::actingAs($bareUser)
            ->test(EditUnit::class, ['modelsUnit' => $unit])
            ->assertForbidden();
    }

    public function test_edit_unit_save_succeeds_with_permission(): void
    {
        $this->seed(RolSeeder::class);
        $unit = Unit::create(['name' => 'Docena', 'abbreviation' => 'DOC', 'code' => 'DOC01']);
        $user = $this->userWithRole('Administrador');
        Livewire::actingAs($user)
            ->test(EditUnit::class, ['modelsUnit' => $unit])
            ->set('name', 'Docena Actualizada')
            ->call('save');
        $this->assertDatabaseHas('units', ['id' => $unit->id, 'name' => 'Docena Actualizada']);
    }

    public function test_product_table_delete_is_forbidden_without_permission(): void
    {
        $this->seed(RolSeeder::class);
        $product = Product::create(['name' => 'Producto de prueba']);
        $bareUser = User::factory()->create();
        Employee::create([
            'user_id' => $bareUser->id,
            'document' => '00000004',
            'phone' => '0000000',
            'address' => 'test',
            'fechaNacimiento' => '1990-01-01',
        ]);
        Livewire::actingAs($bareUser)
            ->test(ProductTable::class)
            ->call('delete', $product->uuid)
            ->assertForbidden();
    }

    public function test_product_table_delete_succeeds_with_permission(): void
    {
        $this->seed(RolSeeder::class);
        $product = Product::create(['name' => 'Producto de prueba']);
        $user = $this->userWithRole('Administrador');
        Livewire::actingAs($user)
            ->test(ProductTable::class)
            ->call('delete', $product->uuid);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_create_user_save_is_forbidden_for_role_without_permission(): void
    {
        $this->seed(RolSeeder::class);
        $user = $this->userWithRole('Jefe de Almacen');
        Livewire::actingAs($user)
            ->test(CreateUser::class)
            ->call('save')
            ->assertForbidden();
    }
}
