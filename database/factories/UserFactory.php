<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'USER',
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'profile_image' => 'imagess.png',
            'email_verified' => false,
        ];
    }

    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'ADMIN',
        ]);
    }
}
