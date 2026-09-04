<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles base disponibles para todos los tests de esta clase
        Role::create(['name' => 'prueba']);
        Role::create(['name' => 'prueba2']);

    }

    public function test_login_screen_can_be_rendered(): void
    {
        $testResponse = $this->get('/login');

        $testResponse->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        Employee::create([
            'user_id' => $user->id,
            'document' => '00000000',
            'phone' => '0000000',
            'address' => 'test',
            'fechaNacimiento' => '1990-01-01',
        ]);

        $user->assignRole('prueba');

        $testResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $testResponse->assertRedirect('/');
    }
    public function test_users_without_role_or_employee_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        // Sin rol y sin employee asignado

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_with_role_and_employee_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('prueba2');
        Employee::create([
            'user_id' => $user->id,
            'document' => '00000000',
            'phone' => '0000000',
            'address' => 'test',
            'fechaNacimiento' => '1990-01-01',
        ]);
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
