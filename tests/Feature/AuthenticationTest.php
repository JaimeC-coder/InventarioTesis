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
        Role::create(['name' => 'Administrador']);
        $user->assignRole('Administrador');

        $testResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $testResponse->assertRedirect('/');
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
