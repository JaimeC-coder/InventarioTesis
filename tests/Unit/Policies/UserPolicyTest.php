<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * admin.users.* solo lo tienen Administrador y Gerente en RolSeeder — es el
 * caso más representativo de "recurso sensible" para verificar la denegación.
 */
class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private UserPolicy $userPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolSeeder::class);
        $this->userPolicy = new UserPolicy();
    }

    public function test_administrador_can_manage_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Administrador');
        $this->assertTrue($this->userPolicy->viewAny($user));
        $this->assertTrue($this->userPolicy->create($user));
        $this->assertTrue($this->userPolicy->update($user));
        $this->assertTrue($this->userPolicy->delete($user));
    }

    public function test_jefe_de_almacen_cannot_manage_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Jefe de Almacen');
        $this->assertFalse($this->userPolicy->viewAny($user));
        $this->assertFalse($this->userPolicy->create($user));
        $this->assertFalse($this->userPolicy->update($user));
        $this->assertFalse($this->userPolicy->delete($user));
    }

    public function test_jefe_de_abastecimiento_cannot_manage_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Jefe de abastecimiento');
        $this->assertFalse($this->userPolicy->viewAny($user));
        $this->assertFalse($this->userPolicy->create($user));
    }
}
