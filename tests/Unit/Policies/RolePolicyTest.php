<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\RolePolicy;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Role vive en Spatie\Permission\Models\Role, fuera de App\Models: el
 * auto-discovery de Laravel no lo encuentra solo, por eso se registra a
 * mano en AppServiceProvider::boot(). Esta prueba también confirma que ese
 * registro funciona a través de Gate::authorize().
 */
class RolePolicyTest extends TestCase
{
    use RefreshDatabase;

    private RolePolicy $rolePolicy;

    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolSeeder::class);
        $this->rolePolicy = new RolePolicy();
    }

    public function test_administrador_can_manage_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Administrador');
        $this->assertTrue($this->rolePolicy->viewAny($user));
        $this->assertTrue($this->rolePolicy->create($user));
    }

    public function test_jefe_de_almacen_cannot_manage_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Jefe de Almacen');
        $this->assertFalse($this->rolePolicy->viewAny($user));
        $this->assertFalse($this->rolePolicy->create($user));
    }

    public function test_gate_resolves_role_policy_registered_in_provider(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Administrador');
        $role = Role::where('name', 'Gerente')->firstOrFail();
        $this->assertTrue($user->can('update', $role));
    }
}
