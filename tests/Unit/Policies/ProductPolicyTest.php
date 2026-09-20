<?php

namespace Tests\Unit\Policies;

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prueba directa de la Policy (sin HTTP ni Livewire): confirma que cada
 * método delega en el permiso admin.products.* correcto, no en el rol.
 */
class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ProductPolicy $productPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolSeeder::class);
        $this->productPolicy = new ProductPolicy();
    }

    public function test_role_with_products_permissions_is_allowed(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Gerente');
        $this->assertTrue($this->productPolicy->viewAny($user));
        $this->assertTrue($this->productPolicy->create($user));
        $this->assertTrue($this->productPolicy->update($user));
        $this->assertTrue($this->productPolicy->delete($user));
    }

    public function test_role_without_products_permissions_is_denied(): void
    {
        // Ningún rol de RolSeeder carece de admin.products.*, así que se
        // prueba con un usuario sin ningún rol asignado: no debe tener nada.
        $user = User::factory()->create();
        $this->assertFalse($this->productPolicy->viewAny($user));
        $this->assertFalse($this->productPolicy->create($user));
        $this->assertFalse($this->productPolicy->update($user));
        $this->assertFalse($this->productPolicy->delete($user));
    }
}
