<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Smoke test for the Livewire reorganization (Admin/Create + Admin/Edit).
 * Runs on a case-sensitive filesystem (CI/Linux) so a wrong-case namespace,
 * view('...') path, or <livewire:...> tag fails here even though it would
 * silently work on a case-insensitive Windows dev machine.
 */
class AdminReorganizedViewsTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsEmployee(): User
    {
        $user = User::factory()->create();

        Employee::create([
            'user_id' => $user->id,
            'document' => '00000000',
            'phone' => '0000000',
            'address' => 'test',
            'fechaNacimiento' => '1990-01-01',
        ]);

        $this->actingAs($user);

        return $user;
    }

    public static function createPageRouteProvider(): array
    {
        return [
            'products create' => ['admin.products.create'],
            'purchases create' => ['admin.purchases.create'],
            'sales create' => ['admin.sales.create'],
            'customers create' => ['admin.customers.create'],
            'suppliers create' => ['admin.suppliers.create'],
            'quotes create' => ['admin.quotes.create'],
            'purchases-orders create' => ['admin.purchases-orders.create'],
        ];
    }

    #[DataProvider('createPageRouteProvider')]
    public function test_create_page_renders_its_livewire_component(string $routeName): void
    {
        $this->actingAsEmployee();

        $testResponse = $this->get(route($routeName));

        $testResponse->assertOk();
    }

    public static function indexPageRouteProvider(): array
    {
        return [
            'categories index' => ['admin.categories.index'],
            'warehouses index' => ['admin.warehouses.index'],
            'products index' => ['admin.products.index'],
            'suppliers index' => ['admin.suppliers.index'],
        ];
    }

    #[DataProvider('indexPageRouteProvider')]
    public function test_index_page_renders_its_edit_livewire_component(string $routeName): void
    {
        $this->actingAsEmployee();

        $testResponse = $this->get(route($routeName));

        $testResponse->assertOk();
    }
}
