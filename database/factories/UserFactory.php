<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Position;
use App\Models\Department;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */

    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'position_id' => Position::inRandomOrder()->first()->id ?? 1,  // fallback to 1 if none exists
            'department_id' => Department::inRandomOrder()->first()->id ?? 1,      // fallback to 1 if none exists
            'username' => $this->faker->unique()->userName(),
            'fname' => $this->faker->firstName(),
            'lname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'contact' => $this->faker->phoneNumber(),
            'emp_id' => strtoupper(Str::random(8)),
            'password' => Hash::make('password'),
            'is_active' => fake()->randomElement(["pending", "active", "declined"]),
            'date_hired' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'signature' => $this->faker->text(200),
            'avatar' => $this->faker->optional()->imageUrl(200, 200, 'people'),
            'bio' => $this->faker->optional()->sentence(12),
        ];
    }


    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
