<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document' => $this->faker->unique()->numerify('##########'),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'fechaNacimiento' => $this->faker->date(),
            'user_id' => \App\Models\User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Employee $employee): void {
            $roles = Role::all();
            $randomRole = $roles->random();
            $employee->user->assignRole($randomRole);
        });
    }
}
